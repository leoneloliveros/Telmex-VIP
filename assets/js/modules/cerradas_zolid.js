// Datos de configuracion del datatable
$(function() {
    cerradas = {
        init: function() {
            cerradas.getInfoCerradasZolid()
        },

        getInfoCerradasZolid: function() {
            $.post(baseurl + '/OtHija/c_getInfoCerradasZolid',
                    {
                        idTipo: null // parametros que se envian
                    },
                    function(data) {
                        var obj = JSON.parse(data);
                        cerradas.printTableCerradasZolid(obj);
                    });
        },

        printTableCerradasZolid: function(data) {
            ///lleno la cerradas con los valores enviados
            cerradas.tableCerradasZolid = $('#tableCerradasZolid').DataTable(cerradas.configTableReportAct(data, [
                {title: "OT Padre", data: "nro_ot_onyx"},
                {title: "Id Orden Trabajo Hija", data: "id_orden_trabajo_hija"},
                {title: "Nombre Cliente", data: "nombre_cliente"},
                {title: "Ot Hija", data: "n_name_tipo"},
                {title: "Estado Orden Trabajo Hija", data: "estado_orden_trabajo_hija"},
                {title: "Ingeniero Responsable", data: "ingeniero"},
                {title: "Fecha Creación", data: "fecha_creacion"},
                {title: "Recurrente", data: "MRC"},
            ]));

        },

        configTableReportAct: function(data, columns, onDraw) {
            return {
                initComplete: function() {
                    $('#tableCerradasZolid  tfoot th').each(function() {
                        $(this).html('<input type="text" placeholder="Buscar" />');
                    });

                    var r = $('#tableCerradasZolid tfoot tr');
                    r.find('th').each(function() {
                        $(this).css({'padding': 8});
                    });
                    $('#tableCerradasZolid thead').append(r);
                    $('#search_0').css('text-align', 'center');

                    // DataTable
                    var table = $('#tableCerradasZolid').DataTable();

                    // Apply the search
                    table.columns().every(function() {
                        var that = this;

                        $('input', this.footer()).on('keyup change', function() {
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
                        text: 'Excel <span class="fa fa-file-excel-o"></span>',
                        className: 'btn-cami_cool',
                        extend: 'excel',
                        title: 'ZOLID EXCEL',
                        filename: 'zolid ' + fecha_actual,
                    },
                    {
                        text: 'Imprimir <span class="fa fa-print"></span>',
                        className: 'btn-cami_cool',
                        extend: 'print',
                        title: 'Reporte Zolid',
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
            }
        },

    }

    cerradas.init();
});