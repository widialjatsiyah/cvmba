<?php

namespace App\Http\Controllers;

use App\Reportday;
use App\Reportday_detail;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;



class ReportdayController extends Controller
{
    //
    public function index()
    {
        if (! auth()->user()->can('reportday.view') && ! auth()->user()->can('reportday.create')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            // $reportday = Reportday::with()->leftJoin('business_locations', 'Reportday.cabang_id', '=', 'business_locations.id')->get();
            $reportday = Reportday::all();
            return Datatables::of($reportday)
                ->addColumn(
                    'action',
                    ' <button data-href="{{action(\'App\Http\Controllers\ReportdayController@show\', [$id])}}" class="btn btn-xs btn-primary show_reportday_button"><i class="fa fa-eye"></i> Detail</button>
                    <br>
                     <button data-href="{{action(\'App\Http\Controllers\ReportdayController@destroy\', [$id])}}" class="btn btn-xs btn-danger delete_reportday_button"><i class="glyphicon glyphicon-trash"></i> Delete</button>
                  '
                )
                ->editColumn('created_at', function ($row) {
                   
                        return date_format(date_create($row->created_at),'d-m-Y');
                   
                })
                ->editColumn('modal', function ($row) {
                   return '<span class="display_currency" data-currency_symbol="true" ata-orig-value="'. $row->modal.'">'.
                    $row->modal.'</span>';
                   
                })
                ->removeColumn('id')
                ->removeColumn('keterangan')
                ->removeColumn('updated_at')
                ->rawColumns([0,5])
                ->make(false);
        }

        return view('reportday.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // if (! auth()->user()->can('reportday.create')) {
        //     abort(403, 'Unauthorized action.');
        // }

        $quick_add = false;
        if (! empty(request()->input('quick_add'))) {
            $quick_add = true;
        }


        return view('reportday.create')
                ->with(compact('quick_add'));
    }
    
    public function show($id)
    {
        // if (! auth()->user()->can('reportday.create')) {
        //     abort(403, 'Unauthorized action.');
        // }

            $reportday = Reportday::find($id)->first();
            $reportday_detail = Reportday_detail::where('id_reportday',$id)->get();

        return view('reportday.show')
                ->with(compact('reportday','reportday_detail'));
    }


    public function edit()
    {
        if (! auth()->user()->can('reportday.create')) {
            abort(403, 'Unauthorized action.');
        }

        $quick_add = false;
        if (! empty(request()->input('quick_add'))) {
            $quick_add = true;
        }


        return view('brand.create')
                ->with(compact('quick_add', 'is_repair_installed'));
    }

    public function destroy($id)
    {
        // if (! auth()->user()->can('reportday.delete')) {
        //     abort(403, 'Unauthorized action.');
        // }

        if (request()->ajax()) {
            try {
                // $business_id = request()->user()->business_id;

                $reportday = Reportday::findOrFail($id);
                $reportday->delete();

                $reportday_detail = Reportday_detail::where('id_reportday',$id);
                $reportday_detail->delete();

                $output = ['success' => true,
                    'msg' => "Data Berhasil Dihapus",
                ];
            } catch (\Exception $e) {
                \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

                $output = ['success' => false,
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
           
            $rday = New Reportday;
            $rday->modal = $request->input('modal');
            $rday->stok = $request->input('stok');
            $rday->cash = $request->input('cash');
            $rday->untung = $request->input('untung');
            $rday->created_at = $request->input('created_at');
            $rday->save();
            $last_id = $rday->id;

            // $detail = array();
            $cabang = $request->input('cabang');
            $pendapatan = $request->input('pendapatan');
            $setor = $request->input('setor');
            $untung_kotor = $request->input('untung_kotor');
            $untung_bersih = $request->input('untung_bersih');
            $ongkos_bubut = $request->input('ongkos_bubut');
            $ongkos_mekanik = $request->input('ongkos_mekanik');

            foreach ($cabang as $key => $value) {
                $data_detail[] = [
                    'cabang' => $value,
                    'pendapatan' => $pendapatan[$key],
                    'setor' => $setor[$key],
                    'untung_kotor' => $untung_kotor[$key],
                    'untung_bersih' => $untung_bersih[$key],
                    'ongkos_bubut' => $ongkos_bubut[$key],
                    'ongkos_mekanik' => $ongkos_mekanik[$key],
                    'id_reportday' => $last_id,
                    'created_at'=> $request->input('created_at')
                ];
            }
            // dd($data_detail);
            Reportday_detail::insert($data_detail);

            $output = ['success' => true,
                'data' => $data_detail,
                'msg' => "Data Berhasil disimpan",
            ];
        } catch (\Exception $e) {
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            $output = ['success' => false,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        return $output;
    }

}
