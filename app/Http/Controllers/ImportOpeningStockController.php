<?php

namespace App\Http\Controllers;

use App\Business;
use App\BusinessLocation;
use App\Product;
use App\Transaction;
use App\Utils\ProductUtil;
use App\Variation;

use App\ProductVariation;
use App\PurchaseLine;
use App\SellingPriceGroup;
use App\TaxRate;
use App\Unit;
use App\Utils\ModuleUtil;
use App\VariationGroupPrice;
use App\VariationLocationDetails;
use App\VariationTemplate;
use App\Warranty;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use App\Events\ProductsCreatedOrModified;
use App\TransactionSellLine;
use DB;
use Excel;
use Illuminate\Http\Request;

class ImportOpeningStockController extends Controller
{
    /**
     * All Utils instance.
     */
    protected $productUtil;

    /**
     * Constructor
     *
     * @param  ProductUtils  $product
     * @return void
     */
    public function __construct(ProductUtil $productUtil)
    {
        $this->productUtil = $productUtil;
    }

    /**
     * Display import product screen.
     *
     * @return \Illuminate\Http\Response
     */
     
     
   public function callStockSo()
{
    // Ambil data produk berdasarkan location_id
    $products = VariationLocationDetails::where('location_id', 2)->get();

    foreach ($products as $product) {
         $stock_history = Transaction::leftjoin('transaction_sell_lines as sl',
            'sl.transaction_id', '=', 'transactions.id')
                                ->leftjoin('purchase_lines as pl',
                                    'pl.transaction_id', '=', 'transactions.id')
                                ->leftjoin('stock_adjustment_lines as al',
                                    'al.transaction_id', '=', 'transactions.id')
                                ->leftjoin('transactions as return', 'transactions.return_parent_id', '=', 'return.id')
                                ->leftjoin('purchase_lines as rpl',
                                    'rpl.transaction_id', '=', 'return.id')
                                ->leftjoin('transaction_sell_lines as rsl',
                                        'rsl.transaction_id', '=', 'return.id')
                                ->leftjoin('contacts as c', 'transactions.contact_id', '=', 'c.id')
                                ->where('transactions.location_id', $location_id)
                                ->where(function ($q) use ($variation_id) {
                                    $q->where('sl.variation_id', $variation_id)
                                        ->orWhere('pl.variation_id', $variation_id)
                                        ->orWhere('al.variation_id', $variation_id)
                                        ->orWhere('rpl.variation_id', $variation_id)
                                        ->orWhere('rsl.variation_id', $variation_id);
                                })
                                ->whereIn('transactions.type', ['sell', 'purchase', 'stock_adjustment', 'opening_stock', 'sell_transfer', 'purchase_transfer', 'production_purchase', 'purchase_return', 'sell_return', 'production_sell'])
                                ->select(
                                    'transactions.id as transaction_id',
                                    'transactions.type as transaction_type',
                                    'sl.quantity as sell_line_quantity',
                                    'pl.quantity as purchase_line_quantity',
                                    'rsl.quantity_returned as sell_return',
                                    'rpl.quantity_returned as purchase_return',
                                    'al.quantity as stock_adjusted',
                                    'pl.quantity_returned as combined_purchase_return',
                                )
                                ->orderBy('transactions.transaction_date', 'asc')
                                ->get();

        $stock_history_array = [];
        $stock = 0;
        $stock_in_second_unit = 0;
        foreach ($stock_history as $stock_line) {
         
            if ($stock_line->transaction_type == 'sell') {
                if ($stock_line->status != 'final') {
                    continue;
                }
                $quantity_change = -1 * $stock_line->sell_line_quantity;
                $stock += $quantity_change;

                $stock_in_second_unit -= $stock_line->sell_secondary_unit_quantity;
                $stock_history_array[] = array_merge($temp_array, [
                    'quantity_change' => $quantity_change,
                    'stock' => $this->roundQuantity($stock),
                ]);
            } elseif ($stock_line->transaction_type == 'purchase') {
                if ($stock_line->status != 'received') {
                    continue;
                }
                $quantity_change = $stock_line->purchase_line_quantity;
                $stock += $quantity_change;
                $stock_in_second_unit += $stock_line->purchase_secondary_unit_quantity;
                $stock_history_array[] = array_merge($temp_array, [
                    'quantity_change' => $quantity_change,
                    'stock' => $this->roundQuantity($stock),
                ]);
            } elseif ($stock_line->transaction_type == 'stock_adjustment') {
                $quantity_change = -1 * $stock_line->stock_adjusted;
                $stock += $quantity_change;
                $stock_history_array[] = array_merge($temp_array, [
                    'quantity_change' => $quantity_change,
                    'stock' => $this->roundQuantity($stock),
                ]);
            } elseif ($stock_line->transaction_type == 'opening_stock') {
                $quantity_change = $stock_line->purchase_line_quantity;
                $stock += $quantity_change;
                $stock_in_second_unit += $stock_line->purchase_secondary_unit_quantity;
                $stock_history_array[] = array_merge($temp_array, [
                    'quantity_change' => $quantity_change,
                    'stock' => $this->roundQuantity($stock),
                ]);
            } elseif ($stock_line->transaction_type == 'sell_transfer') {
                if ($stock_line->status != 'final') {
                    continue;
                }
                $quantity_change = -1 * $stock_line->sell_line_quantity;
                $stock += $quantity_change;
                $stock_history_array[] = array_merge($temp_array, [
                    'quantity_change' => $quantity_change,
                    'stock' => $this->roundQuantity($stock),
                ]);
            } elseif ($stock_line->transaction_type == 'purchase_transfer') {
                if ($stock_line->status != 'received') {
                    continue;
                }

                $quantity_change = $stock_line->purchase_line_quantity;
                $stock += $quantity_change;
                $stock_history_array[] = array_merge($temp_array, [
                    'quantity_change' => $quantity_change,
                    'stock' => $this->roundQuantity($stock),
                ]);
            }  elseif ($stock_line->transaction_type == 'purchase_return') {
                $quantity_change = -1 * ($stock_line->combined_purchase_return + $stock_line->purchase_return);
                $stock += $quantity_change;
                $stock_history_array[] = array_merge($temp_array, [
                    'quantity_change' => $quantity_change,
                    'stock' => $this->roundQuantity($stock),
                  
                ]);
            } elseif ($stock_line->transaction_type == 'sell_return') {
                $quantity_change = $stock_line->sell_return;
                $stock += $quantity_change;
                $stock_history_array[] = array_merge($temp_array, [
                    'quantity_change' => $quantity_change,
                    'stock' => $this->roundQuantity($stock),
                ]);
            }
        }

        // Ambil detail stok dan riwayat stok menggunakan helper
        // $stock_history = $this->productUtil->getVariationStockHistory(1, $product->product_id, 2);
            $stock_history = array_reverse($stock_history_array);
        // Cek jika ada ketidaksesuaian antara stok saat ini dan riwayat stok
        if (
            isset($stock_history[0]) 
        ) {
            // Perbarui qty_available pada tabel VariationLocationDetails
            VariationLocationDetails::where('variation_id', $product->product_id)
                ->where('location_id', 2)
                ->update(['qty_available' => $stock_history[0]['stock']]);

        }
    }
}

    
    public function index()
    {
        if (! auth()->user()->can('product.opening_stock')) {
            abort(403, 'Unauthorized action.');
        }

        $zip_loaded = extension_loaded('zip') ? true : false;

        $date_formats = Business::date_formats();
        $date_format = session('business.date_format');
        $date_format = isset($date_formats[$date_format]) ? $date_formats[$date_format] : $date_format;

        //Check if zip extension it loaded or not.
        if ($zip_loaded === false) {
            $notification = ['success' => 0,
                'msg' => 'Please install/enable PHP Zip archive for import',
            ];

            return view('import_opening_stock.index')
                ->with(compact('notification', 'date_format'));
        } else {
            return view('import_opening_stock.index')
                ->with(compact('date_format'));
        }
    }

    /**
     * Imports the uploaded file to database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (! auth()->user()->can('product.opening_stock')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $notAllowed = $this->productUtil->notAllowedInDemo();
            if (! empty($notAllowed)) {
                return $notAllowed;
            }

            //Set maximum php execution time
            ini_set('max_execution_time', 0);
            ini_set('memory_limit', -1);

            if ($request->hasFile('products_csv')) {
                $file = $request->file('products_csv');

                $parsed_array = Excel::toArray([], $file);
                //Remove header row
                $imported_data = array_splice($parsed_array[0], 1);

                $business_id = $request->session()->get('user.business_id');
                $user_id = $request->session()->get('user.id');

                $formated_data = [];

                $is_valid = true;
                $error_msg = '';

                DB::beginTransaction();
                foreach ($imported_data as $key => $value) {
                    $row_no = $key + 1;

                    //Check for product SKU, get product id, variation id.
                    if (! empty($value[0])) {
                        $sku = $value[0];
                        $product_info = Variation::where('sub_sku', $sku)
                                ->join('products AS P', 'variations.product_id', '=', 'P.id')
                                ->leftjoin('tax_rates AS TR', 'P.tax', 'TR.id')
                                ->where('P.business_id', $business_id)
                                ->select(['P.id', 'variations.id as variation_id',
                                    'P.enable_stock', 'TR.amount as tax_percent',
                                    'TR.id as tax_id', ])
                                ->first();
                        if (empty($product_info)) {
                            $is_valid = false;
                            $error_msg = "Product with sku $sku not found in row no. $row_no";
                            break;
                        } elseif ($product_info->enable_stock == 0) {
                            $is_valid = false;
                            $error_msg = "Manage Stock not enabled for the product with $sku in row no. $row_no";
                            break;
                        }
                    } else {
                        $is_valid = false;
                        $error_msg = "PRODUCT SKU is required in row no. $row_no";
                        break;
                    }

                    //Get location details.
                    if (! empty(trim($value[1]))) {
                        $location_name = trim($value[1]);
                        $location = BusinessLocation::where('name', $location_name)
                                            ->where('business_id', $business_id)
                                            ->first();
                        if (empty($location)) {
                            $is_valid = false;
                            $error_msg = "Location with name '$location_name' not found in row no. $row_no";
                            break;
                        }
                    } else {
                        $location = BusinessLocation::where('business_id', $business_id)->first();
                    }

                    $opening_stock = ['quantity' => trim($value[2]),
                        'location_id' => $location->id,
                        'lot_number' => trim($value[4]),
                    ];
                    if (! empty(trim($value[5]))) {
                        $opening_stock['exp_date'] = $this->productUtil->uf_date($value[5]);
                    }

                    if (! empty(trim($value[3]))) {
                        $unit_cost_before_tax = trim($value[3]);
                    } else {
                        $is_valid = false;
                        $error_msg = "Invalid UNIT COST in row no. $row_no";
                        break;
                    }

                    if (! is_numeric(trim($value[2]))) {
                        $is_valid = false;
                        $error_msg = "Invalid quantity $value[2] in row no. $row_no";
                        break;
                    }

                    //Check for tra, location_id, opening_stock_product_id, type=opening stock.
                    $os_transaction = Transaction::where('business_id', $business_id)
                            ->where('location_id', $location->id)
                            ->where('type', 'opening_stock')
                            ->where('opening_stock_product_id', $product_info->id)
                            ->first();

                    $this->addOpeningStock($opening_stock, $product_info, $business_id, $unit_cost_before_tax, $os_transaction);

                    // //If exist add to it.
                    // if(!empty($os_transaction)){
                    //  //If not create new

                    // } else {
                    //  //If not create new
                    //  $this->addOpeningStock($opening_stock, $product_info, $business_id, $unit_cost_before_tax);
                    // }
                }
            }

            if (! $is_valid) {
                throw new \Exception($error_msg);
            }

            $output = ['success' => 1,
                'msg' => __('product.file_imported_successfully'),
            ];

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            $output = ['success' => 0,
                'msg' => 'Message:'.$e->getMessage(),
            ];

            return redirect('import-opening-stock')->with('notification', $output);
        }

        return redirect('import-opening-stock')->with('status', $output);
    }


// HAPUS MASSAL



    public function importDeleteProductIndex()
    {

        $zip_loaded = extension_loaded('zip') ? true : false;

        $date_formats = Business::date_formats();
        $date_format = session('business.date_format');
        $date_format = isset($date_formats[$date_format]) ? $date_formats[$date_format] : $date_format;

        //Check if zip extension it loaded or not.
        if ($zip_loaded === false) {
            $notification = [
                'success' => 0,
                'msg' => 'Please install/enable PHP Zip archive for import',
            ];

            return view('import_opening_stock.importdelete')
                ->with(compact('notification', 'date_format'));
        } else {
            return view('import_opening_stock.importdelete')
                ->with(compact('date_format'));
        }
    }

    // Import Delete Produk 
    public function importDeleteProduct(Request $request)
    {
        try {
            $notAllowed = $this->productUtil->notAllowedInDemo();
            if (! empty($notAllowed)) {
                return $notAllowed;
            }

            //Set maximum php execution time
            ini_set('max_execution_time', 0);
            ini_set('memory_limit', -1);

            if ($request->hasFile('products_csv')) {
                $file = $request->file('products_csv');

                $parsed_array = Excel::toArray([], $file);
                //Remove header row
                $imported_data = array_splice($parsed_array[0], 1);

                $business_id = $request->session()->get('user.business_id');
                $user_id = $request->session()->get('user.id');

                $formated_data = [];

                $is_valid = true;
                $error_msg = '';

                DB::beginTransaction();
                foreach ($imported_data as $key => $value) {
                    $row_no = $key + 1;

                    //Check for product SKU, get product id, variation id.
                    if (! empty($value[0])) {
                        $sku = $value[0];
                        $product_info = Variation::where('sub_sku', $sku)
                            ->join('products AS P', 'variations.product_id', '=', 'P.id')
                            ->leftjoin('tax_rates AS TR', 'P.tax', 'TR.id')
                            ->where('P.business_id', $business_id)
                            ->select([
                                'P.id',
                                'variations.id as variation_id',
                                'P.enable_stock',
                                'TR.amount as tax_percent',
                                'TR.id as tax_id',
                            ])
                            ->first();
                        if (empty($product_info)) {
                            $is_valid = false;
                            $error_msg = "Product with sku $sku not found in row no. $row_no";
                            break;
                        }
                    } else {
                        $is_valid = false;
                        $error_msg = "PRODUCT SKU is required in row no. $row_no";
                        break;
                    }



                    $error_msg = '';

                    //Check if any purchase or transfer exists
                    $count = PurchaseLine::join(
                        'transactions as T',
                        'purchase_lines.transaction_id',
                        '=',
                        'T.id'
                    )
                        ->whereIn('T.type', ['purchase'])
                        ->where('T.business_id', $business_id)
                        ->where('purchase_lines.product_id', $product_info->id)
                        ->count();
                    if ($count > 0) {
                        $is_valid = false;
                        $error_msg = "PRODUCT SKU ada pembelian in row no. $row_no";
                        break;
                    } else {
                        //Check if any opening stock sold
                        $count = PurchaseLine::join(
                            'transactions as T',
                            'purchase_lines.transaction_id',
                            '=',
                            'T.id'
                        )
                            ->where('T.type', 'opening_stock')
                            ->where('T.business_id', $business_id)
                            ->where('purchase_lines.product_id', $product_info->id)
                            ->where('purchase_lines.quantity_sold', '>', 0)
                            ->count();
                        if ($count > 0) {
                            $is_valid = false;
                            $error_msg = "PRODUCT SKU ada opening stok in row no. $row_no";
                            break;
                        } else {
                            //Check if any stock is adjusted
                            $count = PurchaseLine::join(
                                'transactions as T',
                                'purchase_lines.transaction_id',
                                '=',
                                'T.id'
                            )
                                ->where('T.business_id', $business_id)
                                ->where('purchase_lines.product_id', $product_info->id)
                                ->where('purchase_lines.quantity_adjusted', '>', 0)
                                ->count();
                            if ($count > 0) {
                                $is_valid = false;
                                $error_msg = "PRODUCT SKU ada ADJUSTED in row no. $row_no";
                                break;
                            } else {
                                $count = TransactionSellLine::join(
                                    'transactions as T',
                                    'transaction_sell_lines.transaction_id',
                                    '=',
                                    'T.id'
                                )
                                    ->whereIn('T.type', ['sell'])
                                    ->where('T.business_id', $business_id)
                                    ->where('transaction_sell_lines.product_id', $product_info->id)
                                    ->count();
                                if ($count > 0) {
                                    $is_valid = false;
                                    $error_msg = "PRODUCT SKU ada PENJUALAN in row no. $row_no";
                                    break;
                                }
                            }
                        }
                    }

                    $product = Product::where('id', $product_info->id)
                        ->where('business_id', $business_id)
                        ->with('variations')
                        ->first();

                    if ($is_valid) {
                        if (! empty($product)) {
                            DB::beginTransaction();
                            //Delete variation location details
                            VariationLocationDetails::where('product_id', $product_info->id)
                                ->delete();
                            $product->delete();
                            event(new ProductsCreatedOrModified($product, 'deleted'));
                            DB::commit();
                        }

                        $output = [
                            'success' => true,
                            'msg' => __('lang_v1.product_delete_success'),
                        ];
                    } else {
                        $output = [
                            'success' => false,
                            'msg' => $error_msg,
                        ];
                    }
                }
            }

            if (! $is_valid) {
                throw new \Exception($error_msg);
            }

            $output = [
                'success' => 1,
                'msg' => __('product.file_imported_successfully'),
            ];

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => 'Message:' . $e->getMessage(),
            ];

            return redirect('import-opening-stock/import-delete-products')->with('notification', $output);
        }

        // return redirect('import-opening-stock/import-delete-products')->with('status', $output);

        return redirect('import-opening-stock/import-delete-products')->with('notification', $output);
    }
    
    /**
     * Adds opening stock of a single product
     *
     * @param  array  $opening_stock
     * @param  obj  $product
     * @param  int  $business_id
     * @return void
     */
    private function addOpeningStock($opening_stock, $product, $business_id, $unit_cost_before_tax, $transaction = null)
    {
        $user_id = request()->session()->get('user.id');

        $transaction_date = request()->session()->get('financial_year.start');
        $transaction_date = \Carbon::createFromFormat('Y-m-d', $transaction_date)->toDateTimeString();

        //Get product tax
        $tax_percent = ! empty($product->tax_percent) ? $product->tax_percent : 0;
        $tax_id = ! empty($product->tax_id) ? $product->tax_id : null;

        $item_tax = $this->productUtil->calc_percentage($unit_cost_before_tax, $tax_percent);

        //total before transaction tax
        $total_before_trans_tax = $opening_stock['quantity'] * ($unit_cost_before_tax + $item_tax);

        //Add opening stock transaction
        if (empty($transaction)) {
            $transaction = new Transaction();
            $transaction->type = 'opening_stock';
            $transaction->status = 'received';
            $transaction->opening_stock_product_id = $product->id;
            $transaction->business_id = $business_id;
            $transaction->transaction_date = $transaction_date;
            $transaction->location_id = $opening_stock['location_id'];
            $transaction->payment_status = 'paid';
            $transaction->created_by = $user_id;
            $transaction->total_before_tax = 0;
            $transaction->final_total = 0;
        }
        $transaction->total_before_tax += $total_before_trans_tax;
        $transaction->final_total += $total_before_trans_tax;
        $transaction->save();

        //Create purchase line
        $transaction->purchase_lines()->create([
            'product_id' => $product->id,
            'variation_id' => $product->variation_id,
            'quantity' => $opening_stock['quantity'],
            'pp_without_discount' => $unit_cost_before_tax,
            'item_tax' => $item_tax,
            'tax_id' => $tax_id,
            'pp_without_discount' => $unit_cost_before_tax,
            'purchase_price' => $unit_cost_before_tax,
            'purchase_price_inc_tax' => $unit_cost_before_tax + $item_tax,
            'exp_date' => ! empty($opening_stock['exp_date']) ? $opening_stock['exp_date'] : null,
            'lot_number' => ! empty($opening_stock['lot_number']) ? $opening_stock['lot_number'] : null,
        ]);
        //Update variation location details
        $this->productUtil->updateProductQuantity($opening_stock['location_id'], $product->id, $product->variation_id, $opening_stock['quantity']);
    }
}
