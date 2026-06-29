<div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">

                 
                    <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Detail Day Report</h4>
                    </div>

                    <div class="modal-body">
                            <div class="row">
                                <div class="col-md-4"> 
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Date</label>
                                        <div class="col-sm-9">
                                            {{ date_format(date_create($reportday->created_at),'d F Y') }}
                                        </div>
                                    </div>
                                </div>
                                
                            </div> 
                            
                            <hr>
                            
                            <div class="row mt-5">
                                <div class="col-sm-12">
                                    <table class="table table-hover table-bordered" id="tableItem">
                                    <thead>
                                        <th>Cabang</th>
                                        <th>Pendapatan</th>
                                        <th>Setor</th>
                                        <th>Untung Kotor</th>
                                        <th>Untung Bersih</th>
                                        <th>Ongkos Mekanik</th>
                                        <th>Ongkos Bubut</th>
                                    </thead>
                                    <tbody >
                                        <?php
                                        $pendapatan = 0;
                                        $setor = 0;
                                        $untung_kotor = 0;
                                        $untung_bersih = 0;
                                        $ongkos_bubut = 0;
                                        $ongkos_mekanik = 0 ;
                                        ?>

                                        @foreach($reportday_detail as $rdetail)
                                       
                                            <tr>
                                                <td>{{ $rdetail->cabang }}</td>
                                                <td><span class="display_currency" data-currency_symbol="true">{{ $rdetail->pendapatan }}</span></td>
                                                <td><span class="display_currency" data-currency_symbol="true">{{ $rdetail->setor }}</span></td>
                                                <td><span class="display_currency" data-currency_symbol="true">{{ $rdetail->untung_kotor }}</span></td>
                                                <td><span class="display_currency" data-currency_symbol="true">{{ $rdetail->untung_bersih }}</span></td>
                                                <td><span class="display_currency" data-currency_symbol="true">{{ $rdetail->ongkos_mekanik }}</span></td>
                                                <td><span class="display_currency" data-currency_symbol="true">{{ $rdetail->ongkos_bubut }}</span></td>
                                            </tr>
                                            @php
                                        $pendapatan   += $rdetail->pendapatan;
                                        $setor        += $rdetail->setor;
                                        $untung_kotor       += $rdetail->untung_kotor;
                                        $untung_bersih        += $rdetail->untung_bersih;
                                        $ongkos_mekanik       += $rdetail->ongkos_mekanik;
                                        $ongkos_bubut        += $rdetail->ongkos_bubut;
                                        @endphp

                                        @endforeach

                                    </tbody>
                                    <tfoot>
                                    <tr class="bg bg-success">
                                                <td>Total</td>
                                                <td><span class="display_currency" data-currency_symbol="true">{{ $pendapatan }}</span></td>
                                                <td><span class="display_currency" data-currency_symbol="true">{{ $setor }}</span></td>
                                                <td><span class="display_currency" data-currency_symbol="true">{{ $untung_kotor }}</span></td>
                                                <td><span class="display_currency" data-currency_symbol="true">{{ $untung_bersih }}</span></td>
                                                <td><span class="display_currency" data-currency_symbol="true">{{ $ongkos_mekanik }}</span></td>
                                                <td><span class="display_currency" data-currency_symbol="true">{{ $ongkos_bubut }}</span></td>
                                            </tr>
                                    </tfoot>
                                    <table>
                                </div>
                            </div>
            

                    </div>

                  
                    <div class="row" style="margin-top:20px">
                    <div class="col-md-12"> 
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Modal</label>
                                        <div class="col-sm-9">
                                          {{ $reportday->modal }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">                           
                                    <div class="form-group ">
                                        <label class="col-sm-3 control-label">Stok </label>
                                        <div class="col-sm-9">
                                           {{ $reportday->stok }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">                           
                                    <div class="form-group ">
                                        <label class="col-sm-3 control-label">Cash </label>
                                        <div class="col-sm-9">
                                            
                                           {{ $reportday->cash }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12"> 
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Untung </label>
                                        <div class="col-sm-9">
                                           
                                        {{ $reportday->untung }}
                                        </div>
                                    </div>
                                </div>

                            </div> 
                            <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
                    </div>
                </div><!-- /.modal-content -->
            </div>