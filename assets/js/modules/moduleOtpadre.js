// *******************************************TABLAS de OT PADRE ***************************
$(function () {

    gral = {
        init: function () {
            gral.events();

        },

        //Eventos de la ventana.
        events: function () {
            //funciones para guardar los codigos de resolucion
            $('body').on('change', 'select.cod_resolucion', gral.guardar_codigo_observacion);
            $('body').on('blur', 'textarea.obs_cod_resolucion', gral.guardar_codigo_observacion);
            // boton para refrescar la pantalla
            $('body').on('click', 'a#reload', function () {
                location.reload();
            });

        },

        // Guarda el codigo de resolucion y su observacion cuandocambie el select o el textarea
        guardar_codigo_observacion: function () {
            var input = $(this);
            var trParent = input.parents('tr');
            var tabla = input.parents('table').attr('id');

            const val_select = trParent.find('.cod_resolucion').val();
            const val_observacion = trParent.find('.obs_cod_resolucion').val();

            // const val_select =

            var record;
            switch (tabla) {
                case 'table_otPadreList':
                    record = vista.table_otPadreList.row(trParent).data();
                    break;
                case 'table_otPadreListHoy':
                    record = hoy.table_otPadreListHoy.row(trParent).data();
                    break;
                case 'table_otPadreListVencidas':
                    record = vencidas.table_otPadreListVencidas.row(trParent).data();
                    break;
                case 'table_list_opc':
                    record = lista.tableOpcList.row(trParent).data();
                    break;
                case 'table_otPadreListEmails':
                    record = emails.table_otPadreListEmails.row(trParent).data();
                    break;
                case 'table_reporte_actualizacion':
                    record = reporte_act.table_reporte_actualizacion.row(trParent).data();
                    break;
            }


            $.post(baseurl + '/OtPadre/update_data',
                    {
                        // clave: 'valor' // parametros que se envian
                        id: record.k_id_ot_padre,
                        lista: val_select,
                        observacion: val_observacion
                    },
                    function (data) {
                        var res = JSON.parse(data);
                        // if (res == true) {
                        //     swal(
                        //             'Guardado!',
                        //             'Actualizo correctamente los campos',
                        //             'success'
                        //             )
                        //     setTimeout("location.reload()", 1500);
                        // } else {
                        //     swal('Error',
                        //             'No tiene permiso para esta accíon',
                        //             'error'
                        //             )
                        // }
                    });
        },

        // Retorna cantidad de dias desde el ultimo reporte
        cant_dias_ultimo_reporte: function (obj) {

            if (obj.ultimo_envio_reporte) {
                const hoy = new Date(formato_fecha.getFullYear(), formato_fecha.getMonth(), formato_fecha.getDate());
                const s = obj.ultimo_envio_reporte.split("-");
                const send = new Date(s[0], s[1] - 1, s[2]);
                const diasdif = hoy.getTime() - send.getTime();
                const cantdias = Math.round(diasdif / (1000 * 60 * 60 * 24));
                return cantdias;
            }
            return null;
        },

        // Retorna un input con las observaciones dejadas de la lista
        inputObservaciones: function (obj) {
            // console.log("obj", obj);
            const observacion = (obj.observacion == null) ? '' : obj.observacion;
            return `<textarea class="obs_cod_resolucion" spellcheck="false">${observacion}</textarea>`;
        },

        // retorna select con la lista de observaciones
        listaObservaciones: function (obj) {
            const seleccionada = (obj.lista_observaciones == null) ? `<option value=""></option>` : `<option value="${obj.lista_observaciones}" style="color: blue;">${obj.lista_observaciones.toLowerCase()}</option>`;
            const select = `
                <select class="cod_resolucion">
                    <optgroup label="Códigos Nuevos">
                        ${seleccionada}
                        <option value="CLIENTE - SIN FECHA PARA RECIBIR EL SERVICIO">Cliente - sin fecha para recibir el servicio</option>
                        <option value="CLIENTE/SIN FECHA ADECUACIONES EN SEDE (ELEC/FIS)">Cliente/sin fecha adecuaciones en sede (elec/fis)</option>
                        <option value="CLIENTE/SIN DISPONIBILIDAD INFRA (PTA TELEF/LAN)">Cliente/sin disponibilidad infra (pta telef/lan)</option>
                        <option value="CLIENTE/CAMBIO DE ALCANCE (CBIO  TIPO SERVICIO)">Cliente/cambio de alcance (cbio  tipo servicio)</option>
                        <option value="CLIENTE/CAMBIO DE UBICACIÓN DE ULTIMA MILLA">Cliente/cambio de ubicación de ultima milla</option>
                        <option value="CLIENTE/NO APRUEBA COSTOS DE OBRA CIVIL">Cliente/no aprueba costos de obra civil</option>
                        <option value="CLIENTE/NO PERMITE CIERRE DE KO">Cliente/no permite cierre de ko</option>
                        <option value="CLIENTE/SIN DEFINICIÓN DIR DE UBICACIÓN SERVICIO">Cliente/sin definición dir de ubicación servicio</option>
                        <option value="CLIENTE/NO PERMITE PROG ACT ETAPA INICIAL VOC">Cliente/no permite prog act etapa inicial voc</option>
                        <option value="CLIENTE/NO PERMITE PROG ACT ETAPA INTERMEDIA EOC">Cliente/no permite prog act etapa intermedia eoc</option>
                        <option value="CLIENTE/NO PERMITE PROG ACT ETAPA INTERMEDIA EMP">Cliente/no permite prog act etapa intermedia emp</option>
                        <option value="CLIENTE/NO PERMITE PROG ACT  VOC TERCERO">Cliente/no permite prog act  voc tercero</option>
                        <option value="CLIENTE/NO PERMITE PROG ACT ETAP INTERMEDIA UM TER">Cliente/no permite prog act etap intermedia um ter</option>
                        <option value="CLIENTE/NO PERMITE PROG ACT ETAPA FINAL ES">Cliente/no permite prog act etapa final es</option>
                        <option value="CLIENTE/NO PERMITE PROG ACT ETAPA FINAL ES REQ VM">Cliente/no permite prog act etapa final es req vm</option>
                        <option value="CLIENTE/SIN CONTRATO FIRMADO">Cliente/sin contrato firmado</option>
                        <option value="CLIENTE/PROGRAMADA_PROXIMO PERIODO">Cliente/programada_proximo periodo</option>
                        <option value="PL_ EXT/PERMISO MUNI - PERMISO ARREND INFRAESTRUC">Pl_ ext/permiso muni - permiso arrend infraestruc</option>
                        <option value="PL_ EXT/NO VIABLE EN FACTIBILIDAD POR TERCEROS">Pl_ ext/no viable en factibilidad por terceros</option>
                        <option value="PL_ EXT/ETAPA INTERMEDIA/SIN PERSONAL  EOC/EMP">Pl_ ext/etapa intermedia/sin personal  eoc/emp</option>
                        <option value="PL_ EXT/SIN APROBACIÓN COSTOS TENDIDO EXTERNO">Pl_ ext/sin aprobación costos tendido externo</option>
                        <option value="PL_ EXT/NO VIABLE EN FO - EN INSTALACIÓN POR HFC">Pl_ ext/no viable en fo - en instalación por hfc</option>
                        <option value="PLANTA EXTERNA - ERROR EN LA EJECUCIÓN DE EOC">Planta externa - error en la ejecución de eoc</option>
                        <option value="PL_ EXT/INCUMPLIMIENTO FE DE UM/CANCELO/REPR ES">Pl_ ext/incumplimiento fe de um/cancelo/repr es</option>
                        <option value="PL_ EXT/EN CURSO SIN INCONVENIENTE REPORTADO">Pl_ ext/en curso sin inconveniente reportado</option>
                        <option value="PL_ EXT/ESCALADO_IFO_RESULTADO DE ACTIVIDAD">Pl_ ext/escalado_ifo_resultado de actividad</option>
                        <option value="PL_ EXT/ESCALADO_IFO_SOLICITUD DE DESBORDE">Pl_ ext/escalado_ifo_solicitud de desborde</option>
                        <option value="PL_ EXT/ESCALADO_IFO_SOLICITUD DE PERSONAL">Pl_ ext/escalado_ifo_solicitud de personal</option>
                        <option value="PLANTA EXTERNA - EN CURSO SOBRE OTP PYMES">Planta externa - en curso sobre otp pymes</option>
                        <option value="PLANTA EXTERNA - EN CURSO SOBRE OTP ASOCIADA">Planta externa - en curso sobre otp asociada</option>
                        <option value="TERCEROS/NO VIABLE/EN PROC CANCELACIÓN">Terceros/no viable/en proc cancelación</option>
                        <option value="TERCEROS/INCUMPLIMIENTO FECHA ENTREGA UM">Terceros/incumplimiento fecha entrega um</option>
                        <option value="TERCEROS/SIN AVANCE SOBRE LA FECHA ENTREGA UM">Terceros/sin avance sobre la fecha entrega um</option>
                        <option value="TERCEROS - EN CURSO SIN INCONVENIENTE REPORTADO">Terceros - en curso sin inconveniente reportado</option>
                        <option value="ALIADO/SIN INFORM ENTREGADA A TERC PARA INICIAR">Aliado/sin inform entregada a terc para iniciar</option>
                        <option value="PREVENTA - SIN ID  FACTIBILIDAD PARA TERCEROS">Preventa - sin id  factibilidad para terceros</option>
                        <option value="PREVENTA - NO ES CLARA LA SOLUCIÓN A IMPLEMENTAR">Preventa - no es clara la solución a implementar</option>
                        <option value="IMPLEMENTACIÓN - SOLUCIÓN NO ESTANDAR">Implementación - solución no estandar</option>
                        <option value="COMERCIAL - ESCALADO ORDEN DE REEMPLAZO">Comercial - escalado orden de reemplazo</option>
                        <option value="EQUIPOS - EN COMPRAS">Equipos - en compras</option>
                        <option value="EQUIPOS - DEFECTUOSOS">Equipos - defectuosos</option>
                        <option value="EQUIPOS - SIN CODIGO SAP PARA SOLICITUD DE EQUIPOS">Equipos - sin codigo sap para solicitud de equipos</option>
                        <option value="GPC/PENDIENTE INFOR DEL CLIENTE PARA CONFIGURAR">Gpc/pendiente infor del cliente para configurar</option>
                        <option value="GPC/PENDIENTE ACEPTACIÓN CRONOGRAMA POR CLIENTE">Gpc/pendiente aceptación cronograma por cliente</option>
                        <option value="GPC - CAMBIO DE ALCANCE ORDEN DE PEDIDO">Gpc - cambio de alcance orden de pedido</option>
                        <option value="GPC - EN PROCESO DE CANCELACIÓN">Gpc - en proceso de cancelación</option>
                        <option value="GPC/PENDIENTE ACEPTACIÓN CRONOGRAMA POR CLIENTE">Gpc/pendiente aceptación cronograma por cliente</option>
                        <option value="GPC - SIN ALCANCE PARA FABRICA">Gpc - sin alcance para fabrica</option>
                        <option value="LIDER TECNICO - PENDIENTE PLAN TECNICO">Lider tecnico - pendiente plan tecnico</option>
                        <option value="LIDER TECNICO - CAMBIO DE ALCANCE PLAN TECNICO">Lider tecnico - cambio de alcance plan tecnico</option>
                        <option value="LIDER TECNICO/SOLUCIÓN NO ESTANDAR SIN DEFINICIÓN">Lider tecnico/solución no estandar sin definición</option>
                        <option value="CONTROL DE CAMBIOS - RFC NO ESTANDAR EN APROBACIÓN">Control de cambios - rfc no estandar en aprobación</option>
                        <option value="COEX - EN PROCESO DE CONFIGURACIÓN BACKEND">Coex - en proceso de configuración backend</option>
                        <option value="COEX -ATRASO CONFIGURACIÓN BACKEND">Coex -atraso configuración backend</option>
                        <option value="ESCALADO/EN PROCESO PASO A PENDIENTE CLIENTE">Escalado/en proceso paso a pendiente cliente</option>
                        <option value="ENTREGA - SERVICIO_ENTREGADO_PROCESO DE CIERRE">Entrega - servicio_entregado_proceso de cierre</option>
                        <option value="ENTREGA/SIN DISPONIBILIDAD AGENDA">Entrega/sin disponibilidad agenda</option>
                        <option value="ENTREGA Y/O SOPORTE PROGRAMADO">Entrega y/o soporte programado</option>
                        <option value="PENDIENTE SOLICITAR ENTREGA DEL SERVICIO">Pendiente solicitar entrega del servicio</option>
                        <option value="DATACENTER CLARO- CABLEADO EN CURSO">Datacenter claro- cableado en curso</option>
                        <option value="DATACENTER  CLARO- CABLEADO SIN EJECUTAR">Datacenter  claro- cableado sin ejecutar</option>
                        <option value="DATACENTER  CLARO- SIN CONSUMIBLES EN DATACENTER">Datacenter  claro- sin consumibles en datacenter</option>
                        <option value="EN PROCESO DE PASO A ESTADO PENDIENTE CLIENTE">En proceso de paso a estado pendiente cliente</option>
                        <option value="EN PROCESO DE PASO A ESTADO CANCELADO ">En proceso de paso a estado cancelado </option>
                        <option value="INCONVENIENTE TECNICO">Inconveniente tecnico</option>
                        <option value="KO PENDIENTE">Ko pendiente</option>
                        <option value="EN CONFIGURACIÓN">En configuración</option>
                        <option value="GPC/CAMBIO DE ALCANCE ORDEN DE PEDIDO">Gpc/cambio de alcance orden de pedido</option>
                        <option value="GPC/EN PROCESO DE CANCELACIÓN">Gpc/en proceso de cancelación</option>
                        <option value="GPC/PENDIENTE INFORM DEL CLIENTE PARA CONFIGURAR">Gpc/pendiente inform del cliente para configurar</option>
                        <option value="GPC/SIN ALCANCE PARA FABRICA">Gpc/sin alcance para fabrica</option>
                        <option value="ESTADO CANCELADO">Estado cancelado</option>
                        <option value="ESTADO PENDIENTE CLIENTE">Estado pendiente cliente</option>
                    </optgroup>
                    <optgroup label="Códigos Antiguos">
                        <option value="EN PROCESOS CIERRE KO">En procesos cierre ko</option>
                        <option value="ALIADO - PENDIENTE SOLICITAR ENTREGA DEL SERVICIO">Aliado - pendiente solicitar entrega del servicio</option>
                        <option value="ALIADO - SIN INFORMACIÓN ENTREGADA A TERCEROS PARA INICIAR PROCESO">Aliado - sin información entregada a terceros para iniciar proceso</option>
                        <option value="ASIGNADO LIDER TECNICO">Asignado lider tecnico</option>
                        <option value="CLIENTE - CAMBIO DE ALCANCE (CAMBIO DE TIPO DE SERVICIO)">Cliente - cambio de alcance (cambio de tipo de servicio)</option>
                        <option value="CLIENTE - CAMBIO DE UBICACIÓN DE ULTIMA MILLA">Cliente - cambio de ubicación de ultima milla</option>
                        <option value="CLIENTE - NO APRUEBA COSTOS DE OBRA CIVIL">Cliente - no aprueba costos de obra civil</option>
                        <option value="CLIENTE - NO PERMITE CIERRE DE KO">Cliente - no permite cierre de ko</option>
                        <option value="CLIENTE - NO PERMITE PROGRAMAR ACTIVIDAD ETAPA FINAL DE ENTREGA DEL SERVICIO">Cliente - no permite programar actividad etapa final de entrega del servicio</option>
                        <option value="CLIENTE - NO PERMITE PROGRAMAR ACTIVIDAD ETAPA FINAL DE ENTREGA DEL SERVICIO - REQUIERE VENTANA">Cliente - no permite programar actividad etapa final de entrega del servicio - requiere ventana</option>
                        <option value="CLIENTE - NO PERMITE PROGRAMAR ACTIVIDAD ETAPA INICIAL SURVEY O VISITA O CON TERCERO">Cliente - no permite programar actividad etapa inicial survey o visita o con tercero</option>
                        <option value="CLIENTE - NO PERMITE PROGRAMAR ACTIVIDAD ETAPA INICIAL VOC">Cliente - no permite programar actividad etapa inicial voc</option>
                        <option value="CLIENTE - NO PERMITE PROGRAMAR ACTIVIDAD ETAPA INTERMEDIA  DE ULTIMA MILLA CON TERCERO ">Cliente - no permite programar actividad etapa intermedia  de ultima milla con tercero </option>
                        <option value="CLIENTE - NO PERMITE PROGRAMAR ACTIVIDAD ETAPA INTERMEDIA EMPALMES">Cliente - no permite programar actividad etapa intermedia empalmes</option>
                        <option value="CLIENTE - NO PERMITE PROGRAMAR ACTIVIDAD ETAPA INTERMEDIA EOC">Cliente - no permite programar actividad etapa intermedia eoc</option>
                        <option value="CLIENTE - NO TIENE DEFINIDA LA DIRECCIÓN DONDE VA A QUEDAR UBICADO EL SERVICIO">Cliente - no tiene definida la dirección donde va a quedar ubicado el servicio</option>
                        <option value="CLIENTE - PROGRAMADA POSTERIOR ">Cliente - programada posterior </option>
                        <option value="CLIENTE - SIN CONTRATO FIRMADO">Cliente - sin contrato firmado</option>
                        <option value="CLIENTE - SIN DISPONIBILIDAD DE INFRAESTRUCTURA (PLANTA TELEFONICA - LAN DIRECCIONAMIENTO )">Cliente - sin disponibilidad de infraestructura (planta telefonica - lan direccionamiento )</option>
                        <option value="CLIENTE - SIN FECHA ADECUACIONES EN LA SEDE (ELECTRICAS Y/O FISICA)">Cliente - sin fecha adecuaciones en la sede (electricas y/o fisica)</option>
                        <option value="CLIENTE - SIN FECHA PARA RECIBIR EL SERVICIO">Cliente - sin fecha para recibir el servicio</option>
                        <option value="COEX - EN PROCESO DE CONFIGURACIÓN BACKEND">Coex - en proceso de configuración backend</option>
                        <option value="COEX -ATRASO CONFIGURACIÓN BACKEND">Coex -atraso configuración backend</option>
                        <option value="COMERCIAL - ESCALADO ORDEN DE REEMPLAZO">Comercial - escalado orden de reemplazo</option>
                        <option value="COMERCIAL - ESCALADO PENDIENTE INGRESO OTS">Comercial - escalado pendiente ingreso ots</option>
                        <option value="CONTROL DE CAMBIOS - RFC NO ESTANDAR EN APROBACIÓN">Control de cambios - rfc no estandar en aprobación</option>
                        <option value="CSM - Retiro equipos - Renovación de Contrato">Csm - retiro equipos - renovación de contrato</option>
                        <option value="DATACENTER  CLARO- CABLEADO SIN EJECUTAR">Datacenter  claro- cableado sin ejecutar</option>
                        <option value="DATACENTER  CLARO- SIN CONSUMIBLES EN DATACENTER">Datacenter  claro- sin consumibles en datacenter</option>
                        <option value="DATACENTER CLARO- CABLEADO EN CURSO">Datacenter claro- cableado en curso</option>
                        <option value="ENTREGA - SERVICIO ENTREGADO PROCESO DE CIERRE">Entrega - servicio entregado proceso de cierre</option>
                        <option value="ENTREGA - SIN DISPONIBILIDAD AGENDA EN VERIFICACIÓN DE RECURSOS">Entrega - sin disponibilidad agenda en verificación de recursos</option>
                        <option value="ENTREGA Y/O SOPORTE PROGRAMADO">Entrega y/o soporte programado</option>
                        <option value="EQUIPOS - DEFECTUOSOS">Equipos - defectuosos</option>
                        <option value="EQUIPOS - EN COMPRAS">Equipos - en compras</option>
                        <option value="ESCALADO LIDER IMPLEMENTACIÓN PASO A PENDIENTE CLIENTE">Escalado lider implementación paso a pendiente cliente</option>
                        <option value="GPC - CAMBIO DE ALCANCE ORDEN DE PEDIDO">Gpc - cambio de alcance orden de pedido</option>
                        <option value="GPC - EN PROCESO DE CANCELACIÓN">Gpc - en proceso de cancelación</option>
                        <option value="GPC - PENDIENTE ACEPTACIÓN CRONOGRAMA POR PARTE DEL CLIENTE">Gpc - pendiente aceptación cronograma por parte del cliente</option>
                        <option value="GPC - PENDIENTE INFORMACIÓN DEL CLIENTE PARA CONFIGURAR">Gpc - pendiente información del cliente para configurar</option>
                        <option value="GPC - SIN ALCANCE PARA FABRICA">Gpc - sin alcance para fabrica</option>
                        <option value="IMPLEMENTACIÓN - SOLUCIÓN NO ESTANDAR">Implementación - solución no estandar</option>
                        <option value="INCONVENIENTE TECNICO">Inconveniente tecnico</option>
                        <option value="LIDER TECNICO - CAMBIO DE ALCANCE PLAN TECNICO">Lider tecnico - cambio de alcance plan tecnico</option>
                        <option value="LIDER TECNICO - PENDIENTE PLAN TECNICO">Lider tecnico - pendiente plan tecnico</option>
                        <option value="LIDER TECNICO - SOLUCIÓN NO ESTANDAR">Lider tecnico - solución no estandar</option>
                        <option value="LIDER TECNICO - SOLUCIÓN NO ESTANDAR SIN DEFINICIÓN">Lider tecnico - solución no estandar sin definición</option>
                        <option value="PASO A PENDIENTE CLIENTE">Paso a pendiente cliente</option>
                        <option value="PENDIENTE SOLICITAR ENTREGA DEL SERVICIO">Pendiente solicitar entrega del servicio</option>
                        <option value="PLANTA EXTERNA - EN CURSO SIN INCONVENIENTE REPORTADO">Planta externa - en curso sin inconveniente reportado</option>
                        <option value="PLANTA EXTERNA - ERROR EN LA EJECUCIÓN DE EOC">Planta externa - error en la ejecución de eoc</option>
                        <option value="PLANTA EXTERNA - ESCALADO IFO RESULTADO DE ACTIVIDAD">Planta externa - escalado ifo resultado de actividad</option>
                        <option value="PLANTA EXTERNA - ESCALADO IFO SOLICITUD DE DESBORDE">Planta externa - escalado ifo solicitud de desborde</option>
                        <option value="PLANTA EXTERNA - ESCALADO IFO SOLICITUD DE PERSONAL">Planta externa - escalado ifo solicitud de personal</option>
                        <option value="PLANTA EXTERNA - ETAPA INTERMEDIA - SIN CONFIRMACIÓN DE PERSONAL PARA EOC Y EMPALMES">Planta externa - etapa intermedia - sin confirmación de personal para eoc y empalmes</option>
                        <option value="PLANTA EXTERNA - INCUMPLIMIENTO EN LA FECHA DE ENTREGA DE ULTIMA MILLA - SE CANCELO O REPROGRAMO ENTREGA DE SERVICIO">Planta externa - incumplimiento en la fecha de entrega de ultima milla - se cancelo o reprogramo entrega de servicio</option>
                        <option value="PLANTA EXTERNA - NO VIABLE EN FACTIBILIDAD POR TERCEROS">Planta externa - no viable en factibilidad por terceros</option>
                        <option value="PLANTA EXTERNA - NO VIABLE EN FO - EN INSTALACIÓN POR HFC">Planta externa - no viable en fo - en instalación por hfc</option>
                        <option value="PLANTA EXTERNA - PERMISOS MUNICIPALES - PERMISOS DE ARRENDADOR DE INFRAESTRUCTURA">Planta externa - permisos municipales - permisos de arrendador de infraestructura</option>
                        <option value="PLANTA EXTERNA - SIN APROBACIÓN DE TENDIDO EXTERNO POR COSTOS">Planta externa - sin aprobación de tendido externo por costos</option>
                        <option value="PREVENTA - NO ES CLARA LA SOLUCIÓN A IMPLEMENTAR">Preventa - no es clara la solución a implementar</option>
                        <option value="PREVENTA - SIN ID  FACTIBILIDAD PARA TERCEROS">Preventa - sin id  factibilidad para terceros</option>
                        <option value="PROYECTO ÉXITO ANTIGUO">Proyecto éxito antiguo</option>
                        <option value="TERCEROS - EN CURSO SIN INCONVENIENTE REPORTADO">Terceros - en curso sin inconveniente reportado</option>
                        <option value="TERCEROS - INCUMPLIMIENTO EN LA FECHA DE ENTREGA DE ULTIMA MILLA - SE CANCELO O REPROGRAMO ENTREGA DE SERVICIO">Terceros - incumplimiento en la fecha de entrega de ultima milla - se cancelo o reprogramo entrega de servicio</option>
                        <option value="TERCEROS - NO VIABLE - EN PROCESO NOTIFICACIÓN A CLIENTE Y COMERCIAL PARA CANCELACIÓN">Terceros - no viable - en proceso notificación a cliente y comercial para cancelación</option>
                        <option value="TERCEROS - SIN AVANCE SOBRE LA FECHA DE ENTREGA DE ULTIMA MILL">Terceros - sin avance sobre la fecha de entrega de ultima mill</option>
                    </optgroup>
                </select>
            `;
            return select;
        },

    };
    gral.init();

//table_otPadreList
    vista = {
        init: function () {
            vista.events();
            vista.getListOtsOtPadre('all');

        },
        //Eventos de la ventana.
        events: function () {
            // $("#filterGroupIng").on('change',vista.getListOtsOtPadre);
        },
        getListOtsOtPadre: function (filtro) {
            //metodo ajax (post)
            (role_session === 'administrador') ? helper.showLoading('Filtrando...') : helper.showLoading();
            $.post(baseurl + '/OtPadre/c_getListOtsOtPadre',
                    {
                        filter: filtro,
                    },
                    // funcion que recibe los datos
                            function (data) {
                                // convertir el json a objeto de javascript
                                var obj = JSON.parse(data);
                                helper.hideLoading('.8');
                                vista.printTable(obj.data);
                                $('#badge_cant_total_OTP').html(obj.cantOTPs);
                            }
                    );
                },
        printTable: function (data) {
            if (vista.table_otPadreList) {
                var tabla = vista.table_otPadreList;
                tabla.clear().draw();
                tabla.rows.add(data);
                tabla.columns.adjust().draw();
                return;
            }


            // nombramos la variable para la tabla y llamamos la configuiracion
            vista.table_otPadreList = $('#table_otPadreList').DataTable(vista.configTable(data, [

                {title: "Ot Padre", data: "k_id_ot_padre"},
                {title: "Nombre Cliente", data: "n_nombre_cliente"},
                {title: "Tipo", data: "orden_trabajo"},
                {title: "Servicio", data: "servicio"},
                {title: "Estado OT Padre", data: "estado_orden_trabajo"},
                {title: "Fecha Programación", data: "fecha_programacion"},
                {title: "Fecha Compromiso", data: "fecha_compromiso"},
                {title: "Fecha Creación", data: "fecha_creacion"},
                {title: "Ingeniero", data: "ingeniero"},
                {title: "Lista", data: "lista_observaciones"},
                {title: "Observaciónes dejadas", data: gral.inputObservaciones},
                {title: "Recurrente", data: "MRC", visible: false},
                {title: "ultimo envio", data: gral.cant_dias_ultimo_reporte, visible: false},
                {title: "No. OTHs", data: vista.getColourPerOTHs},
                {title: "Opc", data: vista.getButtonsOTP/**/},
            ]));

        },

        getColourPerOTHs: function (data) {
            let num = '';
            if (data.cant_oths != 0) {
                num += `<span class="styleNum" title="${data.cant_oths} OTHs Asociadas" >${data.cant_oths}</span>`;
            } else {
                num += `<span class="styleNum noOTHs" title="sin OTHs">${data.cant_oths}</span>`;
            }

            return num;
        },
        // Datos de configuracion del datatable
        configTable: function (data, columns, onDraw) {
            return {
                initComplete: function () {

                    // $('.cod_resolucion').selectize();

                    $('#table_otPadreList tfoot th').each(function () {
                        $(this).html('<input type="text" placeholder="Buscar" />');
                    });
                    var r = $('#table_otPadreList tfoot tr');
                    r.find('th').each(function () {
                        $(this).css('padding', 8);
                    });
                    $('#table_otPadreList thead').append(r);
                    $('#search_0').css('text-align', 'center');

                    // DataTable
                    var table = $('#table_otPadreList').DataTable();

                    // Apply the search
                    table.columns().every(function () {
                        var that = this;

                        $('input', this.footer()).on('keyup change', function () {
                            if (that.search() !== this.value) {
                                that.search(this.value).draw();
                            }
                        });
                    });

                    columna9 = table.column(9);
                    columna9.visible(!columna9.visible());
                    columna10 = table.column(10);
                    columna10.visible(!columna10.visible());
                    $("#table_otPadreList").css("text-align", "center");
                },

                // Este callback se ejecuta cada vex que hay cambio de pagina, ordenamiento, o cambio en cantidad de registros a mostrar
                // o un cambio especifico en la pagina
                fnInfoCallback: function (oSettings, iStart, iEnd, iMax, iTotal, sPre) {


                },

                data: data,
                columns: columns,
                "language": {
                    "url": baseurl + "/assets/plugins/datatables/lang/es.json"
                },
                dom: 'Blfrtip',
                buttons: [
                    {
                        text: 'Excel <span class="fa fa-file-excel-o"></span>',
                        className: 'btn-cami_cool',
                        extend: 'excel',
                        title: 'ZOLID EXCEL',
                        filename: 'zolid ' + fecha_actual
                    },
                    {
                        text: 'Imprimir <span class="fa fa-print"></span>',
                        className: 'btn-cami_cool',
                        extend: 'print',
                        title: 'Reporte Zolid',
                    },
                    {
                        text: '<span class="fa fa-envelope-o" aria-hidden="true"></span> Reporte Actualización',
                        className: 'btn-cami_cool btn-rpt_act',
                        action: eventos.otp_seleccionadas,
                    }
                ],
                select: true,
                "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
                ordering: true,
                columnDefs: [{
                        // targets: -1,
                        // visible: false,
                        defaultContent: "",
                        // targets: -1,
                        orderable: false,
                    }],
                order: [[7, 'desc']],
                drawCallback: onDraw,
                "createdRow": function (row, data, dataIndex) {
                    if (data["cant_oths"] == 0) {
                        $(row).css("background-color", "#f50e0e69");
                    }
                },
            }
        },

        // observacion con funcion de mostrar mas
        getObservacionTotal: function (obj) {

            if (typeof obj.observacion == 'string') {
                var array_cadena = obj.observacion.split(" ");
                var cadena = "";
                if (array_cadena.length > 10) {

                    for (var i = 0; i < 10; i++) {
                        cadena += array_cadena[i] + " ";
                    }

                    return `<div class="tooltipo">${cadena} <img class="rigth" style="width:15px; margin-left:96%;" src="${baseurl}/assets/images/plus.png">
                              <span class="tooltiptext">${obj.observacion}</span>
                            </div>
                            `;
                }
            }
            return obj.observacion;
        },

        getButtonsOTP/**/: function (obj) {
            var span = '';
            var title = '';
            var cierreKo = '';
            var icon = '';
            var reportInicio = ''; //si tiene reporte de inicio y tiene emails enviados

            //si existe una OTP con contador de reportes enviados, aparecerá, de lo contrario, pondrá el icono del ojo
            if (obj.MAIL_enviados) {
                if (obj.MAIL_enviados != 0) {
                    reportInicio = (obj.cant_mails != 0) ? "<span class='fa fa-fw '>| &nbsp" + obj.cant_mails + "</span> <span style='color: #7eec7c;' class='fa fa-check-circle'  aria-hidden='true'></span>" : '';
                    span = "<span class='fa fa-fw'>" + obj.MAIL_enviados + "</span>";
                    icon = "<span class='fa fa-envelope' aria-hidden='true' style='color: #fff700;'></span>"
                    title = (obj.MAIL_enviados == 1) ? obj.MAIL_enviados + " correo enviado" : obj.MAIL_enviados + " correos enviados";
                } else if (obj.cant_mails != 0) {
                    span = "<span class='fa fa-fw '>" + obj.cant_mails + "</span>";
                    reportInicio = "<span style='color: #7eec7c;' class='fa fa-check-circle' aria-hidden='true'></span>";
                    title = (obj.cant_mails == 1) ? obj.cant_mails + " correo enviado" : obj.cant_mails + " correos enviados";
                } else {
                    span = "<span class='fa fa-fw fa-eye'></span>";
                    title = "ver OT Hijas";
                }
            } else {
                //SI no es reporte de act. entra acá, pero si lo es, entrará arriba
                if (obj.cant_mails != 0) {
                    span = "<span class='fa fa-fw '>" + obj.cant_mails + "</span>";
                    title = (obj.cant_mails == 1) ? obj.cant_mails + " correo enviado" : obj.cant_mails + " correos enviados";
                } else {
                    span = "<span class='fa fa-fw fa-eye'></span>";
                    title = "ver OT Hijas";
                }
            }
            if (obj.finalizo != null) {
                cierreKo = "<a class='btn btn-default btn-xs product-otp btn_datatable_cami' data-btn='cierreKo' title='Ver Detalle Cierre KO'><span class='fa fa-fw fa-info-circle'></span></a>";
            }

            const color = (obj.id_hitos) ? 'clr_lime' : '';
            var botones = "<div class='btn-group-vertical' style=''>"
                    + "<a class='btn btn-default btn-xs btnoths btn_datatable_cami' title='" + title + "'>" + icon + span + reportInicio + "</a>"
                    // + "<a class='btn btn-default btn-xs edit-otp btn_datatable_cami' title='Editar Ots'><span class='glyphicon glyphicon-save'></span></a>"
                    + "<a class='btn btn-default btn-xs hitos-otp btn_datatable_cami' data-btn='hito' title='Hitos Ots'><span class='glyphicon glyphicon-header " + color + "'></span></a>"
                    + cierreKo
                    + "</div>";
            return botones;
        }
    };
    vista.init();

    /**********************TABLA OT PADRES CON FECHA COMPROMISO EN HOY**************************/
    hoy = {
        init: function () {
            hoy.events();
            hoy.getListOtsOtPadreHoy('all');
        },
        //Eventos de la ventana.
        events: function () {

        },
        getListOtsOtPadreHoy: function (filtro) {
            //metodo ajax (post)
            $.post(baseurl + '/OtPadre/c_getListOtsOtPadreHoy',
                    {
                        //parametros
                        filter: filtro,
                    },
                    // funcion que recibe los datos
                            function (data) {
                                // convertir el json a objeto de javascript
                                var obj = JSON.parse(data);
                                hoy.printTable(obj);
                            }
                    );
                },
        printTable: function (data) {
            if (hoy.table_otPadreListHoy) {
                var tabla = hoy.table_otPadreListHoy;
                tabla.clear().draw();
                tabla.rows.add(data);
                tabla.columns.adjust().draw();
                return;
            }
            // nombramos la variable para la tabla y llamamos la configuiracion
            hoy.table_otPadreListHoy = $('#table_otPadreListHoy').DataTable(hoy.configTable(data, [

                {title: "Ot Padre", data: "k_id_ot_padre"},
                {title: "Nombre Cliente", data: "n_nombre_cliente"},
                {title: "Tipo", data: "orden_trabajo"},
                {title: "Servicio", data: "servicio"},
                {title: "Estado OT Padre", data: "estado_orden_trabajo"},
                {title: "Fecha Programación", data: "fecha_programacion"},
                {title: "Fecha Compromiso", data: "fecha_compromiso"},
                {title: "Fecha Creación", data: "fecha_creacion"},
                {title: "Ingeniero", data: "ingeniero"},
                {title: "Lista", data: "lista_observaciones"},
                {title: "Observaciónes dejadas", data: gral.inputObservaciones},
                {title: "Recurrente", data: "MRC", visible: false},
                {title: "ultimo envio", data: gral.cant_dias_ultimo_reporte, visible: false},
                {title: "Opciones", data: vista.getButtonsOTP},
            ]));
        },
        // Datos de configuracion del datatable
        configTable: function (data, columns, onDraw) {
            return {
                initComplete: function () {
                    $('#table_otPadreListHoy tfoot th').each(function () {
                        $(this).html('<input type="text" placeholder="Buscar" />');
                    });
                    var r = $('#table_otPadreListHoy tfoot tr');
                    r.find('th').each(function () {
                        $(this).css('padding', 8);
                    });
                    $('#table_otPadreListHoy thead').append(r);
                    $('#search_0').css('text-align', 'center');

                    // DataTable
                    var table = $('#table_otPadreListHoy').DataTable();

                    // Apply the search
                    table.columns().every(function () {
                        var that = this;

                        $('input', this.footer()).on('keyup change', function () {
                            if (that.search() !== this.value) {
                                that.search(this.value).draw();
                            }
                        });
                    });

                    columna9 = table.column(9);
                    columna9.visible(!columna9.visible());
                    columna10 = table.column(10);
                    columna10.visible(!columna10.visible());
                    $("#table_otPadreListHoy").css("text-align", "center");
                },
                // Este callback se ejecuta cada vex que hay cambio de pagina, ordenamiento, o cambio en cantidad de registros a mostrar
                // o un cambio especifico en la pagina
                fnInfoCallback: function (oSettings, iStart, iEnd, iMax, iTotal, sPre) {
                    // $('#table_otPadreListHoy .cod_resolucion').selectize();
                },
                data: data,
                columns: columns,
                "language": {
                    "url": baseurl + "/assets/plugins/datatables/lang/es.json"
                },
                dom: 'Blfrtip',
                buttons: [
                    {
                        text: 'Excel <span class="fa fa-file-excel-o"></span>',
                        className: 'btn-cami_cool',
                        extend: 'excel',
                        title: 'ZOLID EXCEL',
                        exportOptions: {
                            modifier: {
                                page: 'current'
                            }
                        },
                        filename: 'zolid ' + fecha_actual
                    },
                    {
                        text: 'Imprimir <span class="fa fa-print"></span>',
                        className: 'btn-cami_cool',
                        extend: 'print',
                        title: 'Reporte Zolid',
                    },
                    {
                        text: '<span class="fa fa-envelope-o" aria-hidden="true"></span> Reporte Actualización',
                        className: 'btn-cami_cool btn-rpt_act',
                        action: eventos.otp_seleccionadas,
                    }
                ],
                select: true,
                "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
                ordering: true,
                columnDefs: [{
                        // targets: -1,
                        // visible: false,
                        defaultContent: "",
                        // targets: -1,
                        orderable: false,
                    }],
                order: [[7, 'desc']],
                drawCallback: onDraw
            }
        }
    };
    hoy.init();

    /**********************TABLA OT PADRES CON FECHA COMPROMISO VENCIDA**************************/
    vencidas = {
        init: function () {
            vencidas.events();
            vencidas.getListOtsOtPadreVencidas('all');
        },
        //Eventos de la ventana.
        events: function () {

        },
        getListOtsOtPadreVencidas: function (fil) {
            //metodo ajax (post)
            $.post(baseurl + '/OtPadre/c_getListOtsOtPadreVencidas',
                    {
                        //parametros
                        filter: fil
                    },
                    // funcion que recibe los datos
                            function (data) {
                                // convertir el json a objeto de javascript
                                var obj = JSON.parse(data);
                                vencidas.printTable(obj);
                            }
                    );
                },
        printTable: function (data) {
            if (vencidas.table_otPadreListVencidas) {
                var tabla = vencidas.table_otPadreListVencidas;
                tabla.clear().draw();
                tabla.rows.add(data);
                tabla.columns.adjust().draw();
                return;
            }
            // nombramos la variable para la tabla y llamamos la configuiracion
            vencidas.table_otPadreListVencidas = $('#table_otPadreListVencidas').DataTable(vencidas.configTable(data, [

                {title: "Ot Padre", data: "k_id_ot_padre"},
                {title: "Nombre Cliente", data: "n_nombre_cliente"},
                {title: "Tipo", data: "orden_trabajo"},
                {title: "Servicio", data: "servicio"},
                {title: "Estado OT Padre", data: "estado_orden_trabajo"},
                {title: "Fecha Programación", data: "fecha_programacion"},
                {title: "Fecha Compromiso", data: "fecha_compromiso"},
                {title: "Fecha Creación", data: "fecha_creacion"},
                {title: "Ingeniero", data: "ingeniero"},
                {title: "Lista", data: "lista_observaciones"},
                {title: "Observaciónes dejadas", data: gral.inputObservaciones},
                {title: "Recurrente", data: "MRC", visible: false},
                {title: "ultimo envio", data: gral.cant_dias_ultimo_reporte, visible: false},
                {title: "Opciones", data: vista.getButtonsOTP},
            ]));
        },
        // Datos de configuracion del datatable
        configTable: function (data, columns, onDraw) {
            return {
                initComplete: function () {
                    $('#table_otPadreListVencidas tfoot th').each(function () {
                        $(this).html('<input type="text" placeholder="Buscar" />');
                    });
                    var r = $('#table_otPadreListVencidas tfoot tr');
                    r.find('th').each(function () {
                        $(this).css('padding', 8);
                    });
                    $('#table_otPadreListVencidas thead').append(r);
                    $('#search_0').css('text-align', 'center');

                    // DataTable
                    var table = $('#table_otPadreListVencidas').DataTable();

                    // Apply the search
                    table.columns().every(function () {
                        var that = this;

                        $('input', this.footer()).on('keyup change', function () {
                            if (that.search() !== this.value) {
                                that.search(this.value).draw();
                            }
                        });
                    });

                    columna9 = table.column(9);
                    columna9.visible(!columna9.visible());
                    columna10 = table.column(10);
                    columna10.visible(!columna10.visible());
                    $("#table_otPadreListVencidas").css("text-align", "center");
                },
                // Este callback se ejecuta cada vex que hay cambio de pagina, ordenamiento, o cambio en cantidad de registros a mostrar
                // o un cambio especifico en la pagina
                fnInfoCallback: function (oSettings, iStart, iEnd, iMax, iTotal, sPre) {
                    // $('#table_otPadreListVencidas .cod_resolucion').selectize();
                },
                data: data,
                columns: columns,
                "language": {
                    "url": baseurl + "/assets/plugins/datatables/lang/es.json"
                },
                dom: 'Blfrtip',
                buttons: [
                    {
                        text: 'Excel <span class="fa fa-file-excel-o"></span>',
                        className: 'btn-cami_cool',
                        extend: 'excel',
                        title: 'ZOLID EXCEL',
                        filename: 'zolid ' + fecha_actual
                    },
                    {
                        text: 'Imprimir <span class="fa fa-print"></span>',
                        className: 'btn-cami_cool',
                        extend: 'print',
                        title: 'Reporte Zolid',
                    },
                    {
                        text: '<span class="fa fa-envelope-o" aria-hidden="true"></span> modalHitosOtp',
                        className: 'btn-cami_cool btn-rpt_act',
                        action: eventos.otp_seleccionadas,
                    }
                ],
                select: true,
                "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
                ordering: true,
                columnDefs: [{
                        // targets: -1,
                        // visible: false,
                        defaultContent: "",
                        // targets: -1,
                        orderable: false,
                    }],
                order: [[7, 'desc']],
                drawCallback: onDraw
            }
        }
    };
    vencidas.init();

    /**********************TABLA OT PADRES PARA FILTRO POR OPC DE LISTA**************************/
    lista = {
        init: function () {
            lista.events();
            lista.getOtpByOpcListJs(null, 'all');
        },
        //Eventos de la ventana.
        events: function () {
            $('#select_filter').change(lista.cambio_opc);
        },
        getOtpByOpcListJs: function (value = null, filter) {
            //metodo ajax (post)
            var opcion = (value) ? value : "CLIENTE - SIN FECHA PARA RECIBIR EL SERVICIO";
            $.post(baseurl + '/OtPadre/c_getOtpByOpcList',
                    {
                        opcion: opcion,
                        filter: filter,
                    },
                    // funcion que recibe los datos
                            function (data) {
                                // convertir el json a objeto de javascript
                                var obj = JSON.parse(data);
                                lista.printTable(obj);
                            }
                    );
                },

        printTable: function (data) {
            if (lista.tableOpcList) {
                var tabla = lista.tableOpcList;
                tabla.clear().draw();
                tabla.rows.add(data);
                tabla.columns.adjust().draw();
                return;
            }
            // nombramos la variable para la tabla y llamamos la configuiracion
            lista.tableOpcList = $('#table_list_opc').DataTable(lista.configTable(data, [

                {title: "Ot Padre", data: "k_id_ot_padre"},
                {title: "Nombre Cliente", data: "n_nombre_cliente"},
                {title: "Tipo", data: "orden_trabajo"},
                {title: "Servicio", data: "servicio"},
                {title: "Estado OT Padre", data: "estado_orden_trabajo"},
                {title: "Fecha Programación", data: "fecha_programacion"},
                {title: "Fecha Compromiso", data: "fecha_compromiso"},
                {title: "Fecha Creación", data: "fecha_creacion"},
                {title: "Ingeniero", data: "ingeniero"},
                {title: "Lista", data: "lista_observaciones"},
                {title: "Observaciónes dejadas", data: gral.inputObservaciones},
                {title: "Recurrente", data: "MRC", visible: false},
                {title: "ultimo envio", data: gral.cant_dias_ultimo_reporte, visible: false},
                {title: "Opciones", data: vista.getButtonsOTP},
            ]));
        },
        // Datos de configuracion del datatable
        configTable: function (data, columns, onDraw) {
            return {
                initComplete: function () {
                    $('#table_list_opc tfoot th').each(function () {
                        $(this).html('<input type="text" placeholder="Buscar" />');
                    });
                    var r = $('#table_list_opc tfoot tr');
                    r.find('th').each(function () {
                        $(this).css('padding', 8);
                    });
                    $('#table_list_opc thead').append(r);
                    $('#search_0').css('text-align', 'center');

                    // DataTable
                    var table = $('#table_list_opc').DataTable();

                    // Apply the search
                    table.columns().every(function () {
                        var that = this;

                        $('input', this.footer()).on('keyup change', function () {
                            if (that.search() !== this.value) {
                                that.search(this.value).draw();
                            }
                        });
                    });

                    columna9 = table.column(9);
                    columna9.visible(!columna9.visible());
                    columna10 = table.column(10);
                    columna10.visible(!columna10.visible());
                    $("#table_list_opc").css("text-align", "center");
                },
                // Este callback se ejecuta cada vex que hay cambio de pagina, ordenamiento, o cambio en cantidad de registros a mostrar
                // o un cambio especifico en la pagina
                fnInfoCallback: function (oSettings, iStart, iEnd, iMax, iTotal, sPre) {
                    // $('#table_list_opc .cod_resolucion').selectize();
                },
                data: data,
                columns: columns,
                "language": {
                    "url": baseurl + "/assets/plugins/datatables/lang/es.json"
                },
                dom: 'Blfrtip',
                buttons: [
                    {
                        text: 'Excel <span class="fa fa-file-excel-o"></span>',
                        className: 'btn-cami_cool',
                        extend: 'excel',
                        title: 'ZOLID EXCEL',
                        filename: 'zolid ' + fecha_actual
                    },
                    {
                        text: 'Imprimir <span class="fa fa-print"></span>',
                        className: 'btn-cami_cool',
                        extend: 'print',
                        title: 'Reporte Zolid',
                    },
                    {
                        text: '<span class="fa fa-envelope-o" aria-hidden="true"></span> Reporte Actualización',
                        className: 'btn-cami_cool btn-rpt_act',
                        action: eventos.otp_seleccionadas,
                    }
                ],
                select: true,
                "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
                ordering: true,
                columnDefs: [{
                        // targets: -1,
                        // visible: false,
                        defaultContent: "",
                        // targets: -1,
                        orderable: false,
                    }],
                order: [[7, 'desc']],
                drawCallback: onDraw
            }
        },

        // cuando se cambia de opcion
        cambio_opc: function () {
            var opcion = $('#select_filter').val();
            lista.getOtpByOpcListJs(opcion, $("#filterGroupIng").val());
        },
    };
    lista.init();

    // *******************************************EVENTOS ***************************
    eventos = {
        init: function () {
            eventos.events();
        },

        //Eventos de la ventana.
        events: function () {
            $('#contenido_tablas').on('click', 'a.product-otp', eventos.onClickBtnCloseOtp);
            $('#contenido_tablas').on('click', 'a.edit-otp', eventos.onClickBtnEditOtp);
            // $('#table_oths_otp').on('click', 'a.ver-log', eventos.onClickShowEmailOth); //fue remplazado por el botón general
            $('#formModalOTHS').on('click', 'div.ver-log-general', eventos.showEmailOthGeneral);
            $('#ModalHistorialLog').on('click', 'button.ver-mail', eventos.onClickVerLogMailOTP);// ver detalles de correo btn impresora
            // $('#table_oths_otp').on('click', 'a.ver-det', formulario.onClickShowModalEdit);
            // correccion scroll modal sobre modal
            $('#Modal_detalle').on("hidden.bs.modal", eventos.modal_sobre_modal);
            $('#ModalHistorialLog').on("hidden.bs.modal", eventos.modal_sobre_modal);
            $('#ModalHistorialLog').on("hidden.bs.modal", eventos.limpiarLogs);
            $('#contenido_tablas').on('click', 'a.hitos-otp', eventos.onClickBtnCloseOtp);
            $('#btnGuardarModalHitos').on('click', eventos.onClickSaveHitosOtp);// ver detalles de correo btn impresora
            $('#table_selected').on('click', 'img.quitar_fila', eventos.quitarFila);
            $('#mdl-enviar-reporte').on('click', eventos.onClickSendReportUpdate);
            $('#mdl_cierre').on("hidden.bs.modal", eventos.cleanFormReportUpdate);
//            $('#modalHitosOtp').on('hidden.bs.modal', eventos.resetHitosModal)

            // ***********************Inicio del evento del menu sticky******************
            $('.contenedor_sticky').on('click', function () {
                $(this).hide();
                $('.contenedor_menu_sticky').show(300);
            });
            $('.btn_cerrar_sticky').on('click', function () {
                $('.contenedor_menu_sticky').hide(300);
                $('.contenedor_sticky').show(300);
            });

            // dar mostrar o ocultar la columna en la sesion work managment por medio del menu stick
            $('.toggle-vis').click(eventos.showHideTable);

            // Fin del evento del menu sticky

            // ***************************************Fin del evento del menu sticky***************************************

            $("#filterGroupIng").on('change', eventos.printNewTablesAccordingIngGroup);
        },

        // vuelve a pintar todas las tablas según el grupo de ingenieros se tenga filtrado
        printNewTablesAccordingIngGroup: function () {
            // $('#filterGroupIng').attr('disabled', true);
            var filtro = $("#filterGroupIng").val();
            vista.getListOtsOtPadre(filtro);
            hoy.getListOtsOtPadreHoy(filtro);
            vencidas.getListOtsOtPadreVencidas(filtro);
            lista.getOtpByOpcListJs($('#select_filter').val(), filtro);
            reporte_act.getOtsPtesPorEnvio(filtro);
            reporte_act.getCountPtesPorEnvio(filtro);
            // $('#filterGroupIng').attr('disabled', false);
        },

        // dar mostrar o ocultar la columna en la sesion work managment por medio del menu stick segun el id de la tabla
        showHideTable: function () {
            let icono = $(this).children('i');
            if ($(this).hasClass('inactive')) {
                $(this).removeClass('inactive');
                icono.removeClass('glyphicon-eye-close');
                icono.addClass('glyphicon-eye-open');
            } else {
                $(this).addClass('inactive');
                icono.removeClass('glyphicon-eye-open');
                icono.addClass('glyphicon-eye-close');
            }
            const tablas = [vista.table_otPadreList, hoy.table_otPadreListHoy, vencidas.table_otPadreListVencidas, lista.tableOpcList];
            let number_column = $(this).data('column');
            let columna;
            for (var i = 0; i < tablas.length; i++) {
                columna = tablas[i].column(number_column);
                columna.visible(!columna.visible());
            }
        },

        // funcion para correcion modal sobre modal
        modal_sobre_modal: function (event) {
            if ($('.modal:visible').length) {
                $('body').addClass('modal-open');
            }
        },

        onClickBtnCloseOtp: function (e) {
            var aLinkLog = $(this);
            var trParent = aLinkLog.parents('tr');
            var tabla = aLinkLog.parents('table').attr('id');
            var record;

            switch (tabla) {
                case 'table_otPadreList':
                    record = vista.table_otPadreList.row(trParent).data();
                    break;
                case 'table_otPadreListHoy':
                    record = hoy.table_otPadreListHoy.row(trParent).data();
                    break;
                case 'table_otPadreListVencidas':
                    record = vencidas.table_otPadreListVencidas.row(trParent).data();
                    break;
                case 'table_list_opc':
                    record = lista.tableOpcList.row(trParent).data();
                    break;
                case 'table_otPadreListEmails':
                    record = emails.table_otPadreListEmails.row(trParent).data();
                    break;
                case 'table_reporte_actualizacion':
                    record = reporte_act.table_reporte_actualizacion.row(trParent).data();
                    break;
            }

            var btn_clas = e.currentTarget;



            switch (btn_clas.dataset.btn) {
                case 'cierreKo':
                    eventos.showDetailsCierreKo(record);
                    break;

                case 'hito':
                    eventos.showModalHitosOthp(record, aLinkLog.children());
                    break;
            }

        },

        showDetailsCierreKo: function (data) {
            var s = data.finalizo;
            var flag = false;
            var form = setForm.returnFormularyProduct(s);
            if (s == 3 || s == 4 || s == 5 || s == 6 || s == 7 || s == 8 || s == 9 || s == 10) {
                form += setForm.formProduct_mpls_form_origen();
                flag = true;
            }
            $("#form_cierreKo").html(form);
            $('.max-w_border-n').remove();

            $.post(baseurl + '/OtPadre/c_getProductByOtp',
                    {
                        id_otp: data.k_id_ot_padre,
                        num_servicio: data.finalizo
                    },
                    function (data) {
                        var obj = JSON.parse(data);
                        $.each(obj, function (i, item) {

                            var $el = $('#pr_' + i);
                            $el.replaceWith($('<input />').attr({
                                type: 'text',
                                id: $el.attr('id'),
                                name: $el.attr('name'),
                                class: $el.attr('class'),
                                value: $el.val(),
                                readonly: true,
                                style: 'font-size: 12px;'
                            }));
                            $('#pr_' + i).val(item);
                        });

                        if (flag && obj.ciudad_ori == null) {
                            $('#seccion_mpls_ori').remove();
                        }

                        $("#mdl_cierreKo #id_ot_padre").val(obj.id_ot_padre);
                        $("#mdl_cierreKo #id_ot_padre_ori").val(obj.id_ot_padre);
                        $("#mdl_cierreKo #id_ot_padre_des").val(obj.id_ot_padre);
                        $("#mdl_cierreKo").css("font-size", "12px");
                        $("#mdl_cierreKo label").css("width", "150px");
                        $("#mdl_cierreKo .selectContainer").css("margin-bottom", "5px");
                    });

            $('#mdl_cierreKo').modal('show');
        },
        onClickBtnEditOtp: function () {
            var btn_obs = $(this);
            var tr = btn_obs.parents('tr');
            var id_otp = tr.find('td').eq(0).html();


            swal.mixin({
                input: 'text',
                confirmButtonText: 'Siguiente &rarr;',
                showCancelButton: true,
                progressSteps: ['1', '2'],
                //option group
                onOpen: function () {
                    var lista = $('.select-sweet option');
                    console.log(lista[1]);
                    lista[1].setAttribute("disabled", true);
                    lista[1].style.background = "#3085d6";
                    lista[1].style.color = "white";
                    lista[74].setAttribute("disabled", true);
                    lista[74].style.background = "#3085d6";
                    lista[74].style.color = "white";
                    $.each(lista, function (i, option) {
                        if (i < 74 && i > 1) {
                            option.style.background = "#add8e6";
                            option.style.color = "black";
                        }
                        if (i >= 75) {
                            option.style.background = "#db8181bd";
                            option.style.color = "black";
                        }
                    });


                },
            }).queue([
                {
                    title: 'Lista',
                    text: 'Seleccione una opcion de la lista',
                    input: 'select',
                    inputClass: 'select-sweet f-s-12',
                    inputOptions: {
                        'nuevos': '**CODIGOS NUEVOS**',
                        'CLIENTE - SIN FECHA PARA RECIBIR EL SERVICIO': 'CLIENTE - SIN FECHA PARA RECIBIR EL SERVICIO',
                        'CLIENTE/SIN FECHA ADECUACIONES EN SEDE (ELEC/FIS)': 'CLIENTE/SIN FECHA ADECUACIONES EN SEDE (ELEC/FIS)',
                        'CLIENTE/SIN DISPONIBILIDAD INFRA (PTA TELEF/LAN)': 'CLIENTE/SIN DISPONIBILIDAD INFRA (PTA TELEF/LAN)',
                        'CLIENTE/CAMBIO DE ALCANCE (CBIO  TIPO SERVICIO)': 'CLIENTE/CAMBIO DE ALCANCE (CBIO  TIPO SERVICIO)',
                        'CLIENTE/CAMBIO DE UBICACIÓN DE ULTIMA MILLA': 'CLIENTE/CAMBIO DE UBICACIÓN DE ULTIMA MILLA',
                        'CLIENTE/NO APRUEBA COSTOS DE OBRA CIVIL': 'CLIENTE/NO APRUEBA COSTOS DE OBRA CIVIL',
                        'CLIENTE/NO PERMITE CIERRE DE KO': 'CLIENTE/NO PERMITE CIERRE DE KO',
                        'CLIENTE/SIN DEFINICIÓN DIR DE UBICACIÓN SERVICIO': 'CLIENTE/SIN DEFINICIÓN DIR DE UBICACIÓN SERVICIO',
                        'CLIENTE/NO PERMITE PROG ACT ETAPA INICIAL VOC': 'CLIENTE/NO PERMITE PROG ACT ETAPA INICIAL VOC',
                        'CLIENTE/NO PERMITE PROG ACT ETAPA INTERMEDIA EOC': 'CLIENTE/NO PERMITE PROG ACT ETAPA INTERMEDIA EOC',
                        'CLIENTE/NO PERMITE PROG ACT ETAPA INTERMEDIA EMP': 'CLIENTE/NO PERMITE PROG ACT ETAPA INTERMEDIA EMP',
                        'CLIENTE/NO PERMITE PROG ACT  VOC TERCERO': 'CLIENTE/NO PERMITE PROG ACT  VOC TERCERO',
                        'CLIENTE/NO PERMITE PROG ACT ETAP INTERMEDIA UM TER': 'CLIENTE/NO PERMITE PROG ACT ETAP INTERMEDIA UM TER',
                        'CLIENTE/NO PERMITE PROG ACT ETAPA FINAL ES': 'CLIENTE/NO PERMITE PROG ACT ETAPA FINAL ES',
                        'CLIENTE/NO PERMITE PROG ACT ETAPA FINAL ES REQ VM': 'CLIENTE/NO PERMITE PROG ACT ETAPA FINAL ES REQ VM',
                        'CLIENTE/SIN CONTRATO FIRMADO': 'CLIENTE/SIN CONTRATO FIRMADO',
                        'CLIENTE/PROGRAMADA_PROXIMO PERIODO': 'CLIENTE/PROGRAMADA_PROXIMO PERIODO',
                        'PL_ EXT/PERMISO MUNI - PERMISO ARREND INFRAESTRUC': 'PL_ EXT/PERMISO MUNI - PERMISO ARREND INFRAESTRUC',
                        'PL_ EXT/NO VIABLE EN FACTIBILIDAD POR TERCEROS': 'PL_ EXT/NO VIABLE EN FACTIBILIDAD POR TERCEROS',
                        'PL_ EXT/ETAPA INTERMEDIA/SIN PERSONAL  EOC/EMP': 'PL_ EXT/ETAPA INTERMEDIA/SIN PERSONAL  EOC/EMP',
                        'PL_ EXT/SIN APROBACIÓN COSTOS TENDIDO EXTERNO': 'PL_ EXT/SIN APROBACIÓN COSTOS TENDIDO EXTERNO',
                        'PL_ EXT/NO VIABLE EN FO - EN INSTALACIÓN POR HFC': 'PL_ EXT/NO VIABLE EN FO - EN INSTALACIÓN POR HFC',
                        'PLANTA EXTERNA - ERROR EN LA EJECUCIÓN DE EOC': 'PLANTA EXTERNA - ERROR EN LA EJECUCIÓN DE EOC',
                        'PL_ EXT/INCUMPLIMIENTO FE DE UM/CANCELO/REPR ES': 'PL_ EXT/INCUMPLIMIENTO FE DE UM/CANCELO/REPR ES',
                        'PL_ EXT/EN CURSO SIN INCONVENIENTE REPORTADO': 'PL_ EXT/EN CURSO SIN INCONVENIENTE REPORTADO',
                        'PL_ EXT/ESCALADO_IFO_RESULTADO DE ACTIVIDAD': 'PL_ EXT/ESCALADO_IFO_RESULTADO DE ACTIVIDAD',
                        'PL_ EXT/ESCALADO_IFO_SOLICITUD DE DESBORDE': 'PL_ EXT/ESCALADO_IFO_SOLICITUD DE DESBORDE',
                        'PL_ EXT/ESCALADO_IFO_SOLICITUD DE PERSONAL': 'PL_ EXT/ESCALADO_IFO_SOLICITUD DE PERSONAL',
                        'PLANTA EXTERNA - EN CURSO SOBRE OTP PYMES': 'PLANTA EXTERNA - EN CURSO SOBRE OTP PYMES',
                        'PLANTA EXTERNA - EN CURSO SOBRE OTP ASOCIADA': 'PLANTA EXTERNA - EN CURSO SOBRE OTP ASOCIADA',
                        'TERCEROS/NO VIABLE/EN PROC CANCELACIÓN': 'TERCEROS/NO VIABLE/EN PROC CANCELACIÓN',
                        'TERCEROS/INCUMPLIMIENTO FECHA ENTREGA UM': 'TERCEROS/INCUMPLIMIENTO FECHA ENTREGA UM',
                        'TERCEROS/SIN AVANCE SOBRE LA FECHA ENTREGA UM': 'TERCEROS/SIN AVANCE SOBRE LA FECHA ENTREGA UM',
                        'TERCEROS - EN CURSO SIN INCONVENIENTE REPORTADO': 'TERCEROS - EN CURSO SIN INCONVENIENTE REPORTADO',
                        'ALIADO/SIN INFORM ENTREGADA A TERC PARA INICIAR': 'ALIADO/SIN INFORM ENTREGADA A TERC PARA INICIAR',
                        'PREVENTA - SIN ID  FACTIBILIDAD PARA TERCEROS': 'PREVENTA - SIN ID  FACTIBILIDAD PARA TERCEROS',
                        'PREVENTA - NO ES CLARA LA SOLUCIÓN A IMPLEMENTAR': 'PREVENTA - NO ES CLARA LA SOLUCIÓN A IMPLEMENTAR',
                        'IMPLEMENTACIÓN - SOLUCIÓN NO ESTANDAR': 'IMPLEMENTACIÓN - SOLUCIÓN NO ESTANDAR',
                        'COMERCIAL - ESCALADO ORDEN DE REEMPLAZO': 'COMERCIAL - ESCALADO ORDEN DE REEMPLAZO',
                        'EQUIPOS - EN COMPRAS': 'EQUIPOS - EN COMPRAS',
                        'EQUIPOS - DEFECTUOSOS': 'EQUIPOS - DEFECTUOSOS',
                        'EQUIPOS - SIN CODIGO SAP PARA SOLICITUD DE EQUIPOS': 'EQUIPOS - SIN CODIGO SAP PARA SOLICITUD DE EQUIPOS',
                        'GPC/PENDIENTE INFOR DEL CLIENTE PARA CONFIGURAR': 'GPC/PENDIENTE INFOR DEL CLIENTE PARA CONFIGURAR',
                        'GPC/PENDIENTE ACEPTACIÓN CRONOGRAMA POR CLIENTE': 'GPC/PENDIENTE ACEPTACIÓN CRONOGRAMA POR CLIENTE',
                        'GPC - CAMBIO DE ALCANCE ORDEN DE PEDIDO': 'GPC - CAMBIO DE ALCANCE ORDEN DE PEDIDO',
                        'GPC - EN PROCESO DE CANCELACIÓN': 'GPC - EN PROCESO DE CANCELACIÓN',
                        'GPC/PENDIENTE ACEPTACIÓN CRONOGRAMA POR CLIENTE': 'GPC/PENDIENTE ACEPTACIÓN CRONOGRAMA POR CLIENTE',
                        'GPC - SIN ALCANCE PARA FABRICA': 'GPC - SIN ALCANCE PARA FABRICA',
                        'LIDER TECNICO - PENDIENTE PLAN TECNICO': 'LIDER TECNICO - PENDIENTE PLAN TECNICO',
                        'LIDER TECNICO - CAMBIO DE ALCANCE PLAN TECNICO': 'LIDER TECNICO - CAMBIO DE ALCANCE PLAN TECNICO',
                        'LIDER TECNICO/SOLUCIÓN NO ESTANDAR SIN DEFINICIÓN': 'LIDER TECNICO/SOLUCIÓN NO ESTANDAR SIN DEFINICIÓN',
                        'CONTROL DE CAMBIOS - RFC NO ESTANDAR EN APROBACIÓN': 'CONTROL DE CAMBIOS - RFC NO ESTANDAR EN APROBACIÓN',
                        'COEX - EN PROCESO DE CONFIGURACIÓN BACKEND': 'COEX - EN PROCESO DE CONFIGURACIÓN BACKEND',
                        'COEX -ATRASO CONFIGURACIÓN BACKEND': 'COEX -ATRASO CONFIGURACIÓN BACKEND',
                        'ESCALADO/EN PROCESO PASO A PENDIENTE CLIENTE': 'ESCALADO/EN PROCESO PASO A PENDIENTE CLIENTE',
                        'ENTREGA - SERVICIO_ENTREGADO_PROCESO DE CIERRE': 'ENTREGA - SERVICIO_ENTREGADO_PROCESO DE CIERRE',
                        'ENTREGA/SIN DISPONIBILIDAD AGENDA': 'ENTREGA/SIN DISPONIBILIDAD AGENDA',
                        'ENTREGA Y/O SOPORTE PROGRAMADO': 'ENTREGA Y/O SOPORTE PROGRAMADO',
                        'PENDIENTE SOLICITAR ENTREGA DEL SERVICIO': 'PENDIENTE SOLICITAR ENTREGA DEL SERVICIO',
                        'DATACENTER CLARO- CABLEADO EN CURSO': 'DATACENTER CLARO- CABLEADO EN CURSO',
                        'DATACENTER  CLARO- CABLEADO SIN EJECUTAR': 'DATACENTER  CLARO- CABLEADO SIN EJECUTAR',
                        'DATACENTER  CLARO- SIN CONSUMIBLES EN DATACENTER': 'DATACENTER  CLARO- SIN CONSUMIBLES EN DATACENTER',
                        'EN PROCESO DE PASO A ESTADO PENDIENTE CLIENTE': 'EN PROCESO DE PASO A ESTADO PENDIENTE CLIENTE',
                        'EN PROCESO DE PASO A ESTADO CANCELADO ': 'EN PROCESO DE PASO A ESTADO CANCELADO ',
                        'INCONVENIENTE TECNICO': 'INCONVENIENTE TECNICO',
                        'KO PENDIENTE': 'KO PENDIENTE',
                        'EN CONFIGURACIÓN': 'EN CONFIGURACIÓN',
                        'GPC/CAMBIO DE ALCANCE ORDEN DE PEDIDO': 'GPC/CAMBIO DE ALCANCE ORDEN DE PEDIDO',
                        'GPC/EN PROCESO DE CANCELACIÓN': 'GPC/EN PROCESO DE CANCELACIÓN',
                        'GPC/PENDIENTE INFORM DEL CLIENTE PARA CONFIGURAR': 'GPC/PENDIENTE INFORM DEL CLIENTE PARA CONFIGURAR',
                        'GPC/SIN ALCANCE PARA FABRICA': 'GPC/SIN ALCANCE PARA FABRICA',
                        'ESTADO CANCELADO': 'ESTADO CANCELADO',
                        'ESTADO PENDIENTE CLIENTE': 'ESTADO PENDIENTE CLIENTE',
                        'codigos': '**CODIGOS ANTIGUOS**',
                        'EN PROCESOS CIERRE KO': 'EN PROCESOS CIERRE KO',
                        'ALIADO - PENDIENTE SOLICITAR ENTREGA DEL SERVICIO': 'ALIADO - PENDIENTE SOLICITAR ENTREGA DEL SERVICIO',
                        'ALIADO - SIN INFORMACIÓN ENTREGADA A TERCEROS PARA INICIAR PROCESO': 'ALIADO - SIN INFORMACIÓN ENTREGADA A TERCEROS PARA INICIAR PROCESO',
                        'ASIGNADO LIDER TECNICO': 'ASIGNADO LIDER TECNICO',
                        'CLIENTE - CAMBIO DE ALCANCE (CAMBIO DE TIPO DE SERVICIO)': 'CLIENTE - CAMBIO DE ALCANCE (CAMBIO DE TIPO DE SERVICIO)',
                        'CLIENTE - CAMBIO DE UBICACIÓN DE ULTIMA MILLA': 'CLIENTE - CAMBIO DE UBICACIÓN DE ULTIMA MILLA',
                        'CLIENTE - NO APRUEBA COSTOS DE OBRA CIVIL': 'CLIENTE - NO APRUEBA COSTOS DE OBRA CIVIL',
                        'CLIENTE - NO PERMITE CIERRE DE KO': 'CLIENTE - NO PERMITE CIERRE DE KO',
                        'CLIENTE - NO PERMITE PROGRAMAR ACTIVIDAD ETAPA FINAL DE ENTREGA DEL SERVICIO': 'CLIENTE - NO PERMITE PROGRAMAR ACTIVIDAD ETAPA FINAL DE ENTREGA DEL SERVICIO',
                        'CLIENTE - NO PERMITE PROGRAMAR ACTIVIDAD ETAPA FINAL DE ENTREGA DEL SERVICIO - REQUIERE VENTANA': 'CLIENTE - NO PERMITE PROGRAMAR ACTIVIDAD ETAPA FINAL DE ENTREGA DEL SERVICIO - REQUIERE VENTANA',
                        'CLIENTE - NO PERMITE PROGRAMAR ACTIVIDAD ETAPA INICIAL SURVEY O VISITA O CON TERCERO': 'CLIENTE - NO PERMITE PROGRAMAR ACTIVIDAD ETAPA INICIAL SURVEY O VISITA O CON TERCERO',
                        'CLIENTE - NO PERMITE PROGRAMAR ACTIVIDAD ETAPA INICIAL VOC': 'CLIENTE - NO PERMITE PROGRAMAR ACTIVIDAD ETAPA INICIAL VOC',
                        'CLIENTE - NO PERMITE PROGRAMAR ACTIVIDAD ETAPA INTERMEDIA  DE ULTIMA MILLA CON TERCERO ': 'CLIENTE - NO PERMITE PROGRAMAR ACTIVIDAD ETAPA INTERMEDIA  DE ULTIMA MILLA CON TERCERO ',
                        'CLIENTE - NO PERMITE PROGRAMAR ACTIVIDAD ETAPA INTERMEDIA EMPALMES': 'CLIENTE - NO PERMITE PROGRAMAR ACTIVIDAD ETAPA INTERMEDIA EMPALMES',
                        'CLIENTE - NO PERMITE PROGRAMAR ACTIVIDAD ETAPA INTERMEDIA EOC': 'CLIENTE - NO PERMITE PROGRAMAR ACTIVIDAD ETAPA INTERMEDIA EOC',
                        'CLIENTE - NO TIENE DEFINIDA LA DIRECCIÓN DONDE VA A QUEDAR UBICADO EL SERVICIO': 'CLIENTE - NO TIENE DEFINIDA LA DIRECCIÓN DONDE VA A QUEDAR UBICADO EL SERVICIO',
                        'CLIENTE - PROGRAMADA POSTERIOR ': 'CLIENTE - PROGRAMADA POSTERIOR ',
                        'CLIENTE - SIN CONTRATO FIRMADO': 'CLIENTE - SIN CONTRATO FIRMADO',
                        'CLIENTE - SIN DISPONIBILIDAD DE INFRAESTRUCTURA (PLANTA TELEFONICA - LAN DIRECCIONAMIENTO )': 'CLIENTE - SIN DISPONIBILIDAD DE INFRAESTRUCTURA (PLANTA TELEFONICA - LAN DIRECCIONAMIENTO )',
                        'CLIENTE - SIN FECHA ADECUACIONES EN LA SEDE (ELECTRICAS Y/O FISICA)': 'CLIENTE - SIN FECHA ADECUACIONES EN LA SEDE (ELECTRICAS Y/O FISICA)',
                        'CLIENTE - SIN FECHA PARA RECIBIR EL SERVICIO': 'CLIENTE - SIN FECHA PARA RECIBIR EL SERVICIO',
                        'COEX - EN PROCESO DE CONFIGURACIÓN BACKEND': 'COEX - EN PROCESO DE CONFIGURACIÓN BACKEND',
                        'COEX -ATRASO CONFIGURACIÓN BACKEND': 'COEX -ATRASO CONFIGURACIÓN BACKEND',
                        'COMERCIAL - ESCALADO ORDEN DE REEMPLAZO': 'COMERCIAL - ESCALADO ORDEN DE REEMPLAZO',
                        'COMERCIAL - ESCALADO PENDIENTE INGRESO OTS': 'COMERCIAL - ESCALADO PENDIENTE INGRESO OTS',
                        'CONTROL DE CAMBIOS - RFC NO ESTANDAR EN APROBACIÓN': 'CONTROL DE CAMBIOS - RFC NO ESTANDAR EN APROBACIÓN',
                        'CSM - Retiro equipos - Renovación de Contrato': 'CSM - Retiro equipos - Renovación de Contrato',
                        'DATACENTER  CLARO- CABLEADO SIN EJECUTAR': 'DATACENTER  CLARO- CABLEADO SIN EJECUTAR',
                        'DATACENTER  CLARO- SIN CONSUMIBLES EN DATACENTER': 'DATACENTER  CLARO- SIN CONSUMIBLES EN DATACENTER',
                        'DATACENTER CLARO- CABLEADO EN CURSO': 'DATACENTER CLARO- CABLEADO EN CURSO',
                        'ENTREGA - SERVICIO ENTREGADO PROCESO DE CIERRE': 'ENTREGA - SERVICIO ENTREGADO PROCESO DE CIERRE',
                        'ENTREGA - SIN DISPONIBILIDAD AGENDA EN VERIFICACIÓN DE RECURSOS': 'ENTREGA - SIN DISPONIBILIDAD AGENDA EN VERIFICACIÓN DE RECURSOS',
                        'ENTREGA Y/O SOPORTE PROGRAMADO': 'ENTREGA Y/O SOPORTE PROGRAMADO',
                        'EQUIPOS - DEFECTUOSOS': 'EQUIPOS - DEFECTUOSOS',
                        'EQUIPOS - EN COMPRAS': 'EQUIPOS - EN COMPRAS',
                        'ESCALADO LIDER IMPLEMENTACIÓN PASO A PENDIENTE CLIENTE': 'ESCALADO LIDER IMPLEMENTACIÓN PASO A PENDIENTE CLIENTE',
                        'GPC - CAMBIO DE ALCANCE ORDEN DE PEDIDO': 'GPC - CAMBIO DE ALCANCE ORDEN DE PEDIDO',
                        'GPC - EN PROCESO DE CANCELACIÓN': 'GPC - EN PROCESO DE CANCELACIÓN',
                        'GPC - PENDIENTE ACEPTACIÓN CRONOGRAMA POR PARTE DEL CLIENTE': 'GPC - PENDIENTE ACEPTACIÓN CRONOGRAMA POR PARTE DEL CLIENTE',
                        'GPC - PENDIENTE INFORMACIÓN DEL CLIENTE PARA CONFIGURAR': 'GPC - PENDIENTE INFORMACIÓN DEL CLIENTE PARA CONFIGURAR',
                        'GPC - SIN ALCANCE PARA FABRICA': 'GPC - SIN ALCANCE PARA FABRICA',
                        'IMPLEMENTACIÓN - SOLUCIÓN NO ESTANDAR': 'IMPLEMENTACIÓN - SOLUCIÓN NO ESTANDAR',
                        'INCONVENIENTE TECNICO': 'INCONVENIENTE TECNICO',
                        'LIDER TECNICO - CAMBIO DE ALCANCE PLAN TECNICO': 'LIDER TECNICO - CAMBIO DE ALCANCE PLAN TECNICO',
                        'LIDER TECNICO - PENDIENTE PLAN TECNICO': 'LIDER TECNICO - PENDIENTE PLAN TECNICO',
                        'LIDER TECNICO - SOLUCIÓN NO ESTANDAR': 'LIDER TECNICO - SOLUCIÓN NO ESTANDAR',
                        'LIDER TECNICO - SOLUCIÓN NO ESTANDAR SIN DEFINICIÓN': 'LIDER TECNICO - SOLUCIÓN NO ESTANDAR SIN DEFINICIÓN',
                        'PASO A PENDIENTE CLIENTE': 'PASO A PENDIENTE CLIENTE',
                        'PENDIENTE SOLICITAR ENTREGA DEL SERVICIO': 'PENDIENTE SOLICITAR ENTREGA DEL SERVICIO',
                        'PLANTA EXTERNA - EN CURSO SIN INCONVENIENTE REPORTADO': 'PLANTA EXTERNA - EN CURSO SIN INCONVENIENTE REPORTADO',
                        'PLANTA EXTERNA - ERROR EN LA EJECUCIÓN DE EOC': 'PLANTA EXTERNA - ERROR EN LA EJECUCIÓN DE EOC',
                        'PLANTA EXTERNA - ESCALADO IFO RESULTADO DE ACTIVIDAD': 'PLANTA EXTERNA - ESCALADO IFO RESULTADO DE ACTIVIDAD',
                        'PLANTA EXTERNA - ESCALADO IFO SOLICITUD DE DESBORDE': 'PLANTA EXTERNA - ESCALADO IFO SOLICITUD DE DESBORDE',
                        'PLANTA EXTERNA - ESCALADO IFO SOLICITUD DE PERSONAL': 'PLANTA EXTERNA - ESCALADO IFO SOLICITUD DE PERSONAL',
                        'PLANTA EXTERNA - ETAPA INTERMEDIA - SIN CONFIRMACIÓN DE PERSONAL PARA EOC Y EMPALMES': 'PLANTA EXTERNA - ETAPA INTERMEDIA - SIN CONFIRMACIÓN DE PERSONAL PARA EOC Y EMPALMES',
                        'PLANTA EXTERNA - INCUMPLIMIENTO EN LA FECHA DE ENTREGA DE ULTIMA MILLA - SE CANCELO O REPROGRAMO ENTREGA DE SERVICIO': 'PLANTA EXTERNA - INCUMPLIMIENTO EN LA FECHA DE ENTREGA DE ULTIMA MILLA - SE CANCELO O REPROGRAMO ENTREGA DE SERVICIO',
                        'PLANTA EXTERNA - NO VIABLE EN FACTIBILIDAD POR TERCEROS': 'PLANTA EXTERNA - NO VIABLE EN FACTIBILIDAD POR TERCEROS',
                        'PLANTA EXTERNA - NO VIABLE EN FO - EN INSTALACIÓN POR HFC': 'PLANTA EXTERNA - NO VIABLE EN FO - EN INSTALACIÓN POR HFC',
                        'PLANTA EXTERNA - PERMISOS MUNICIPALES - PERMISOS DE ARRENDADOR DE INFRAESTRUCTURA': 'PLANTA EXTERNA - PERMISOS MUNICIPALES - PERMISOS DE ARRENDADOR DE INFRAESTRUCTURA',
                        'PLANTA EXTERNA - SIN APROBACIÓN DE TENDIDO EXTERNO POR COSTOS': 'PLANTA EXTERNA - SIN APROBACIÓN DE TENDIDO EXTERNO POR COSTOS',
                        'PREVENTA - NO ES CLARA LA SOLUCIÓN A IMPLEMENTAR': 'PREVENTA - NO ES CLARA LA SOLUCIÓN A IMPLEMENTAR',
                        'PREVENTA - SIN ID  FACTIBILIDAD PARA TERCEROS': 'PREVENTA - SIN ID  FACTIBILIDAD PARA TERCEROS',
                        'PROYECTO ÉXITO ANTIGUO': 'PROYECTO ÉXITO ANTIGUO',
                        'TERCEROS - EN CURSO SIN INCONVENIENTE REPORTADO': 'TERCEROS - EN CURSO SIN INCONVENIENTE REPORTADO',
                        'TERCEROS - INCUMPLIMIENTO EN LA FECHA DE ENTREGA DE ULTIMA MILLA - SE CANCELO O REPROGRAMO ENTREGA DE SERVICIO': 'TERCEROS - INCUMPLIMIENTO EN LA FECHA DE ENTREGA DE ULTIMA MILLA - SE CANCELO O REPROGRAMO ENTREGA DE SERVICIO',
                        'TERCEROS - NO VIABLE - EN PROCESO NOTIFICACIÓN A CLIENTE Y COMERCIAL PARA CANCELACIÓN': 'TERCEROS - NO VIABLE - EN PROCESO NOTIFICACIÓN A CLIENTE Y COMERCIAL PARA CANCELACIÓN',
                        'TERCEROS - SIN AVANCE SOBRE LA FECHA DE ENTREGA DE ULTIMA MILL': 'TERCEROS - SIN AVANCE SOBRE LA FECHA DE ENTREGA DE ULTIMA MILLA'
                    },

                    inputPlaceholder: 'Seleccione...',
                    showCancelButton: true
                },
                {
                    title: 'Observaciones',
                    text: '¿Desea guardar observaciones?',
                    input: 'textarea',
                    // inputClass: 'algo' ,
                    confirmButtonText: 'Guardar!',
                    inputOptions: {

                    },
                    inputPlaceholder: 'Observaciones...',
                    showCancelButton: true
                },
            ]).then((result) => {
                if (result.value) {
                    if (!result.value[0] == "") {

                        swal({
                            title: 'Desea guardar?',
                            text: "Se actualizará esta informacion en esta OTP",
                            type: 'question',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Guardar!'
                        }).then((result1) => {
                            if (result1.value) {
                                $.post(baseurl + '/OtPadre/update_data',
                                        {
                                            // clave: 'valor' // parametros que se envian
                                            id: id_otp,
                                            lista: result.value[0],
                                            observacion: result.value[1]
                                        },
                                        function (data) {
                                            var res = JSON.parse(data);
                                            if (res == true) {
                                                swal(
                                                        'Guardado!',
                                                        'Actualizo correctamente los campos',
                                                        'success'
                                                        )
                                                setTimeout("location.reload()", 1500);
                                            } else {
                                                swal('Error',
                                                        'No tiene permiso para esta accíon',
                                                        'error'
                                                        )
                                            }
                                        });
                            } else {
                                swal({
                                    type: 'error',
                                    title: 'Oops...',
                                    text: 'No se actuallizo ningun campo!',
                                })
                            }
                        })
                    } else {
                        swal({
                            type: 'error',
                            title: 'Error',
                            text: 'No selecciono ningun registro de la lista',
                        })
                    }
                }

            })
        },

        // LO REMPLAZARÁ EL DE ABAJO, showEmailOthGeneral
        // onClickShowEmailOth: function(obj) {
        //     var aLinkLog = $(this);
        //     var trParent = aLinkLog.parents('tr');
        //     var record = listoth.table_oths_otp.row(trParent).data();
        //     $.post(baseurl + '/Log/getLogById',
        //             {
        //                 id: record.id_orden_trabajo_hija
        //             },
        //             function(data) {
        //                 var obj = JSON.parse(data);
        //                 eventos.showModalHistorial(obj, record.id_orden_trabajo_hija);
        //             }
        //     );
        // },

        //evento que pintará la tabla log mail y historial mail
        showEmailOthGeneral: function () {
            var tabla = $("#table_oths_otp");
            var OTHs = tabla.find("tbody tr td.sorting_1");
            //seleccionamos las OTHs de la OTP seleccionada
            var valorOTHs = new Array;
            var OTP = $("#NroOTPSelect").html();
            //creamos el arreglo para enviarlo por ajax
            $.each(OTHs, function (i, item) {
                valorOTHs.push(item.innerHTML)
            })

            $.post(baseurl + "/Log/c_getLogsByOTP",
                    {
                        valOTHs: valorOTHs,
                        OTP: OTP,
                    },
                    function (data) {
                        var obj = JSON.parse(data);
                        eventos.showModalHistorial(obj, OTP);
                    });
        },

        // Muestra modal detalle historial log por id
        showModalHistorial: function (obj, OTP) {
            $('#ModalHistorialLog').modal('show');
            $('#titleEventHistory').html('Historial Cambios de OTP N.' + OTP + '');
            // la pestaña de log historial mail reporte act. estará escondida por defecto
            $("li#liLogReporAct").hide();
            if ($("#pestana_cant_report").parents("li").hasClass("active") || $("#pestana_cant_total").parents("li").hasClass("active")) {
                // si esta en la pestaña de reporte de act. la pintará, de lo contrario, no lo hará
                eventos.printTableLogMailAct(obj.reportAct);
                $("li#liLogReporAct").show();
            }
            eventos.printTableHistory(obj.log);
            eventos.printTableLogMailReportInit(obj.mail);
        },
        //pintamos la tabla de log
        printTableHistory: function (data) {
            // limpio el cache si ya habia pintado otra tabla
            if (eventos.tableModalHistory) {
                //si ya estaba inicializada la tabla la destruyo
                eventos.tableModalHistory.destroy();
            }
            ///lleno la tabla con los valores enviados
            eventos.tableModalHistory = $('#tableHistorialLog').DataTable(listoth.configTable(data, [
                {data: "id_ot_hija"},
                {data: "antes"},
                {data: "ahora"},
                {data: "columna"},
                {data: "fecha_mod"}
            ]));
        },

        //pintamos la tabla de log de correos
        printTableLogMailReportInit: function (data) {
            // limpio el cache si ya habia pintado otra tabla
            if (eventos.tableModalLogMailReportInit) {
                //si ya estaba inicializada la tabla la destruyo
                eventos.tableModalLogMailReportInit.destroy();
            }
            ///lleno la tabla con los valores enviados
            eventos.tableModalLogMailReportInit = $('#tableLogReportInit').DataTable(listoth.configTable(data, [
                {data: "fecha"},
                {data: "clase"},
                {data: "servicio"},
                {data: "usuario_en_sesion"},
                // {data: "destinatarios"},
                {data: "nombre"},
                {data: eventos.getButonsViewEmail}
            ]));
        },

        printTableLogMailAct: function (data) {

            if (eventos.tableModalLogReportAct) {
                //si ya estaba inicializada la tabla la destruyo
                eventos.tableModalLogReportAct.destroy();
            }
            eventos.tableModalLogReportAct = $('#tableLogReportAct').DataTable(listoth.configTable(data, [
                // {data: "id_ot_padre"},
                {data: "senior"},
                {data: "nombre_cliente"},
                // {data: "f_entrega_servicio"},
                // {data: "observaciones"},
                {data: "last_enviador"},
                {data: "last_f_envio"},
                // {data: "paquete_enviados"},
                {data: eventos.getButonsViewEmail}
            ]));
        },

        // creamos los botones para imprimir el correo enviado
        getButonsViewEmail: function (obj) {
            if (obj.paquete_enviados) {
                var button = '<button class="btn btn-default btn-xs ver-mail act btn_datatable_cami" title="ver correo"><span class="fa fa-fw fa-print"></span></button>'
            } else {
                var button = '<button class="btn btn-default btn-xs ver-mail init btn_datatable_cami" title="ver correo"><span class="fa fa-fw fa-print"></span></button>'
            }

            return button;
        },

        onClickVerLogMailOTP: function () {
            var tr = $(this).parents('tr');
            if ($(this).hasClass("init")) {
                var record = eventos.tableModalLogMailReportInit.row(tr).data();
            } else {
                var record = eventos.tableModalLogReportAct.row(tr).data();
            }
            eventos.generarPDF(record);
        },

        // generar pdf redireccionar
        generarPDF: function (data) {
            if (!data.paquete_enviados) {
                // entra si el reporte es de inicio
                var funcionControlador = 'generatePDF'
            } else {
                // entra si es reporte de actualizacion
                var funcionControlador = 'ViewLogMail'
            }


            $.post(baseurl + '/Templates/' + funcionControlador,
                    {
                        data: data
                    },
                    function (data) {
                        if (funcionControlador === 'generatePDF')
                            var plantilla = JSON.parse(data);
                        $('body').append(
                                `
                            <form action="${baseurl}/Log/view_email" method="POST" target="_blank" hidden>
                                <textarea name="txt_template" id="txt_template"></textarea>
                                <input type="submit" value="e" id="smt_ver_correo">
                            </form>
                        `
                                );
                        var enviar = (funcionControlador === 'generatePDF') ? enviar = plantilla : enviar = data;
                        $('#txt_template').val(enviar);
                        $('#smt_ver_correo').click();


                    });

        },

        onClickShowModalDetEvent: function () {
            document.getElementById("formModal_detalle").reset();
            $('#title_modal').html('');
            var aLinkLog = $(this);
            var trParent = aLinkLog.parents('tr');
            var record = listoth.table_oths_otp.row(trParent).data();
            eventos.fillFormModalDetEvent(record);
        },
        fillFormModalDetEvent: function (registros) {
            $.post(baseurl + '/OtHija/c_fillmodals',
                    {
                        idOth: registros.id_orden_trabajo_hija // parametros que se envian
                    },
                    function (data) {
                        $.each(data, function (i, item) {
                            $('#mdl_' + i).val(item);
                        });
                    });
            $('#title_modal').html('<b>Detalle de la orden  ' + registros.id_orden_trabajo_hija + '</b>');
            $('#Modal_detalle').modal('show');
        },

        // limpia el modal de los hitos
        resetHitosModal: function () {
            // resetea el formulario y lo deja vacio
            document.getElementById("formModalHitosOTP").reset();
            $("#table_hitos_otp tbody").children().remove(); //limpia la tabla de hitos
            $("#actividad_actual").children('.optH').remove(); //limpia el select de los hitos
            $("#btnGuardarModalHitos").show();
        },

        // Muestra los hitos de la ot padre seleccionada
        showModalHitosOthp: function (datax, x) {
            /*
             if (x.hasClass('clr_lime')) {
             
             $.post(baseurl + '/OtPadre/c_getLinearBaseForHitos',
             {
             idOtp: datax.k_id_ot_padre
             },
             function(data) {
             const obj = JSON.parse(data);
             eventos.printDynamicHitosTable(obj);
             // si tiene hitos entrará acá y los buscará, pero si no, irá a la linea base
             $.post(baseurl + '/OtPadre/c_getHitosOtp',
             {
             //WE, ESTO ENVIA EN ID DE LA OT PADRE, PARA ESO SIRVE EL POST EN EL CONTROLADOR
             idOtp: datax.k_id_ot_padre
             },
             function(data) {
             var obj = JSON.parse(data);
             $(".timeline-badge").css("background-color", "#7c7c7c");
             if (obj !== null) {
             
             switch (obj.actividad_actual) {
             case "KICK OFF":
             $("#act_ko").css("background-color", "#4bd605");
             break;
             
             case "VISITA OBRA CIVIL":
             $("#act_voc").css("background-color", "#4bd605");
             break;
             
             case "VISITA OBRA CIVIL TERCEROS":
             $("#act_voc").css("background-color", "#4bd605");
             break;
             
             case "ENVIO COTIZACION":
             $("#act_ec").css("background-color", "#4bd605");
             break;
             
             case "APROBACION COTIZACION":
             $("#act_ac").css("background-color", "#4bd605");
             break;
             
             case "SOLICITUD INFORMACIÓN TECNICA":
             $("#act_sit").css("background-color", "#4bd605");
             break;
             
             case "VISITA EJECUCION OBRA CIVIL":
             $("#act_veoc").css("background-color", "#4bd605");
             break;
             
             case "VISITA EJECUCION OBRA CIVIL TERCERO":
             $("#act_veoc").css("background-color", "#4bd605");
             break;
             
             case "CONFIGURACION RED CLARO":
             $("#act_crc").css("background-color", "#4bd605");
             break;
             
             case "VISITA ENTREGA UM TERCEROS":
             $("#act_veut").css("background-color", "#4bd605");
             break;
             case "EMPALMES":
             $("#act_empalmes").css("background-color", "#4bd605");
             break;
             }
             
             $.each(obj, function(i, item) {
             $('#' + i).val(item);
             });
             }
             });
             },
             );
             
             
             } else {
             $.post(baseurl + '/OtPadre/c_getLinearBaseForHitos',
             {
             idOtp: datax.k_id_ot_padre
             },
             function(data) {
             const obj = JSON.parse(data);
             eventos.printDynamicHitosTable(obj);
             
             },
             );
             }
             */
            reporte_act.resetFormHitos();
            $.post(baseurl + '/OtPadre/c_getInfoHitosByOtp',
                    {
                        idOtp: datax.k_id_ot_padre // parametros que se envian
                    },
                    function (data) {
                        var obj = JSON.parse(data);
                        if (obj.length > 0) {
                            $.each(obj, function (i, item) {
                                $.each(item, function (i2, item2) {
                                    if ($('#' + i2).attr('type') == 'checkbox' && item2 == 'no aplica') {
                                        $('#' + i2).prop( "checked", true );
                                    } else {
                                        $('#' + i2).val(item2);
                                    }
                                });
                            });
                        }                        
                    });
            //pinta el titulo del modal y cambia dependiendo de la otp seleccionada
            $('#myModalLabelHitos').html('<strong> Hitos de la OTP N.<span id="otpHIto">' + datax.k_id_ot_padre + '</span></strong>');
            $('#servivio_hito').html('<strong> OT ' + datax.k_id_ot_padre + ' - ' + datax.servicio + '</strong>');
            $('#cliente_hito').html('<strong> CLIENTE: ' + datax.n_nombre_cliente + '</strong>');
            $('#ciudad_hito').html('<strong> CIUDAD: ' + datax.ciudad + ' - ' + datax.direccion + '</strong>');
            $('#modalHitosOtp').modal('show');
        },

        // según lo que se haya encontrado en la linea base, dependiendo de la fecha, pintará los campos de la tabla
        printDynamicHitosTable: function (data) {
            var numCampo = 1;
            var tabla = ``;
            var optionSelect = '';
            if (data === null) {
                tabla = `
                    <tr>
                        <td>
                            <ul class="timeline timeline-jhon">
                                <li class="timeline-item">
                                    <div class="timeline-badge" id="act_ko">1</div>
                                </li>
                            </ul>
                        </td>
                        <td><span>CIERRE KICKOFF</span> (KO)</td>
                        <td>
                            <input type="date" name="f_compromiso" id="f_compromiso_ko" class="form-control fechas_hitos">
                        </td>
                        <td>
                            <select name="estado" id="estado_ko" class="form-control">
                                <option value="" >SELECCIONE...</option>
                                <option value="EJECUTADA" >EJECUTADA</option>
                                <option value="ENVIADA" >ENVIADA</option>
                                <option value="APROBADA" >APROBADA</option>
                                <option value="CONFIGURADO" >CONFIGURADO</option>
                                <option value="PENDIENTE" >PENDIENTE</option>
                                <option value="CERRADA" >CERRADA</option>
                                <option value="NO APLICA" >NO APLICA</option>
                            </select>
                        </td>
                        <td>
                            <textarea name="observaciones" id="observaciones_ko" rows="2"></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <ul class="timeline timeline-jhon">
                                <li class="timeline-item">
                                    <div class="timeline-badge" id="act_voc">2</div>
                                </li>
                            </ul>
                        </td>
                        <td><!--VISITA OBRA CIVIL-->
                            <select name="tipo_voc" id="tipo_voc" class="form-control">
                                <option value="VISITA OBRA CIVIL" >VISITA OBRA CIVIL (VOC)</option>
                                <option value="VISITA OBRA CIVIL TERCEROS" >VISITA OBRA CIVIL TERCEROS</option>
                            </select>
                        </td>
                        <td>
                            <input type="date" name="f_compromiso" id="f_compromiso_voc" class="form-control fechas_hitos fechVoc">
                        </td>
                        <td>
                            <select name="estado" id="estado_voc" class="form-control estVoc">
                                <option value="" >SELECCIONE...</option>
                                <option value="EJECUTADA" >EJECUTADA</option>
                                <option value="ENVIADA" >ENVIADA</option>
                                <option value="APROBADA" >APROBADA</option>
                                <option value="CONFIGURADO" >CONFIGURADO</option>
                                <option value="PENDIENTE" >PENDIENTE</option>
                                <option value="CERRADA" >CERRADA</option>
                                <option value="NO APLICA" >NO APLICA</option>
                            </select>
                        </td>
                        <td>
                            <textarea name="observaciones" id="observaciones_voc" rows="2" class="obsVoc"></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <ul class="timeline timeline-jhon">
                                <li class="timeline-item">
                                    <div class="timeline-badge" id="act_ec">3</div>
                                </li>
                            </ul>
                        </td>
                        <td><span>ENVIÓ COTIZACIÓN</span> OC</td>
                        <td>
                            <input type="date" name="f_compromiso" id="f_compromiso_ec" class="form-control fechas_hitos">
                        </td>
                        <td>
                            <select name="estado" id="estado_ec" class="form-control">
                                <option value="" >SELECCIONE...</option>
                                <option value="EJECUTADA" >EJECUTADA</option>
                                <option value="ENVIADA" >ENVIADA</option>
                                <option value="APROBADA" >APROBADA</option>
                                <option value="CONFIGURADO" >CONFIGURADO</option>
                                <option value="PENDIENTE" >PENDIENTE</option>
                                <option value="CERRADA" >CERRADA</option>
                                <option value="NO APLICA" >NO APLICA</option>
                            </select>
                        </td>
                        <td>
                            <textarea name="observaciones" id="observaciones_ec" rows="2"></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <ul class="timeline timeline-jhon">
                                <li class="timeline-item">
                                    <div class="timeline-badge" id="act_ac">4</div>
                                </li>
                            </ul>
                        </td>
                        <td><span>APROBACIÓN COTIZACIÓN OC</span></td>
                        <td>
                            <input type="date" name="f_compromiso" id="f_compromiso_ac" class="form-control fechas_hitos">
                        </td>
                        <td>
                            <select name="estado" id="estado_ac" class="form-control">
                                <option value="" >SELECCIONE...</option>
                                <option value="EJECUTADA" >EJECUTADA</option>
                                <option value="ENVIADA" >ENVIADA</option>
                                <option value="APROBADA" >APROBADA</option>
                                <option value="CONFIGURADO" >CONFIGURADO</option>
                                <option value="PENDIENTE" >PENDIENTE</option>
                                <option value="CERRADA" >CERRADA</option>
                                <option value="NO APLICA" >NO APLICA</option>
                            </select>
                        </td>
                        <td>
                            <textarea name="observaciones" id="observaciones_ac" rows="2"></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <ul class="timeline timeline-jhon">
                                <li class="timeline-item">
                                    <div class="timeline-badge" id="act_sit">5</div>
                                </li>
                            </ul>
                        </td>
                        <td><span>SOLICITUD INFORMACIÓN TÉCNICA</span></td>
                        <td>
                            <input type="date" name="f_compromiso" id="f_compromiso_sit" class="form-control fechas_hitos">
                        </td>
                        <td>
                            <select name="estado" id="estado_sit" class="form-control">
                                <option value="" >SELECCIONE...</option>
                                <option value="EJECUTADA" >EJECUTADA</option>
                                <option value="ENVIADA" >ENVIADA</option>
                                <option value="APROBADA" >APROBADA</option>
                                <option value="CONFIGURADO" >CONFIGURADO</option>
                                <option value="PENDIENTE" >PENDIENTE</option>
                                <option value="CERRADA" >CERRADA</option>
                                <option value="NO APLICA" >NO APLICA</option>
                            </select>
                        </td>
                        <td>
                            <textarea name="observaciones" id="observaciones_sit" rows="2"></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <ul class="timeline timeline-jhon">
                                <li class="timeline-item">
                                    <div class="timeline-badge" id="act_veoc">6</div>
                                </li>
                            </ul>
                        </td>
                        <td><!--VISITA EJECUCION OBRA CIVIL-->
                            <select name="tipo_veoc" id="tipo_veoc" class="form-control">
                                <option value="VISITA EJECUCION OBRA CIVIL" >VISITA EJECUCION OBRA CIVIL (EOC)</option>
                                <option value="VISITA EJECUCION OBRA CIVIL TERCERO" >VISITA EJECUCION OBRA CIVIL TERCERO</option>
                            </select>
                        </td>
                        <td>
                            <input type="date" name="f_compromiso" id="f_compromiso_veoc" class="form-control fechas_hitos">
                        </td>
                        <td>
                            <select name="estado" id="estado_veoc" class="form-control">
                                <option value="" >SELECCIONE...</option>
                                <option value="EJECUTADA" >EJECUTADA</option>
                                <option value="ENVIADA" >ENVIADA</option>
                                <option value="APROBADA" >APROBADA</option>
                                <option value="CONFIGURADO" >CONFIGURADO</option>
                                <option value="PENDIENTE" >PENDIENTE</option>
                                <option value="CERRADA" >CERRADA</option>
                                <option value="NO APLICA" >NO APLICA</option>
                            </select>
                        </td>
                        <td>
                            <textarea name="observaciones" id="observaciones_veoc" rows="2"></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <ul class="timeline timeline-jhon">
                                <li class="timeline-item">
                                    <div class="timeline-badge" id="act_empalmes">7</div>
                                </li>
                            </ul>
                        </td>
                        <td><span>EMPALMES</span></td>
                        <td>
                            <input type="date" name="f_compromiso" id="f_compromiso_empalmes" class="form-control fechas_hitos">
                        </td>
                        <td>
                            <select name="estado" id="estado_empalmes" class="form-control">
                                <option value="" >SELECCIONE...</option>
                                <option value="EJECUTADA" >EJECUTADA</option>
                                <option value="ENVIADA" >ENVIADA</option>
                                <option value="APROBADA" >APROBADA</option>
                                <option value="CONFIGURADO" >CONFIGURADO</option>
                                <option value="PENDIENTE" >PENDIENTE</option>
                                <option value="CERRADA" >CERRADA</option>
                                <option value="NO APLICA" >NO APLICA</option>
                            </select>
                        </td>
                        <td>
                            <textarea name="observaciones" id="observaciones_empalmes" rows="2"></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <ul class="timeline timeline-jhon">
                                <li class="timeline-item">
                                    <div class="timeline-badge" id="act_crc">8</div>
                                </li>
                            </ul>
                        </td>
                        <td><span>CONFIGURACION</span></td>
                        <td>
                            <input type="date" name="f_compromiso" id="f_compromiso_crc" class="form-control fechas_hitos">
                        </td>
                        <td>
                            <select name="estado" id="estado_crc" class="form-control">
                                <option value="" >SELECCIONE...</option>
                                <option value="EJECUTADA" >EJECUTADA</option>
                                <option value="ENVIADA" >ENVIADA</option>
                                <option value="APROBADA" >APROBADA</option>
                                <option value="CONFIGURADO" >CONFIGURADO</option>
                                <option value="PENDIENTE" >PENDIENTE</option>
                                <option value="CERRADA" >CERRADA</option>
                                <option value="NO APLICA" >NO APLICA</option>
                            </select>
                        </td>
                        <td>
                            <textarea name="observaciones" id="observaciones_crc" rows="2"></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <ul class="timeline timeline-jhon">
                                <li class="timeline-item">
                                    <div class="timeline-badge" id="act_veut">9</div>
                                </li>
                            </ul>
                        </td>
                        <td><span>VISITA ENTREGA DE SERVICIO</span></td>
                        <td>
                            <input type="date" name="f_compromiso" id="f_compromiso_veut" class="form-control fechas_hitos">
                        </td>
                        <td>
                            <select name="estado" id="estado_veut" class="form-control">
                                <option value="" >SELECCIONE...</option>
                                <option value="EJECUTADA" >EJECUTADA</option>
                                <option value="ENVIADA" >ENVIADA</option>
                                <option value="APROBADA" >APROBADA</option>
                                <option value="CONFIGURADO" >CONFIGURADO</option>
                                <option value="PENDIENTE" >PENDIENTE</option>
                                <option value="CERRADA" >CERRADA</option>
                                <option value="NO APLICA" >NO APLICA</option>
                            </select>
                        </td>
                        <td>
                            <textarea name="observaciones" id="observaciones_veut" rows="2"></textarea>
                        </td>
                    </tr>
                `;
                optionSelect += `<option class="optH" value="KICK OFF">CIERRE KICKOFF (KO)</option>`;
                optionSelect += `<option class="optH" value="VISITA OBRA CIVIL">VISITA OBRA CIVIL (VOC)</option>`;
                optionSelect += `<option class="optH" value="ENVIO COTIZACION">ENVIÓ COTIZACIÓN OC</option>`;
                optionSelect += `<option class="optH" value="APROBACION COTIZACION">APROBACION COTIZACION</option>`;
                optionSelect += `<option class="optH" value="SOLICITUD INFORMACIÓN TECNICA">SOLICITUD INFORMACIÓN TÉCNICA</option>`;
                optionSelect += `<option class="optH" value="VISITA EJECUCION OBRA CIVIL">VISITA EJECUCION OBRA CIVIL (EOC)</option>`;
                optionSelect += `<option class="optH" value="EMPALMES">EMPALMES</option>`;
                optionSelect += `<option class="optH" value="CONFIGURACION RED CLARO">CONFIGURACION</option>`;
                optionSelect += `<option class="optH" value="VISITA ENTREGA UM TERCEROS">VISITA ENTREGA DE SERVICIO</option> `;
//                $("#btnGuardarModalHitos").hide();
            } else {
                $.each(data, function (campo, fecha) {
                    if (fecha) {
                        switch (campo) {
                            case 'fecha_cierre_ko':
                                optionSelect += `<option class="optH" value="KICK OFF">CIERRE KICKOFF (KO)</option>`;

                                tabla += `<tr>
                                <td>
                                    <ul class="timeline timeline-jhon">
                                        <li class="timeline-item">
                                            <div class="timeline-badge" id="act_ko">${numCampo}</div>
                                        </li>
                                    </ul>
                                </td>
                                <td><span>CIERRE KICKOFF</span> (KO)</td>
                                <td>
                                    <input type="date" name="f_compromiso" value="${fecha}" id="f_compromiso_ko" class="form-control fechas_hitos">
                                </td>
                                <td>
                                    <select name="estado" id="estado_ko" class="form-control">
                                        <option value="">SELECCIONE...</option>
                                        <option value="EJECUTADA">EJECUTADA</option>
                                        <option value="ENVIADA">ENVIADA</option>
                                        <option value="APROBADA">APROBADA</option>
                                        <option value="CONFIGURADO">CONFIGURADO</option>
                                        <option value="PENDIENTE">PENDIENTE</option>
                                        <option value="CERRADA">CERRADA</option>
                                        <option value="NO APLICA">NO APLICA</option>
                                    </select>
                                </td>
                                <td>
                                    <textarea name="observaciones" id="observaciones_ko" rows="2"></textarea>
                                </td>
                            </tr>`;
                                break;

                            case 'fecha_visita_obra_civil':
                                optionSelect += `<option class="optH" value="VISITA OBRA CIVIL">VISITA OBRA CIVIL (VOC)</option>`;
                                tabla += `<tr>
                                <td>
                                    <ul class="timeline timeline-jhon">
                                        <li class="timeline-item">
                                            <div class="timeline-badge" id="act_voc">${numCampo}</div>
                                        </li>
                                    </ul>
                                </td>
                                <td>
                                    <!--VISITA OBRA CIVIL-->
                                    <select name="tipo_voc" id="tipo_voc" class="form-control">
                                        <option value="VISITA OBRA CIVIL">VISITA OBRA CIVIL (VOC)</option>
                                        <option value="VISITA OBRA CIVIL TERCEROS">VISITA OBRA CIVIL TERCEROS</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="date" name="f_compromiso" value="${fecha}" id="f_compromiso_voc" class="form-control fechas_hitos fechVoc">
                                </td>
                                <td>
                                    <select name="estado" id="estado_voc" class="form-control estVoc">
                                        <option value="">SELECCIONE...</option>
                                        <option value="EJECUTADA">EJECUTADA</option>
                                        <option value="ENVIADA">ENVIADA</option>
                                        <option value="APROBADA">APROBADA</option>
                                        <option value="CONFIGURADO">CONFIGURADO</option>
                                        <option value="PENDIENTE">PENDIENTE</option>
                                        <option value="CERRADA">CERRADA</option>
                                        <option value="NO APLICA">NO APLICA</option>
                                    </select>
                                </td>
                                <td>
                                    <textarea name="observaciones" id="observaciones_voc" rows="2" class="obsVoc"></textarea>
                                </td>
                            </tr>`;
                                break;

                            case 'fecha_dcoc':
                                optionSelect += `<option class="optH" value="ENVIO COTIZACION">ENVIÓ COTIZACIÓN OC</option>`;
                                tabla += `<tr>
                                <td>
                                    <ul class="timeline timeline-jhon">
                                        <li class="timeline-item">
                                            <div class="timeline-badge" id="act_ec">${numCampo}</div>
                                        </li>
                                    </ul>
                                </td>
                                <td><span>ENVIÓ COTIZACIÓN</span> OC</td>
                                <td>
                                    <input type="date" name="f_compromiso" value="${fecha}" id="f_compromiso_ec" class="form-control fechas_hitos">
                                </td>
                                <td>
                                    <select name="estado" id="estado_ec" class="form-control">
                                        <option value="">SELECCIONE...</option>
                                        <option value="EJECUTADA">EJECUTADA</option>
                                        <option value="ENVIADA">ENVIADA</option>
                                        <option value="APROBADA">APROBADA</option>
                                        <option value="CONFIGURADO">CONFIGURADO</option>
                                        <option value="PENDIENTE">PENDIENTE</option>
                                        <option value="CERRADA">CERRADA</option>
                                        <option value="NO APLICA">NO APLICA</option>
                                    </select>
                                </td>
                                <td>
                                    <textarea name="observaciones" id="observaciones_ec" rows="2"></textarea>
                                </td>
                            </tr>`;
                                break;

                            case 'fecha_aprobacion_coc':
                                optionSelect += `<option class="optH" value="APROBACION COTIZACION">APROBACION COTIZACION</option>`;
                                tabla += `<tr>
                                <td>
                                    <ul class="timeline timeline-jhon">
                                        <li class="timeline-item">
                                            <div class="timeline-badge" id="act_ac">${numCampo}</div>
                                        </li>
                                    </ul>
                                </td>
                                <td><span>APROBACIÓN COTIZACIÓN OC</span></td>
                                <td>
                                    <input type="date" name="f_compromiso" value="${fecha}" id="f_compromiso_ac" class="form-control fechas_hitos">
                                </td>
                                <td>
                                    <select name="estado" id="estado_ac" class="form-control">
                                        <option value="">SELECCIONE...</option>
                                        <option value="EJECUTADA">EJECUTADA</option>
                                        <option value="ENVIADA">ENVIADA</option>
                                        <option value="APROBADA">APROBADA</option>
                                        <option value="CONFIGURADO">CONFIGURADO</option>
                                        <option value="PENDIENTE">PENDIENTE</option>
                                        <option value="CERRADA">CERRADA</option>
                                        <option value="NO APLICA">NO APLICA</option>
                                    </select>
                                </td>
                                <td>
                                    <textarea name="observaciones" id="observaciones_ac" rows="2"></textarea>
                                </td>
                            </tr>`;
                                break;

                            case 'fecha_ingenieria_detalle':
                                optionSelect += `<option class="optH" value="SOLICITUD INFORMACIÓN TECNICA">SOLICITUD INFORMACIÓN TÉCNICA</option>`;
                                tabla += `<tr>
                                <td>
                                    <ul class="timeline timeline-jhon">
                                        <li class="timeline-item">
                                            <div class="timeline-badge" id="act_sit">${numCampo}</div>
                                        </li>
                                    </ul>
                                </td>
                                <td><span>SOLICITUD INFORMACIÓN TÉCNICA</span></td>
                                <td>
                                    <input type="date" name="f_compromiso" value="${fecha}" id="f_compromiso_sit" class="form-control fechas_hitos">
                                </td>
                                <td>
                                    <select name="estado" id="estado_sit" class="form-control">
                                        <option value="">SELECCIONE...</option>
                                        <option value="EJECUTADA">EJECUTADA</option>
                                        <option value="ENVIADA">ENVIADA</option>
                                        <option value="APROBADA">APROBADA</option>
                                        <option value="CONFIGURADO">CONFIGURADO</option>
                                        <option value="PENDIENTE">PENDIENTE</option>
                                        <option value="CERRADA">CERRADA</option>
                                        <option value="NO APLICA">NO APLICA</option>
                                    </select>
                                </td>
                                <td>
                                    <textarea name="observaciones" id="observaciones_sit" rows="2"></textarea>
                                </td>
                            </tr>`;
                                break;

                            case 'fecha_ejecucion_obra_civil':
                                optionSelect += `<option class="optH" value="VISITA EJECUCION OBRA CIVIL">VISITA EJECUCION OBRA CIVIL (EOC)</option>`;
                                tabla += ` <tr>
                                <td>
                                    <ul class="timeline timeline-jhon">
                                        <li class="timeline-item">
                                            <div class="timeline-badge" id="act_veoc">${numCampo}</div>
                                        </li>
                                    </ul>
                                </td>
                                <td>
                                    <!--VISITA EJECUCION OBRA CIVIL-->
                                    <select name="tipo_veoc" id="tipo_veoc" class="form-control">
                                        <option value="VISITA EJECUCION OBRA CIVIL">VISITA EJECUCION OBRA CIVIL (EOC)</option>
                                        <option value="VISITA EJECUCION OBRA CIVIL TERCERO">VISITA EJECUCION OBRA CIVIL TERCERO</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="date" name="f_compromiso" value="${fecha}" id="f_compromiso_veoc" class="form-control fechas_hitos">
                                </td>
                                <td>
                                    <select name="estado" id="estado_veoc" class="form-control">
                                        <option value="">SELECCIONE...</option>
                                        <option value="EJECUTADA">EJECUTADA</option>
                                        <option value="ENVIADA">ENVIADA</option>
                                        <option value="APROBADA">APROBADA</option>
                                        <option value="CONFIGURADO">CONFIGURADO</option>
                                        <option value="PENDIENTE">PENDIENTE</option>
                                        <option value="CERRADA">CERRADA</option>
                                        <option value="NO APLICA">NO APLICA</option>
                                    </select>
                                </td>
                                <td>
                                    <textarea name="observaciones" id="observaciones_veoc" rows="2"></textarea>
                                </td>
                            </tr>`;
                                break;

                            case 'fecha_empalmes':
                                optionSelect += `<option class="optH" value="EMPALMES">EMPALMES</option>`;
                                tabla += `<tr>
                                <td>
                                    <ul class="timeline timeline-jhon">
                                        <li class="timeline-item">
                                            <div class="timeline-badge" id="act_empalmes">${numCampo}</div>
                                        </li>
                                    </ul>
                                </td>
                                <td><span>EMPALMES</span></td>
                                <td>
                                    <input type="date" name="f_compromiso" value="${fecha}" id="f_compromiso_empalmes" class="form-control fechas_hitos">
                                </td>
                                <td>
                                    <select name="estado" id="estado_empalmes" class="form-control">
                                        <option value="">SELECCIONE...</option>
                                        <option value="EJECUTADA">EJECUTADA</option>
                                        <option value="ENVIADA">ENVIADA</option>
                                        <option value="APROBADA">APROBADA</option>
                                        <option value="CONFIGURADO">CONFIGURADO</option>
                                        <option value="PENDIENTE">PENDIENTE</option>
                                        <option value="CERRADA">CERRADA</option>
                                        <option value="NO APLICA">NO APLICA</option>
                                    </select>
                                </td>
                                <td>
                                    <textarea name="observaciones" id="observaciones_empalmes" rows="2"></textarea>
                                </td>
                            </tr>`;
                                break;

                            case 'fecha_configuracion':
                                optionSelect += `<option class="optH" value="CONFIGURACION RED CLARO">CONFIGURACION</option>`;
                                tabla += `<tr>
                                <td>
                                    <ul class="timeline timeline-jhon">
                                        <li class="timeline-item">
                                            <div class="timeline-badge" id="act_crc">${numCampo}</div>
                                        </li>
                                    </ul>
                                </td>
                                <td><span>CONFIGURACION</span></td>
                                <td>
                                    <input type="date" name="f_compromiso" value="${fecha}" id="f_compromiso_crc" class="form-control fechas_hitos">
                                </td>
                                <td>
                                    <select name="estado" id="estado_crc" class="form-control">
                                        <option value="">SELECCIONE...</option>
                                        <option value="EJECUTADA">EJECUTADA</option>
                                        <option value="ENVIADA">ENVIADA</option>
                                        <option value="APROBADA">APROBADA</option>
                                        <option value="CONFIGURADO">CONFIGURADO</option>
                                        <option value="PENDIENTE">PENDIENTE</option>
                                        <option value="CERRADA">CERRADA</option>
                                        <option value="NO APLICA">NO APLICA</option>
                                    </select>
                                </td>
                                <td>
                                    <textarea name="observaciones" id="observaciones_crc" rows="2"></textarea>
                                </td>
                            </tr>`;
                                break;

                            case 'fecha_entrega_servicio':
                                optionSelect += `<option class="optH" value="VISITA ENTREGA UM TERCEROS">VISITA ENTREGA DE SERVICIO</option> `;
                                tabla += `<tr>
                                <td>
                                    <ul class="timeline timeline-jhon">
                                        <li class="timeline-item">
                                            <div class="timeline-badge" id="act_veut">${numCampo}</div>
                                        </li>
                                    </ul>
                                </td>
                                <td><span>VISITA ENTREGA DE SERVICIO</span></td>
                                <td>
                                    <input type="date" name="f_compromiso" value="${fecha}" id="f_compromiso_veut" class="form-control fechas_hitos">
                                </td>
                                <td>
                                    <select name="estado" id="estado_veut" class="form-control">
                                        <option value="">SELECCIONE...</option>
                                        <option value="EJECUTADA">EJECUTADA</option>
                                        <option value="ENVIADA">ENVIADA</option>
                                        <option value="APROBADA">APROBADA</option>
                                        <option value="CONFIGURADO">CONFIGURADO</option>
                                        <option value="PENDIENTE">PENDIENTE</option>
                                        <option value="CERRADA">CERRADA</option>
                                        <option value="NO APLICA">NO APLICA</option>
                                    </select>
                                </td>
                                <td>
                                    <textarea name="observaciones" id="observaciones_veut" rows="2"></textarea>
                                </td>
                            </tr>`;
                                break;

                        }
                        numCampo++;
                    }

                });
            }

            $("#actividad_actual").append(optionSelect);
            $("#table_hitos_otp tbody").append(tabla);
        },

        // Muestra los hitos de la ot padre seleccionada
        onClickSaveHitosOtp: function () {
            var vacios = 0;
            $('.fechas_hitos').each(function () {
                if ($(this).val() == '' && $("[data-check='" + $(this).attr('id') + "']").prop('checked') == false) {
                    vacios++;
                }
            });
            // console.log($("#formModalHitosOTP").serializeArray());

            if (vacios == 0) {
                const rows = $(".timeline-badge");
                var infox = {};
                $.each(rows, function (ix, val) {
                    var info = $(val).parents('td').siblings().children();
                    $.each(info, function (i, value) {
                        if (!$(value).is('span')) {
                            if ($(value).attr('id') === 'tipo_voc' || $(value).attr('id') === 'tipo_veoc') {
                                posicion = $(value).val();
                            } else {
                                if (infox[ posicion ] == undefined) {
                                    if ($(value).attr('type') == "checkbox") {
                                        infox[ posicion ] = ($(value).prop('checked')) ? 'no aplica' : 'aplica';
                                    } else {
                                        infox[ posicion ] = [$(value).val()];
                                        // infox[ posicion ] = new Object($(value).val());
                                    }
                                } else {
                                    if ($(value).attr('type') == "checkbox") {
                                        infox[ posicion ].push(($(value).prop('checked')) ? 'no aplica' : 'aplica');
                                    } else {
                                        infox[ posicion ].push($(value).val());
                                    }
                                }
                            }
                        } else {
                            posicion = $(value).html();
                        }
                    });
                });
//                console.log(infox);

                $.post(baseurl + '/OtPadre/c_saveHitosOtp',
                        {
                            idOtp: $('#otpHIto').html(),
                            formulario: JSON.stringify(infox),
                            actividadAct: $('#actividad_actual').val(),
                            obsGeneral: $('#observaciones_genrales').val()
                                    // formulario: $("#formModalHitosOTP").serializeArray()
                        },
                        function (data) {
                            var obj = JSON.parse(data);
                            if (obj.response == 'success') {
                                swal({
                                    position: 'top-end',
                                    type: 'success',
                                    title: obj.msg,
                                    showConfirmButton: false,
                                    timer: 1500
                                });
                                $('#modalHitosOtp').modal('toggle');
                                location.reload();
                            } else {
                                swal(
                                        'Error',
                                        obj.msg,
                                        'warning'
                                        )
                            }

                        });
            } else {
                swal(
                        'Recuerde!',
                        'Para poder Guardar la información debe diligenciar todas las fechas de compromiso',
                        'warning'
                        );
            }


        },
        clean: function (names) {
            let unique = {};
            names.forEach(function (i) {
                if (!unique[i]) {
                    unique[i] = true;
                }
            });
            return Object.keys(unique);
        },
        // muestra las otp seleccionadas dependiendo la tabla
        otp_seleccionadas: function () {
            var tabla = $('ul#pestania').find('li.active').attr('tabla');
            ;
            var record;
            switch (tabla) {
                case 'table_otPadreList':
                    record = vista.table_otPadreList;
                    break;
                case 'table_otPadreListHoy':
                    record = hoy.table_otPadreListHoy;
                    break;
                case 'table_otPadreListVencidas':
                    record = vencidas.table_otPadreListVencidas;
                    break;
                case 'table_list_opc':
                    record = lista.tableOpcList;
                    break;
                case 'table_otPadreListEmails':
                    record = emails.table_otPadreListEmails;
                    break;
                case 'table_reporte_actualizacion':
                    record = reporte_act.table_reporte_actualizacion;
                    break;
            }

            let hay_sel = record.rows({selected: true}).any();// booleanos q indica si hay algo seleccionado
            var seleccionadas = record.rows({selected: true}).data();// los datos de los elem seleccionados
            if (hay_sel) {
                eventos.modalSeleccionadas(seleccionadas);
                // console.log(seleccionadas[0].k_id_ot_padre);
                // console.log("==================");
                // console.log(seleccionadas);
                var cuantas = record.rows({selected: true}).count();
                var ids = [];
                if (cuantas > 1) {
                    for (let i = 0; i < cuantas; i++) {
                        ids.push(seleccionadas[i].k_id_ot_padre);
                    }
                } else {
                    ids.push(seleccionadas[0].k_id_ot_padre);
                }
                // console.log(ids);

                $('#mdl-title-cierre').html(`<b>${cuantas}</b> ORDENES SELECCIONADAS`);
                $('#mdl_cierre').modal('show');
                $.post(baseurl + '/OtPadre/c_getInfoEmailreport', {idsOtp: ids},
                        function (data) {
                            data = JSON.parse(data);
                            // console.log('data:',data);

                            var ids = ['seniorHitos', 'configuracionHitos', 'entregaServicioHitos', 'observacionesHitos'];
                            if (cuantas == 1) {
                                //significa que hay una seleccion
                                if (data != "sin data") {
                                    //si entra aca es porque tiene la fecha de compromiso de linea base o datos en la tabla reporte_info
                                    if (data['fecha_compromiso']) {
                                        //si entra acá es porque la información viene de la linea base
                                        $('#entregaServicioHitos').val(data.fecha_compromiso);
                                        $('#entregaServicioHitos').parents("div.col-sm-10").append(`<b class='vieneDeLineaBase'>Fecha extraída de la fecha de compromiso en línea base</b>`);
                                    } else {
                                        //si entra acá es porque la info viene de la tabla reporte_indo
                                        $('#seniorHitos').val(data['senior']);
                                        $('#configuracionHitos').val(data['nombre_cliente']);
                                        $('#entregaServicioHitos').val(data['f_entrega_servicio']);
                                        $('#observacionesHitos').val(data['observaciones']);
                                    }

                                }//si no entra a ninguno no hace nada porque no tiene nada de info.
                            } else {
                                //entra si hay más de una seleccion

                                //creamos los arrays para almacenar toda la info. de la bd
                                const seniores = [];
                                const nomCliente = [];
                                const f_entregaServicio = [];
                                const obsr = [];
                                const lineabasearr = [];

                                //ESTE EACH LLENA LA INFORMACION A VALIDAR Y LOS PONE EN DISTINTOS ARREGLOS
                                $.each(data, function (i, item) {

                                    if (item['fecha_compromiso']) {
                                        //entra si los datos es igual a la fehca de compromiso de la linea base
                                        lineabasearr.push(item['fecha_compromiso']);

                                        f_entregaServicio.push("se debe eliminar"); // este se debe eliminar es para que entre a la condicional para crear el select

                                        //si es igual a sin data significa que no existe en base de datos
                                    } else if (item != "sin data") {
                                        //si entra acá significa que son datos de la tabla


                                        //estos ifs validan si algún campo está vacío, porque puede que algún campo no esté lleno, pero exista en la tabla reporte_info, es para que no se vayan en null

                                        //crean los arreglos para llenar la informacion del select
                                        if (item['senior'] != null || item['senior'] != undefined) {
                                            seniores.push(item['senior']);
                                        }
                                        if (item['nombre_cliente'] != null || item['nombre_cliente'] != undefined) {
                                            nomCliente.push(item['nombre_cliente']);
                                        }
                                        if (item['f_entrega_servicio'] != null || item['f_entrega_servicio'] != undefined) {
                                            f_entregaServicio.push(item['f_entrega_servicio']);
                                        }
                                        if (item['observaciones'] != null || item['observaciones'] != undefined) {
                                            obsr.push(item['observaciones']);
                                        }
                                    }
                                });






                                //LIMPIA TODOS LOS VALORES QUE SEAN REPETIDOS Y SOLO DEJA UNO DE CADA UNO
                                const fseniores = eventos.clean(seniores);
                                const fnomCliente = eventos.clean(nomCliente);
                                const ff_entregaServicio = eventos.clean(f_entregaServicio);
                                const fobsr = eventos.clean(obsr);

                                //ARMAMOS EL OBJETO PARA ENVIARLO A LA FUCNIÓN DE VALIDACIÓN
                                const todo = {0: fseniores,
                                    1: fnomCliente,
                                    2: ff_entregaServicio,
                                    3: fobsr}


                                eventos.validarIgualesReporteAct(todo, ids, lineabasearr)

                            }
                        });

            } else {
                const toast = swal.mixin({
                    toast: true,
                    position: 'top',
                    showConfirmButton: false,
                    timer: 3000
                });
                toast({
                    type: 'error',
                    title: 'No seleccionaste ninguna fila!'
                });
            }

        },
        //validará qué campos son iguales y cuales no
        validarIgualesReporteAct: function (obj, ids, lineabasearr) {

            //se crea el arreglo que devolverá las opc. del selectInfo en caso que no todas las selecciones sean iguales
            $.each(obj, function (i) {
                //se hace para poder ingresar algo al arreglo normal de fechas y así entre a crear el select
                if (lineabasearr.length > 1 && ids[i] == 'entregaServicioHitos') {
                    obj[i].push("msg");
                }
                //pasa a true si la posicion 1 del array es undefined en el array, cosa que no debería pasar si todas son iguales
                if (obj[i][1] == undefined) {
                    // console.log(ids[i],": todos son iguales ");
                    $('#' + ids[i]).val(obj[i]); //uso los ids en cierta posicion para poder pintar el que es y no todos a la vez
                } else {

                    //ya que entró en el false, se debe limpiar los msg enviados anteriormente para que no se pinten en el select
                    // console.log(ids[i],": no se puede llenar");
                    if (ids[i] == 'entregaServicioHitos') {
                        //se eliminan
                        if (obj[i].includes('se debe eliminar')) {
                            var eliminar = obj[i].indexOf('se debe eliminar');
                            obj[i].splice(eliminar, 1);
                        }
                        if (obj[i].includes('msg')) {
                            var eliminar2 = obj[i].indexOf('msg');
                            obj[i].splice(eliminar2, 1);
                        }
                    }
                    // console.log(Object.values(obj[i]));

                    var input = $('#' + ids[i]); //selecciono el input
                    input.css({"border-color": "#ffc800", "background-color": "#ffd92030"}); //le doy color al input

                    //extraigo el div padre y el hermano para insertar el select y cambiar el tamaño del input
                    var divPadre = input.parents("div.form-group");
                    var divHermano = input.parents("div.col-sm-10");

                    //le cambio el tamaño al input
                    divHermano.addClass("col-sm-7");
                    divHermano.removeClass("col-sm-10");

                    //aparezco el select
                    divPadre.append(`<div class="col-sm-3 borrar">
                        <select class="form-control select${i}" onchange="eventos.changeInput(this,${ids[i]})")>
                          <option value="">Seleccione</option>`);
                    $.each(obj[i], function (ii, valor) {
                        $('.select' + i).append("<option value='" + valor + "'>" + valor + "</option>");
                    });
                    divPadre.append(`
                        </select>
                      </div>`);

                }
                //valido si se deben aggregar al select fechas de la linea base
                if (lineabasearr.length > 0 && ids[i] == 'entregaServicioHitos') {

                    //las agrego
                    $('.select' + i).append("<optgroup class='lbopt' label='fechas de línea base' >");
                    $.each(lineabasearr, function (ii, valor) {
                        $('.select' + i).append("<option class='lbopt' value='" + valor + "'>" + valor + "</option>");
                    });

                    if (lineabasearr.length == 1) {
                        //si de todas las selecciones sólo hay una linea base y las demás están vacías, entra acá para pintar la fecha
                        // console.log(lineabasearr);
                        $('#' + ids[i]).val(lineabasearr);
                    }

                }
            })
        },

        changeInput: function (elemento, vall) {
            var valor = elemento.value;
            $(vall).val(valor);
        },

        modalSeleccionadas: function (data) {
            if (eventos.table_selected) {
                var tabla = eventos.table_selected;
                tabla.clear().draw();
                tabla.rows.add(data);
                tabla.columns.adjust().draw();
                return;
            }

            eventos.table_selected = $('#table_selected').DataTable(eventos.configTableSelect(data, [
                {title: "Ingeniero", data: "ingeniero"},
                {title: "OTP", data: "k_id_ot_padre"},
                {title: "Cliente", data: "n_nombre_cliente"},
                {title: "Tipo", data: "orden_trabajo"},
                {title: "Servicio", data: "servicio"},
                {title: "Estado OTP", data: "estado_orden_trabajo"},
                {title: "Lista", data: "lista_observaciones"},
                {title: "Observación", data: "observacion"},
                {title: "Quitar", data: eventos.getButtonQuitar},
            ]));

        },

        configTableSelect: function (data, columns, onDraw) {
            return {
                data: data,
                columns: columns,
                "language": {
                    "url": baseurl + "/assets/plugins/datatables/lang/es.json"
                },
                columnDefs: [{
                        defaultContent: "",
                        targets: -1,
                        orderable: false,
                    }],
                order: [[3, 'asc']],
                drawCallback: onDraw,
                "createdRow": function (row, data, dataIndex) {
                    if (data["id_hitos"] == null) {
                        $(row).css("background-color", "#ff000087");
                    }
                    if (data["n_nombre_cliente"] == "BANCO COLPATRIA RED MULTIBANCA COLPATRIA S.A" || data["n_nombre_cliente"] == "BANCO DAVIVIENDA S.A" || data["n_nombre_cliente"] == "SERVIBANCA S.A." /*|| data["n_nombre_cliente"] == "ADCAP Colombia SA Comisionistas de Bolsda"*/) {
                        $(row).css("background-color", "#ffff00b8");
                    }
                },
            }
        },
        // retorna el boton para quitar registro
        getButtonQuitar: function (obj) {
            const button = `<img src="${baseurl}/assets/images/minus.png" alt="quitar" class="quitar_fila"/>`;
            return button;
        },
        // elimina la fila
        quitarFila: function (e) {
            eventos.table_selected.row($(this).parents('tr')).remove().draw();// remover de la tabla modal
            var cuantas = eventos.table_selected.rows().count();
            $('#mdl-title-cierre').html(`<b>${cuantas}</b> ORDENES SELECCIONADAS`);

        },
        ya_se_envio: true,
        //Envia el reporte de actualizacion dependiendo de las OTP seleccionadas
        onClickSendReportUpdate: function () {

            if (eventos.ya_se_envio) {

                var tableSelected = eventos.table_selected.rows().data();
                var clientesSinCorreo = true;
                var ids_otp = [];
                var flag = true;
                var servicios = []
                // console.log(tableSelected);

                tableSelected.each(function (otp) {
                    servicios.push(otp.servicio)
                    ids_otp.push(otp.k_id_ot_padre);
                    if (otp.id_hitos === null) {
                        flag = false;
                    }
                    if (otp["n_nombre_cliente"] == "BANCO COLPATRIA RED MULTIBANCA COLPATRIA S.A" || otp["n_nombre_cliente"] == "BANCO DAVIVIENDA S.A" || otp["n_nombre_cliente"] == "SERVIBANCA S.A.") {
                        clientesSinCorreo = false;
                    }
                });
                //  console.log(servicios);

                if (flag && clientesSinCorreo) {
                    $.post(baseurl + '/OtPadre/saveInfoEmailReport',
                            {
                                ids_otp: ids_otp,
                                senior: $('#seniorHitos').val(),
                                configuracion: $('#configuracionHitos').val(),
                                entregaServicio: $('#entregaServicioHitos').val(),
                                observaciones: $('#observacionesHitos').val(),
                                servicios: servicios,
                            }, function (data) {
                        //no necesita hacer nada
                    });
                    helper.alertLoading('Enviando Email...', 'Por favor espere.');
                    $.post(baseurl + '/OtPadre/c_sendReportUpdateV2',
                            {
                                ids_otp: ids_otp,
                                senior: $('#seniorHitos').val(),
                                configuracion: $('#configuracionHitos').val(),
                                entregaServicio: $('#entregaServicioHitos').val(),
                                observaciones: $('#observacionesHitos').val()
                            },
                            function (data) {
                                swal.close();
                                var obj = JSON.parse(data);
                                swal({
                                    title: (obj.success) ? 'OK' : 'Error',
                                    html: (obj.success) ? 'Correo enviado' : 'Error',
                                    type: (obj.success) ? 'success' : 'error',
                                    // confirmButtonColor: '#3085d6',
                                    // confirmButtonText: 'OK!',
                                    allowOutsideClick: false // al darle clic fuera se cierra el alert
                                }).then((respuesta) => {
                                    if (respuesta.value) {
                                        location.reload()
                                    }
                                });
                                $('#mdl_cierre').modal('toggle');
                            });
                } else {
                    swal(
                            'Recuerde!',
                            'No se puede enviar el email sin haber diligenciado los hitos de los registros marcados en rojo, tampoco se puede enviar correo de los registros en amarillo',
                            'warning'
                            );
                }
                eventos.ya_se_envio = false;
                setTimeout(function () {
                    eventos.ya_se_envio = true;
                }, 3000);

            }

        },
        //limpia el formulario, colores y selects aparecidos en el reporte de act.
        cleanFormReportUpdate: function () {
            $("#formEmail")[0].reset();
            $("#seniorHitos, #configuracionHitos, #entregaServicioHitos, #observacionesHitos").css({"border-color": "#ccc",
                "background-color": "white"});
            $("div.borrar").remove();
            $("div.col-sm-7").addClass("col-sm-10");
            $("div.col-sm-7").removeClass("col-sm-7");
            $("b.vieneDeLineaBase").remove();
        },

        limpiarLogs: function () {
            $("li#liLogHistory").addClass("active")
            $("#tab_log").addClass("active").addClass("in");
            $("#tabLogReportInit, #tabLogReportAct, #liLogReportInit, #liLogReporAct").removeClass("active").removeClass("in");
        },
        
        onChangeStatusHitos: function (hito) {
            var select = '';
            var check = '';
            switch (hito) {
                case 'voc':
                    select = 'n_estado_voc';
                    check = 'no_aplica_voc';
                    break;
                
                case 'eoc':
                    select = 'estado_eoc';
                    check = 'no_aplica_eoc';
                    break;
                    
                case 'em':
                    select = 'estado_em';
                    check = 'no_aplica_em';
                    break;
                
                case 'entrega':
                    select = 'estado_entrega_servicio';
                    check = 'no_aplica_entrega';
                    break;
            }
            
            if ($('#' + select).val() == 'NO APLICA') {
                $("#" + check).prop( "checked", true);
            } else {
                $("#" + check).prop( "checked", false);
            }
        }
        
        
    };
    eventos.init();

// ******************************************TABLA QUE TRAER TODAS LAS OTHS DE UNA OTP SELECCIONADA ***************************

    listoth = {

        init: function () {
            listoth.events();
            //listoth.getothofothp();
        },
        //Eventos de la ventana.
        events: function () {
            // al darle clic al boton de opciones traiga el modal
            $('#contenido_tablas').on('click', 'a.btnoths', listoth.onClickShowModal);

        },

        onClickShowModal: function () {
            var aLinkLog = $(this);
            var trParent = aLinkLog.parents('tr');
            var tabla = aLinkLog.parents('table').attr('id');
            var record;
            switch (tabla) {
                case 'table_otPadreList':
                    record = vista.table_otPadreList.row(trParent).data();
                    break;
                case 'table_otPadreListHoy':
                    record = hoy.table_otPadreListHoy.row(trParent).data();
                    break;
                case 'table_otPadreListVencidas':
                    record = vencidas.table_otPadreListVencidas.row(trParent).data();
                    break;
                case 'table_list_opc':
                    record = lista.tableOpcList.row(trParent).data();
                    break;
                case 'table_otPadreListEmails':
                    record = emails.table_otPadreListEmails.row(trParent).data();
                    break;
                case 'table_reporte_actualizacion':
                    record = reporte_act.table_reporte_actualizacion.row(trParent).data();
                    break;
            }

            listoth.showModalOthDeOthp(record);
        },

        getothofothp: function (obj) {
            //metodo ajax (post)
            $.post(baseurl + '/OtPadre/c_getOthOfOtp',
                    {
                        idOtp: obj.k_id_ot_padre
                    },
                    // funcion que recibe los datos
                            function (data) {
                                // convertir el json a objeto de javascript
                                var obj = JSON.parse(data);
                                listoth.printTable(obj);
                            }
                    );
                },

        // Muestra modal con todas las ots hija de la otp seleccionada
        showModalOthDeOthp: function (data) {
            listoth.getothofothp(data);
            // resetea el formulario y lo deja vacio
            document.getElementById("formModalOTHS").reset();
            //pinta el titulo del modal y cambia dependiendo de la otp seleccionada
            $('#myModalLabel').html('<strong> Lista OTH de la OTP N.<span id="NroOTPSelect">' + data.k_id_ot_padre + '</span></strong>');
            $('#modalOthDeOtp').modal('show');
        },
        //pintar tabla
        printTable: function (data) {
            //funcion para limpiar el modal
            if (listoth.table_oths_otp) {
                var tabla = listoth.table_oths_otp;
                tabla.clear().draw();
                tabla.rows.add(data);
                tabla.columns.adjust().draw();
                return;
            }

            // nombramos la variable para la tabla y llamamos la configuiracion
            listoth.table_oths_otp = $('#table_oths_otp').DataTable(listoth.configTable(data, [

                {title: "OTH", data: "id_orden_trabajo_hija"},
                {title: "Tipo OTH", data: "ot_hija"},
                {title: "Estado OTH", data: "estado_orden_trabajo_hija"},
                {title: "Recurrente", data: "MRC"},
                {title: "Fecha Compromiso", data: "fecha_compromiso"},
                {title: "Fecha Programacion", data: "fecha_programacion"},
                {title: "Opc", data: listoth.getButtonsOth},
            ]));
        },
        // Datos de configuracion del datatable
        configTable: function (data, columns, onDraw) {
            return {
                data: data,
                columns: columns,
                //lenguaje del plugin
                /*"language": {
                 "url": baseurl + "assets/plugins/datatables/lang/es.json"
                 },*/
                columnDefs: [{
                        defaultContent: "",
                        targets: -1,
                        orderable: false,
                    }],
                order: [[0, 'asc']],
                drawCallback: onDraw
            }
        },
        getButtonsOth: function (obj) {
            var botones = '<div class="btn-group" style="display: inline-flex;">';
            botones += '<a class="btn btn-default btn-xs ver-det btn_datatable_cami" title="Editar Oth"><span class="fa fa-fw fa-edit"></span></a>';
            // este era el botón privado de cada oth
            // if (obj.function != 0) {
            //     if (obj.c_email > 0) {
            //         botones += '<a class="btn btn-default btn-xs ver-log btn_datatable_cami" title="Historial"><span class="fa fa-fw">' + obj.c_email + '</span></a>';
            //     } else {
            //         botones += '<a class="btn btn-default btn-xs ver-log btn_datatable_cami" title="Historial"><span class="fa fa-fw fa-info"></span></a>';
            //     }
            // }

            botones += '</div>';
            return botones;
        }
    };
    listoth.init();

    //*********************************** lista las  ot padres con emails enviados
    // emails = {
    //     init: function() {
    //         emails.events();
    //         emails.getListOtsOtPadreEmail();

    //     },
    //     //Eventos de la ventana.
    //     events: function() {

    //     },
    //     getListOtsOtPadreEmail: function() {
    //         //metodo ajax (post)
    //         $.post(baseurl + '/OtPadre/c_getListOtsOtPadreEmail',
    //                 {
    //                     //parametros

    //                 },
    //                 // funcion que recibe los datos
    //                         function(data) {
    //                             // convertir el json a objeto de javascript
    //                             var obj = JSON.parse(data);
    //                             emails.printTableEmail(obj);
    //                         }
    //                 );
    //             },
    //     printTableEmail: function(data) {
    //         // nombramos la variable para la tabla y llamamos la configuiracion
    //         emails.table_otPadreListEmails = $('#table_otPadreListEmails').DataTable(emails.configTableEmail(data, [
    //             {title: "Ot Padre", data: "k_id_ot_padre"},
    //             {title: "Nombre Cliente", data: "n_nombre_cliente"},
    //             {title: "Tipo", data: "orden_trabajo"},
    //             {title: "Servicio", data: "servicio"},
    //             {title: "Estado OT Padre", data: "estado_orden_trabajo"},
    //             {title: "Fecha Programación", data: "fecha_programacion"},
    //             {title: "Fecha Compromiso", data: "fecha_compromiso"},
    //             {title: "Fecha Creación", data: "fecha_creacion"},
    //             {title: "Ingeniero", data: "ingeniero"},
    //             {title: "Lista", data: gral.listaObservaciones, visible: false},
    //             {title: "Observaciónes dejadas", data: gral.inputObservaciones, visible: false},
    //             {title: "Recurrente", data: "MRC", visible: false},
    //             {title: "ultimo envio", data: gral.cant_dias_ultimo_reporte, visible: false},
    //             {title: "Opc", data: vista.getButtonsOTP},
    //         ]));
    //     },
    //     // Datos de configuracion del datatable
    //     configTableEmail: function(data, columns, onDraw) {
    //         return {
    //             initComplete: function() {
    //                 $('#table_otPadreListEmails tfoot th').each(function() {
    //                     $(this).html('<input type="text" placeholder="Buscar" />');
    //                 });
    //                 var r = $('#table_otPadreListEmails tfoot tr');
    //                 r.find('th').each(function() {
    //                     $(this).css('padding', 8);
    //                 });
    //                 $('#table_otPadreListEmails thead').append(r);
    //                 $('#search_0').css('text-align', 'center');

    //                 // DataTable
    //                 var table = $('#table_otPadreListEmails').DataTable();

    //                 // Apply the search
    //                 table.columns().every(function() {
    //                     var that = this;

    //                     $('input', this.footer()).on('keyup change', function() {
    //                         if (that.search() !== this.value) {
    //                             that.search(this.value).draw();
    //                         }
    //                     });
    //                 });
    //             },
    //             // Este callback se ejecuta cada vex que hay cambio de pagina, ordenamiento, o cambio en cantidad de registros a mostrar
    //             // o un cambio especifico en la pagina
    //             fnInfoCallback: function( oSettings, iStart, iEnd, iMax, iTotal, sPre ) {
    //                 // $('#table_otPadreListEmails .cod_resolucion').selectize();
    //             },
    //             data: data,
    //             columns: columns,
    //             "language": {
    //                 "url": baseurl + "/assets/plugins/datatables/lang/es.json"
    //             },
    //             dom: 'Blfrtip',
    //             buttons: [
    //                 {
    //                     text: 'Excel <span class="fa fa-file-excel-o"></span>',
    //                     className: 'btn-cami_cool',
    //                     extend: 'excel',
    //                     title: 'ZOLID EXCEL',
    //                     filename: 'zolid ' + fecha_actual
    //                 },
    //                 {
    //                     text: 'Imprimir <span class="fa fa-print"></span>',
    //                     className: 'btn-cami_cool',
    //                     extend: 'print',
    //                     title: 'Reporte Zolid',
    //                 },
    //                 {
    //                     text: '<span class="fa fa-envelope-o" aria-hidden="true"></span> Reporte Actualización',
    //                     className: 'btn-cami_cool btn-rpt_act',
    //                     action: eventos.otp_seleccionadas,
    //                 }
    //             ],
    //             select: true,
    //             "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
    //             ordering: true,
    //             columnDefs: [{
    //                     // targets: -1,
    //                     // visible: false,
    //                     defaultContent: "",
    //                     // targets: -1,
    //                     orderable: false,
    //                 }],
    //             order: [[11, 'desc']],
    //             drawCallback: onDraw
    //         }
    //     }
    // };
    // emails.init();

    //*********************************** lista las ot padres conreporte de actualizacion pendiente para hoy
    reporte_act = {
        init: function () {
            reporte_act.events();
            reporte_act.getOtsPtesPorEnvio('all');
            reporte_act.getCountPtesPorEnvio('all');

        },
        //Eventos de la ventana.
        events: function () {

        },
        getOtsPtesPorEnvio: function (filtro) {
            //metodo ajax (post)
            $.post(baseurl + '/OtPadre/c_getOtsPtesPorEnvio',
                    {
                        //parametros
                        filter: filtro
                    },
                    // funcion que recibe los datos
                            function (data) {
                                // convertir el json a objeto de javascript
                                var obj = JSON.parse(data);
                                reporte_act.printTableReporteAtc(obj.data);
                                $('#badge_cant_report').html(obj.cantidad);
                            }
                    );
                },
        printTableReporteAtc: function (data) {
            // nombramos la variable para la tabla y llamamos la configuiracion
            if (reporte_act.table_reporte_actualizacion) {
                var tabla = reporte_act.table_reporte_actualizacion;
                tabla.clear().draw();
                tabla.rows.add(data);
                tabla.columns.adjust().draw();
                return;
            }
            reporte_act.table_reporte_actualizacion = $('#table_reporte_actualizacion').DataTable(reporte_act.configTableEmail(data, [
                {title: "Ot Padre", data: "k_id_ot_padre"},
                {title: "Nombre Cliente", data: "n_nombre_cliente"},
                {title: "Tipo", data: "orden_trabajo"},
                {title: "Servicio", data: "servicio"},
                {title: "Estado OT Padre", data: "estado_orden_trabajo"},
                {title: "Fecha Programación", data: "fecha_programacion"},
                {title: "Fecha Compromiso", data: "fecha_compromiso"},
                {title: "Fecha Creación", data: "fecha_creacion"},
                {title: "Ingeniero", data: "ingeniero"},
                {title: "Lista", data: "lista_observaciones", visible: false},
                {title: "Observaciónes dejadas", data: "observacion", visible: false},
                {title: "Recurrente", data: "MRC"},
                {title: "ultimo envio", data: gral.cant_dias_ultimo_reporte},
                {title: "Opc", data: vista.getButtonsOTP/**/},
            ]));
        },
        // Datos de configuracion del datatable
        configTableEmail: function (data, columns, onDraw) {
            return {
                initComplete: function () {
                    $('#table_reporte_actualizacion tfoot th').each(function () {
                        $(this).html('<input type="text" placeholder="Buscar" />');
                    });
                    var r = $('#table_reporte_actualizacion tfoot tr');
                    r.find('th').each(function () {
                        $(this).css('padding', 8);
                    });
                    $('#table_reporte_actualizacion thead').append(r);
                    $('#search_0').css('text-align', 'center');

                    // DataTable
                    var table = $('#table_reporte_actualizacion').DataTable();

                    // Apply the search
                    table.columns().every(function () {
                        var that = this;

                        $('input', this.footer()).on('keyup change', function () {
                            if (that.search() !== this.value) {
                                that.search(this.value).draw();
                            }
                        });
                    });
                    $("#table_reporte_actualizacion").css("text-align", "center");
                },
                data: data,
                columns: columns,
                "language": {
                    "url": baseurl + "/assets/plugins/datatables/lang/es.json"
                },
                dom: 'Blfrtip',
                buttons: [
                    {
                        text: 'Total <span class="fa fa-file-excel-o"></span>',
                        className: 'btn-cami_cool btn-rpt_act',
                        action: reporte_act.downloadAllReportAct,
                    },
                    {
                        text: 'Excel <span class="fa fa-file-excel-o"></span>',
                        className: 'btn-cami_cool',
                        extend: 'excel',
                        title: 'ZOLID EXCEL',
                        filename: 'zolid ' + fecha_actual
                    },
                    {
                        text: 'Imprimir <span class="fa fa-print"></span>',
                        className: 'btn-cami_cool',
                        extend: 'print',
                        title: 'Reporte Zolid',
                    },
                    {
                        text: '<span class="fa fa-envelope-o" aria-hidden="true"></span> Reporte Actualización',
                        className: 'btn-cami_cool btn-rpt_act',
                        action: eventos.otp_seleccionadas,
                    },
                ],
                select: true,
                "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
                ordering: true,
                columnDefs: [{
                        // targets: -1,
                        // visible: false,
                        defaultContent: "",
                        // targets: -1,
                        orderable: false,
                    }],
                order: [[11, 'desc']],
                drawCallback: onDraw
            }
        },
        getCountPtesPorEnvio: function (filtro) {
            $.post(baseurl + '/OtPadre/c_getCountPtesPorEnvio', {
                //parametros
                filter: filtro
            },
                    function (data) {
                        var obj = JSON.parse(data);
                        reporte_act.printTableCountPtesPorEnvio(obj);
                    }
            );
        },
        printTableCountPtesPorEnvio: function (data) {

            let html = '';
            for (var i = 0; i < data.length; i += 2) {
                html += `<tr>
                        <td style="text-align:left"><b>${data[i].ingeniero.toLowerCase()} </b></td>
                        <td ${(data[i].menor_7 > 0) ? 'style="background:#00800075"' : ''}>${data[i].menor_7}</td>
                        <td ${(data[i].entre_8_15 > 0) ? 'style="background:#fcff02a6"' : ''}>${data[i].entre_8_15}</td>
                        <td ${(data[i].entre_16_30 > 0) ? 'style="background:#f2a404c7"' : ''}>${data[i].entre_16_30}</td>
                        <td ${(data[i].mayor_30 > 0) ? 'style="background:#ed220073"' : ''}>${data[i].mayor_30}</td>
                        `;
                if (typeof data[i + 1] != 'undefined') {
                    html += `
                        <td style="text-align:left"><b>${data[i + 1].ingeniero.toLowerCase()} </b></td>
                        <td ${(data[i + 1].menor_7 > 0) ? 'style="background:#00800075"' : ''}>${data[i + 1].menor_7}</td>
                        <td ${(data[i + 1].entre_8_15 > 0) ? 'style="background:#fcff02a6"' : ''}>${data[i + 1].entre_8_15}</td>
                        <td ${(data[i + 1].entre_16_30 > 0) ? 'style="background:#f2a404c7"' : ''}>${data[i + 1].entre_16_30}</td>
                        <td ${(data[i + 1].mayor_30 > 0) ? 'style="background:#ed220073"' : ''}>${data[i + 1].mayor_30}</td>
                        `;
                }
                html += `</tr>`;
            }
            $('#tbodyCount').html(html);
        },
        downloadAllReportAct: function () {
            window.location.href = baseurl + "/OtPadre/c_downloadAllReportAct";
        },
        
        resetFormHitos: function () {
            $("#modalHitosOtp input[type='checkbox']").each(function () {
                $(this).prop( "checked", false );
            });
            
            $('#actividad_actual').val('');
            $('#f_voc').val('');
            $('#n_estado_voc').val('');
            $('#n_observaciones_voc').val('');
            $('#f_eoc').val('');
            $('#estado_eoc').val('');
            $('#observaciones_eoc').val('');
            $('#f_em').val('');
            $('#estado_em').val('');
            $('#observaciones_em').val('');
            $('#f_entrega_servicio').val('');
            $('#estado_entrega_servicio').val('');
            $('#observaciones_entrega_servicio').val('');
            $('#observaciones_genrales').val('');
        }
    };
    reporte_act.init();

});
