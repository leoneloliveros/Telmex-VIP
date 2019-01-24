// Datos de configuracion del datatable
$(function(){
    tablaInicio = {
        init: function(){
            tablaInicio.getInfoInit()
        },

        getInfoInit: function () {
            $.post(baseurl + '/Reportesexcel/c_getInfoReportInit',
            {
                idTipo: null // parametros que se envian
            },
            function (data) {
                var obj = JSON.parse(data)
                console.log(obj);
                
                tablaInicio.printTableReportInit(obj);
            });

        },

        printTableReportInit: function (data) {
            ///lleno la tablaInicio con los valores enviados
            tablaInicio.tablereportinit = $('#tablereportinit').DataTable(tablaInicio.configTableReportInit(data, [
                {title: "OTP",data:"id_ot_padre"},
                {title: "No. de reportes envíados",data:"contador_reportes"},
                {title: "Último en Enviar",data:"enviador"},
                {title: "Última Fecha de Envío",data:"last_f_envio"},
                // {title: "Fecha Entrega de Servicio",data:"f_entrega_servicio"},
                // {title: "Cliente",data:"nombre_cliente"},
                // {title: "Obs.",data:"observaciones"},
            ]));
        },

        configTableReportInit: function (data, columns, onDraw) {
            return {
                initComplete: function () {
                    $('#tablereportinit  tfoot th').each(function () {
                        $(this).html('<input type="text" placeholder="Buscar" />');
                    });

                    var r = $('#tablereportinit tfoot tr');
                    r.find('th').each(function () {
                        $(this).css({'padding': 8});
                    });
                    $('#tablereportinit thead').append(r);
                    $('#search_0').css('text-align', 'center');

                    // DataTable
                    var table = $('#tablereportinit').DataTable();

                    // Apply the search
                    table.columns().every(function () {
                        var that = this;

                        $('input', this.footer()).on('keyup change', function () {
                            if (that.search() !== this.value) {
                                that.search(this.value).draw();
                            }
                        });
                    });
                },
                data: data,
                columns: columns,
                "language": {
                    "url": baseurl + "/assets/plugins/datatables/lang/es.json",
                },
                dom: 'Blfrtip',
                buttons: [
                    {
                        text: 'Generar Excel de Inicio <span class="fa fa-file-excel-o"></span>',
                        className: 'btn-cami_cool',
                        extend: 'excel',
                        title: 'REPORTE DE INICIO ZOLID',
                        filename: 'zolid ' + fecha_actual
                    },
                    {
                        text: 'Imprimir <span class="fa fa-print"></span>',
                        className: 'btn-cami_cool',
                        extend: 'print',
                        title: 'REPORTE DE INICIO ZOLID',
                    }
                ],
                select: true,

                "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],

                columnDefs: [{
                        // targets: -1,
                        // visible: false,
                        defaultContent: "",
                        // targets: -1,
                        orderable: false,
                    }],
                order: [[1, 'desc']],
                drawCallback: onDraw
            }},
        
    }
    




    tablaAct ={
        init: function(){
            tablaAct.getInfoAct()
        },
        getInfoAct: function(){
            $.post(baseurl + '/Reportesexcel/c_getInfoReportAct',{

            },
            function(data){
                var obj = JSON.parse(data);
                tablaAct.printTableReportAct(obj);
            });
        },
        printTableReportAct: function (data) {
            ///lleno la tablaInicio con los valores enviados
            tablaAct.tablereportact = $('#tablereportAct').DataTable(tablaAct.configTableReportAct(data, [
                {title: "OTP",data:"k_id_ot_padre"},
                {title: "OTH",data:"id_orden_trabajo_hija"},
                {title: "Ultimo en Actualizar",data:"Nombres"},
                {title: "Cliente",data:"nombre_cliente"},
                {title: "Servicio",data:"servicio"},
                {title: "Fecha de Actualización",data:"fecha"},
            ]));
        },

        configTableReportAct: function (data, columns, onDraw) {
            return {
                initComplete: function () {
                    $('#tablereportAct  tfoot th').each(function () {
                        $(this).html('<input type="text" placeholder="Buscar" />');
                    });

                    var r = $('#tablereportAct tfoot tr');
                    r.find('th').each(function () {
                        $(this).css({'padding': 8});
                    });
                    $('#tablereportAct thead').append(r);
                    $('#search_0').css('text-align', 'center');

                    // DataTable
                    var table = $('#tablereportAct').DataTable();

                    // Apply the search
                    table.columns().every(function () {
                        var that = this;

                        $('input', this.footer()).on('keyup change', function () {
                            if (that.search() !== this.value) {
                                that.search(this.value).draw();
                            }
                        });
                    });
                },
                data: data,
                columns: columns,
                "language": {
                    "url": baseurl + "/assets/plugins/datatables/lang/es.json",
                },
                dom: 'Blfrtip',
                buttons: [
                    {
                        text: 'Generar Excel de Actualización <span class="fa fa-file-excel-o"></span>',
                        className: 'btn-cami_cool',
                        extend: 'excel',
                        title: 'REPORTE DE INICIO ZOLID',
                        filename: 'zolid ' + fecha_actual
                    },
                    {
                        text: 'Imprimir <span class="fa fa-print"></span>',
                        className: 'btn-cami_cool',
                        extend: 'print',
                        title: 'REPORTE DE INICIO ZOLID',
                    }
                ],
                select: true,

                "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],

                columnDefs: [{
                        // targets: -1,
                        // visible: false,
                        defaultContent: "",
                        // targets: -1,
                        orderable: false,
                    }],
                order: [[1, 'desc']],
                drawCallback: onDraw
            }},
        


    }
    tablaAct.init();
    tablaInicio.init();
});