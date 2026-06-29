$(document).ready(function() {
    if ($('#dashboard_date_filter').length == 1) {
        dateRangeSettings.startDate = moment();
        dateRangeSettings.endDate = moment();
        $('#dashboard_date_filter').daterangepicker(dateRangeSettings, function(start, end) {
            $('#dashboard_date_filter span').html(
                start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format)
            );
            update_statistics(start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD'));
            
         updateProfitLossMobile();
         
            if ($('#quotation_table').length && $('#dashboard_location').length) {
                quotation_datatable.ajax.reload();
            }
        });

        update_statistics(moment().format('YYYY-MM-DD'), moment().format('YYYY-MM-DD'));
        get_stock_value();
         updateProfitLossMobile();
    }


    //Profit / Loss
   
function updateProfitLossMobile(start = null, end = null, location_id = null, selector = null) {
    if(start == null){
        var start = $('#dashboard_date_filter')
                    .data('daterangepicker')
                    .startDate.format('YYYY-MM-DD');
    }
    if(end == null){
        var end = $('#dashboard_date_filter')
                    .data('daterangepicker')
                    .endDate.format('YYYY-MM-DD');
    }
   
    var data = { start_date: start, end_date: end, location_id: location_id };
    var loader = '<i class="fas fa-sync fa-spin fa-fw margin-bottom"></i>';
   
    $('.total_profit').html(loader);
    $('.total_profit_dc').html(loader);
    $('.total_profit_grosir').html(loader);
    $('.total_profit_mart').html(loader);
    $('.total_profit_lepari').html(loader);
    
    $.ajax({
        method: 'GET',
        url: '/home-mobile/profit-loss',
        data: data,
        success: function(res) {
            // alert(res);
            $('.total_profit').html(__currency_trans_from_en(res.total_profit, true));
            $('.total_profit_dc').html(__currency_trans_from_en(res.total_profit_dc, true));
            $('.total_profit_grosir').html(__currency_trans_from_en(res.total_profit_grosir, true));
            $('.total_profit_mart').html(__currency_trans_from_en(res.total_profit_mart, true));
            $('.total_profit_lepari').html(__currency_trans_from_en(res.total_profit_lepari, true));
        },
    });

}
    
    
function get_stock_value() {
    var loader = __fa_awesome();
    $('#closing_stock_by_pp').html(loader);
    $('#closing_stock_by_sp').html(loader);
    $('#potential_profit').html(loader);
    $('#profit_margin').html(loader);
    var data = {
        location_id: $('#dashboard_location').val()
    }
    $.ajax({
        url: '/reports/get-stock-value',
        data: data,
        success: function(data) {
            $('.closing_stock_by_pp').text(__currency_trans_from_en(data.closing_stock_by_pp));
            $('.closing_stock_by_sp').text(__currency_trans_from_en(data.closing_stock_by_sp));
            $('.potential_profit').text(__currency_trans_from_en(data.potential_profit));
            $('.profit_margin').text(__currency_trans_from_en(data.profit_margin, false));
        },
    });
}

    $('#dashboard_location').change( function(e) {
        get_stock_value() 
    });

    //atock alert datatables
    var stock_alert_table = $('#stock_alert_table').DataTable({
        processing: true,
        serverSide: true,
        ordering: false,
        searching: false,
        scrollY:        "75vh",
        scrollX:        true,
        scrollCollapse: true,
        fixedHeader: false,
        dom: 'tirp',
        ajax: {
            "url": '/home/product-stock-alert',
            "data": function ( d ) {
                if ($('#stock_alert_location').length > 0) {
                    d.location_id = $('#stock_alert_location').val();
                }
            }
        },
        fnDrawCallback: function(oSettings) {
            __currency_convert_recursively($('#stock_alert_table'));
        },
    });

    $('#stock_alert_location').change( function(){
        stock_alert_table.ajax.reload();
    });
    //payment dues datatables
    purchase_payment_dues_table = $('#purchase_payment_dues_table').DataTable({
        processing: true,
        serverSide: true,
        ordering: false,
        searching: false,
        scrollY:        "75vh",
        scrollX:        true,
        scrollCollapse: true,
        fixedHeader: false,
        dom: 'tirp',
        ajax: {
            "url": '/home/purchase-payment-dues',
            "data": function ( d ) {
                if ($('#purchase_payment_dues_location').length > 0) {
                    d.location_id = $('#purchase_payment_dues_location').val();
                }
            }
        },
        fnDrawCallback: function(oSettings) {
            __currency_convert_recursively($('#purchase_payment_dues_table'));
        },
    });

    $('#purchase_payment_dues_location').change( function(){
        purchase_payment_dues_table.ajax.reload();
    });

    //Sales dues datatables
    sales_payment_dues_table = $('#sales_payment_dues_table').DataTable({
        processing: true,
        serverSide: true,
        ordering: false,
        searching: false,
        scrollY:        "75vh",
        scrollX:        true,
        scrollCollapse: true,
        fixedHeader: false,
        dom: 'tirp',
        ajax: {
            "url": '/home/sales-payment-dues',
            "data": function ( d ) {
                if ($('#sales_payment_dues_location').length > 0) {
                    d.location_id = $('#sales_payment_dues_location').val();
                }
            }
        },
        fnDrawCallback: function(oSettings) {
            __currency_convert_recursively($('#sales_payment_dues_table'));
        },
    });

    $('#sales_payment_dues_location').change( function(){
        sales_payment_dues_table.ajax.reload();
    });

    //Stock expiry report table
    stock_expiry_alert_table = $('#stock_expiry_alert_table').DataTable({
        processing: true,
        serverSide: true,
        searching: false,
        scrollY:        "75vh",
        scrollX:        true,
        scrollCollapse: true,
        fixedHeader: false,
        dom: 'tirp',
        ajax: {
            url: '/reports/stock-expiry',
            data: function(d) {
                d.exp_date_filter = $('#stock_expiry_alert_days').val();
            },
        },
        order: [[3, 'asc']],
        columns: [
            { data: 'product', name: 'p.name' },
            { data: 'location', name: 'l.name' },
            { data: 'stock_left', name: 'stock_left' },
            { data: 'exp_date', name: 'exp_date' },
        ],
        fnDrawCallback: function(oSettings) {
            __show_date_diff_for_human($('#stock_expiry_alert_table'));
            __currency_convert_recursively($('#stock_expiry_alert_table'));
        },
    });

    if ($('#quotation_table').length) {
        quotation_datatable = $('#quotation_table').DataTable({
            processing: true,
            serverSide: true,
            aaSorting: [[0, 'desc']],
            "ajax": {
                "url": '/sells/draft-dt?is_quotation=1',
                "data": function ( d ) {
                    if ($('#dashboard_location').length > 0) {
                        d.location_id = $('#dashboard_location').val();
                    }
                }
            },
            columnDefs: [ {
                "targets": 4,
                "orderable": false,
                "searchable": false
            } ],
            columns: [
                { data: 'transaction_date', name: 'transaction_date'  },
                { data: 'invoice_no', name: 'invoice_no'},
                { data: 'name', name: 'contacts.name'},
                { data: 'business_location', name: 'bl.name'},
                { data: 'action', name: 'action'}
            ]            
        });
    }
});

function update_statistics(start, end) {
    var location_id = '';
    if ($('#dashboard_location').length > 0) {
        location_id = $('#dashboard_location').val();
    }
    var data = { start: start, end: end, location_id: location_id };
    //get purchase details
    var loader = '<i class="fas fa-sync fa-spin fa-fw margin-bottom"></i>';
   
    $('.total_sell').html(loader);
    $('.total_sell_dc').html(loader);
    $('.total_sell_grosir').html(loader);
    $('.total_sell_mart').html(loader);
    $('.total_sell_lepari').html(loader);
    $.ajax({
        method: 'get',
        url: '/home-mobile/get-totals',
        dataType: 'json',
        data: data,
        success: function(data) {
            //sell details
            $('.total_sell').html(__currency_trans_from_en(data.total_sell, true));
            $('.total_sell_dc').html(__currency_trans_from_en(data.total_sell_dc, true));
            $('.total_sell_grosir').html(__currency_trans_from_en(data.total_sell_grosir, true));
            $('.total_sell_mart').html(__currency_trans_from_en(data.total_sell_mart, true));
            $('.total_sell_lepari').html(__currency_trans_from_en(data.total_sell_lepari, true));
        },
    });
}
