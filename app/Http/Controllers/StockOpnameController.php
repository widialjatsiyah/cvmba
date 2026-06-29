<?php

namespace App\Http\Controllers;

use App\BusinessLocation;
use App\Product;
use App\PurchaseLine;
use App\Transaction;
use App\StockAdjustmentLine;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Utils\ModuleUtil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Events\StockAdjustmentCreatedOrModified;
use Yajra\DataTables\DataTables;

class StockOpnameController extends Controller
{
    /**
     * All Utils instance.
     */
    protected $productUtil;
    protected $transactionUtil;
    protected $moduleUtil;

    /**
     * Constructor
     *
     * @param ProductUtil $productUtil
     * @param TransactionUtil $transactionUtil
     * @param ModuleUtil $moduleUtil
     * @return void
     */
    public function __construct(
        ProductUtil $productUtil, 
        TransactionUtil $transactionUtil, 
        ModuleUtil $moduleUtil
    ) {
        $this->productUtil = $productUtil;
        $this->transactionUtil = $transactionUtil;
        $this->moduleUtil = $moduleUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!auth()->user()->can('purchase.view') && !auth()->user()->can('purchase.create')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            $stock_opnames = Transaction::join(
                'business_locations AS BL',
                'transactions.location_id',
                '=',
                'BL.id'
            )
                ->leftJoin('users as u', 'transactions.created_by', '=', 'u.id')
                    ->where('transactions.business_id', $business_id)
                    ->where('transactions.type', 'stock_opname')  // Ensure we only get stock_opname transactions
                    ->select(
                        'transactions.id',
                        'transaction_date',
                        'ref_no',
                        'BL.name as location_name',
                        'adjustment_type',
                        'final_total',
                        'total_amount_recovered',
                        'additional_notes',
                        'transactions.id as DT_RowId',
                        DB::raw("CONCAT(COALESCE(u.surname, ''),' ',COALESCE(u.first_name, ''),' ',COALESCE(u.last_name,'')) as added_by")
                    );

            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $stock_opnames->whereIn('transactions.location_id', $permitted_locations);
            }

            return DataTables::of($stock_opnames)
                ->addColumn('action', '
                <a href="{{action([\App\Http\Controllers\StockOpnameController::class, \'show\'], [$id]) }}" class="so_modal"><i class="fas fa-eye" aria-hidden="true" ></i> @lang("messages.view")</a>
                ')
                ->removeColumn('id')
                ->editColumn(
                    'final_total',
                    function ($row) {
                        return $this->transactionUtil->num_f($row->final_total, true);
                    }
                )
                ->editColumn(
                    'total_amount_recovered',
                    function ($row) {
                        return $this->transactionUtil->num_f($row->total_amount_recovered, true);
                    }
                )
                ->editColumn('transaction_date', '{{@format_datetime($transaction_date)}}')
                ->editColumn('adjustment_type', function ($row) {
                    return __('stock_adjustment.'.$row->adjustment_type);
                })
                ->setRowAttr([
                    'data-href' => function ($row) {
                        return  action([\App\Http\Controllers\StockOpnameController::class, 'show'], [$row->id]);
                    }, ])
                ->rawColumns(['final_total', 'action', 'total_amount_recovered'])
                ->make(true);
        }

        return view('stock_opname.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('purchase.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        // Check if subscribed or not
        if (!$this->moduleUtil->isSubscribed($business_id)) {
            return $this->moduleUtil->expiredResponse(action([\App\Http\Controllers\StockOpnameController::class, 'index']));
        }

        $business_locations = BusinessLocation::forDropdown($business_id);
        
        // Get all products for the dropdown
        $products = Product::where('business_id', $business_id)
            ->where('enable_stock', 1)
            ->select('id', 'name')
            ->get();

        $asset_v = config('app.asset_version');

        return view('stock_opname.create')
                ->with(compact('business_locations', 'products', 'asset_v'));
    }

    /**
     * Store a newly created stock opname in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('purchase.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            DB::beginTransaction();

            $input_data = $request->only(['location_id', 'adjustment_type', 'additional_notes', 'ref_no', 'transaction_date']);
            $business_id = $request->session()->get('user.business_id');
            $user_id = $request->session()->get('user.id');

            // Check if subscribed or not
            if (!$this->moduleUtil->isSubscribed($business_id)) {
                return $this->moduleUtil->expiredResponse(action([\App\Http\Controllers\StockOpnameController::class, 'create']));
            }

            // Get products data from request
            $products = $request->input('products');

            if (empty($products)) {
                $output = [
                    'success' => 0,
                    'msg' => 'Tidak ada produk yang dipilih untuk stock opname.'
                ];
                
                DB::rollBack();
                return redirect('stock-opname/create')->with('status', $output);
            }

            // Prepare transaction data
            $transaction_data = [
                'type' => 'stock_opname',
                'business_id' => $business_id,
                'location_id' => $input_data['location_id'],
                'created_by' => $user_id,
                'transaction_date' => $this->productUtil->uf_date($input_data['transaction_date'], true),
                'adjustment_type' => $input_data['adjustment_type'] ?? 'normal',
                'additional_notes' => $input_data['additional_notes'] ?? '',
                'total_amount_recovered' => $this->productUtil->num_uf($request->input('total_amount_recovered')),
                'status' => 'final'
            ];

            // Generate reference number if not provided
            if (empty($input_data['ref_no'])) {
                $ref_count = $this->productUtil->setAndGetReferenceCount('stock_opname');
                $transaction_data['ref_no'] = $this->productUtil->generateReferenceNumber('stock_opname', $ref_count);
            } else {
                $transaction_data['ref_no'] = $input_data['ref_no'];
            }

            // Create main transaction
            $stock_opname = Transaction::create($transaction_data);

            // Process products data for stock opname
            $opname_lines = [];

            foreach ($products as $key => $product) {
                if(empty($product['product_id']) || empty($product['variation_id'])) {
                    continue;
                }
                
                $product_id = $product['product_id'];
                $variation_id = $product['variation_id'];
                $current_qty = $this->productUtil->num_uf($product['current_qty'] ?? 0);
                $actual_qty = $this->productUtil->num_uf($product['actual_qty']);
                $unit_price = $this->productUtil->num_uf($product['unit_price']);
                
                // Calculate difference (positive = need to add, negative = need to reduce)
                $difference = $actual_qty - $current_qty;
                
                if ($difference == 0) {
                    continue; // Skip if no difference
                }

                $opname_line = [
                    'transaction_id' => $stock_opname->id,
                    'product_id' => $product_id,
                    'variation_id' => $variation_id,
                    'current_qty' => $current_qty,
                    'actual_qty' => $actual_qty,
                    'difference' => $difference,
                    'unit_price' => $unit_price,
                ];
                
                $opname_lines[] = $opname_line;

                // If difference is positive, we need to add stock (using opening stock approach)
                // If difference is negative, we need to reduce stock (using stock adjustment approach)
                if ($difference > 0) {
                    // Add stock using updateProductQuantity (like opening stock)
                    $this->productUtil->updateProductQuantity(
                        $input_data['location_id'],
                        $product_id,
                        $variation_id,
                        abs($difference),
                        0,
                        null,
                        false
                    );
                } else {
                    // Reduce stock using decreaseProductQuantity (like stock adjustment)
                    $this->productUtil->decreaseProductQuantity(
                        $product_id,
                        $variation_id,
                        $input_data['location_id'],
                        abs($difference)
                    );
                }
            }

            // Save all stock opname lines
            if (!empty($opname_lines)) {
                foreach ($opname_lines as $line) {
                    $stock_opname->stock_opname_lines()->create($line);
                }
            }

            // Map purchase and sell for accounting
            $business = [
                'id' => $business_id,
                'accounting_method' => $request->session()->get('business.accounting_method'),
                'location_id' => $input_data['location_id'],
            ];
            
            // We need to create a custom mapping since this is a stock opname
            $opname_line_objects = collect([]);
            foreach ($opname_lines as $line) {
                $obj = new \stdClass();
                $obj->product_id = $line['product_id'];
                $obj->variation_id = $line['variation_id'];
                $obj->quantity = abs($line['difference']);
                $opname_line_objects->push($obj);
            }
            
            $this->transactionUtil->mapPurchaseSell($business, $opname_line_objects, 'stock_opname');

            // Trigger event that stock opname was created
            event(new StockAdjustmentCreatedOrModified($stock_opname, 'added'));

            $this->transactionUtil->activityLog($stock_opname, 'added', null, [], false);

            $output = [
                'success' => 1,
                'msg' => 'Stock opname berhasil ditambahkan.'
            ];

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());
            $msg = trans('messages.something_went_wrong');

            if (get_class($e) == \App\Exceptions\PurchaseSellMismatch::class) {
                $msg = $e->getMessage();
            }

            $output = [
                'success' => 0,
                'msg' => $msg
            ];
        }

        return redirect('stock-opname')->with('status', $output);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (!auth()->user()->can('purchase.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $stock_opname = Transaction::where('transactions.business_id', $business_id)
                    ->where('transactions.id', $id)
                    ->where('transactions.type', 'stock_opname')  // Changed from 'stock_opname' to include this type
                    ->with([
                        'location', 
                        'business',
                        // 'created_by_user'
                    ])
                    ->firstOrFail();

        // Get stock opname lines with additional product info
        $opname_lines = DB::table('stock_opname_lines as sol')
            ->join('products as p', 'sol.product_id', '=', 'p.id')
            ->join('variations as v', 'sol.variation_id', '=', 'v.id')
            ->leftJoin('units as u', 'p.unit_id', '=', 'u.id')
            ->select(
                'sol.*',
                'p.name as product_name',
                'p.sku as sku',
                'v.name as variation_name',
                'u.short_name as unit_name'
            )
            ->where('sol.transaction_id', $stock_opname->id)
            ->get();

        return view('stock_opname.show')
                ->with(compact('stock_opname', 'opname_lines'));
    }

    /**
     * Fetch stock data for the specified location
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function fetchStockData(Request $request)
    {
        $location_id = $request->get('location_id');
        
        try {
            $stock_data = DB::table('variation_location_details as vld')
                ->join('variations as v', 'vld.variation_id', '=', 'v.id')
                ->join('products as p', 'v.product_id', '=', 'p.id')
                ->leftJoin('units as u', 'p.unit_id', '=', 'u.id')
                ->select(
                    'v.id as variation_id',
                    'p.id as product_id',
                    'p.name as product_name',
                    'v.name as variation_name',
                    'vld.qty_available',
                    'v.dflt_sell_price as sell_price',
                    'u.short_name as unit_name'
                )
                ->where('vld.location_id', $location_id)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $stock_data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get variations for a product
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getVariations(Request $request)
    {
        $product_id = $request->get('product_id');
        
        try {
            $variations = \App\Variation::join('product_variations as pv', 'variations.product_variation_id', '=', 'pv.id')
                ->where('pv.product_id', $product_id)
                ->select('variations.id', 'variations.name')
                ->get();

            return response()->json($variations);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get current stock for a variation at a location
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCurrentStock(Request $request)
    {
        $variation_id = $request->get('variation_id');
        $location_id = $request->get('location_id');
        
        try {
            $current_qty = \App\VariationLocationDetails::where('variation_id', $variation_id)
                ->where('location_id', $location_id)
                ->value('qty_available') ?? 0;

            return response()->json([
                'success' => true,
                'current_qty' => (float)$current_qty
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'current_qty' => 0
            ]);
        }
    }

    /**
     * Get products for search in stock opname
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProducts(Request $request)
    {
        $term = $request->input('term');
        $location_id = $request->input('location_id');
        
        if (empty($term) || empty($location_id)) {
            return response()->json([], 200);
        }

        $business_id = request()->session()->get('user.business_id');

        // Gunakan metode filterProduct yang benar dari ProductUtil
        $products = $this->productUtil->filterProduct($business_id, $term, $location_id, null, null, [], ['name', 'sku', 'sub_sku'], false);

        // Format ulang data agar sesuai dengan kebutuhan stock opname
        $formatted_products = [];
        foreach ($products as $product) {
            // Ambil informasi qty dan harga dari produk
            $qty_available = !empty($product->qty_available) ? $product->qty_available : 0;
            $default_sell_price = !empty($product->default_sell_price) ? $product->default_sell_price : 0;
            
            $formatted_products[] = [
                'id' => $product->product_id,
                'product_id' => $product->product_id,
                'variation_id' => $product->variation_id,
                'name' => $product->name,
                'variation' => !empty($product->variation) ? $product->variation : '',
                'qty_available' => $qty_available,
                'default_sell_price' => $default_sell_price,
                'sub_sku' => !empty($product->sub_sku) ? $product->sub_sku : $product->sku,
                'tax_id' => !empty($product->tax_id) ? $product->tax_id : null,
                'tax' => !empty($product->tax) ? $product->tax : null,
                'unit' => !empty($product->unit) ? $product->unit : '',
                'enable_stock' => !empty($product->enable_stock) ? $product->enable_stock : 0,
                'alert_quantity' => !empty($product->alert_quantity) ? $product->alert_quantity : 0,
                'image' => !empty($product->image) ? $product->image : 'dummy_product.png',
                'brand' => !empty($product->brand) ? $product->brand : '',
                'category' => !empty($product->category) ? $product->category : '',
                'unit_id' => !empty($product->unit_id) ? $product->unit_id : null,
                'item_tax' => !empty($product->item_tax) ? $product->item_tax : null,
                'tax_type' => !empty($product->tax_type) ? $product->tax_type : null,
                'combo_variations' => !empty($product->combo_variations) ? $product->combo_variations : null,
                'batch' => !empty($product->batch) ? $product->batch : null,
                'purchase_line_id' => !empty($product->purchase_line_id) ? $product->purchase_line_id : null,
                'lot_number' => !empty($product->lot_number) ? $product->lot_number : null,
                'focussed' => !empty($product->focussed) ? $product->focussed : null,
                'enable_sr_no' => !empty($product->enable_sr_no) ? $product->enable_sr_no : null,
                'mfg_date' => !empty($product->mfg_date) ? $product->mfg_date : null,
                'warranty_ends' => !empty($product->warranty_ends) ? $product->warranty_ends : null,
                'product_not_for_selling' => !empty($product->product_not_for_selling) ? $product->product_not_for_selling : null,
                'variation_group_price' => !empty($product->variation_group_price) ? $product->variation_group_price : null,
                'xtra_col' => !empty($product->xtra_col) ? $product->xtra_col : null,
            ];
        }

        return response()->json($formatted_products, 200);
    }
}