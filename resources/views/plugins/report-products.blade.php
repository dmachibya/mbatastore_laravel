<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
<script src="/js/chartjs.js" charset=utf-8></script>

<script type="text/javascript">
    ;(function ($, window, document) {
        var startDate;
        var endDate;

        $(document).ready(function () {
            $('#daterangepicker').daterangepicker(
                {
                    startDate: moment().subtract('days', 6),
                    endDate: moment(),
                    showDropdowns: false,
                    showWeekNumbers: true,
                    timePicker: false,
                    timePickerIncrement: 30,
                    timePicker12Hour: false,
                    ranges: {
                        '{{ trans('app.today') }}': [moment(), moment()],
                        '{{ trans('app.yesterday') }}': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                        '{{ trans('app.last_7_days') }}': [moment().subtract(6, 'days'), moment()],
                        '{{ trans('app.last_30_day') }}': [moment().subtract(29, 'days'), moment()],
                        '{{ trans('app.this_month') }}': [moment().startOf('month'), moment()],
                        '{{ trans('app.last_month') }}': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                        '{{trans('app.last_12_month')}}': [moment().startOf('month').subtract(12, 'month'), moment().endOf('month')],
                        '{{trans('app.this_year')}}': [moment().startOf('year'), moment()],
                        '{{trans('app.last_year')}}': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year').endOf('year')]
                    },
                    opens: 'left',
                    buttonClasses: ['btn btn-default'],
                    cancelClass: 'btn-small',
                    format: 'DD/MM/YYYY',
                    separator: ' to ',
                },
                function (start, end) {
                    //console.log("Callback has been called!");
                    $('#daterangepicker span').html(start.format('D MMMM YYYY') + ' - ' + end.format('D MMMM YYYY'));
                    startDate = start.format('YYYY-MM-DD');
                    endDate = end.format('YYYY-MM-DD');
                    $('#getFromDate').val(start.format('YYYY-MM-DD'));
                    $('#getToDate').val(end.format('YYYY-MM-DD'));
                    //Get Filter Data
                    let productId = $('#productId').val();
                    let shopId = $('#shopId').val();
                    //console.log(window.location.hostname)
                    let dataString = "fromDate=" + startDate + "&toDate=" + endDate + "&productId="+productId+"&shopId="+shopId;
                    //Data Table Reset After Ajax:
                    dataTableResetting(dataString);
                }
            );
            //Set the initial state of the picker label
            $('#daterangepicker span').html(moment().subtract('days', 29).format('D MMMM YYYY') + ' - ' + moment().format('D MMMM YYYY'));
            $('#getFromDate').val(moment().subtract('days', 7).format('YYYY-MM-DD'));
            $('#getToDate').val(moment().format('YYYY-MM-DD'));
        });
        ///Calling Chart Function to manipulate:

    }(window.jQuery, window, document));

    ///Searching and Manipulating DataTable Data:
    function dataTableResetting(dataString) {

        var table = $('.table-no-sort');
        if ($.fn.dataTable.isDataTable(table)) {
            table.DataTable().destroy();
            //table.clear();
        }
        let url = '{{route('admin.sales.products.getMore') }}';

        table.DataTable({
            "responsive": true,
            "iDisplayLength": {{ getPaginationValue() }},
            "ajax": url + '/?' + dataString,
            "columns": [
                {
                    'data': 'name',
                    'name': 'name',
                    'orderable': true,
                    'searchable': true,
                    'exportable': true,
                    'printable': true
                },
                {
                    'data': 'model_number',
                    'name': 'model_number',
                    'orderable': true,
                    'searchable': true,
                    'exportable': true,
                    'printable': true
                },
                {
                    'data': null,
                    "render": function (data) {
                        return '<span class="label label-outline"> '+ data.gtin_type + ' </span> ' + data.gtin;
                    },
                    'name': 'gtin',
                    'orderable': true,
                    'searchable': true,
                    'exportable': true,
                    'printable': true
                },
                {
                    'data': 'quantity',
                    'name': 'quantity',
                    'orderable': true,
                    'searchable': true,
                    'exportable': true,
                    'printable': true
                },
                {
                    'data': 'uniquePurchase',
                    'name': 'uniquePurchase',
                    'orderable': true,
                    'searchable': true,
                    'exportable': true,
                    'printable': true
                },
                {
                    'data': null,
                    'render' : function (data) {
                        return Number(data.avgPrice);
                    },
                    'name': 'avgPrice',
                    'orderable': true,
                    'searchable': true,
                    'exportable': true,
                    'printable': true
                },
                {
                    'data': null,
                    'render' : function (data) {
                        return Number(data.totalSale);
                    },
                    'name': 'totalSale',
                    'orderable': true,
                    'searchable': true,
                    'exportable': true,
                    'printable': true
                }

            ],
            "oLanguage": {
                "sInfo": "_START_ to _END_ of _TOTAL_ entries",
                "sLengthMenu": "Show _MENU_",
                "sSearch": "",
                "sEmptyTable": "No data found!",
                "oPaginate": {
                    "sNext": '<i class="fa fa-hand-o-right"></i>',
                    "sPrevious": '<i class="fa fa-hand-o-left"></i>',
                },
            },
            "aoColumnDefs": [{
                "bSortable": false,
                "aTargets": [-1]
            }],
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ]
        });
    }

    function ajaxFire(ajaxUrl, params,  handleData){

        $.ajax({
            url:ajaxUrl+'/?'+params,
            method:'get',
            contentType: 'application/json',
            success:function (response){
                //console.log(response)
                handleData(response.data);
            }
        });
    }

    //Clear All Filter:
    function clearAllFilter(){
        $('#productId').select2("val", "");
        $('#shopId').select2("val", "");
    }

    function fireEventOnFilter(str) {

        let fromDate = $('#getFromDate').val();
        let toDate = $('#getToDate').val();
        let productId = $('#productId').val();
        let shopId = $('#shopId').val();
        //console.log(window.location.hostname)
        let dataString = "fromDate=" + fromDate + "&toDate=" + toDate + "&productId="+productId+"&shopId="+shopId;
        //Data Table Reset After Ajax:
        dataTableResetting(dataString);

    }

</script>