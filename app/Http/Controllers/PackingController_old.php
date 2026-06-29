<?php

namespace App\Http\Controllers;

use App\Business;
use App\DetailPacking;
use App\Packing;
use App\Transaction;
use App\TransactionSellLine;
use App\Utils\BusinessUtil;
use App\Utils\TransactionUtil;
use Illuminate\Http\Request;
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

    /**
     * Show the form for creating a new Packing.
     *
     * @param  string  $transactionIds (comma-separated)
     * @return \Illuminate\Http\Response
     */
    public function create($transactionIds)
    {
        if (!auth()->user()->can('sell.view')) {
            abort(403, 'Unauthorized action.');
        }

        $ids = explode(',', $transactionIds);

        $transactions = Transaction::where('business_id', request()->session()->get('user.business_id'))
            ->whereIn('id', $ids)
            ->with(['contact']) // Load contact relationship for invoice details
            ->get();

        if ($transactions->isEmpty()) {
            abort(404, 'Transaction not found');
        }

        // Get all sell lines for these transactions
        $sellLines = TransactionSellLine::whereIn('transaction_id', $ids)
            ->with(['product', 'variations', 'transaction', 'sub_unit'])
            ->get();

        return view('Packing.create', compact('transactions', 'sellLines'));
    }

    /**
     * Store a newly created Packing in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('sell.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->only(['transaction_id', 'packing_name', 'packing_date']);
            $input['created_by'] = auth()->user()->id;
            $input['business_id'] = request()->session()->get('user.business_id');

            DB::beginTransaction();

            // Check if transaction_id is an array (for bulk Packing)
            $transactionIds = is_array($input['transaction_id']) ? $input['transaction_id'] : [$input['transaction_id']];

            foreach ($transactionIds as $transactionId) {
                $input['transaction_id'] = $transactionId;

                // Create the main packing record
                $packing = Packing::create($input);

                // Process the packed items
                if ($request->has('sell_line_ids') && is_array($request->sell_line_ids)) {
                    foreach ($request->sell_line_ids as $key => $sell_line_id) {
                        if (!empty($sell_line_id) && isset($request->quantities_packed[$key]) && $request->quantities_packed[$key] > 0) {
                            DetailPacking::create([
                                'packing_id' => $packing->id,
                                'transaction_sell_line_id' => $sell_line_id,
                                'quantity_packed' => $request->quantities_packed[$key],
                                'notes' => $request->notes[$key] ?? null
                            ]);
                        }
                    }
                }
            }

            DB::commit();

            $output = ['success' => true, 'msg' => __('lang_v1.Packing_successfully_created')];
        } catch (\Exception $e) {
            DB::rollback();
            \Log::emergency('File: ' . $e->getFile() . ' Line: ' . $e->getLine() . ' Message: ' . $e->getMessage());
            $output = ['success' => false, 'msg' => __('messages.something_went_wrong')];
        }

        return redirect()->back()->with('status', $output);
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

    /**
     * Display the specified Packing.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (!auth()->user()->can('sell.view')) {
            abort(403, 'Unauthorized action.');
        }

        $packing = Packing::where('business_id', request()->session()->get('user.business_id'))
            ->with(['transaction', 'details.sellLine.product', 'createdBy'])
            ->findOrFail($id);

        return view('Packing.show', compact('packing'));
    }

    /**
     * Show Packing history for a specific transaction
     *
     * @param  int  $transactionId
     * @return \Illuminate\Http\Response
     */
    public function history($transactionId)
    {
        if (!auth()->user()->can('sell.view')) {
            abort(403, 'Unauthorized action.');
        }

        $packings = Packing::where('transaction_id', $transactionId)
            ->with(['details.sellLine.product'])
            ->get();

        return view('Packing.history', compact('packings'));
    }
}
