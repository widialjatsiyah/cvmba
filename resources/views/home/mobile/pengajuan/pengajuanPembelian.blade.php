@extends('layouts.app_mobile')
@section('title','Pengajuan Pembelian')

@section('content')

<div class="card bg-primary">
    <div class="card-body">
        <span style="font-weight: bolder;"> Pengajuan Pembelian </span>
    </div>
</div>

<section class="content" style="margin-bottom:150px">
    <div class="row">
        <div class="col-md-12">
            <div class="nav-tabs-custom tab-info">
                <ul class="nav nav-tabs justify-content-center">
                    <li class="nav-item">
                        <a class="nav-link active" href="#menunggu" data-toggle="tab" aria-expanded="true" style="font-size:14px">Menunggu</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="#disetujui" data-toggle="tab" aria-expanded="true" style="font-size:14px">Disetujui</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="#ditolak" data-toggle="tab" aria-expanded="true" style="font-size:14px">Ditolak</a>
                    </li>
                </ul>

                <div class="tab-content">
                    <!-- Tab Menunggu -->
                    <div class="tab-pane active" id="menunggu">
                       
                                @foreach($menunggu as $wait)
                                    <a href="{{ url('pengajuan-pembelian-detail/'.$wait->id) }}" class="text-primary">
                                <div class="row border-top p-3 border-primary">
                                    <div class="col">
                                        <span>{{ $wait->supplier_business_name }}</span><br>
                                        <span class="badge bg-primary">{{ $wait->ref_no }}</span>
                                    </div>
                                    <div class="col text-right">
                                        <span class="fw-bolder">Rp. {{ number_format($wait->final_total) }}</span><br>
                                        <span class="text-mute">{{ date_format(date_create($wait->transaction_date), 'd M Y') }}</span>
                                    </div>
                                </div>
                            </a>
                                @endforeach
                        <!-- Tambahkan Pagination -->
                        <div class="pagination-container">
                            {{ $menunggu->appends(['tab' => 'menunggu'])->links() }}
                        </div>
                    </div>

                    <!-- Tab Disetujui -->
                    <div class="tab-pane" id="disetujui">
                        @foreach($disetujui as $acc)
                            <a href="{{ url('pengajuan-pembelian-detail/'.$acc->id) }}" class="text-success">
                                <div class="row border-top p-3 border-success">
                                    <div class="col">
                                        <span>{{ $acc->supplier_business_name }}</span><br>
                                        <span class="badge bg-success">{{ $acc->ref_no }}</span>
                                    </div>
                                    <div class="col text-right">
                                        <span class="fw-bolder">Rp. {{ number_format($acc->final_total) }}</span><br>
                                        <span class="text-mute">{{ date_format(date_create($acc->transaction_date), 'd M Y') }}</span>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                        <!-- Tambahkan Pagination -->
                        <div class="pagination-container">
                            {{ $disetujui->appends(['tab' => 'disetujui'])->links() }}
                        </div>
                    </div>

                    <!-- Tab Ditolak -->
                    <div class="tab-pane" id="ditolak">
                        @foreach($ditolak as $dt)
                            <a href="{{ url('pengajuan-pembelian-detail/'.$dt->id) }}" class="text-danger">
                                <div class="row border-top p-3">
                                    <div class="col">
                                        <span>{{ $dt->supplier_business_name }}</span><br>
                                        <span class="badge bg-red">{{ $dt->ref_no }}</span>
                                    </div>
                                    <div class="col text-right">
                                        <span class="fw-bolder">Rp. {{ number_format($dt->final_total) }}</span><br>
                                        <span class="text-mute">{{ date_format(date_create($dt->transaction_date), 'd M Y') }}</span>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                        <!-- Tambahkan Pagination -->
                        <div class="pagination-container">
                            {{ $ditolak->appends(['tab' => 'ditolak'])->links() }}
                        </div>
                    </div>

                </div>
            </div>
        </div>
</section>

@endsection

@section('javascript')
<script type="text/javascript">
    $(document).ready(function() {
        // Ambil tab aktif dari query string di URL
        var urlParams = new URLSearchParams(window.location.search);
        var activeTab = urlParams.get('tab');

        // Jika ada tab yang dipilih, aktifkan tab tersebut
        if (activeTab) {
            $('.nav-tabs a[href="#' + activeTab + '"]').tab('show');
        }

        // Saat pengguna mengklik tab, ubah URL tanpa reload halaman
        $('.nav-tabs a').on('shown.bs.tab', function (e) {
            var tabId = $(e.target).attr("href").substr(1); // Ambil ID tab
            var newUrl = window.location.pathname + '?tab=' + tabId;
            history.pushState(null, '', newUrl); // Perbarui URL
        });
    });
</script>
@endsection
