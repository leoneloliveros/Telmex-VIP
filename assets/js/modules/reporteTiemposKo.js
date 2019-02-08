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
            $.post(baseurl + '/ReporteTiemposKo/c_generateReportTimesKickOff',
                    {
                        //parametros
                        f_inicio: $('#f_inicio').val(),
                        f_final: $('#f_final').val()
                    },
                    // funcion que recibe los datos (callback)
                            function(data) {
                                // convertir el json a objeto de javascript
                                var obj = JSON.parse(data);
//                                console.log(data);
//                                console.log(obj);

                                vista.printTableReportTimesKickOff(obj);
                            }
                    );
                },
        // Organiza la informacion para poder pintarla en la vista del reporte de tiempos
        printTableReportTimesKickOff: function(data) {
            var html = '';
            $.each(data, function(key, value) {
                var clase = (key == '1') ? 'grupo-info-tiempo-ko' : 'text-right';
                var promedio_cerradas = (value.total_cerradas != 0) ? value.dia_promedio_cerrado / value.total_cerradas : 0;
                var promedio_abiertas = (value.total_abiertas != 0) ? value.dia_promedio_abierto / value.total_abiertas : 0;

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
        },

        // obtener el color de la celda segun la cantidad enviada (v)
        getColotTd: function(v){
            let clase = '';
            if (v >= 2 && v < 3) {
                clase = 'amarillito';
            } else if(v >= 3){
                clase = 'rojito';
            } 
            return clase;
        },



    };
    vista.init();
});