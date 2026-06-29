<?php

namespace App\Providers;

use App\System;
use App\Utils\ModuleUtil;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\Filesystem;
use Spatie\Dropbox\Client as DropboxClient;
use Spatie\FlysystemDropbox\DropboxAdapter;
use Laravel\Passport\Console\ClientCommand;
use Laravel\Passport\Console\InstallCommand;
use Laravel\Passport\Console\KeysCommand;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        ini_set('memory_limit', '-1');
        set_time_limit(0);

        if (config('app.debug')) {
            error_reporting(E_ALL & ~E_USER_DEPRECATED);
        } else {
            error_reporting(0);
        }

        // Force HTTPS jika perlu
        $url = parse_url(config('app.url'));
        if ($url['scheme'] == 'https') {
            \URL::forceScheme('https');
        }

        if (request()->has('lang')) {
            \App::setLocale(request()->get('lang'));
        }

        // Ambil asset tambahan dari DB dan cache jika memungkinkan
        if (isAppInstalled()) {
            $keys = ['additional_js', 'additional_css'];

            try {
                $settings = Cache::remember('system_additional_assets', now()->addMinutes(10), function () use ($keys) {
                    return System::whereIn('key', $keys)->pluck('value', 'key');
                });
            } catch (\Exception $e) {
                // Fallback jika Redis atau cache lain error
                $settings = System::whereIn('key', $keys)->pluck('value', 'key');
            }

            View::share('__system_settings', $settings);
        }

        Blade::withoutDoubleEncoding();
        Paginator::useBootstrapThree();
        \Illuminate\Pagination\Paginator::useBootstrap();

        // Integrasi Dropbox
        Storage::extend('dropbox', function ($app, $config) {
            $adapter = new DropboxAdapter(new DropboxClient(
                $config['authorization_token']
            ));

            return new FilesystemAdapter(
                new Filesystem($adapter, $config),
                $adapter,
                $config
            );
        });



        $asset_v = config('constants.asset_version', 1);
        View::share('asset_v', $asset_v);
        // Share data tambahan dari modul
        View::composer(['*'], function ($view) {
            $enabled_modules = !empty(session('business.enabled_modules')) ? session('business.enabled_modules') : [];
            $__is_pusher_enabled = isPusherEnabled();

            if (!Auth::check()) {
                $__is_pusher_enabled = false;
            }

            $view->with('enabled_modules', $enabled_modules);
            $view->with('__is_pusher_enabled', $__is_pusher_enabled);
        });

        View::composer(['layouts.*'], function ($view) {
            if (isAppInstalled()) {
                $keys = ['additional_js', 'additional_css'];
                $__system_settings = View::shared('__system_settings');

                $moduleUtil = new ModuleUtil;
                $module_additional_script = $moduleUtil->getModuleData('get_additional_script');

                $additional_views = [];
                $additional_html = '';

                foreach ($module_additional_script as $value) {
                    if (!empty($value['additional_js'])) {
                        $__system_settings['additional_js'] = ($__system_settings['additional_js'] ?? '') . $value['additional_js'];
                    }
                    if (!empty($value['additional_css'])) {
                        $__system_settings['additional_css'] = ($__system_settings['additional_css'] ?? '') . $value['additional_css'];
                    }
                    if (!empty($value['additional_html'])) {
                        $additional_html .= $value['additional_html'];
                    }
                    if (!empty($value['additional_views'])) {
                        $additional_views = array_merge($additional_views, $value['additional_views']);
                    }
                }

                $view->with('__additional_views', $additional_views);
                $view->with('__additional_html', $additional_html);
                $view->with('__system_settings', $__system_settings);
            }
        });

        Schema::defaultStringLength(191);

        // Blade directives
        Blade::directive('num_format', function ($expression) {
            return "number_format($expression, session('business.currency_precision', 2), session('currency')['decimal_separator'], session('currency')['thousand_separator'])";
        });

        Blade::directive('format_quantity', function ($expression) {
            return "number_format($expression, session('business.quantity_precision', 2), session('currency')['decimal_separator'], session('currency')['thousand_separator'])";
        });

        Blade::directive('transaction_status', function ($status) {
            return "<?php if($status == 'ordered'){ echo 'bg-aqua'; }elseif($status == 'pending'){ echo 'bg-red'; }elseif ($status == 'received') { echo 'bg-light-green'; }?>";
        });

        Blade::directive('payment_status', function ($status) {
            return "<?php if($status == 'partial'){ echo 'bg-aqua'; }elseif($status == 'due'){ echo 'bg-yellow'; }elseif ($status == 'paid') { echo 'bg-light-green'; }elseif ($status == 'overdue' || $status == 'partial-overdue') { echo 'bg-red'; }?>";
        });

        Blade::directive('show_tooltip', function ($message) {
            return "<?php if(session('business.enable_tooltip')){ echo '<i class=\"fa fa-info-circle text-info hover-q no-print\" aria-hidden=\"true\" data-container=\"body\" data-toggle=\"popover\" data-placement=\"auto bottom\" data-content=\"' . $message . '\" data-html=\"true\" data-trigger=\"hover\"></i>'; } ?>";
        });

        Blade::directive('format_date', function ($date) {
            return "\Carbon::createFromTimestamp(strtotime($date))->format(session('business.date_format'))";
        });

        Blade::directive('format_time', function ($date) {
            return "\Carbon::createFromTimestamp(strtotime($date))->format(session('business.time_format') == 24 ? 'H:i' : 'h:i A')";
        });

        Blade::directive('format_datetime', function ($date) {
            return "\Carbon::createFromTimestamp(strtotime($date))->format(session('business.date_format') . ' ' . (session('business.time_format') == 24 ? 'H:i' : 'h:i A'))";
        });

        Blade::directive('format_currency', function ($number) {
            return '<?php 
                $formated_number = "";
                if (session("business.currency_symbol_placement") == "before") {
                    $formated_number .= session("currency")["symbol"] . " ";
                }
                $formated_number .= number_format((float) '.$number.', session("business.currency_precision", 2), session("currency")["decimal_separator"], session("currency")["thousand_separator"]);
                if (session("business.currency_symbol_placement") == "after") {
                    $formated_number .= " " . session("currency")["symbol"];
                }
                echo $formated_number;
            ?>';
        });
    }

    public function register()
    {
        //
    }

    protected function registerCommands()
    {
        $this->commands([
            InstallCommand::class,
            ClientCommand::class,
            KeysCommand::class,
        ]);
    }
}
