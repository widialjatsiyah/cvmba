<?php

namespace App\Http\Controllers;


use App\Business;
use App\DetailPacking;
use App\Packing;
use App\Transaction;
use App\TransactionSellLine;
use App\VariationGroupPrice;
use App\Unit;
use App\Utils\BusinessUtil;
use App\Utils\TransactionUtil;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;



class PackingController extends Controller
{

    /**
     * All Utils instance.
     */
    protected $businessUtil;
    protected $transactionUtil;

    /**
     * Constructor
     *
     * @param  BusinessUtil  $businessUtil
     * @param  TransactionUtil  $transactionUtil
     * @return void
     */
    public function __construct(BusinessUtil $businessUtil, TransactionUtil $transactionUtil)
    {
        $this->businessUtil = $businessUtil;
        $this->transactionUtil = $transactionUtil;
    }
    //
    public function index()
    {
        if (! auth()->user()->can('packing.view') && ! auth()->user()->can('packing.create')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            $packing = Packing::select([
                'id',
                'packing_name',
                'packing_date',
                'created_at',
                'updated_at'
            ]);

            return Datatables::of($packing)
                ->addColumn(
                    'action',
                    function ($row) {
                        $html = '<button data-href="' . action([\App\Http\Controllers\PackingController::class, 'show'], [$row->id]) . '" class="btn btn-xs btn-primary show_packing_button"><i class="fa fa-eye"></i> Detail</button>';
                        $html .= '<br>';
                        $html .= '<button data-href="' . action([\App\Http\Controllers\PackingController::class, 'destroy'], [$row->id]) . '" class="btn btn-xs btn-danger delete_packing_button"><i class="glyphicon glyphicon-trash"></i> Delete</button>';
                        return $html;
                    }
                )
                ->editColumn('packing_date', function ($row) {
                    return !empty($row->packing_date) ? $row->packing_date->format('d-m-Y') : '';
                })
                ->editColumn('created_at', function ($row) {
                    return !empty($row->created_at) ? $row->created_at->format('d-m-Y H:i:s') : '';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('packing.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! auth()->user()->can('packing.create')) {
            abort(403, 'Unauthorized action.');
        }

        $quick_add = false;
        if (! empty(request()->input('quick_add'))) {
            $quick_add = true;
        }

        return view('packing.create')
            ->with(compact('quick_add'));
    }

    public function show($id)
    {
        // if (! auth()->user()->can('packing.create')) {
        //     abort(403, 'Unauthorized action.');
        // }

        $packing = Packing::findOrFail($id);
        $packing_details = DetailPacking::where('packing_id', $id)
            ->with(['transactions' => function($query) {
                $query->with(['contact', 'sell_lines', 'sell_lines.product', 'sell_lines.variations']);
            }])
            ->get();
            
        $transaction_ids = $packing_details->pluck('transaction_id')->toArray();
        
        $transactions = Transaction::with(['sell_lines.product', 'sell_lines.variations'])->whereIn('id', $transaction_ids)->get();

        // Aggregate product data with accumulated quantities
        $aggregated_products = [];
        foreach ($transactions as $transaction) {
            foreach ($transaction->sell_lines as $sell_line) {
                $product_key = $sell_line->product_id . '_' . $sell_line->variation_id;
                
                if (!isset($aggregated_products[$product_key])) {
                    $aggregated_products[$product_key] = [
                        'product' => $sell_line->product,
                        'variation' => $sell_line->variations,
                        'total_quantity' => 0,
                        'unit' => optional($sell_line->product)->unit,
                        'conversion_details' => []
                    ];
                }
                
                $aggregated_products[$product_key]['total_quantity'] += $sell_line->quantity;
            }
        }

        // Process conversion for each product
        foreach ($aggregated_products as &$product_data) {
            if ($product_data['variation']) {
                $conversion_details = $this->convertQuantityByVariationPrices(
                    $product_data['total_quantity'],
                    $product_data['variation']->id
                );
                $product_data['conversion_details'] = $conversion_details;
            }
        }

        return view('packing.show')
            ->with(compact('packing', 'packing_details', 'aggregated_products'));
    }

    /**
     * Print packing slip
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function printPacking($id)
    {
        $packing = Packing::findOrFail($id);
        $packing_details = DetailPacking::where('packing_id', $id)
            ->with(['transactions' => function($query) {
                $query->with(['contact', 'sell_lines', 'sell_lines.product', 'sell_lines.variations']);
            }])
            ->get();
            
        $transaction_ids = $packing_details->pluck('transaction_id')->toArray();
        
        $transactions = Transaction::with(['sell_lines.product', 'sell_lines.variations'])->whereIn('id', $transaction_ids)->get();

        // Aggregate product data with accumulated quantities
        $aggregated_products = [];
        foreach ($transactions as $transaction) {
            foreach ($transaction->sell_lines as $sell_line) {
                $product_key = $sell_line->product_id . '_' . $sell_line->variation_id;
                
                if (!isset($aggregated_products[$product_key])) {
                    $aggregated_products[$product_key] = [
                        'product' => $sell_line->product,
                        'variation' => $sell_line->variations,
                        'total_quantity' => 0,
                        'unit' => optional($sell_line->product)->unit,
                        'conversion_details' => []
                    ];
                }
                
                $aggregated_products[$product_key]['total_quantity'] += $sell_line->quantity;
            }
        }

        // Process conversion for each product
        foreach ($aggregated_products as &$product_data) {
            if ($product_data['variation']) {
                $conversion_details = $this->convertQuantityByVariationPrices(
                    $product_data['total_quantity'],
                    $product_data['variation']->id
                );
                $product_data['conversion_details'] = $conversion_details;
            }
        }

        return view('packing.packing_slip')
            ->with(compact('packing', 'packing_details', 'aggregated_products'));
    }

    /**
     * Print all related invoices for a packing
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function printAllInvoices($id)
    {
        $packing = Packing::findOrFail($id);
        $packing_details = DetailPacking::where('packing_id', $id)
            ->with(['transaction' => function($query) {
                $query->with(['contact', 'sell_lines', 'sell_lines.product', 'sell_lines.variations']);
            }])
            ->get();
            
        $transaction_ids = $packing_details->pluck('transaction_id')->toArray();
        
        $transactions = Transaction::with(['sell_lines.product', 'sell_lines.variations'])->whereIn('id', $transaction_ids)->get();

        // Aggregate product data with accumulated quantities
        $aggregated_products = [];
        foreach ($transactions as $transaction) {
            foreach ($transaction->sell_lines as $sell_line) {
                $product_key = $sell_line->product_id . '_' . $sell_line->variation_id;
                
                if (!isset($aggregated_products[$product_key])) {
                    $aggregated_products[$product_key] = [
                        'product' => $sell_line->product,
                        'variation' => $sell_line->variations,
                        'total_quantity' => 0,
                        'unit' => optional($sell_line->product)->unit,
                        'conversion_details' => []
                    ];
                }
                
                $aggregated_products[$product_key]['total_quantity'] += $sell_line->quantity;
            }
        }

        // Process conversion for each product
        foreach ($aggregated_products as &$product_data) {
            if ($product_data['variation']) {
                $conversion_details = $this->convertQuantityByVariationPrices(
                    $product_data['total_quantity'],
                    $product_data['variation']->id
                );
                $product_data['conversion_details'] = $conversion_details;
            }
        }

        return view('packing.print_all_invoices')
            ->with(compact('packing', 'packing_details', 'aggregated_products', 'transactions'));
    }

    private function convertQuantityByVariationPrices($total_qty, $variation_id)
    {
        // Get all variation group prices for this variation ordered by conversion factor descending
        $variation_prices = VariationGroupPrice::where('variation_id', $variation_id)
            ->orderBy('konversi', 'desc')
            ->with('unit')
            ->get();

        $result = [];
        $remaining_qty = $total_qty;

        foreach ($variation_prices as $vp) {
            if ($vp->konversi > 0) {
                $unit_count = floor($remaining_qty / $vp->konversi);
                if ($unit_count > 0) {
                    $result[] = [
                        'unit_name' => optional($vp->unit)->short_name ?: 'Unit',
                        'unit_count' => $unit_count,
                        'conversion_factor' => $vp->konversi
                    ];
                    $remaining_qty = $remaining_qty % $vp->konversi;
                }
            }
        }

        // If there's still remaining quantity that couldn't be converted, add it as smallest unit
        if ($remaining_qty > 0) {
            // Find the smallest conversion factor
            $smallest_conversion = $variation_prices->min('konversi');
            if ($smallest_conversion && $smallest_conversion > 0) {
                $result[] = [
                    'unit_name' => 'pcs', // Default to pcs if smallest unit not found
                    'unit_count' => $remaining_qty,
                    'conversion_factor' => 1
                ];
            } else {
                // If no conversion factors exist, just show the total
                $result[] = [
                    'unit_name' => 'pcs',
                    'unit_count' => $total_qty,
                    'conversion_factor' => 1
                ];
            }
        }

        return $result;
    }


    public function edit($id)
    {
        if (! auth()->user()->can('packing.create')) {
            abort(403, 'Unauthorized action.');
        }

        $packing = Packing::findOrFail($id);

        return view('packing.edit')
            ->with(compact('packing'));
    }

    public function destroy($id)
    {
        // if (! auth()->user()->can('packing.delete')) {
        //     abort(403, 'Unauthorized action.');
        // }

        if (request()->ajax()) {
            try {
                $packing = Packing::findOrFail($id);
                $packing->delete();

                // Also delete related detail packings
                DetailPacking::where('packing_id', $id)->delete();

                $output = [
                    'success' => true,
                    'msg' => "Data Berhasil Dihapus",
                ];
            } catch (\Exception $e) {
                \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());

                $output = [
                    'success' => false,
                    'msg' => __('messages.something_went_wrong'),
                ];
            }

            return $output;
        }
    }

    public function store(Request $request)
    {
        // if (! auth()->user()->can('brand.create')) {
        //     abort(403, 'Unauthorized action.');
        // }

        try {
            $packing = new Packing;
            $packing->packing_name = $request->input('packing_name');
            $packing->packing_date = $request->input('packing_date');
            $packing->created_by = auth()->user()->id;
            $packing->business_id = request()->session()->get('user.business_id');
            $packing->save();

            $output = [
                'success' => true,
                'msg' => "Packing data saved successfully",
            ];
        } catch (\Exception $e) {
            \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());

            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        return $output;
    }


    public function addBulkPacking(Request $request)
    {
        // if (! auth()->user()->can('product.update')) {
        //     abort(403, 'Unauthorized action.');
        // }

        try {
            $selected_transactions = $request->input('transactions');
            $packing_name = $request->input('packing_name');
            $packing_date = $request->input('packing_date');

            $transaction_ids = explode(',', $selected_transactions);

            $transactions = Transaction::whereIn('id', $transaction_ids)
                ->get();
            DB::beginTransaction();
            $packing = Packing::create([
                'packing_name' => $packing_name,
                'packing_date' => $packing_date,
            ]);

            foreach ($transactions as $transaction) {
                DetailPacking::create([
                    'packing_id' => $packing->id,
                    'transaction_id' => $transaction->id,
                ]);

                $transaction->update([
                    'shipping_status' => 'ordered',
                    'shipping_details' => $packing_name
                ]);
            }

            DB::commit();
            $output = [
                'success' => 1,
                'msg' => __('Packing created successfully!'),
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        return $output;
    }
}