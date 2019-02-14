// Imprimir lo que esta dentro del div

/*$("#btnPrint").on("click", function () {
 var divContents = $("#dvContainer").html();
 var printWindow = window.open('', '', 'height=400,width=800');
 printWindow.document.write('<html><head><title>DIV Contents</title>');
 printWindow.document.write('</head><body >');
 printWindow.document.write(divContents);
 printWindow.document.write('</body></html>');
 printWindow.document.close();
 printWindow.print();
 });*/

$(function() {
    vista = {
        init: function() {
            vista.events();
            vista.generateReportTimesKickOff();
        },
        //Eventos de la ventana.
        events: function() {

            $('#btnGenerarReporte').on('click', vista.generateReportTimesKickOff);
        },
        //Realiza una peticion para consultar la informacion del reporte de tiempos
        // de acuerdo a las fechas seleccionadas en el formulario
        generateReportTimesKickOff: function() {
            //metodo ajax (post)
            $.post(baseurl + '/ReporteTiemposKo/c_generateReportTimesKickOff', {
                //parametros
                f_inicio: $('#f_inicio').val(),
                f_final: $('#f_final').val()
            },
                    // funcion que recibe los datos (callback)
                            function(data) {
                                // convertir el json a objeto de javascript
                                var obj = JSON.parse(data);
                                vista.printTableReportTimesKickOff(obj);
                            }
                    );
                },
        // Organiza la informacion para poder pintarla en la vista del reporte de tiempos
        printTableReportTimesKickOff: function(data) {
            const graficas = {
                ingenieros: [],
                d_min_cerr: [],
                d_max_cerr: [],
                d_prom_cerr: []
            };

            var html = '';
            $.each(data, function(key, value) {
                var promedio_cerradas = (value.total_cerradas != 0) ? value.dia_promedio_cerrado / value.total_cerradas : 0;
                var promedio_abiertas = (value.total_abiertas != 0) ? value.dia_promedio_abierto / value.total_abiertas : 0;

                /*SECCION PARA ARMAR LOS ARREGLOS PARA LAS GRAFICAS*/
                graficas.ingenieros.push(value.ingeniero);
                graficas.d_min_cerr.push(parseInt(value.dia_min_cerrado));
                graficas.d_max_cerr.push(parseInt(value.dia_max_cerrado));
                graficas.d_prom_cerr.push(promedio_cerradas);
                /*FIN  ARREGLOS PARA LAS GRAFICAS*/

                var clase = (key == '1') ? 'grupo-info-tiempo-ko' : 'text-right';

                html += '<tr>' +
                        '<td class="' + clase + '">' + value.ingeniero + '</td>' +
                        '<td>' + value.total_cerradas + '</td>' +
                        '<td>' + value.total_abiertas + '</td>' +
                        `<td class="${vista.getColotTd(value.dia_min_cerrado)}">` + value.dia_min_cerrado + '</td>' +
                        `<td class="${vista.getColotTd(value.dia_min_abierto)}">` + value.dia_min_abierto + '</td>' +
                        `<td class="${vista.getColotTd(value.dia_max_cerrado)}">` + value.dia_max_cerrado + '</td>' +
                        `<td class="${vista.getColotTd(value.dia_max_abierto)}">` + value.dia_max_abierto + '</td>' +
                        `<td class="${vista.getColotTd(promedio_cerradas)}">` + parseFloat(promedio_cerradas).toFixed(2) + '</td>' +
                        `<td class="${vista.getColotTd(promedio_abiertas)}">` + parseFloat(promedio_abiertas).toFixed(2) + '</td>' +
                        '</tr>';
            });

            $('#bodyInfo').empty();
            $('#bodyInfo').append(html);


            (data[1].total_cerradas != 0) ? vista.createGraphics(graficas) : '';
        },

        // obtener el color de la celda segun la cantidad enviada (v)
        getColotTd: function(v) {
            let clase = '';
            if (v >= 2 && v < 3) {
                clase = 'amarillito';
            } else if (v >= 3) {
                clase = 'rojito';
            }
            return clase;
        },

        // Funcion para pintar las graficas de KO estandar
        createGraphics: function(data) {
            Highcharts.chart('containerGraphics', {
                chart: {
                    type: 'column',
                    // backgroundColor: '#6d9ea3',
                    options3d: {
                        enabled: true,
                        alpha: 20,
                        beta: 5,
                        depth: 50
                    }
                },
                title: {
                    text: 'KO Estándar - Cerradas'
                },
                // subtitle: {
                //     text: 'Source: el subtitulo'
                // },
                xAxis: {
                    categories: data.ingenieros,
                    crosshair: true
                },
                yAxis: {
                    min: 0,
                    title: {
                        text: 'Cantidad'
                    }
                },
                tooltip: {
                    headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                    pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                            '<td style="padding:0"><b>{point.y:.1f} Días</b></td></tr>',
                    footerFormat: '</table>',
                    shared: true,
                    useHTML: true
                },
                plotOptions: {
                    column: {
                        pointPadding: 0.2,
                        borderWidth: 0
                    }
                },
                series: [{
                        name: 'Días Min Cerrado',
                        data: data.d_min_cerr,
                        color: '#084c6f'

                    }, {
                        name: 'Días Max Cerrado',
                        data: data.d_max_cerr,
                        color: '#1b771a'

                    }, {
                        name: 'Días Prom Cerrado',
                        data: data.d_prom_cerr,
                        color: '#5313c3'

                    }]
            });




        },

    };
    vista.init();
});