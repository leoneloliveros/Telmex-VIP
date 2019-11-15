$(function() {
    formulario = {
        flag_img: true,
        init: function() {
            formulario.events();
        },
        //Eventos de la ventana.
        events: function() {
            $('#btn_mostrar_detalle').click(formulario.toggle_info_detail); //toggle info
            $('.cerrar').on('click', formulario.clearModal); // limpiar formulario
            $('#table_oths_otp').on('click', 'a.ver-det', formulario.onClickShowModalEditOTH);
            // funcion para formulario tabs vertical
            $("div.bhoechie-tab-menu>div.list-group>a").on('click', formulario.onClickTab);
            // funcion para multiselect
            $('.multiselect_forms').multiselect();
            // funcion para duplicar la seccion al darle click al add del form
            $('#formModal').on('click', 'span#añadir_seccion', e => {
                helper.duplicar_seccion($('#seccion_duplidar'), $('#append_aca'));
            });

            // funcion para duplicar referencia y cantidad
            $('#formModal').on('click', 'span#añadir_seccion_ref_cant', function() {
                helper.duplicar_seccion($('#duplicar_ref_cant'), $('#aca_ref_cant'));
            })
            $(".span_fecha_compromiso,.span_fecha_voc,.span_fecha_programacion,.span_fecha_dcoc,.span_fecha_aprobacion_coc,.span_fecha_configuracion,.span_fecha_ingenieria_detalle,.span_fecha_equipos,.span_fecha_ejecucion_obra_civil,.span_fecha_entrega_servicio,.span_fecha_empalmes").on('click',function(){
                    let span = $(this);
                    if (span.hasClass('activeDate')) {
                        span.removeClass('activeDate');
                        span.addClass('inActiveDate');
                        if (span.siblings().attr('id') === "lb_fecha_voc" ) {
                            formulario.deshabilitarFechas(true);
                        }                        
                        span.children('i').removeClass('fa-calendar-check-o');
                        span.children('i').addClass('fa-calendar-times-o');
                        span.siblings().attr('disabled',true);
                        span.siblings().css('color', "#eeeeee");
                    }else{
                        if (span.siblings().attr('id') === "lb_fecha_voc") {
                            formulario.deshabilitarFechas(false);
                        }
                        span.children('i').removeClass('fa-calendar-times-o');
                        span.children('i').addClass('fa-calendar-check-o');
                        span.removeClass('inActiveDate');
                        span.addClass('activeDate');
                        span.siblings().attr('disabled',false);
                        span.siblings().css('color',"#000");
                    }
                formulario.calcularLineaBase();
                 })

            // funcion para remover seccion del form con el boton menos
            $('#formModal').on('click', 'span.remover_seccion', helper.remover_seccion);

            //validacion al darle click al boton actualizar del formulario
            $('#btnUpdOt').on('click', formulario.validarFormulario);
            // evento del check para mpls fomr origen form destino
            $('#formModal').on('change', 'input#checking', formulario.toggle_origen_destino);

            $('#btnTodayDateCalculate').on('click', function(){
                $('#lb_fecha_cierreKo').val(get_hoy);
                formulario.calcularLineaBase();
            });
            $('#lb_fecha_cierreKo').on('change', formulario.calcularLineaBase);
            $('#modalEditTicket').on('blur','input#pr_cant_servicios_trasladar,input#pr_cod_servicio_trasladar,#pr_cod_servicios_trasladar',formulario.llenarCamposTraslado);
        },

        deshabilitarFechas: function(activeOrInactive){

            if (activeOrInactive) {
                var disabled = true;
                var colLetter = '#eeeeee';
                var removeClass = 'fa-calendar-check-o';
                var addClass = 'fa-calendar-times-o'
                var remove = 'activeDate';
                var add = 'inActiveDate';
            }else{
                var disabled = false;
                var colLetter = '#000';
                var removeClass = 'fa-calendar-times-o';
                var addClass = 'fa-calendar-check-o'
                var remove = 'inActiveDate';
                var add = 'activeDate';
            }

            $("#lb_fecha_dcoc,#lb_fecha_aprobacion_coc,#lb_fecha_ejecucion_obra_civil,#lb_fecha_empalmes").attr('disabled', disabled);
            $("#lb_fecha_dcoc,#lb_fecha_aprobacion_coc,#lb_fecha_ejecucion_obra_civil,#lb_fecha_empalmes").css('color', colLetter);
            $("#lb_fecha_dcoc,#lb_fecha_aprobacion_coc,#lb_fecha_ejecucion_obra_civil,#lb_fecha_empalmes").siblings().children('i').removeClass(removeClass);
            $("#lb_fecha_dcoc,#lb_fecha_aprobacion_coc,#lb_fecha_ejecucion_obra_civil,#lb_fecha_empalmes").siblings().children('i').addClass(addClass);
            $("#lb_fecha_dcoc,#lb_fecha_aprobacion_coc,#lb_fecha_ejecucion_obra_civil,#lb_fecha_empalmes").siblings().removeClass(remove);
            $("#lb_fecha_dcoc,#lb_fecha_aprobacion_coc,#lb_fecha_ejecucion_obra_civil,#lb_fecha_empalmes").siblings().addClass(add);
        },
        // formulario tabs vertical
        onClickTab: function(e) {
            e.preventDefault();
            $(this).siblings('a.active').removeClass("active");
            $(this).addClass("active");
            var index = $(this).index();
            $("div.bhoechie-tab-content").removeClass("active");
            $("div.bhoechie-tab-content").eq(index).addClass("active");
            if ($(this).attr("id") === 'contentAll') {
                $("div.bhoechie-tab-content").addClass("active");
            }
            $('.cmb-control').prop('disabled', false).trigger('selectfilled');
            $('.cmb-factor-riesgo').prop('disabled', false).trigger('selectfilled');
        },
        // show or hide informacion del formulario del modal
        toggle_info_detail: function() {
            $(".toggle_info_detail").toggle("slow", function() {
                let image = $('#btn_mostrar_detalle img');
                if (formulario.flag_img) {
                    image.attr('src', baseurl + '/assets/images/minus.png');
                    formulario.flag_img = false;
                } else {
                    image.attr('src', baseurl + '/assets/images/plus.png');
                    formulario.flag_img = true;
                }
            });
        },

        //******************* formulario de edicion oth ***************************************//
        onClickShowModalEditOTH: function() {
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
                    
                case 'table_oths_otp':
                    var pestania = $('ul#pestania').find('li.active').attr('tabla');
                    if (pestania == 'table_reporte_actualizacion') {
                        record = listoth.table_oths_otp.row(trParent).data();
                    } else {
                        var script = $('ul#pestania').find('li.active').attr('script');
                        switch (script) {
                            case 'scripPlus':
                                record = scripPlus.table_oths_otp.row(trParent).data();
                                break;
                            
                            case 'scripPlus2':
                                record = scripPlus2.table_oths_otp.row(trParent).data();
                                break;
                            
                            case 'scripPlus3':
                                record = scripPlus3.table_oths_otp.row(trParent).data();
                                break;
                            
                            case 'scripPlus4':
                                record = scripPlus4.table_oths_otp.row(trParent).data();
                                break;   
                        }
                    }
                break;
            }
            // mostrar modal y llenar el formulario de info
            formulario.fillFormModalForm(record);
        },

        //llenamos los input del modal con la informacion a la q le dio click
        fillFormModalForm: function(data) {
            $.post(baseurl + '/OtHija/c_fillmodals', {
                idOth: data.id_orden_trabajo_hija // parametros que se envian
            }, function(registro) {
                // limpiar el formulario...
                $('#general_servicio').html("");
                $('#k_id_estado_ot').html("");
                $('#num_servicio').val("");
                // formulario.cambiar_required_linea_base(false)// quitar requerido a form linea base
                $.each(registro, function(i, item) {
                    $('#' + i).val(item);
                });
                // valores calculados
                $('#k_id_estado_ot_value').val(registro.k_id_estado_ot);
                $('#id_ot_modal_edit_oth').text(registro.id_orden_trabajo_hija);
                // ocultar select se servicio y mostrar modal
                $('.ins_servicio').hide();
                // formulario.calcularLineaBase();
                $('#modalEditTicket').modal('show');
                // llear el select
                formulario.fillSelect(registro.k_id_tipo, registro.k_id_estado_ot, registro.i_orden);
                // si el tipo de la ot es KO validamos el select por si es cerrado
                if (registro.k_id_tipo == 1) {
                    $('#k_id_estado_ot').on('change', function() {
                        //argumentos para pasar a los templates
                        const arg = {
                            otp: registro.k_id_ot_padre
                        };
                        formulario.cierreKickOf(registro.n_nombre_cliente, registro.direccion_destino, arg);
                    });
                }
            });
        },

        // mostrar select de servicios
        cierreKickOf: function(nombre_cliente, direccion_destino, arg) {
            $('#general_servicio').html("");
            // si se lecciona la opcion cerrada
            if ($('#k_id_estado_ot').val() == 3) {
                $('#btnUpdOt').attr('disabled', true);
                // mostrar el select de servicios
                $('.ins_servicio').show();
                // al seleccionar uno de los servicios
                $('#ins_servicio').on('change', function() {
                    formulario.cambiarOpcionesForm(nombre_cliente, direccion_destino, arg);
                    $('.multiselect_forms').multiselect();
                });
            }
            // si no se elige cerrada se resetean todos los valores
            else {
                $('#num_servicio').val("");
                $('.ins_servicio').hide();
                $('#btnUpdOt').attr('disabled', false);
                $('#tabs_form').hide();
                $('#general_servicio').html('');
                $('#ins_servicio').val('');
                // formulario.cambiar_required_linea_base(false); // quitar requerido a linea base
            }
        },
        // al elegir el servicio se llena el formulario correspondiente
        cambiarOpcionesForm: function(nombre_cliente, direccion_destino, arg) {
            formulario.get_eingenieer();
            const servicio_seleccionado = $('#ins_servicio').val();
            $('#num_servicio').val(servicio_seleccionado);
            if (servicio_seleccionado == '') {
                $('#tabs_form').hide();
                $('#general_servicio').html('');
                $('#btnUpdOt').attr('disabled', true); // Se desactiva el boton de actualizar
            } else {
                $('#btnUpdOt').attr('disabled', false); // se activa el boton de actualizar
                $('#tabs_form').show();
                const servicio_nombre = $("#ins_servicio option:selected").html();
                const form_servicio = setForm.returnFormularyService(nombre_cliente, direccion_destino, servicio_seleccionado, servicio_nombre, arg);
                const form_producto = setForm.returnFormularyProduct(servicio_seleccionado, arg);
                // pinto el formulario de servicio
                $('#general_servicio').html(form_servicio);
                $('#general_producto').html(form_producto);
                // dejar requeridos los campos de linea base
                // formulario.cambiar_required_linea_base(true);
                formulario.get_eingenieer(); // lenar los selects con los ingenieros actuales
                formulario.llenarInfoIngeniero(); // llena la informacion en los input de los inge seleccionados
            }
            // si el servicio es traslado interno, pintará el nombre del servicio en el input traslado servicio del reporte de inicio
            if (servicio_seleccionado == '16') {
                $("#campo8").val($("#servicio_val").val());
            }
        },
        //limpia el modal cada vez que se cierra
        clearModal: function() {
            $('#formModal')[0].reset();
            $("label.error").remove();
            $('#num_servicio').val("");
            $('.ins_servicio').hide();
            $('#btnUpdOt').attr('disabled', false);
            $('#tabs_form').hide();
            $('#general_servicio').html('');
            $('#ins_servicio').val('');
        },
        fillSelect: function(idtipo, val_estado, orden) {
            $.ajaxSetup({
                async: false
            });
            $.post(baseurl + "/User/c_getStatusByType", {
                idtipo: idtipo
            }, function(data) {

                // Decodifica el objeto traido desde el controlador
                var status = JSON.parse(data);
                // Pinto el select de estado
                $.each(status, function(i, item) {
                    if (val_estado == item.k_id_estado_ot) {
                        $('.llenarEstadosJS').append('<option value="' + item.k_id_estado_ot + '" selected>' + item.n_name_estado_ot + '</option>');
                    } else {
                        if (parseInt(item.i_orden) < parseInt(orden)) {
                            $('.llenarEstadosJS').append('<option value="' + item.k_id_estado_ot + '" disabled>' + item.n_name_estado_ot + '</option>');
                        } else {
                            $('.llenarEstadosJS').append('<option value="' + item.k_id_estado_ot + '">' + item.n_name_estado_ot + '</option>');
                        }
                    }
                });
            });
        },
        // lllenar la informacion del ingeniero seleccionado
        llenarInfoIngeniero: function() {
            $('#ingeniero1').on('change', formulario.fill_information);
            $('#ingeniero2').on('change', formulario.fill_information);
            $('#ingeniero3').on('change', formulario.fill_information);
        },
        // Metodo para validar formularios al darle click en actualizar
        validarFormulario: function() {
            var val_estado = $('#num_servicio').val();
            if ($("#k_id_estado_ot").val() == 3) {

                let msj = false;
                // const mail = $('#ingeniero1_email').val();
                // const mail1 = $('#mail_envio').val();
                // const expresiones = /\w+@\w+\.+[a-z]/;
                const inputs = $('.validar_required');
                $.each(inputs, function(i, input) {
                    if (input.value == '') {
                        msj = true;
                        input.style.boxShadow = "0 0 5px rgba(253, 1, 1)";
                    } else {
                        input.style.boxShadow = "none";
                    }
                });
                if (msj) {
                    helper.miniAlert('!Estos campos son requeridos¡');
                    return false;
                }
                /*validar el formato del correo*/
                // if (!expresiones.test(mail) || !expresiones.test(mail1)) {
                //     swal('Error', 'El formato del correo está mal', 'error');
                //     return false;
                // }
                const email_user = helper.inSession('n_mail_user');

                swal({
                    title: "Desea Guardar?",
                    html: (val_estado <= 30) ? `La información se enviará al correo <br> <b>${email_user}</b>` : '',
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, Continuar!',
                    cancelButtonText: 'Cancelar!',
                }).then((continuar) => {

                    if (continuar.value) {
                        // valida si la opcion no tiene reporte reinicio(correo) y la dijire a otro controlador,
                        // el cual solo actualiza la oth y no envia correo
                        if (val_estado ==30 || val_estado ==31) {

                            $('#formModal').attr('action', baseurl + '/Templates/c_updateStatusOtEspeciales');
                            $('#btnUpdOt').attr('disabled', false);
                            $('.inActiveDate').siblings().attr("type","text");
                            $('.inActiveDate').siblings().val(null);
                            $('.inActiveDate').siblings().attr("disabled",false);
                            $('#formModal').submit();

                        } else {
                            $('.inActiveDate').siblings().attr("type","text");
                            $('.inActiveDate').siblings().val(null);
                            $('.inActiveDate').siblings().attr("disabled",false);
                            $('#formModal').submit();
                        }
                        
                    } else {
                        helper.miniAlert();
                        return false;
                    }
                });
            } else {
                $('.inActiveDate').siblings().attr("type","text");
                $('.inActiveDate').siblings().val(null);
                $('.inActiveDate').siblings().attr("disabled",false);
                $('#formModal').submit();
            }

        },
        //llena el select de ingeniero
        get_eingenieer: function() {
            $.post(baseurl + '/User/c_get_eingenieer', {}, function(data) {
                var ingeniero = JSON.parse(data);
                $.each(ingeniero, function(i, item) {
                    $('.class_fill_eingenieer').append('<option data-tel="' + item.telefono + '" data-email="' + item.mail + '" value="' + item.nombre + '" >' + item.nombre + '</option>');
                });
            });
        },
        fill_information: function(event) {
            var ing = event.target.id;
            $('#' + ing + '_tel').val($(this).find(':selected').data('tel'));
            $('#' + ing + '_email').val($(this).find(':selected').data('email'));
        },
        // cambia el required del formulario de linea base segun lo que le pasemos de argumento
        cambiar_required_linea_base: function(bool) {
            const inputs = $('#general_linea_base input');
            $.each(inputs, function(i, input) {
                if (bool) {
                    input.setAttribute('required', true);
                } else {
                    input.removeAttribute('required');
                }
            });
        },
        // cambia segun el interruptor entre origen form y destino
        toggle_origen_destino: function() {
            var val_estado = $('#num_servicio').val();

            let checkeado = this.checked;
            if (checkeado) {
                if (val_estado == 24) {
                    $('#mpls_punto_origen').html(setForm.formProduct_private_line_origen());

                } else {
                    $('#mpls_punto_origen').html(setForm.formProduct_mpls_form_origen());
                }
                $('#pestana_puto_origen').show(300);
                $('#is_origen').val('1');
            } else {
                $('#mpls_punto_origen').html("");
                $('#pestana_puto_origen').hide(300);
                $('#pestana_punto_destino').click();
                $('#is_origen').val('0');
            }

        },
        //************ fin formulario de edicion oth ***************//
        // calcula la linea base de acuerdo a la fecha de cierre de KO
        calcularLineaBase: function() {
            const fecha_cierreKO = $('#lb_fecha_cierreKo').val();

            var comodin = fecha_cierreKO;

            const fecha_compromiso = formulario.calcular_compromiso(fecha_cierreKO);
            if ($("#lb_fecha_compromiso").siblings().hasClass("activeDate")) {
                comodin = fecha_compromiso;
            }
            $('#lb_fecha_compromiso').val(fecha_compromiso);

            $('#lb_fecha_programacion').val(fecha_compromiso);
            $('#lb_fecha_entrega_servicio').val(fecha_compromiso);
            // $('#lb_fecha_programacion').val(comodin);
            // $('#lb_fecha_entrega_servicio').val(comodin);
            const fecha_empalmes = formulario.calcular_nueva_fecha(fecha_compromiso, -2);
            $('#lb_fecha_empalmes').val(fecha_empalmes);
            // ============================= DEPENDER DE FECHA COMPROMISO


            const fecha_voc = formulario.calcular_nueva_fecha(fecha_cierreKO, 2);
            if ($("#lb_fecha_voc").siblings().hasClass("activeDate")) {
                comodin = fecha_voc;
                $(".comodin").val(comodin);
            } else {
                comodin = fecha_cierreKO; 
                $(".comodin").val(comodin);
            }
                $('#lb_fecha_voc').val(comodin);

            const fecha_dcoc = formulario.calcular_nueva_fecha(comodin, 2);

            if ($("#lb_fecha_dcoc").siblings().hasClass("activeDate")) {
                comodin = fecha_dcoc;
                $(".comodin").val(comodin);
            } else { 
                if ($("#lb_fecha_voc").siblings().hasClass("activeDate")) {
                    comodin = fecha_voc;
                }else{
                    comodin = fecha_cierreKO;
                }
            }

            $('#lb_fecha_dcoc').val(comodin);
            
            const fecha_aprobacion_coc = formulario.calcular_nueva_fecha(comodin, 3);
            
            if ($("#lb_fecha_aprobacion_coc").siblings().hasClass("activeDate")) {
                comodin = fecha_aprobacion_coc;
            } else {
                if ($("#lb_fecha_dcoc").siblings().hasClass("activeDate")) {
                    comodin = fecha_dcoc;
                }else{
                    if ($("#lb_fecha_voc").siblings().hasClass("activeDate")) {
                        comodin = fecha_voc;
                    }else{
                        comodin = fecha_cierreKO;
                    }
                }
            }
            $('#lb_fecha_aprobacion_coc').val(comodin);

            var comodin2 = comodin;

            const fecha_ingenieria_detalle = formulario.calcular_nueva_fecha(comodin, 2);

            if ($("#lb_fecha_ingenieria_detalle").siblings().hasClass("activeDate")) {
                comodin = fecha_ingenieria_detalle;
            } else {
                if ($("#lb_fecha_aprobacion_coc").siblings().hasClass("activeDate")) {
                    comodin = fecha_aprobacion_coc;
                } else {
                    if ($("#lb_fecha_dcoc").siblings().hasClass("activeDate")) {
                        comodin = fecha_dcoc;
                    } else {
                        if ($("#lb_fecha_voc").siblings().hasClass("activeDate")) {
                            comodin = fecha_voc;
                        } else {
                            comodin = fecha_cierreKO;
                        }
                    }
                }
            }

            $('#lb_fecha_ingenieria_detalle').val(comodin);


            // const fecha_configuracion = formulario.calcular_nueva_fecha(comodin, ($("#lb_fecha_ingenieria_detalle").siblings().hasClass("activeDate"))? 3 : 1 );
            const fecha_configuracion = formulario.calcular_nueva_fecha(comodin, 1 );
            if ($("#lb_fecha_configuracion").siblings().hasClass("activeDate")) {
                comodin = fecha_configuracion;
            } else {
                if ($("#lb_fecha_ingenieria_detalle").siblings().hasClass("activeDate")) {
                    comodin = fecha_ingenieria_detalle;
                } else {
                    if ($("#lb_fecha_aprobacion_coc").siblings().hasClass("activeDate")) {
                        comodin = fecha_aprobacion_coc;
                    } else {
                        if ($("#lb_fecha_dcoc").siblings().hasClass("activeDate")) {
                            comodin = fecha_dcoc;
                        } else {
                            if ($("#lb_fecha_voc").siblings().hasClass("activeDate")) {
                                comodin = fecha_voc;
                            } else {
                                comodin = fecha_cierreKO;
                            }
                        }
                    }

                }
            }
            $('#lb_fecha_equipos').val(fecha_configuracion);
            $('#lb_fecha_configuracion').val(comodin);

            const fecha_ejecucion_obra_civil = formulario.calcular_nueva_fecha(comodin2, 5);
            if ($("#lb_fecha_ejecucion_obra_civil").siblings().hasClass("activeDate")) {
                comodin2 = fecha_ejecucion_obra_civil;
            }else{
                if ($("#lb_fecha_aprobacion_coc").siblings().hasClass("activeDate")) {
                    comodin2 = fecha_aprobacion_coc;
                } else {
                    if ($("#lb_fecha_dcoc").siblings().hasClass("activeDate")) {
                        comodin2 = fecha_dcoc;
                    } else {
                        if ($("#lb_fecha_voc").siblings().hasClass("activeDate")) {
                            comodin2 = fecha_voc;
                        } else {
                            comodin2 = fecha_cierreKO;
                        }
                    }
                }
            }
            $('#lb_fecha_ejecucion_obra_civil').val(comodin2);
            
            
            
        },
        //retorna fecha sumando X dias habiles (la fecha base debe tener el formato yyyy-mm-dd)
        calcular_nueva_fecha: function(fecha_base, dias_h) {

            if (dias_h > 0) {
                while (dias_h > 0) {
                    fecha_base = helper.formatDate(helper.sumar_o_restar_dias_a_fecha(fecha_base));
                    if (!helper.validar_domingo_festivo(fecha_base)) {
                        dias_h--;
                    }
                }
            } else {
                while (dias_h < 0){
                    fecha_base = helper.formatDate(helper.sumar_o_restar_dias_a_fecha(fecha_base, -1));
                    if (!helper.validar_domingo_festivo(fecha_base)) {
                        dias_h++;
                    }                    
                }
            }
            return fecha_base;
        }, 

         
        // Calcular la fecha de compromiso (caso especial)
        calcular_compromiso: function(fecha_base){
            const consultaDias = document.getElementById('consultaDias').value;
            let DiasDeConsulta;
            if(consultaDias  ==  "" || consultaDias == null){
                DiasDeConsulta = 20;
            } else{
                DiasDeConsulta = consultaDias;
            }
            const veinte = helper.sumar_o_restar_dias_a_fecha(fecha_base, DiasDeConsulta);
            const numDiaSem = veinte.getDay();
            if (numDiaSem == 6) {
                return helper.formatDate(helper.sumar_o_restar_dias_a_fecha(helper.formatDate(veinte), -1));
            } else if(helper.validar_domingo_festivo(helper.formatDate(veinte))){
                // calcular siguiente fecha habil
                return formulario.calcular_nueva_fecha(helper.formatDate(veinte), 1)
            } else {
                return helper.formatDate(veinte);
            }
        },
        // llena los campos de cantidad de servicios a trasladar y codigos de servicios a trasladar del reporte de inicio con la info del form. cierre KO
        llenarCamposTraslado: function(){
            const tipoTraslado = $("#ins_servicio").val();
            const cantidadsServTrasladar = $("#pr_cant_servicios_trasladar").val();
            //si el servicio es traslado externo, entra al 15, 
            if (tipoTraslado =='15'){
                let codigosServTrasladar = $("#pr_cod_servicio_trasladar").val();
                //les damos el valor a cada input 
                $("#campo7").val(cantidadsServTrasladar);
                $("#campo9").val(codigosServTrasladar);
            } else {
                // si no es 15, entrará al else, el traslado interno
                let codigosServTrasladar = $("#pr_cod_servicios_trasladar").val();
                //les damos el valor a cada input 
                $("#campo6").val(cantidadsServTrasladar);
                $("#campo7").val(codigosServTrasladar);
            }
        },
    };
    formulario.init();
});

