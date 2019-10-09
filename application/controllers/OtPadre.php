<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class OtPadre extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('data/Dao_user_model');
        $this->load->model('data/Dao_ot_padre_model');
        $this->load->model('data/Dao_ot_hija_model');
        $this->load->model('data/Dao_email_model');
        $this->load->model('data/Dao_cierre_ots_model');
    }

    // carga la vista para como vamos ot padre
    public function view_otp() {
        if (!Auth::check()) {
            Redirect::to(URL::base());
        }
        $data['last_time'] = $this->Dao_ot_hija_model->get_last_time_import();
        $data['cantidad'] = $this->Dao_ot_hija_model->getCantUndefined();
        $data['ingenieros'] = $this->Dao_user_model->get_eng_trabajanding();
        $data['title'] = '¿Cómo vamos OTP?'; // cargar el  titulo en la pestaña de la pagina para otp
        $this->load->view('parts/headerF', $data);
        $this->load->view('moduleOtp');
        $this->load->view('parts/footerF');
    }

    //trae los contadores de cantidades en tiempos y fuera de tiempos y hoy
    public function in_today_out() {
        $general = $this->Dao_ot_hija_model->get_ots_times(); // consulta de todas las ots_hijas y sus tiempos
        $total_reg = count($general);
        // array para descartar los estados ya ejecutados
        $estados_arr = ['Cancelada', 'Cerrada', '3- Terminada'];

        // Contadores generales
        $cont_total_in_otp = 0;
        $cont_total_out_otp = 0;
        $cont_total_hoy_otp = 0;
        $cont_total_ejec_otp = 0;
        $cont_total_otp = 0;
        // fin contadores generales
        $otp_ejecutadas = []; // array para contar las otp ejecutadas
        $ingenieros = []; // objetro final
        $x = 0;

        // recorrer los registros
        for ($i = 0; $i < $total_reg; $i++) {
            // CReamos el indice del ingeniero para otp si no existe
            if (!isset($ingenieros[$general[$i]->k_id_user])) {
                $ingenieros[$general[$i]->k_id_user] = [];
                $ingenieros[$general[$i]->k_id_user]['out'] = 0;
                $ingenieros[$general[$i]->k_id_user]['in'] = 0;
                $ingenieros[$general[$i]->k_id_user]['hoy'] = 0;
                $ingenieros[$general[$i]->k_id_user]['all'] = 0;
                $ingenieros[$general[$i]->k_id_user]['ejecutadas'] = [];
            }

            // validamos si la oth esta en alguno de los estados ejecutados ('Cancelada','Cerrada','3- Terminada')
            if (!in_array($general[$i]->estado_orden_trabajo_hija, $estados_arr)) {
                // validar si oth está fuera de times
                if ($general[$i]->tiempo > 0) {
                    // se valida si el index de la otp ya fue creada dentro del array del ingeniero
                    if (!array_key_exists($general[$i]->k_id_ot_padre, $ingenieros[$general[$i]->k_id_user])) {
                        // si no se crea la posicion de la otp donde va la iteracion
                        $ingenieros[$general[$i]->k_id_user][$general[$i]->k_id_ot_padre] = array('time' => 1, "cliente" => $general[$i]->n_nombre_cliente);
                        // se alteran contadores
                        $ingenieros[$general[$i]->k_id_user]['out'] ++;
                        $cont_total_out_otp++;
                        $cont_total_otp++;
                    } else {
                        // si existe
                        // Se valida de que time viene si es
                        // SIEMPRE PREDOMINA EL FUERA DE TIEMPOS, POR ESO SE CAMBIA SIEMPRE A 1 (FUERA DE TIEMPO),
                        // porque con una oth que venga fuera de tiempos toda la otp queda fuera de tiempos
                        switch ($ingenieros[$general[$i]->k_id_user][$general[$i]->k_id_ot_padre]['time']) {
                            //1 estaba en fuera de tiempo
                            case '1':
                                // no se hace nada, viene del mismo fuera de tiempos
                                break;
                            //0 estaba en hoy
                            case '0':
                                // el time se cambia a uno, y se modifican contadores
                                $ingenieros[$general[$i]->k_id_user][$general[$i]->k_id_ot_padre]['time'] = 1;
                                $ingenieros[$general[$i]->k_id_user]['out'] ++;
                                $ingenieros[$general[$i]->k_id_user]['hoy'] --;
                                $cont_total_out_otp++;
                                $cont_total_hoy_otp--;
                                break;
                            //-1 viene de en tiempos
                            case '-1':
                                // tambien se cambia el time
                                $ingenieros[$general[$i]->k_id_user][$general[$i]->k_id_ot_padre]['time'] = 1;
                                $ingenieros[$general[$i]->k_id_user]['out'] ++;
                                $ingenieros[$general[$i]->k_id_user]['in'] --;
                                $cont_total_out_otp++;
                                $cont_total_in_otp--;
                                break;
                        }
                    }
                }

                // validar si oth está para now
                if ($general[$i]->tiempo == 0) {
                    // se valida si el index de la otp ya fue creada dentro del array del ingeniero
                    if (!array_key_exists($general[$i]->k_id_ot_padre, $ingenieros[$general[$i]->k_id_user])) {
                        // si no se crea la posicion de la otp donde va la iteracion   time se deja en 0 (hoy)
                        $ingenieros[$general[$i]->k_id_user][$general[$i]->k_id_ot_padre] = array('time' => 0, "cliente" => $general[$i]->n_nombre_cliente);
                        $ingenieros[$general[$i]->k_id_user]['hoy'] ++;
                        $cont_total_otp++;
                        $cont_total_hoy_otp++;
                    }

                    // si time viene en -1 (en tiempos) se deja en 0 (hoy)
                    // porque con una oth que para hoy toda la otp queda para hoy (siempre y cuando no tenga ningna en fuera de tiempos * JERARQUIA)
                    else if ($ingenieros[$general[$i]->k_id_user][$general[$i]->k_id_ot_padre]['time'] == -1) {

                        $ingenieros[$general[$i]->k_id_user][$general[$i]->k_id_ot_padre]['time'] = 0;
                        $ingenieros[$general[$i]->k_id_user]['hoy'] ++;
                        $ingenieros[$general[$i]->k_id_user]['in'] --;
                        $cont_total_hoy_otp++;
                        $cont_total_in_otp--;
                    }
                }

                if ($general[$i]->tiempo < 0) {
                    // se valida si el index de la otp ya fue creada dentro del array del ingeniero
                    if (!array_key_exists($general[$i]->k_id_ot_padre, $ingenieros[$general[$i]->k_id_user])) {
                        $ingenieros[$general[$i]->k_id_user][$general[$i]->k_id_ot_padre] = array('time' => -1, "cliente" => $general[$i]->n_nombre_cliente);
                        ;
                        $ingenieros[$general[$i]->k_id_user]['in'] ++;
                        $cont_total_in_otp++;
                        $cont_total_otp++;
                    }
                }

                // *************************************inicio calculo de color de los botones*************************************
                if ($ingenieros[$general[$i]->k_id_user]['out'] > 0) {
                    $ingenieros[$general[$i]->k_id_user]['color'] = "btn_red";
                } else if ($ingenieros[$general[$i]->k_id_user]['hoy'] > 0) {
                    $ingenieros[$general[$i]->k_id_user]['color'] = "btn_orange";
                } else {
                    $ingenieros[$general[$i]->k_id_user]['color'] = "btn_green";
                }
                // *************************************fin calculo colores*************************************
                // Si entra en este bloque la ot padre no tiene que estar en el arreglo de ejecutadas
                if (in_array($general[$i]->k_id_ot_padre, $ingenieros[$general[$i]->k_id_user]['ejecutadas'])) {
                    // si existe se elimina del array y se decrementas los contadores
                    $ingenieros[$general[$i]->k_id_user]['ejecutadas'] = array_values(array_diff($ingenieros[$general[$i]->k_id_user]['ejecutadas'], array($general[$i]->k_id_ot_padre)));
                    $cont_total_otp--;
                    $cont_total_ejec_otp--;
                }
                // Si está cerrada finalizada o cancelada
            } else {
                // si la otp no existe en el array de ejecutadas y tampoco se ha creado en el array del ingeniero la posicion de la otp
                if (!in_array($general[$i]->k_id_ot_padre, $ingenieros[$general[$i]->k_id_user]['ejecutadas']) && !array_key_exists($general[$i]->k_id_ot_padre, $ingenieros[$general[$i]->k_id_user])) {
                    // se inserta la otp en el array de ejecutadas para al final ser contadas cuantas ejecutó ese ingeniero
                    array_push($ingenieros[$general[$i]->k_id_user]['ejecutadas'], $general[$i]->k_id_ot_padre);

                    // se incrementas los contadores de total y general ejecutadas
                    $cont_total_otp++;
                    $cont_total_ejec_otp++;
                }
            }

            // se suman contadores  para obtener el total de otp
            $ingenieros[$general[$i]->k_id_user]['all'] = $ingenieros[$general[$i]->k_id_user]['in'] + $ingenieros[$general[$i]->k_id_user]['hoy'] + $ingenieros[$general[$i]->k_id_user]['out'] + count($ingenieros[$general[$i]->k_id_user]['ejecutadas']);
            // cuantas otp ejecuto ese ingeniero
            $ingenieros[$general[$i]->k_id_user]['cont_ejec'] = count($ingenieros[$general[$i]->k_id_user]['ejecutadas']);
        }

        // Seccion para el tratamiento de la grafica ppalo
        $grafics = [];

        $grafics['g_inges'] = [];
        $grafics['g_in'] = [];
        $grafics['g_hoy'] = [];
        $grafics['g_out'] = [];
        $grafics['g_all'] = [];
        $n_i = []; // nombre ingeniero

        $array_list_inge = $this->Dao_user_model->get_eng_trabajanding();
        for ($i = 0; $i < count($array_list_inge); $i++) {
            $n_i[$array_list_inge[$i]->k_id_user] = $array_list_inge[$i]->nombre;
        }


        // seccion para las grafica
        foreach ($ingenieros as $cc => $value) {
            array_push($grafics['g_inges'], $n_i[$cc]);
            array_push($grafics['g_in'], $value['in']);
            array_push($grafics['g_hoy'], $value['hoy']);
            array_push($grafics['g_out'], $value['out']);
            array_push($grafics['g_all'], $value['all']);
        }


        $retorno = array(
            'cant_otp' => $cont_total_otp,
            'cant_in' => $cont_total_in_otp,
            'cant_hoy' => $cont_total_hoy_otp,
            'cant_out' => $cont_total_out_otp,
            'cant_exce' => $cont_total_ejec_otp,
            'ing' => $ingenieros,
            'grafics' => $grafics
        );


        echo json_encode($retorno);
    }

    public function managementOtp() {
        if (!Auth::check()) {
            Redirect::to(URL::base());
        }
        
        /*Datatables server site*/
        /*
        $this->load->library('Datatables');
        
        $condicion = " ";
        if (Auth::user()->n_role_user == 'ingeniero') {
            $usuario_session = Auth::user()->k_id_user;
            $condicion = " WHERE otp.k_id_user = $usuario_session ";
        } else {
            $condicion = " WHERE `user`.n_group = 'GESTION OTS PROYECTOS'";
        }
        
        date_default_timezone_set("America/Bogota");
        $fecha_actual = date('Y-m-d');
        
        $ListOtPadre_table = $this->datatables->init();
        $ListOtPadre_table->query("
            SELECT
                otp.k_id_ot_padre,
                otp.n_nombre_cliente,
                otp.orden_trabajo,
                (SELECT COUNT(id_ot_padre) FROM reporte_info WHERE id_ot_padre = otp.k_id_ot_padre) AS MAIL_enviados,
                otp.servicio,
                REPLACE (otp.estado_orden_trabajo, 'otp_cerrada', 'Cerrada') AS estado_orden_trabajo,
                otp.fecha_programacion,
                otp.fecha_compromiso,
                otp.fecha_creacion,
                otp.k_id_user,
                `user`.n_name_user,
                CONCAT( `user`.n_name_user, ' ', `user`.n_last_name_user ) AS ingeniero,
                otp.lista_observaciones,
                otp.observacion,
                IFNULL(SUM( oth.c_email ),0) AS cant_mails,
                hitos.id_hitos,
                otp.finalizo,
                otp.ultimo_envio_reporte,
                CONCAT('$ ', FORMAT(oth.monto_moneda_local_arriendo + oth.monto_moneda_local_cargo_mensual, 2)) AS MRC,
                (SELECT COUNT(oth2.nro_ot_onyx) FROM ot_hija oth2 WHERE otp.k_id_ot_padre = oth2.nro_ot_onyx) AS cant_oths,
                (SELECT IF(oth2.direccion_origen = '', oth2.alias_enlace, oth2.direccion_origen) FROM ot_hija oth2 WHERE otp.k_id_ot_padre = oth2.nro_ot_onyx limit 1) AS direccion,
                (SELECT oth2.ciudad FROM ot_hija oth2 WHERE otp.k_id_ot_padre = oth2.nro_ot_onyx limit 1) AS ciudad
            FROM ot_hija oth
            RIGHT JOIN ot_padre otp ON oth.nro_ot_onyx = otp.k_id_ot_padre
            INNER JOIN `user` ON otp.k_id_user = `user`.k_id_user
            LEFT JOIN hitos ON hitos.id_ot_padre = otp.k_id_ot_padre
            $condicion
            GROUP BY
            otp.k_id_ot_padre ",
            "otp.k_id_ot_padre*
            otp.n_nombre_cliente*
            otp.orden_trabajo*
            otp.servicio*
            estado_orden_trabajo*
            otp.fecha_programacion*
            otp.fecha_compromiso*
            otp.fecha_creacion*
            ingeniero*
            MRC*
            otp.lista_observaciones*
            MAIL_enviados*
            otp.k_id_user*
            n_name_user*
            cant_mails*
            hitos.id_hitos*
            otp.finalizo*
            direccion*
            ciudad*
            otp.observacion*
            otp.ultimo_envio_reporte*
            cant_oths*");
        
        $ListOtPadre_table->set_options('dom',"'Blfrtip'")
                        ->set_options('scrollY','500')
                        ->set_options('scrollX', '0')
                        ->set_options('columnDefs','[
                            {
                              "targets": [ 9 ],
                              "visible": false
                            },
                            {
                              "targets": [ 10 ],
                              "visible": false
                            },
                            {
                              "targets": [ 11 ],
                              "visible": false
                            },
                            {
                              "targets": [ 12 ],
                              "visible": false
                            },
                            {
                              "targets": [ 13 ],
                              "visible": false
                            },
                            {
                              "targets": [ 14 ],
                              "visible": false
                            },
                            {
                              "targets": [ 15 ],
                              "visible": false
                            },
                            {
                              "targets": [ 16 ],
                              "visible": false
                            },
                            {
                              "targets": [ 17 ],
                              "visible": false
                            },
                            {
                              "targets": [ 18 ],
                              "visible": false
                            },
                            {
                              "targets": [ 19 ],
                              "visible": false
                            },
                            {
                              "targets": [ 20 ],
                              "visible": false
                            },
                            {
                              "targets": [ 21 ],
                              "visible": false
                            },
                            {
                              "targets": [ 22 ],
                              "visible": false
                            }
                            ]')
                        ->set_options('bFilter','true')
                        ->set_options('initComplete','function activar(){
                                    scripPlus.init();
                                    $("#table_otPadreList").click(function(){
                                        $(".btnoths ").off(\'click\');
                                        $(".hitos-otp").off(\'click\');
                                        scripPlus.events();
                                    });
                                }')
                        ->set_options('scroller','{
                            loadingIndicator: true
                        }')
//                        ->set_options('dom','Blfrtip')
                        ->set_options('buttons',"[
                                                {
                                                    text: 'Excel <span class=\"fa fa-file-excel-o\"></span>',
                                                    className: 'btn-cami_cool',
                                                    extend: 'excel',
                                                    title: 'ZOLID EXCEL',
                                                    filename: 'zolid - ' + $fecha_actual
                                                },
                                                {
                                                    text: 'Imprimir <span class=\"fa fa-print\"></span>',
                                                    className: 'btn-cami_cool',
                                                    extend: 'print',
                                                    title: 'Reporte Zolid',
                                                },
                                                {
                                                    text: '<span class=\"fa fa-envelope-o\" aria-hidden=\"true\"></span> Reporte Actualización',
                                                    className: 'btn-cami_cool btn-rpt_act',
//                                                    action: eventos.otp_seleccionadas,
                                                }
                                        ]")
                        ->set_options('"createdRow"','function(row, data, dataIndex) {
                                if (data[21] == 0) {
                                    $(row).css("background-color", "#f50e0e69");
                                }
                            }')
                        ->set_options('"drawCallback"','function( settings, json){
                                    queryValue = settings["json"]["query"];
                                }')
                        ->set_options('select','true');

        $ListOtPadre_table->delimitador("where");
        $ListOtPadre_table
                ->style(array(
                    'class' => 'table table-striped table-bordered dataTable_camilo',
                ))
                ->column('Ot Padre', 'k_id_ot_padre')//0
                ->column('Nombre Cliente', 'n_nombre_cliente')//1
                ->column('Tipo', 'orden_trabajo')//2
                ->column('Servicio', 'servicio')//3
                ->column('Estado OT Padre', 'estado_orden_trabajo')//4
                ->column('Fecha Programación', 'fecha_programacion')//5
                ->column('Fecha Compromiso', 'fecha_compromiso')//6
                ->column('Fecha Creación', 'fecha_creacion')//7
                ->column('Ingeniero', 'ingeniero')//8
                ->column('Recurrente', 'MRC')//9--
                ->column('Lista', 'lista_observaciones')//10--
                ->column('Mails Enviados', 'MAIL_enviados')//11--
                ->column('Id Usuario', 'k_id_user')//12--
                ->column('Nombre Usuario', 'n_name_user')//13--
                ->column('Cantidad Mails', 'cant_mails')//14--
                ->column('Id Hitos', 'id_hitos')//15--
                ->column('Finalización', 'finalizo')//16--
                ->column('Dirección', 'direccion')//17--
                ->column('Ciudad', 'ciudad')//18--
                ->column('Observacion', 'observacion')//19--
                ->column('Ultimo Reporte Enviado', 'ultimo_envio_reporte')//20--
                ->column('Cantidad OTH', 'cant_oths')//21--
                ->column('Observaciónes dejadas', 'observacion', function($data, $row){
//                    echo '<pre>';var_dump($row['observacion']);echo '</pre>';
                    $observacion = ($row['observacion'] == null) ? '' : $row['observacion'];
                    $input = "<textarea class=\"obs_cod_resolucion\" spellcheck=\"false\">$observacion</textarea>";
                    return $input;
                })//22
                ->column('ultimo envio', 'ultimo_envio_reporte', function($data, $row){
                    $inicio = strtotime(date('Y-m-d'));
                    $fin = strtotime($row['ultimo_envio_reporte']);
                    $dif = $inicio - $fin;
                    $diasFalt = (( ( $dif / 60 ) / 60 ) / 24);
                    return ceil($diasFalt);
                })//23
                ->column('No. OTHs', 'cant_oths', function($data, $row){
                    $num = '';
                    if ($row['cant_oths'] != 0) {
                        $num .= "<span class=\"styleNum\" title=\" " . $row['cant_oths'] . " OTHs Asociadas\" style=\"text-align: center;\">" . $row['cant_oths'] . "</span>";
                    } else {
                        $num .= "<span class=\"styleNum noOTHs\" title=\"sin OTHs\" style=\"text-align: center;\">" . $row['cant_oths'] . "</span>";
                    }
                    return $num;
                })//24
                ->column('Opc', 'cant_oths', function($data, $row){
                    $span = '';
                    $title = '';
                    $cierreKo = '';
                    $icon = '';
                    $reportInicio = ''; //si tiene reporte de inicio y tiene emails enviados

                    //si existe una OTP con contador de reportes enviados, aparecerá, de lo contrario, pondrá el icono del ojo
                    if ($row['MAIL_enviados']) {
                        if ($row['MAIL_enviados'] != 0) {
                            $reportInicio = ($row['cant_mails'] != 0) ? "<span class='fa fa-fw '>| &nbsp" . $row['cant_mails'] . "</span> <span style='color: #7eec7c;' class='fa fa-check-circle'  aria-hidden='true'></span>" : '';
                            $span = "<span class='fa fa-fw'>" . $row['MAIL_enviados'] . "</span>";
                            $icon = "<span class='fa fa-envelope' aria-hidden='true' style='color: #fff700;'></span>";
                            $title = ($row['MAIL_enviados'] == 1) ? $row['MAIL_enviados'] . " correo enviado" : $row['MAIL_enviados'] . " correos enviados";
                        } else if ($row['cant_mails'] != 0) {
                            $span = "<span class='fa fa-fw '>" . $row['cant_mails'] . "</span>";
                            $reportInicio = "<span style='color: #7eec7c;' class='fa fa-check-circle' aria-hidden='true'></span>";
                            $title = ($row['cant_mails'] == 1) ? $row['cant_mails'] . " correo enviado" : $row['cant_mails'] . " correos enviados";
                        } else {
                            $span = "<span class='fa fa-fw fa-eye'></span>";
                            $title = "ver OT Hijas";
                        }
                    } else {
                        //SI no es reporte de act. entra acá, pero si lo es, entrará arriba
                        if ($row['cant_mails'] != 0) {
                            $span = "<span class='fa fa-fw '>" . $row['cant_mails'] . "</span>";
                            $title = ($row['cant_mails'] == 1) ? $row['cant_mails'] . " correo enviado" : $row['cant_mails'] . " correos enviados";
                        } else {
                            $span = "<span class='fa fa-fw fa-eye'></span>";
                            $title = "ver OT Hijas";
                        }
                    }
                    if ($row['finalizo'] != null) {
                        $cierreKo = "<a class='btn btn-default btn-xs product-otp btn_datatable_cami' data-btn='cierreKo' title='Ver Detalle Cierre KO'><span class='fa fa-fw fa-info-circle'></span></a>";
                    }

                    $color = ($row['id_hitos']) ? 'clr_lime' : '';
                    $botones = "<div class='btn-group-vertical' style=''>"
                            . "<a class='btn btn-default btn-xs btnoths btn_datatable_cami' title='" . $title . "'>" . $icon . $span . $reportInicio . "</a>"
                            // + "<a class='btn btn-default btn-xs edit-otp btn_datatable_cami' title='Editar Ots'><span class='glyphicon glyphicon-save'></span></a>"
                            . "<a class='btn btn-default btn-xs hitos-otp btn_datatable_cami' data-btn='hito' title='Hitos Ots'><span class='glyphicon glyphicon-header " . $color . "'></span></a>"
                            . $cierreKo
                            . "</div>";
                    return $botones;
                })//25
                ;
        $ListOtPadre_table->script("
                            <script type=\"text/javascript\" defer=\"defer\">
                                $(function () {
                                    scripPlus = {
                                        init: function() {
                                            scripPlus.events();
                                        },
                                        events: function() {
                                            $('#contenido_tablas').on('click', 'a.btnoths', scripPlus.onClickShowModal);
                                            $('#contenido_tablas').on('click', 'a.hitos-otp', scripPlus.onClickBtnCloseOtp);
                                            $('#contenido_tablas').on('click', 'a.product-otp', scripPlus.onClickBtnCloseOtp);
                                        },
                                        onClickShowModal: function () {
                                            var aLinkLog = $(this);
                                            var trParent = aLinkLog.parents('tr');
                                            var tabla = aLinkLog.parents('table').attr('id');
                                            var record;
                                            switch (tabla) {
                                                case 'table_otPadreList':
//                                                    record = vista.table_otPadreList.row(trParent).data();
                                                    record = erTable_table_otPadreList.row(trParent).data();
                                                    break;
                                                case 'table_otPadreListHoy':
//                                                    record = hoy.table_otPadreListHoy.row(trParent).data();
                                                    record = erTable_table_otPadreListHoy.row(trParent).data();
                                                    break;
                                                case 'table_otPadreListVencidas':
//                                                    record = vencidas.table_otPadreListVencidas.row(trParent).data();
                                                    record = erTable_table_otPadreListVencidas.row(trParent).data();
                                                    break;
                                                case 'table_list_opc':
//                                                    record = lista.tableOpcList.row(trParent).data();
                                                    record = erTable_tableOpcList.row(trParent).data();
                                                    break;
                                                case 'table_otPadreListEmails':
//                                                    record = emails.table_otPadreListEmails.row(trParent).data();
                                                    record = erTable_table_otPadreListEmails.row(trParent).data();
                                                    break;
                                                case 'table_reporte_actualizacion':
//                                                    record = reporte_act.table_reporte_actualizacion.row(trParent).data();
                                                    record = erTable_table_reporte_actualizacion.row(trParent).data();
                                                    break;
                                            }

                                            scripPlus.showModalOthDeOthp(record);
                                        },
                                        showModalOthDeOthp: function (data) {
                                            scripPlus.getothofothp(data);
                                            // resetea el formulario y lo deja vacio
                                            document.getElementById(\"formModalOTHS\").reset();
                                            //pinta el titulo del modal y cambia dependiendo de la otp seleccionada
                                            $('#myModalLabel').html('<strong> Lista OTH de la OTP N.<span id=\"NroOTPSelect\">' + data[0] + '</span></strong>');
                                            $('#modalOthDeOtp').modal('show');
                                        },
                                        getothofothp: function (obj) {
                                            //metodo ajax (post)
                                            $.post(baseurl + '/OtPadre/c_getOthOfOtp',
                                                    {
                                                        idOtp: obj[0]
                                                    },
                                                    // funcion que recibe los datos
                                                            function (data) {
                                                                // convertir el json a objeto de javascript
                                                                var obj = JSON.parse(data);
                                                                scripPlus.printTable(obj);
                                                            }
                                                    );
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
                                            $(\"#form_cierreKo\").html(form);
                                            $('.max-w_border-n').remove();

                                            $.post(baseurl + '/OtPadre/c_getProductByOtp',
                                                    {
                                                        id_otp: data.k_id_ot_padre,
                                                        num_servicio: data.finalizo
                                                    },
                                                    function (data) {
                                                        var obj = JSON.parse(data);
                                                        $.each(obj, function (i, item) {

                                                            var el = $('#pr_' + i);
                                                            el.replaceWith($('<input />').attr({
                                                                type: 'text',
                                                                id: el.attr('id'),
                                                                name: el.attr('name'),
                                                                class: el.attr('class'),
                                                                value: el.val(),
                                                                readonly: true,
                                                                style: 'font-size: 12px;'
                                                            }));
                                                            $('#pr_' + i).val(item);
                                                        });

                                                        if (flag && obj.ciudad_ori == null) {
                                                            $('#seccion_mpls_ori').remove();
                                                        }

                                                        $(\"#mdl_cierreKo #id_ot_padre\").val(obj.id_ot_padre);
                                                        $(\"#mdl_cierreKo #id_ot_padre_ori\").val(obj.id_ot_padre);
                                                        $(\"#mdl_cierreKo #id_ot_padre_des\").val(obj.id_ot_padre);
                                                        $(\"#mdl_cierreKo\").css(\"font-size\", \"12px\");
                                                        $(\"#mdl_cierreKo label\").css(\"width\", \"150px\");
                                                        $(\"#mdl_cierreKo .selectContainer\").css(\"margin-bottom\", \"5px\");
                                                    });

                                            $('#mdl_cierreKo').modal('show');
                                        },
                                        
                                        // Muestra los hitos de la ot padre seleccionada
                                        showModalHitosOthp: function (datax, x) {
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
                                                                        $('#' + i2).prop( \"checked\", true );
                                                                    } else {
                                                                        $('#' + i2).val(item2);
                                                                    }
                                                                });
                                                            });
                                                        }                        
                                                    });
                                            //pinta el titulo del modal y cambia dependiendo de la otp seleccionada
                                            $('#myModalLabelHitos').html('<strong> Hitos de la OTP N.<span id=\"otpHIto\">' + datax.k_id_ot_padre + '</span></strong>');
                                            $('#servivio_hito').html('<strong> OT ' + datax.k_id_ot_padre + ' - ' + datax.servicio + '</strong>');
                                            $('#cliente_hito').html('<strong> CLIENTE: ' + datax.n_nombre_cliente + '</strong>');
                                            $('#ciudad_hito').html('<strong> CIUDAD: ' + datax.ciudad + ' - ' + datax.direccion + '</strong>');
                                            $('#modalHitosOtp').modal('show');
                                        },
                                        
                                        //pintar tabla
                                        printTable: function (data) {
                                            //funcion para limpiar el modal
                                            if (scripPlus.table_oths_otp) {
                                                var tabla = scripPlus.table_oths_otp;
                                                tabla.clear().draw();
                                                tabla.rows.add(data);
                                                tabla.columns.adjust().draw();
                                                return;
                                            }

                                            // nombramos la variable para la tabla y llamamos la configuiracion
                                            scripPlus.table_oths_otp = $('#table_oths_otp').DataTable(scripPlus.configTable(data, [

                                                {title: 'OTH', data: 'id_orden_trabajo_hija'},
                                                {title: 'Tipo OTH', data: 'ot_hija'},
                                                {title: 'Estado OTH', data: 'estado_orden_trabajo_hija'},
                                                {title: 'Recurrente', data: 'MRC'},
                                                {title: 'Fecha Compromiso', data: 'fecha_compromiso'},
                                                {title: 'Fecha Programacion', data: 'fecha_programacion'},
                                                {title: 'Opc', data: scripPlus.getButtonsOth},
                                            ]));
                                        },
                                        // Datos de configuracion del datatable
                                        configTable: function (data, columns, onDraw) {
                                            return {
                                                data: data,
                                                columns: columns,
                                                //lenguaje del plugin
                                                columnDefs: [{
                                                        defaultContent: \"\",
                                                        targets: -1,
                                                        orderable: false,
                                                    }],
                                                order: [[0, 'asc']],
                                                drawCallback: onDraw
                                            }
                                        },
                                        getButtonsOth: function (obj) {
                                            var botones = '<div class=\"btn-group\" style=\"display: inline-flex;\">';
                                            botones += '<a class=\"btn btn-default btn-xs ver-det btn_datatable_cami\" title=\"Editar Oth\"><span class=\"fa fa-fw fa-edit\"></span></a>';
                                            // este era el botón privado de cada oth
                                            // if (obj.function != 0) {
                                            //     if (obj.c_email > 0) {
                                            //         botones += '<a class=\"btn btn-default btn-xs ver-log btn_datatable_cami\" title=\"Historial\"><span class=\"fa fa-fw\">' + obj.c_email + '</span></a>';
                                            //     } else {
                                            //         botones += '<a class=\"btn btn-default btn-xs ver-log btn_datatable_cami\" title=\"Historial\"><span class=\"fa fa-fw fa-info\"></span></a>';
                                            //     }
                                            // }

                                            botones += '</div>';
                                            return botones;
                                        }
                                        
                                    };

                                    scripPlus.init();
                                });
                            </script>");
                
        $this->datatables->create('table_otPadreList', $ListOtPadre_table);
        */                  
        /*Fin Datatables server site*/
        
        $data['cantidad'] = $this->Dao_ot_hija_model->getCantUndefined();
        $data['title'] = 'Work Management OTP';
        $this->load->view('parts/headerF', $data);
        $this->load->view('work_managementOtp');
        $this->load->view('parts/footerF');
    }

    // Retorna las ot padres de un ingeniero
    public function c_get_otp_by_id_user() {
        $inge_id = $this->input->post('iduser');
        $ots = $this->Dao_ot_padre_model->get_otp_by_id_user($inge_id);
        echo json_encode($ots);
    }

    // TABLA QUE TRAE LA INFORMACION DE OTPADRE
    public function c_getListOtsOtPadre() {
        $filtro = $this->input->post("filter");
        $otPadreList = $this->Dao_ot_padre_model->getListOtsOtPadre($filtro);
        $data = array(
            'data' => $otPadreList->result(),
            'cantOTPs' => $otPadreList->num_rows()
        );
        echo json_encode($data);
    }

    //inserta los datos (lista y observaciones )de la vista detalles
    public function update_data() {

        $fecha_actual = new DateTime();
        $ingeniero = Auth::user()->k_id_user;
        $data = array(
            'k_id_ot_padre' => $this->security->xss_clean(strip_tags($this->input->post('id'))),
            'lista_observaciones' => $this->security->xss_clean(strip_tags($this->input->post('lista'))),
            'observacion' => $this->security->xss_clean(strip_tags($this->input->post('observacion'))),
            'fecha_actualizacion' => $fecha_actual->format('Y-m-d'),
            'usuario_actualizacion' => $ingeniero
        );


        $res = $this->Dao_ot_padre_model->update_new_data($data);

        echo json_encode($res);
    }

    // TABLA QUE TRAE LA INFORMACION DE OTPADRE QUE TENGAN FECHA DE COMPROMISO PARA HOY
    public function c_getListOtsOtPadreHoy() {
        $filtro = $this->input->post('filter');
        $otPadreList = $this->Dao_ot_padre_model->getListOtsOtPadreHoy($filtro);
        echo json_encode($otPadreList);
    }

// TABLA QUE TRAE LA INFORMACION DE OTPADRE QUE TENGAN FECHA DE COMPROMISO VENCIDA
    public function c_getListOtsOtPadreVencidas() {
        $filtro = $this->input->post('filter');
        $otPadreList = $this->Dao_ot_padre_model->getListOtsOtPadreVencidas($filtro);
        echo json_encode($otPadreList);
    }

    // Trae registro otp por opcion de lista
    public function c_getOtpByOpcList() {
        $opcion = $this->input->post('opcion');
        $filtro = $this->input->post('filter');
        $otPadreList = $this->Dao_ot_padre_model->getOtpByOpcList($opcion, $filtro);

        echo json_encode($otPadreList);
    }

    // valida si es posible cerrar la ot padre
    public function c_closeOtp() {
        $respuesta = [];
        $idOtp = $this->input->post('idOtp');
        $cantOthAbiertas = $this->Dao_ot_padre_model->getCantOthInExecutionByIdOtp($idOtp);
        if ($cantOthAbiertas->cant == 0) {
            $data = array(
                'estado_orden_trabajo' => 'otp_cerrada',
            );

            $this->db->where('k_id_ot_padre', $idOtp);

            if ($this->db->update('ot_padre', $data)) {
                $respuesta['response'] = 'success';
            }
        } else {
            $respuesta['response'] = 'error';
            $respuesta['cant_oth_abiertas'] = $cantOthAbiertas->cant;
            $respuesta['oth_abiertas'] = $this->Dao_ot_padre_model->getOthInExecutionByIdOtp($idOtp);
        }
//
        echo json_encode($respuesta);
    }

    // TABLA QUE TRAE TODAS LAS OTH DE UNA OTP
    public function c_getOthOfOtp() {
        $idOtp = $this->input->post('idOtp');
        $listotps = $this->Dao_ot_padre_model->getothofothp($idOtp);
        echo json_encode($listotps);
    }

    // TABLA QUE TRAE TODAS LAS OTH QUE ESTEN EN LA TABLA CIERRE_OTS DE UNA OTP
    public function c_getOthOfOtpCierre() {
        $idOtp = $this->input->post('idOtp');
        $listotps = $this->Dao_ot_padre_model->getOthOfOtpCierre($idOtp);
        echo json_encode($listotps);
    }

    // TABLA QUE TRAE LA INFORMACION DE OTPADRE
    public function c_getListOtsOtPadreEmail() {
        $otPadreList = $this->Dao_ot_padre_model->getListOtsOtPadreEmail();
        echo json_encode($otPadreList);
    }

    // TRAE LOS OTP QUE ESTAN PENDIENTES DE ENVIO DE CORREO DE ACTUALIZACION
    public function c_getOtsPtesPorEnvio() {
        $filtro = $this->input->post('filter');
        $otPadreList = $this->Dao_ot_padre_model->getOtsPtesPorEnvioActualizacion($filtro);
        $data = array(
            'data' => $otPadreList->result(),
            'cantidad' => $otPadreList->num_rows()
        );

        echo json_encode($data);
    }

    //obtine la informacion de los hitos de una otp
    public function c_getHitosOtp() {
        $idOtp = $this->input->post('idOtp');
        $hitosotp = $this->Dao_ot_padre_model->getHitosOtp($idOtp);
        echo json_encode($hitosotp);
    }

    //Guarda la informacion de los hitos de una OTP
    public function c_saveHitosOtp() {
        $idOtp = $this->input->post('idOtp');
        $formulario = json_decode($this->input->post('formulario'));
        $actividadActual = $this->input->post('actividadAct');
        $obsGeneral = $this->input->post('obsGeneral');
        $newFields = array();
        $newFields['id_ot_padre'] = $idOtp;
        $newFields['actividad_actual'] = $actividadActual;
        $newFields['observaciones_genrales'] = $obsGeneral;

        /*
          foreach ($formulario as $key => $value) {
          switch ($key) {
          case 'CIERRE KICKOFF':
          $newFields['f_compromiso_ko'] = $value[0];
          $newFields['estado_ko'] = $value[1];
          $newFields['observaciones_ko'] = $value[2];
          break;

          case 'VISITA OBRA CIVIL TERCEROS':
          case 'VISITA OBRA CIVIL':
          $newFields['f_compromiso_voc'] = $value[0];
          $newFields['estado_voc'] = $value[1];
          $newFields['observaciones_voc'] = $value[2];
          if ($key === 'VISITA OBRA CIVIL') {
          $newFields['tipo_voc'] = 'VISITA OBRA CIVIL';
          }else{
          $newFields['tipo_voc'] = 'VISITA OBRA CIVIL TERCEROS';
          }
          break;

          case 'ENVIÓ COTIZACIÓN':
          $newFields['f_compromiso_ec'] = $value[0];
          $newFields['estado_ec'] = $value[1];
          $newFields['observaciones_ec'] = $value[2];
          break;

          case 'APROBACIÓN COTIZACIÓN OC':
          $newFields['f_compromiso_ac'] = $value[0];
          $newFields['estado_ac'] = $value[1];
          $newFields['observaciones_ac'] = $value[2];
          break;

          case 'SOLICITUD INFORMACIÓN TÉCNICA':
          $newFields['f_compromiso_sit'] = $value[0];
          $newFields['estado_sit'] = $value[1];
          $newFields['observaciones_sit'] = $value[2];
          break;

          case 'VISITA EJECUCION OBRA CIVIL TERCERO':
          case 'VISITA EJECUCION OBRA CIVIL':
          $newFields['f_compromiso_veoc'] = $value[0];
          $newFields['estado_veoc'] = $value[1];
          $newFields['observaciones_veoc'] = $value[2];
          if ($key === 'VISITA EJECUCION OBRA CIVIL') {
          $newFields['tipo_veoc'] = 'VISITA EJECUCION OBRA CIVIL';
          } else {
          $newFields['tipo_veoc'] = 'VISITA EJECUCION OBRA CIVIL TERCERO';
          }

          break;

          case 'EMPALMES':
          $newFields['f_compromiso_empalmes'] = $value[0];
          $newFields['estado_empalmes'] = $value[1];
          $newFields['observaciones_empalmes'] = $value[2];
          break;

          case 'CONFIGURACION':
          $newFields['f_compromiso_crc'] = $value[0];
          $newFields['estado_crc'] = $value[1];
          $newFields['observaciones_crc'] = $value[2];
          break;

          case 'VISITA ENTREGA DE SERVICIO':
          $newFields['f_compromiso_veut'] = $value[0];
          $newFields['estado_veut'] = $value[1];
          $newFields['observaciones_veut'] = $value[2];
          break;
          }
          }
         */

//        print_r($formulario);exit();
        foreach ($formulario as $key => $value) {
            switch ($key) {
                case 'VISITA OBRA CIVIL':
                    $newFields['f_voc'] = $value[0];
                    $newFields['n_estado_voc'] = $value[1];
                    $newFields['n_observaciones_voc'] = $value[2];
                    $newFields['no_aplica_voc'] = $value[3];
                    break;

                case 'VISITA EJECUCION OBRA CIVIL':
                    $newFields['f_eoc'] = $value[0];
                    $newFields['estado_eoc'] = $value[1];
                    $newFields['observaciones_eoc'] = $value[2];
                    $newFields['no_aplica_eoc'] = $value[3];
                    break;

                case 'EMPALMES':
                    $newFields['f_em'] = $value[0];
                    $newFields['estado_em'] = $value[1];
                    $newFields['observaciones_em'] = $value[2];
                    $newFields['no_aplica_em'] = $value[3];
                    break;

                case 'ENTREGA SERVICIO':
                    $newFields['f_entrega_servicio'] = $value[0];
                    $newFields['estado_entrega_servicio'] = $value[1];
                    $newFields['observaciones_entrega_servicio'] = $value[2];
                    $newFields['no_aplica_entrega'] = $value[3];
                    break;

                case 'OBSERVACIONES':
                    $newFields['observaciones_genrales'] = $value[0];
                    break;
            }
        }

        $res = $this->Dao_ot_padre_model->saveHitosOtp(array_filter($newFields));
        $res2 = $this->Dao_ot_padre_model->saveLogHitosOtp(array_filter($newFields));
        echo json_encode($res);
    }

    public function c_sendReportUpdate() {
        $ids_otp = $this->input->post('ids_otp');
        $senior = $this->input->post('senior');
        $configuracion = $this->input->post('configuracion');
        $entregaServicio = $this->input->post('entregaServicio');
        $observacionesEmail = $this->input->post('observaciones');
        $direccionEmail = $this->input->post('direccion');
        $email = Auth::user()->n_mail_user;
        $ingeniero = Auth::user()->n_name_user . ' ' . Auth::user()->n_last_name_user;
        $celIngeniero = Auth::user()->cell_phone;

        $template = '';
        $observaciones = '';
        $asunOtp = ' - ';
        $ids_in = implode(",", $ids_otp);
        $direccionCierreOtp = implode(',', $this->getDireccionCierreOTP($ids_in));
        $detCierreOtp = $this->Dao_cierre_ots_model->getDetailsCierreOTP($ids_in);

        /* print_r($direccionCierreOtp);
          exit(); */

        foreach ($ids_otp as $idOtp) {
            $observaciones = '';
            //actualizar la ultima fecha de envio de reporte de loa ot padre (CAMILO)
            $this->Dao_ot_padre_model->update_ot_padre(array('ultimo_envio_reporte' => date('Y-m-d')), $idOtp);

            $asunOtp .= $idOtp . ' - ';
            $hitosotp = $this->Dao_ot_padre_model->getHitosOtp($idOtp);
            $fechasAEnviar = [
                'f_compromiso_ko' => $hitosotp->f_compromiso_ko,
                'f_compromiso_voc' => $hitosotp->f_compromiso_voc,
                'f_compromiso_ec' => $hitosotp->f_compromiso_ec,
                'f_compromiso_ac' => $hitosotp->f_compromiso_ac,
                'f_compromiso_sit' => $hitosotp->f_compromiso_sit,
                'f_compromiso_veoc' => $hitosotp->f_compromiso_veoc,
                'f_compromiso_crc' => $hitosotp->f_compromiso_crc,
                'f_compromiso_empalmes' => $hitosotp->f_compromiso_empalmes,
                'f_compromiso_veut' => $hitosotp->f_compromiso_veut,];
            $fechasAEnviar = array_filter($fechasAEnviar); //limpiamos las fechas que no existen, para pintar la tabla dinamicamente
            $infOtp = $this->Dao_ot_padre_model->getDetailsHitosOTP($idOtp);
            $obs = [
                'observaciones_ko' => $hitosotp->observaciones_ko,
                "observaciones_voc" => $hitosotp->observaciones_voc,
                "observaciones_ec" => $hitosotp->observaciones_ec,
                "observaciones_ac" => $hitosotp->observaciones_ac,
                "observaciones_sit" => $hitosotp->observaciones_sit,
                "observaciones_veoc" => $hitosotp->observaciones_veoc,
                "observaciones_crc" => $hitosotp->observaciones_crc,
                "observaciones_empalmes" => $hitosotp->observaciones_empalmes,
                "observaciones_veut" => $hitosotp->observaciones_veut,
            ];
            foreach (array_filter($obs) as $value) {
                $observaciones .= $value . '<br><br>';
            }
            // echo("<pre>"); print_r( $observaciones); echo("</pre>");
            $template .= '
                <div dir="ltr">
                    <table border="0" cellpadding="0" cellspacing="0" width="712" style="border-collapse:collapse;box-shadow: rgba(8, 76, 111, 0.5) 6px 7px;">
                        <colgroup><col width="80" style="width:60pt">
                            <col width="252" style="width:189pt">
                            <col width="140" style="width:105pt">
                            <col width="80" span="3" style="width:60pt">
                        </colgroup>
                        <tbody>
                            <tr height="20" style="height:20pt">
                                <td colspan="2" height="20" class="m_-7809522729103588979gmail-xl67" width="200" style="height:15pt;width:100pt;border:0.5pt solid windowtext;padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:middle;white-space:nowrap"><label id="servivio_hito" style="margin-right: 50px; margin-left: 10px;"><strong> OT ' . $idOtp . ' - ' . $infOtp->servicio . ' </strong></label></td>
                                <td colspan="2" class="m_-7809522729103588979gmail-xl67" width="220" style="border-left:none;width:165pt;border-top:0.5pt solid windowtext;border-right:0.5pt solid windowtext;border-bottom:0.5pt solid windowtext;padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:middle;white-space:nowrap"><label id="cliente_hito" style="margin-right: 50px; margin-left: 10px;"><strong> CLIENTE: ' . $infOtp->n_nombre_cliente . '</strong></label></td>
                                <td colspan="2" class="m_-7809522729103588979gmail-xl67" width="160" style="border-left:none;width:120pt;border-top:0.5pt solid windowtext;border-right:0.5pt solid windowtext;border-bottom:0.5pt solid windowtext;padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:middle;white-space:nowrap"><label id="ciudad_hito" style="margin-right: 50px; margin-left: 10px;"><strong> CIUDAD: ' . $infOtp->ciudad . ' - ' . $infOtp->direccion . '</strong></label></td>
                            </tr>
                            <tr height="20" style="height:20pt;background: #084c6f;">
                                <td height="15" class="m_-7809522729103588979gmail-xl65" style="height:15pt;border-top:none;border-right: 0.5pt solid #ffff;border-bottom:0.5pt solid windowtext;border-left:0.5pt solid windowtext;padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:middle;white-space:nowrap">&nbsp;</td>
                                <td class="m_-7809522729103588979gmail-xl70" style="border-top:none;border-left:none;text-align:center;vertical-align:middle;border-right: 0.5pt solid #ffff;border-bottom:0.5pt solid windowtext;padding-top:1px;padding-right:1px;padding-left:1px;color: #ffffff;font-size:11pt;font-family:Calibri,sans-serif;white-space:nowrap;font-weight: bold;">ACTIVIDAD</td>
                                <td class="m_-7809522729103588979gmail-xl70" style="border-top:none;border-left:none;text-align:center;vertical-align:middle;border-right: 0.5pt solid #ffff;border-bottom:0.5pt solid windowtext;padding-top:1px;padding-right:1px;padding-left:1px;color: #ffffff;font-size:11pt;font-family:Calibri,sans-serif;white-space:nowrap;font-weight: bold;">FECHA COMPROMISO</td>
                                <td class="m_-7809522729103588979gmail-xl70" style="border-top:none;border-left:none;text-align:center;vertical-align:middle;border-right: 0.5pt solid #ffff;border-bottom:0.5pt solid windowtext;padding-top:1px;padding-right:1px;padding-left:1px;color: #ffffff;font-size:11pt;font-family:Calibri,sans-serif;white-space:nowrap;font-weight: bold;">ESTADO</td>
                                <td colspan="2" class="m_-7809522729103588979gmail-xl70" style="border-left:none;text-align:center;vertical-align:middle;border-top:0.5pt solid windowtext;border-right: 0.5pt solid #ffff;border-bottom:0.5pt solid windowtext;padding-top:1px;padding-right:1px;padding-left:1px;color: #ffffff;font-size:11pt;font-family:Calibri,sans-serif;white-space:nowrap;font-weight: bold;">OBSERVACIONES</td>
                            </tr>';
            $numFila = 1;
            foreach ($fechasAEnviar as $key => $val) {
                switch ($key) {
                    case 'f_compromiso_ko':
                        $template .= '<tr height="20" style="height:15pt">
                                        <td height="15" class="m_-7809522729103588979gmail-xl65" style="height:15pt;border-top:none;border-right:0.5pt solid windowtext;border-bottom:0.5pt solid windowtext;border-left:0.5pt solid windowtext;padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:middle;white-space:nowrap"><div style="color: #fff; width: 30px; height: 30px; line-height: 30px; font-size: 22px; text-align: center; top: 18px; left: 50%; margin-left: -25px; border: 3px solid #ffffff; z-index: 100; border-top-right-radius: 50%; border-top-left-radius: 50%; border-bottom-right-radius: 50%; border-bottom-left-radius: 50%; background-color: ' . ($hitosotp->actividad_actual == 'KICK OFF' ? '#4bd605' : '#7c7c7c') . ';">' . $numFila . '</div></td>
                                        <td class="m_-7809522729103588979gmail-xl65" style="border-top:none;border-left:none;border-right:0.5pt solid windowtext;border-bottom:0.5pt solid windowtext;padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:middle;white-space:nowrap">KICK OFF</td>
                                        <td class="m_-7809522729103588979gmail-xl65" style="border-top:none;border-left:none;box-sizing:content-box;border-right:0.5pt solid windowtext;border-bottom:0.5pt solid windowtext;padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:middle;white-space:nowrap">&nbsp;' . $hitosotp->f_compromiso_ko . '</td>
                                        <td class="m_-7809522729103588979gmail-xl65" style="border-top:none;border-left:none;border-right:0.5pt solid windowtext;border-bottom:0.5pt solid windowtext;padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:middle;white-space:nowrap">&nbsp;' . $hitosotp->estado_ko . '</td>

                                        <td colspan="2" rowspan="' . count($fechasAEnviar) . '" class="m_-7809522729103588979gmail-xl75" style="border-width:0.5pt;border-style:solid;border-color:windowtext black black windowtext;text-align:left;padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:middle;white-space:nowrap">&nbsp;' . $observaciones . '</td>
                                        </tr>';
                        break;

                    case 'f_compromiso_voc':
                        $template .= '<tr height="20" style="height:15pt">
                                        <td height="15" class="m_-7809522729103588979gmail-xl67" style="height:30pt;border-top:none;text-align:center;border-right:0.5pt solid windowtext;border-bottom:0.5pt solid windowtext;border-left:0.5pt solid windowtext;padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:middle;white-space:nowrap"><div style="color: #fff; width: 30px; height: 30px; line-height: 30px; font-size: 22px; text-align: center; top: 18px; left: 50%; margin-left: -25px; border: 3px solid #ffffff; z-index: 100; border-top-right-radius: 50%; border-top-left-radius: 50%; border-bottom-right-radius: 50%; border-bottom-left-radius: 50%; background-color: ' . (($hitosotp->actividad_actual == 'VISITA OBRA CIVIL' || $hitosotp->actividad_actual == 'VISITA OBRA CIVIL TERCEROS') ? '#4bd605' : '#7c7c7c') . ';">' . $numFila . '</div></td>
                                        <td class="m_-7809522729103588979gmail-xl65" style="border-top:none;border-left:none;border-right:0.5pt solid windowtext;border-bottom:0.5pt solid windowtext;padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:middle;white-space:nowrap">' . $hitosotp->tipo_voc . '</td>
                                        <td class="m_-7809522729103588979gmail-xl65" style="border-top:none;border-left:none;border-right:0.5pt solid windowtext;border-bottom:0.5pt solid windowtext;padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:middle;white-space:nowrap">&nbsp;' . $hitosotp->f_compromiso_voc . '</td>
                                        <td class="m_-7809522729103588979gmail-xl65" style="border-top:none;border-left:none;border-right:0.5pt solid windowtext;border-bottom:0.5pt solid windowtext;padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:middle;white-space:nowrap">&nbsp;' . $hitosotp->estado_voc . '</td>
                                     </tr>';
                        break;

                    case 'f_compromiso_ec':
                        $template .= '<tr height="20" style="height:15pt">
                                        <td height="15" class="m_-7809522729103588979gmail-xl65" style="height:15pt;border-top:none;border-right:0.5pt solid windowtext;border-bottom:0.5pt solid windowtext;border-left:0.5pt solid windowtext;padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:middle;white-space:nowrap"><div style="color: #fff; width: 30px; height: 30px; line-height: 30px; font-size: 22px; text-align: center; top: 18px; left: 50%; margin-left: -25px; border: 3px solid #ffffff; z-index: 100; border-top-right-radius: 50%; border-top-left-radius: 50%; border-bottom-right-radius: 50%; border-bottom-left-radius: 50%; background-color: ' . ($hitosotp->actividad_actual == 'ENVIO COTIZACION' ? '#4bd605' : '#7c7c7c') . ';">' . $numFila . '</div></td>
                                        <td class="m_-7809522729103588979gmail-xl65" style="border-top:none;border-left:none;border-right:0.5pt solid windowtext;border-bottom:0.5pt solid windowtext;padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:middle;white-space:nowrap">ENVIO COTIZACIÓN</td>
                                        <td class="m_-7809522729103588979gmail-xl65" style="border-top:none;border-left:none;border-right:0.5pt solid windowtext;border-bottom:0.5pt solid windowtext;padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:middle;white-space:nowrap">&nbsp;' . $hitosotp->f_compromiso_ec . '</td>
                                        <td class="m_-7809522729103588979gmail-xl65" style="border-top:none;border-left:none;border-right:0.5pt solid windowtext;border-bottom:0.5pt solid windowtext;padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:middle;white-space:nowrap">&nbsp;' . $hitosotp->estado_ec . '</td>
                                       </tr>';
                        break;

                    case 'f_compromiso_ac':
                        $template .= '<tr height="20" style="height:15pt">
                                        <td height="15" class="m_-7809522729103588979gmail-xl65" style="height:15pt;border-top:none;border-right:0.5pt solid windowtext;border-bottom:0.5pt solid windowtext;border-left:0.5pt solid windowtext;padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:middle;white-space:nowrap"><div style="color: #fff; width: 30px; height: 30px; line-height: 30px; font-size: 22px; text-align: center; top: 18px; left: 50%; margin-left: -25px; border: 3px solid #ffffff; z-index: 100; border-top-right-radius: 50%; border-top-left-radius: 50%; border-bottom-right-radius: 50%; border-bottom-left-radius: 50%; background-color: ' . ($hitosotp->actividad_actual == 'APROBACION COTIZACION' ? '#4bd605' : '#7c7c7c') . ';">' . $numFila . '</div></td>
                                        <td class="m_-7809522729103588979gmail-xl65" style="border-top:none;border-left:none;border-right:0.5pt solid windowtext;border-bottom:0.5pt solid windowtext;padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:middle;white-space:nowrap">APROBACIÓN COTIZACIÓN</td>
                                        <td class="m_-7809522729103588979gmail-xl65" style="border-top:none;border-left:none;border-right:0.5pt solid windowtext;border-bottom:0.5pt solid windowtext;padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:middle;white-space:nowrap">&nbsp;' . $hitosotp->f_compromiso_ac . '</td>
                                        <td class="m_-7809522729103588979gmail-xl65" style="border-top:none;border-left:none;border-right:0.5pt solid windowtext;border-bottom:0.5pt solid windowtext;padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:middle;white-space:nowrap">&nbsp;' . $hitosotp->estado_ac . '</td>
                                     </tr>';
                        break;

                    case 'f_compromiso_sit':
                        $template .= '<tr height="20" style="height:15pt">
                                        <td height="15" class="m_-7809522729103588979gmail-xl65" style="height:15pt;border-top:none;border-right:0.5pt solid windowtext;border-bottom:0.5pt solid windowtext;border-left:0.5pt solid windowtext;padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:middle;white-space:nowrap"><div style="color: #fff; width: 30px; height: 30px; line-height: 30px; font-size: 22px; text-align: center; top: 18px; left: 50%; margin-left: -25px; border: 3px solid #ffffff; z-index: 100; border-top-right-radius: 50%; border-top-left-radius: 50%; border-bottom-right-radius: 50%; border-bottom-left-radius: 50%; background-color: ' . ($hitosotp->actividad_actual == 'SOLICITUD INFORMACIÓN TECNICA' ? '#4bd605' : '#7c7c7c') . ';">' . $numFila . '</div></td>
                                        <td class="m_-7809522729103588979gmail-xl65" style="border-top:none;border-left:none;border-right:0.5pt solid windowtext;border-bottom:0.5pt solid windowtext;padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:middle;white-space:nowrap">SOLICITUD INFORMACIÓN TÉCNICA</td>
                                        <td class="m_-7809522729103588979gmail-xl65" style="border-top:none;border-left:none;border-right:0.5pt solid windowtext;border-bottom:0.5pt solid windowtext;padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:middle;white-space:nowrap">&nbsp;' . $hitosotp->f_compromiso_sit . '</td>
                                        <td class="m_-7809522729103588979gmail-xl65" style="border-top:none;border-left:none;border-right:0.5pt solid windowtext;border-bottom:0.5pt solid windowtext;padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:middle;white-space:nowrap">&nbsp;' . $hitosotp->estado_sit . '</td>
                                       </tr>';
                        break;

                    case 'f_compromiso_veoc':
                        $template .= '<tr height="20" style="height:15pt">
                                        <td height="15" class="m_-7809522729103588979gmail-xl73" style="border-bottom:0.5pt solid black;height:30pt;border-top:none;text-align:center;border-right:0.5pt solid windowtext;border-left:0.5pt solid windowtext;padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:middle;white-space:nowrap"><div style="color: #fff; width: 30px; height: 30px; line-height: 30px; font-size: 22px; text-align: center; top: 18px; left: 50%; margin-left: -25px; border: 3px solid #ffffff; z-index: 100; border-top-right-radius: 50%; border-top-left-radius: 50%; border-bottom-right-radius: 50%; border-bottom-left-radius: 50%; background-color: ' . ($hitosotp->actividad_actual == 'VISITA EJECUCION OBRA CIVIL' || $hitosotp->actividad_actual == 'VISITA EJECUCION OBRA CIVIL TERCERO' ? '#4bd605' : '#7c7c7c') . ';">' . $numFila . '</div></td>
                                        <td class="m_-7809522729103588979gmail-xl65" style="border-top:none;border-left:none;border-right:0.5pt solid windowtext;border-bottom:0.5pt solid windowtext;padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:middle;white-space:nowrap">' . $hitosotp->tipo_veoc . '</td>
                                        <td class="m_-7809522729103588979gmail-xl65" style="border-top:none;border-left:none;border-right:0.5pt solid windowtext;border-bottom:0.5pt solid windowtext;padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:middle;white-space:nowrap">&nbsp;' . $hitosotp->f_compromiso_veoc . '</td>
                                        <td class="m_-7809522729103588979gmail-xl65" style="border-top:none;border-left:none;border-right:0.5pt solid windowtext;border-bottom:0.5pt solid windowtext;padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:middle;white-space:nowrap">&nbsp;' . $hitosotp->estado_veoc . '</td>
                                     </tr>';
                        break;

                    case 'f_compromiso_empalmes':
                        $template .= '<tr height="20" style="height:15pt">
                                        <td height="15" class="m_-7809522729103588979gmail-xl65" style="height:15pt;border-top:none;border-right:0.5pt solid windowtext;border-bottom:0.5pt solid windowtext;border-left:0.5pt solid windowtext;padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:middle;white-space:nowrap"><div style="color: #fff; width: 30px; height: 30px; line-height: 30px; font-size: 22px; text-align: center; top: 18px; left: 50%; margin-left: -25px; border: 3px solid #ffffff; z-index: 100; border-top-right-radius: 50%; border-top-left-radius: 50%; border-bottom-right-radius: 50%; border-bottom-left-radius: 50%; background-color: ' . ($hitosotp->actividad_actual == 'EMPALMES' ? '#4bd605' : '#7c7c7c') . ';">' . $numFila . '</div></td>
                                        <td class="m_-7809522729103588979gmail-xl65" style="border-top:none;border-left:none;border-right:0.5pt solid windowtext;border-bottom:0.5pt solid windowtext;padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:middle;white-space:nowrap">EMPALMES</td>
                                        <td class="m_-7809522729103588979gmail-xl65" style="border-top:none;border-left:none;border-right:0.5pt solid windowtext;border-bottom:0.5pt solid windowtext;padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:middle;white-space:nowrap">&nbsp;' . $hitosotp->f_compromiso_empalmes . '</td>
                                        <td class="m_-7809522729103588979gmail-xl65" style="border-top:none;border-left:none;border-right:0.5pt solid windowtext;border-bottom:0.5pt solid windowtext;padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:middle;white-space:nowrap">&nbsp;' . $hitosotp->estado_empalmes . '</td>
                                        </tr>';
                        break;

                    case 'f_compromiso_crc':
                        $template .= '<tr height="20" style="height:15pt">
                                        <td height="15" class="m_-7809522729103588979gmail-xl65" style="height:15pt;border-top:none;border-right:0.5pt solid windowtext;border-bottom:0.5pt solid windowtext;border-left:0.5pt solid windowtext;padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:middle;white-space:nowrap"><div style="color: #fff; width: 30px; height: 30px; line-height: 30px; font-size: 22px; text-align: center; top: 18px; left: 50%; margin-left: -25px; border: 3px solid #ffffff; z-index: 100; border-top-right-radius: 50%; border-top-left-radius: 50%; border-bottom-right-radius: 50%; border-bottom-left-radius: 50%; background-color: ' . ($hitosotp->actividad_actual == 'CONFIGURACION RED CLARO' ? '#4bd605' : '#7c7c7c') . ';">' . $numFila . '</div></td>
                                        <td class="m_-7809522729103588979gmail-xl65" style="border-top:none;border-left:none;border-right:0.5pt solid windowtext;border-bottom:0.5pt solid windowtext;padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:middle;white-space:nowrap">CONFIGURACIÓN</td>
                                        <td class="m_-7809522729103588979gmail-xl65" style="border-top:none;border-left:none;border-right:0.5pt solid windowtext;border-bottom:0.5pt solid windowtext;padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:middle;white-space:nowrap">&nbsp;' . $hitosotp->f_compromiso_crc . '</td>
                                        <td class="m_-7809522729103588979gmail-xl65" style="border-top:none;border-left:none;border-right:0.5pt solid windowtext;border-bottom:0.5pt solid windowtext;padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:middle;white-space:nowrap">&nbsp;' . $hitosotp->estado_crc . '</td>
                                     </tr>';
                        break;

                    case 'f_compromiso_veut':
                        $template .= '<tr height="20" style="height:15pt">
                                        <td height="15" class="m_-7809522729103588979gmail-xl65" style="height:15pt;border-top:none;border-right:0.5pt solid windowtext;border-bottom:0.5pt solid windowtext;border-left:0.5pt solid windowtext;padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:middle;white-space:nowrap"><div style="color: #fff; width: 30px; height: 30px; line-height: 30px; font-size: 22px; text-align: center; top: 18px; left: 50%; margin-left: -25px; border: 3px solid #ffffff; z-index: 100; border-top-right-radius: 50%; border-top-left-radius: 50%; border-bottom-right-radius: 50%; border-bottom-left-radius: 50%; background-color: ' . ($hitosotp->actividad_actual == 'VISITA ENTREGA UM TERCEROS' ? '#4bd605' : '#7c7c7c') . ';">' . $numFila . '</div></td>
                                        <td class="m_-7809522729103588979gmail-xl65" style="border-top:none;border-left:none;border-right:0.5pt solid windowtext;border-bottom:0.5pt solid windowtext;padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:middle;white-space:nowrap">ENTREGA SERVICIO</td>
                                        <td class="m_-7809522729103588979gmail-xl65" style="border-top:none;border-left:none;border-right:0.5pt solid windowtext;border-bottom:0.5pt solid windowtext;padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:middle;white-space:nowrap">&nbsp;' . $hitosotp->f_compromiso_veut . '</td>
                                        <td class="m_-7809522729103588979gmail-xl65" style="border-top:none;border-left:none;border-right:0.5pt solid windowtext;border-bottom:0.5pt solid windowtext;padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:middle;white-space:nowrap">&nbsp;' . $hitosotp->estado_veut . '</td>
                                        </tr>';
                        break;
                }
                $numFila++;
            }
            $template .= '</tbody>
                                    </table>
                                </div>
                                <br><br>';

            // echo("<pre>"); print_r($template); echo("</pre>");
        }

        $encabezado = '
            <p class="x_MsoNormal"><span style="font-family: Arial, sans-serif, serif, EmojiFont;">Cordial Saludo</span></p>
            <p class="x_MsoNormal"><span style="font-family: Arial, sans-serif, serif, EmojiFont;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></p>
            <p class="x_MsoNormal"><span style="font-family: Arial, sans-serif, serif, EmojiFont;">Señor(a):</span></p>
            <p class="x_MsoNormal" style="text-align:justify"><strong><span style="font-family: Arial, sans-serif, serif, EmojiFont;">' . $senior . '</span></strong></p>
            <p class="x_MsoNormal" style="text-align:justify"><span style="font-family: Arial, sans-serif, serif, EmojiFont;">&nbsp;</span></p>
            <p class="x_MsoNormal"><span style="font-family: Arial, sans-serif, serif, EmojiFont;">Comprometidos con el servicio y el cumplimiento de sus solicitudes me permito notificar los avances de los asuntos en curso. Es de suma importancia que sea revisado y nos retroalimente con &nbsp;sus comentarios, ya que al término de 2 días hábiles este reporte se dará por aceptado.</span></p>
            <p class="x_MsoNormal">&nbsp;</p>
            <p class="x_MsoListParagraph" style="text-indent:-18.0pt; text-autospace:none"><span style="font-family: Symbol, serif, EmojiFont;"><span style=""><span style="font: 7pt &quot;Times New Roman&quot;, serif, EmojiFont;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </span></span></span><strong><span lang="EN-US" style="font-family: Arial, sans-serif, serif, EmojiFont;">&nbsp;OT DESTINO &nbsp;</span></strong><strong><span style="font-family: Arial, sans-serif, serif, EmojiFont;">' . substr($asunOtp, 0, -2) . '</span></strong><strong><span lang="EN-US" style="font-family: Arial, sans-serif, serif, EmojiFont;">: </span></strong><span lang="EN-US" style="font-family: Arial, sans-serif, serif, EmojiFont;">' . $infOtp->servicio . ' </span><span style="font-family: Arial, sans-serif, serif, EmojiFont;"><strong></strong></span></p>
            <p class="x_MsoNormal" style="text-autospace:none"><strong><span style="font-family: Arial, sans-serif, serif, EmojiFont;">Ciudad: </span></strong><span style="font-family: Arial, sans-serif, serif, EmojiFont;">' . $infOtp->ciudad . '</span><span style="font-family: Arial, sans-serif, serif, EmojiFont;"></span></p>
            <p class="x_MsoNormal" style="text-autospace:none"><strong><span style="font-family: Arial, sans-serif, serif, EmojiFont;">Dirección de servicio: </span></strong><span style="font-family: Arial, sans-serif, serif, EmojiFont;">' . $direccionCierreOtp . '&nbsp; </span><strong><span style="font-family: Arial, sans-serif, serif, EmojiFont;"></span></strong></p>
            <p class="x_MsoNormal"><strong><span style="font-family: Arial, sans-serif, serif, EmojiFont;">Cliente: &nbsp;</span></strong><span style="font-family: Arial, sans-serif, serif, EmojiFont;">' . $configuracion . '<strong></strong></span></p>
            <p class="x_MsoNormal"><strong><span style="font-family: Arial, sans-serif, serif, EmojiFont;">Entrega del servicio:</span></strong><span style="font-family: Arial, sans-serif, serif, EmojiFont;"> ' . $entregaServicio . ' </span><span lang="ES" style="font-family: Arial, sans-serif, serif, EmojiFont;">(Fecha sujeta a cambios en caso de tener algún inconveniente o adelantos en el proceso de instalación). </span><span style="font-family: Arial, sans-serif, serif, EmojiFont;">&nbsp;</span></p>
            <p class="x_MsoNormal" style="text-align:justify"><span style="text-decoration:underline"><span lang="ES" style="font-family: Arial, sans-serif, serif, EmojiFont;">De acuerdo a lo anterior, solicitamos de su colaboración confirmado la siguiente información:</span></span></p>
            <p class="x_MsoListParagraph" style="text-indent:-18.0pt"><span style="font-family: Symbol, serif, EmojiFont;"><span style="">·<span style="font: 7pt &quot;Times New Roman&quot;, serif, EmojiFont;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </span></span></span><span style="font-family: Arial, sans-serif, serif, EmojiFont;"><br><b>Observaciones: </b>&nbsp;' . $observacionesEmail . '</span></p>
            <p class="x_MsoListParagraph"><span style="font-family: Arial, sans-serif, serif, EmojiFont;">&nbsp;</span></p>
            <br><br>';




        $contacto = '
            <p class="x_MsoNormal"><span style="font-family: Arial, sans-serif, serif, EmojiFont;">Durante todo el Proceso de Instalación puede contactar a:</span></p>
            <p class="x_MsoNormal"><span style="font-family: Arial, sans-serif, serif, EmojiFont;">&nbsp;</span></p>
            <p class="x_MsoNormal"><strong><span style="font-family: Arial, sans-serif, serif, EmojiFont;">Nivel de Contacto 1:</span></strong><span style="font-family: Arial, sans-serif, serif, EmojiFont;"> Para cualquier duda o inquietud sobre el proceso.</span></p>
            <p class="x_MsoNormal"><span style="font-family: Arial, sans-serif, serif, EmojiFont;">Ingeniero Implementación Responsable Cuenta: &nbsp;' . $ingeniero . '</span></p>
            <p class="x_MsoNormal"><span style="font-family: Arial, sans-serif, serif, EmojiFont;">Ingeniero Aprovisionamiento Estándar</span></p>
            <p class="x_MsoNormal"><span style="font-family: Arial, sans-serif, serif, EmojiFont;">Celular: </span><span style="font-family: Arial, sans-serif, serif, EmojiFont;">' . $celIngeniero . '</span><span style="font-family: Arial, sans-serif, serif, EmojiFont;"></span></p>
            <p class="x_MsoNormal"><span style="font-family: Arial, sans-serif, serif, EmojiFont;">Correo electrónico: </span><span style="font-family: Arial, sans-serif, serif, EmojiFont; color: rgb(79, 129, 189);"><a href="mailto:' . $email . '" target="_blank" rel="noopener noreferrer" data-auth="NotApplicable"><span style="color:#4F81BD">' . $email . '</span></a></span><span style="font-family: Arial, sans-serif, serif, EmojiFont; color: black;">&nbsp;</span><span style="font-family: Arial, sans-serif, serif, EmojiFont;"></span></p>
            <p class="x_MsoNormal"><span style="font-family: Arial, sans-serif, serif, EmojiFont;">&nbsp;</span></p>


            <p class="m_-5751456617445139844xmsonormal" style="text-align:justify"><strong><span style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;">Nivel de Contacto 2:</span></strong><span style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;"> En caso de que no se obtenga respuesta por parte del Nivel de Contacto &nbsp;1.</span><u></u><u></u></p>

            <p class="m_-5751456617445139844xmsonormal"><span style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;">Coordinador Estándar: <span style="color:#1f497d">&nbsp;</span>Alejandra Rendon Calderon &nbsp;</span><u></u><u></u></p>
            <p class="MsoNormal"><span style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;">Teléfono. 7569858 Ext &nbsp;2008 Celular:</span> 3102129290<u></u><u></u></p>
            <p class="m_-5751456617445139844xmsonormal"><span style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;">Correo: <span style="color:#4f81bd"> <a href="mailto:alejandra.rendon.ext@claro.com.co" target="_blank"><span style="color:#4f81bd">alejandra.rendon.ext@claro.<wbr>com.co</span></a> </span></span><u></u><u></u></p>
            <p class="MsoNormal"><u></u>&nbsp;<u></u></p>
            <p class="m_-5751456617445139844xmsonormal" style="text-align:justify"><strong><span style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;">Nivel de Contacto 3:</span></strong><span style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;"> En caso de que no se obtenga respuesta por parte del Nivel de Contacto &nbsp;1.</span><u></u><u></u></p>
            <p class="m_-5751456617445139844xmsonormal"><span style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;">Coordinador Estándar: &nbsp;Angelica Palomino<u></u><u></u></span></p>
            <p class="m_-5751456617445139844xmsonormal"><span style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;">Teléfono.7500300&nbsp; Ext 83746 &nbsp;&nbsp;Celular 3202750048<u></u><u></u></span></p>
            <p class="m_-5751456617445139844xmsonormal"><span style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;">Correo: <span style="color:#4f81bd"> <a href="mailto:angelica.palomino@claro.com.co" target="_blank"><span style="color:#4f81bd">angelica.palomino@claro.<wbr>com.co</span></a> </span></span><u></u><u></u></p>
            <p class="MsoNormal"><u></u>&nbsp;<u></u></p>

            <p class="x_MsoNormal"><span style="font-family: Arial, sans-serif, serif, EmojiFont; color: rgb(31, 73, 125);">&nbsp;</span></p>
            <p class="x_MsoNormal"><span style="font-family: Arial, sans-serif, serif, EmojiFont;">Gracias por la atención prestada y quedo atento a sus comentarios.</span></p>
            <p class="x_MsoNormal"><span style="font-family: Arial, sans-serif, serif, EmojiFont;">&nbsp;</span></p>';

        $res = $this->Dao_email_model->h_enviarCorreo($encabezado . $template . $contacto, $email, 'REPORTE DE ACTUALIZACION DE ACTIVIDADES ' . strtoupper((isset($detCierreOtp->servicio) ? $detCierreOtp->servicio : $infOtp->servicio)) . ' - ' . $infOtp->n_nombre_cliente . ' / OT ' . substr($asunOtp, 0, -2));
        echo json_encode($res);
    }

    //trae la informacion del cierre de una KO de una otp
    public function c_getProductByOtp() {
        $idOtp = $this->input->post('id_otp');
        $numServicio = $this->input->post('num_servicio');
        $res = $this->Dao_ot_padre_model->getProductByOtp($idOtp, $numServicio);
        echo json_encode($res);
    }

    // TRAE LOS OTP QUE ESTAN PENDIENTES DE ENVIO DE CORREO DE ACTUALIZACION
    public function c_getCountPtesPorEnvio() {
        $filtro = $this->input->post("filter");
        $otPadreCount = $this->Dao_ot_padre_model->getCountPtesPorEnvio($filtro);
        echo json_encode($otPadreCount);
    }

    //Trae la dirrecion de cierre de la otp
    public function getDireccionCierreOTP($ids_in) {
        /* $detCierreOtp = $this->Dao_cierre_ots_model->getDetailsCierreOTP($ids_in); */
        $DirCierreOTP = $this->Dao_ot_hija_model->get_direccionservicio($ids_in);

        return array_column($DirCierreOTP, 'direccion_destino');
    }

    public function c_getInfoEmailreport() {
        $idsOtp = $this->input->post("idsOtp");

        //cuenta cuantas filas están seleccionadas
        $seleccionadas = count($idsOtp);

        $exist = $this->Dao_ot_padre_model->getInfoEmailReport($idsOtp);

        if ($seleccionadas == 1) {
            //verifica si existe en la base de datos, si no, extraerá la info de la linea base
            if ($exist) {
                $ultimo_en_enviar = $this->Dao_ot_padre_model->getLastMailSent(implode(',', $idsOtp));
                echo json_encode($ultimo_en_enviar);
            } else {
                //si no existe en la tabla de reporte_info, buscará si existe en la linea base
                $fLineaBase = $this->Dao_ot_padre_model->getFechaLineaBaseEmailReport($idsOtp);
                if ($fLineaBase) {
                    $answer['fecha_compromiso'] = $fLineaBase->fecha_compromiso;
                    echo json_encode($answer);
                } else {

                    //si no retorna sin data
                    echo json_encode('sin data');
                }
            }
        } else {
            //entra si hay más de una seleccion

            $answer = array();
            //creo el arreglo que devolveré

            for ($i = 0; $i < $seleccionadas; $i++) {
                if ($this->Dao_ot_padre_model->getLastMailSent($idsOtp[$i])) {
                    //si existe algo en la tabla reporte_info, lo pondrá en el arreglo
                    array_push($answer, $this->Dao_ot_padre_model->getLastMailSent($idsOtp[$i]));
                } else if ($this->Dao_ot_padre_model->getFechaLineaBaseEmailReport($idsOtp[$i])) {
                    //si no, intentará obtener si existe algo en la linea base
                    $flb['fecha_compromiso'] = $this->Dao_ot_padre_model->getFechaLineaBaseEmailReport($idsOtp[$i])->fecha_compromiso;
                    array_push($answer, $flb);
                } else {
                    // si no, retornará sin data y seguira iterando
                    array_push($answer, "sin data");
                }
            }
            echo json_encode($answer);
        }
    }

    //funcion que actualiza o ingresa la info. del formulario del reporte de act.
    public function saveInfoEmailReport() {
        $ots = $this->input->post("ids_otp"); // ids seleccionadas;
        $servicios = $this->input->post("servicios");
        $last_sender = Auth::user()->k_id_user; //captura quién envió el reporte

        $last_f_evio = date('Y-m-d H:i:s'); //obtiene la fecha de envío del reporte
        $paquete_envio = $this->Dao_ot_padre_model->getMaxPaqueteEnvío();
        $paquete_envio = $paquete_envio[0]->paquete_enviados;

        $data = array(
            'senior' => $this->input->post("senior"),
            'nombre_cliente' => $this->input->post("configuracion"),
            'f_entrega_servicio' => $this->input->post("entregaServicio"),
            'observaciones' => $this->input->post("observaciones"),
            'last_sender' => $last_sender,
            'last_f_envio' => $last_f_evio,
            'paquete_enviados' => $paquete_envio + 1
        );

        //ELIMINA LOS CAMPOS VACÍOS PARA QUE SI UN INPUT SE VA VACÍO, NO LO ACTUALICE A NULL
        foreach ($data as $key => $value) {
            if ($data[$key] == "" || $data[$key] == " " || $data[$key] == "  ") {
                unset($data[$key]);
            }
        }
        $cant_ots = count($ots); //cuenta cuantas selecciones hay
        for ($i = 0; $i < $cant_ots; $i++) {
            // ya no sirve :'v
            // $exist = $this->Dao_ot_padre_model->get_email_report_by_otp($ots[$i]);
            // if ($exist) {
            //     //actualizar
            //     $data['contador_reportes'] = $exist->contador_reportes + 1;
            //     $this->Dao_ot_padre_model->updateInfoEmailDB($data,$ots[$i]);
            // } else {
            //inserta porque no está en db
            $data['id_ot_padre'] = $ots[$i];
            $data['servicio'] = $servicios[$i];
            $this->Dao_ot_padre_model->saveInfoEmailDB($data);
            // los elimino para que en la siguiente iteracion sólo exista el que debe insertarse
            unset($data['id_ot_padre']);
            unset($data['servicio']);
            // }
        }
    }

    // funcion para exportar a excel todos las otp que tengan reporte de actualizacion
    public function c_downloadAllReportAct() {
        $this->load->helper('camilo');
        $data['registros'] = $this->Dao_ot_padre_model->downloadAllReportAct();
        $this->load->view('exportAllReportAct', $data);
    }

    public function c_getLinearBaseForHitos() {
        $id = $this->input->post('idOtp');
        $fechas = $this->Dao_ot_padre_model->getLineaBasePerOTP($id);
        echo json_encode($fechas);
    }

    public function c_getInfoHitosByOtp() {
        $idOtp = $this->input->post("idOtp");
        $hitosList = $this->Dao_ot_padre_model->getInfoHitosByOtp($idOtp);
        echo json_encode($hitosList);
    }

    public function c_sendReportUpdateV2() {
        $ids_otp = $this->input->post('ids_otp');
        $senior = $this->input->post('senior');
        $configuracion = $this->input->post('configuracion');
        $entregaServicio = $this->input->post('entregaServicio');
        $observacionesEmail = $this->input->post('observaciones');
        $direccionEmail = $this->input->post('direccion');
        $email = Auth::user()->n_mail_user;
        $ingeniero = Auth::user()->n_name_user . ' ' . Auth::user()->n_last_name_user;
        $celIngeniero = Auth::user()->cell_phone;

        $template = '';
        $timeline = '';
        $observaciones = '';
        $asunOtp = ' - ';
        $ids_in = implode(",", $ids_otp);
        $direccionCierreOtp = implode(',', $this->getDireccionCierreOTP($ids_in));
        $detCierreOtp = $this->Dao_cierre_ots_model->getDetailsCierreOTP($ids_in);

        /* print_r($direccionCierreOtp);exit(); */

        $encabezado_timeline = '
            <p align="center" style="margin-right:0cm; margin-left:0cm; font-size:12pt; font-family:&quot;Times New Roman&quot;,serif; margin:0cm; margin-bottom:.0001pt; text-align:center">
                <b style="font-size:12pt; font-style:inherit; font-variant-ligatures:inherit; font-variant-caps:inherit">
                    <span style="font-size:14.0pt; font-family:&quot;Arial Narrow&quot;,sans-serif">
                        <img src="' . URL::base() . "/assets/images/linea_roja.png" . '"  width="865" height="10">
                    </span>
                </b><br>
            </p>
            <p align="center" style="margin-right:0cm; margin-left:0cm; font-size:12pt; font-family:&quot;Times New Roman&quot;,serif; margin:0cm; margin-bottom:.0001pt; text-align:center">
                <span style="z-index:251659264; left:0px; margin-top:29px; width:760px; height:7px"></span>
                <b>
                    <span style="font-size:14.0pt; font-family:&quot;Arial Narrow&quot;,sans-serif">ACTIVIDADES DE INSTALACION DE SU SERVICIO</span>
                </b>
            </p>
            <p align="center" style="margin-right:0cm; margin-left:0cm; font-size:12pt; font-family:&quot;Times New Roman&quot;,serif; margin:0cm; margin-bottom:.0001pt; text-align:center">
                <b style="font-size:12pt; font-style:inherit; font-variant-ligatures:inherit; font-variant-caps:inherit">
                    <span style="font-size:14.0pt; font-family:&quot;Arial Narrow&quot;,sans-serif">
                        <img src="' . URL::base() . "/assets/images/linea_roja.png" . '"  width="865" height="10">
                    </span>
                </b><br>
            </p>';
        
        $template .= '
                <div dir="ltr">
                    <table class="x_m_-1360814042998563018MsoNormalTable" border="0" cellspacing="0" cellpadding="0" width="1660" style="width: 829.95pt; border-collapse: collapse; transform: scale(0.779982, 0.779982); transform-origin: left top;" min-scale="0.7799819657348963">
                        <tbody>
                            <tr style="height:45.0pt">
                                <td width="156" nowrap="" style="width:78.0pt; border:solid windowtext 1.0pt; background:#c00000; padding:0cm 3.5pt 0cm 3.5pt; height:45.0pt">
                                    <p class="x_MsoNormal" align="center" style="text-align:center"><b><span style="font-size:10.0pt; font-family:&quot;Calibri&quot;,&quot;sans-serif&quot;; color:white">OT
                                    <u></u><u></u></span></b></p>
                                </td>
                                <td width="154" nowrap="" style="width:77.0pt; border:solid windowtext 1.0pt; border-left:none; background:#c00000; padding:0cm 3.5pt 0cm 3.5pt; height:45.0pt">
                                    <p class="x_MsoNormal" align="center" style="text-align:center"><b><span style="font-size:10.0pt; font-family:&quot;Calibri&quot;,&quot;sans-serif&quot;; color:white">CODIGO SERVICIO &nbsp;<u></u><u></u></span></b></p>
                                </td>
                                <td width="124" style="width:62.05pt; border:solid windowtext 1.0pt; border-left:none; background:#c00000; padding:0cm 3.5pt 0cm 3.5pt; height:45.0pt">
                                    <p class="x_MsoNormal" align="center" style="text-align:center"><b><span style="font-size:10.0pt; font-family:&quot;Calibri&quot;,&quot;sans-serif&quot;; color:white">CIUDAD / MUNICIPIO
                                <u></u><u></u></span></b></p>
                                </td>
                                <td width="218" nowrap="" style="width:108.75pt; border:solid windowtext 1.0pt; border-left:none; background:#c00000; padding:0cm 3.5pt 0cm 3.5pt; height:45.0pt">
                                    <p class="x_MsoNormal" align="center" style="text-align:center"><b><span style="font-size:10.0pt; font-family:&quot;Calibri&quot;,&quot;sans-serif&quot;; color:white">DIRECCIÓN
                                    <u></u><u></u></span></b></p>
                                </td>
                                <td width="150" nowrap="" style="width:75.0pt; border:solid windowtext 1.0pt; border-left:none; background:#c00000; padding:0cm 3.5pt 0cm 3.5pt; height:45.0pt">
                                    <p class="x_MsoNormal" align="center" style="text-align:center"><b><span style="font-size:10.0pt; font-family:&quot;Calibri&quot;,&quot;sans-serif&quot;; color:white">VOC
                                    <u></u><u></u></span></b></p>
                                </td>
                                <td width="170" nowrap="" style="width:85.15pt; border:solid windowtext 1.0pt; border-left:none; background:#c00000; padding:0cm 3.5pt 0cm 3.5pt; height:45.0pt">
                                    <p class="x_MsoNormal" align="center" style="text-align:center"><b><span style="font-size:10.0pt; font-family:&quot;Calibri&quot;,&quot;sans-serif&quot;; color:white">EOC<u></u><u></u></span></b></p>
                                </td>
                                <td width="150" nowrap="" style="width:75.0pt; border:solid windowtext 1.0pt; border-left:none; background:#c00000; padding:0cm 3.5pt 0cm 3.5pt; height:45.0pt">
                                    <p class="x_MsoNormal" align="center" style="text-align:center"><b><span style="font-size:10.0pt; font-family:&quot;Calibri&quot;,&quot;sans-serif&quot;; color:white">EM<u></u><u></u></span></b></p>
                                </td>
                                <td width="178" nowrap="" style="width:89.0pt; border:solid windowtext 1.0pt; border-left:none; background:#c00000; padding:0cm 3.5pt 0cm 3.5pt; height:45.0pt">
                                    <p class="x_MsoNormal" align="center" style="text-align:center"><b><span style="font-size:10.0pt; font-family:&quot;Calibri&quot;,&quot;sans-serif&quot;; color:white">Entrega servicio<u></u><u></u></span></b></p>
                                </td>
                                <td width="360" nowrap="" style="width:180.0pt; border:solid windowtext 1.0pt; border-left:none; background:#c00000; padding:0cm 3.5pt 0cm 3.5pt; height:45.0pt">
                                    <p class="x_MsoNormal" align="center" style="text-align:center"><b><span style="font-size:10.0pt; font-family:&quot;Calibri&quot;,&quot;sans-serif&quot;; color:white">OBSERVACIONES
                                    <u></u><u></u></span></b></p>
                                </td>
                            </tr>';

        foreach ($ids_otp as $idOtp) {
            //actualizar la ultima fecha de envio de reporte de loa ot padre (CAMILO)
            $this->Dao_ot_padre_model->update_ot_padre(array('ultimo_envio_reporte' => date('Y-m-d')), $idOtp);

            $asunOtp .= $idOtp . ' - ';
            $hitosotp = $this->Dao_ot_padre_model->getInfoHitosByOtpEmail($idOtp);
            $infOtp = $this->Dao_ot_padre_model->getDetailsHitosOTP($idOtp);
            
            switch ($hitosotp[0]->actividad_actual) {
                case 'VISITA OBRA CIVIL':
                    $observaciones = $hitosotp[0]->n_observaciones_voc;
                    break;

                case 'VISITA EJECUCION OBRA CIVIL':
                    $observaciones = $hitosotp[0]->observaciones_eoc;
                    break;
                
                case 'EMPALMES':
                    $observaciones = $hitosotp[0]->observaciones_em;
                    break;
                
                case 'ENTREGA SERVICIO':
                    $observaciones = $hitosotp[0]->observaciones_entrega_servicio;
                    break;
                
                case 'PENDIENTE CLIENTE':
                    $observaciones = $hitosotp[0]->observaciones_genrales;
                    break;
            }
           
            $template .= '
                        <tr>
                            <td width="156" nowrap="" valign="bottom" style="width:78.0pt; border:solid windowtext 1.0pt; border-top:none; padding:0cm 3.5pt 0cm 3.5pt; height:60.0pt">
                                <p class="x_MsoNormal" align="right" style="text-align:right"><span style="font-size:10.0pt; font-family:&quot;Calibri&quot;,&quot;sans-serif&quot;; color:black">' . $idOtp . '<u></u><u></u></span></p>
                            </td>
                            <td width="154" nowrap="" valign="bottom" style="width:77.0pt; border-top:none; border-left:none; border-bottom:solid windowtext 1.0pt; border-right:solid windowtext 1.0pt; padding:0cm 3.5pt 0cm 3.5pt; height:60.0pt">
                                <p class="x_MsoNormal" align="right" style="text-align:right"><span style="font-size:10.0pt; font-family:&quot;Calibri&quot;,&quot;sans-serif&quot;; color:black">' . $infOtp->servicio . '<u></u><u></u></span></p>
                            </td>
                            <td width="124" nowrap="" valign="bottom" style="width:62.05pt; border-top:none; border-left:none; border-bottom:solid windowtext 1.0pt; border-right:solid windowtext 1.0pt; padding:0cm 3.5pt 0cm 3.5pt; height:60.0pt">
                                <p class="x_MsoNormal"><span style="font-size:10.0pt; font-family:&quot;Calibri&quot;,&quot;sans-serif&quot;; color:black">' . $infOtp->ciudad . '
                            <u></u><u></u></span></p>
                            </td>
                            <td width="218" valign="bottom" style="width:108.75pt; border-top:none; border-left:none; border-bottom:solid windowtext 1.0pt; border-right:solid windowtext 1.0pt; padding:0cm 3.5pt 0cm 3.5pt; height:60.0pt">
                                <p class="x_MsoNormal"><span style="font-size:10.0pt; font-family:&quot;Calibri&quot;,&quot;sans-serif&quot;; color:black">' . $infOtp->direccion . '<u></u><u></u></span></p>
                            </td>
                            <td width="150" valign="bottom" style="width:75.0pt; border-top:none; border-left:none; border-bottom:solid windowtext 1.0pt; border-right:solid windowtext 1.0pt; padding:0cm 3.5pt 0cm 3.5pt; height:60.0pt">
                                <p class="x_MsoNormal"><span style="font-size:10.0pt; font-family:&quot;Calibri&quot;,&quot;sans-serif&quot;; color:black">' . $hitosotp[0]->n_estado_voc . ' <br>' . $hitosotp[0]->f_voc . '<u></u><u></u></span></p>
                            </td>
                            <td width="170" valign="bottom" style="width:85.15pt; border-top:none; border-left:none; border-bottom:solid windowtext 1.0pt; border-right:solid windowtext 1.0pt; padding:0cm 3.5pt 0cm 3.5pt; height:60.0pt">
                                <p class="x_MsoNormal"><span style="font-size:10.0pt; font-family:&quot;Calibri&quot;,&quot;sans-serif&quot;; color:black">' . $hitosotp[0]->estado_eoc . ' <br>' . $hitosotp[0]->f_eoc . '
                                <u></u><u></u></span></p>
                            </td>
                            <td width="150" valign="bottom" style="width:75.0pt; border-top:none; border-left:none; border-bottom:solid windowtext 1.0pt; border-right:solid windowtext 1.0pt; padding:0cm 3.5pt 0cm 3.5pt; height:60.0pt">
                                <p class="x_MsoNormal"><span style="font-size:10.0pt; font-family:&quot;Calibri&quot;,&quot;sans-serif&quot;; color:black">&nbsp; ' . $hitosotp[0]->estado_em . ' <br>' . $hitosotp[0]->f_em . '
                                <u></u><u></u></span></p>
                            </td>
                            <td width="178" valign="bottom" style="width:89.0pt; border-top:none; border-left:none; border-bottom:solid windowtext 1.0pt; border-right:solid windowtext 1.0pt; padding:0cm 3.5pt 0cm 3.5pt; height:60.0pt">
                                <p class="x_MsoNormal"><span style="font-size:10.0pt; font-family:&quot;Calibri&quot;,&quot;sans-serif&quot;; color:black">&nbsp; ' . $hitosotp[0]->estado_entrega_servicio . ' <br>' . $hitosotp[0]->f_entrega_servicio . '
                                <u></u><u></u></span></p>
                            </td>
                            <td width="360" valign="bottom" style="width:180.0pt; border-top:none; border-left:none; border-bottom:solid windowtext 1.0pt; border-right:solid windowtext 1.0pt; padding:0cm 3.5pt 0cm 3.5pt; height:60.0pt">
                                <p class="x_MsoNormal"><span style="font-size:10.0pt; font-family:&quot;Calibri&quot;,&quot;sans-serif&quot;; color:black">' . $observaciones . '
                                <u></u><u></u></span></p>
                            </td>
                        </tr>';
//            print_r((($hitosotp[0]->n_estado_voc == '') ? '' : (($hitosotp[0]->n_estado_voc == 'PENDIENTE') ? '<img src="' . URL::base() . "/assets/images/VisitaObraCivil_gris.png" . '"  width="auto" height="110">' : '<img src="' . URL::base() . "/assets/images/VisitaObraCivil_verde.png" . '"  width="auto" height="110">' )));exit();
            $timeline .= '<div dir="ltr">
                            <ul style="font-family:Calibri,Helvetica,sans-serif; margin-bottom:0cm">
                                <li style="list-style-type: none; display: flex; flex-wrap: nowrap;">
                                ' . (($hitosotp[0]->actividad_actual != 'PENDIENTE CLIENTE') ? '' : '<img src="' . URL::base() . "/assets/images/pendienteCliente.png" . '"  width="auto" height="40">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' ) . '
                                <span style="font-size:15.0pt; font-family:&quot;Berlin Sans FB&quot;,sans-serif; color:black; background:white">OT&nbsp;&nbsp;&nbsp;&nbsp;</span>
                                <span style="font-size:15.0pt; color:black; border:none windowtext 1.0pt; padding:0cm">
                                ' . $idOtp . ' – ' . $infOtp->direccion . '</span></li>
                            </ul>
                            <p style="font-family:Calibri,sans-serif; margin:0cm 0cm 0.0001pt 36pt; font-size:11pt; line-height:normal">
                                <span style="font-size:12.0pt; font-family:&quot;Times New Roman&quot;,serif"></span>
                            </p>
                            <!--<ul style="margin-bottom:0cm">
                                <ul style="font-family:Calibri,Helvetica,sans-serif; margin-bottom:0cm">
                                    ' . (($hitosotp[0]->n_estado_voc == '') ? '' : '<li><span style="font-size:12.0pt; font-family:&quot;Times New Roman&quot;,serif">Visita obra civil<span style="">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>' . $hitosotp[0]->n_estado_voc . '</span></li>') . '
                                    ' . (($hitosotp[0]->estado_eoc == '') ? '' : '<li><span style="font-size:12.0pt; font-family:&quot;Times New Roman&quot;,serif">Visita ejecución obra civil<span style="">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>' . $hitosotp[0]->estado_eoc . '</span></li>') . '
                                    ' . (($hitosotp[0]->estado_em == '') ? '' : '<li><span style="font-size:12.0pt; font-family:&quot;Times New Roman&quot;,serif">Empalmes<span style="">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>' . $hitosotp[0]->estado_em . '</span></li>') . '
                                    ' . (($hitosotp[0]->estado_entrega_servicio == '') ? '' : '<li><span style="font-size:12.0pt; font-family:&quot;Times New Roman&quot;,serif">Entrega servicio<span style="">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>' . $hitosotp[0]->estado_entrega_servicio . '</span></li>') . '
                                    <li><span style="font-size:12.0pt; font-family:&quot;Times New Roman&quot;,serif">Observaciones<span style="">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>' . $hitosotp[0]->observaciones_genrales . '</span></li>
                                </ul>
                            </ul>-->
                            <br>
                            <div dir="ltr" style="display: flex; flex-wrap: nowrap; text-align: center;">
                                ' . (($hitosotp[0]->n_estado_voc == '') ? '' : (($hitosotp[0]->n_estado_voc == 'PENDIENTE') ? '<div><b style="font-size:12pt; font-style:inherit; font-variant-ligatures:inherit; font-variant-caps:inherit"><span style="font-size:14.0pt; font-family:&quot;Arial Narrow&quot;,sans-serif">Visita Obra Civil</span></b><br><img src="' . URL::base() . "/assets/images/VisitaObraCivil_gris.png" . '"  width="auto" height="110"><br><b style="font-size:12pt; font-style:inherit; font-variant-ligatures:inherit; font-variant-caps:inherit"><span style="font-size:14.0pt; font-family:&quot;Arial Narrow&quot;,sans-serif">' . $hitosotp[0]->f_voc . '</span></b></div>' : '<div><b style="font-size:12pt; font-style:inherit; font-variant-ligatures:inherit; font-variant-caps:inherit"><span style="font-size:14.0pt; font-family:&quot;Arial Narrow&quot;,sans-serif">Visita Obra Civil</span></b><br><img src="' . URL::base() . "/assets/images/VisitaObraCivil_verde.png" . '"  width="auto" height="110"><br><b style="font-size:12pt; font-style:inherit; font-variant-ligatures:inherit; font-variant-caps:inherit"><span style="font-size:14.0pt; font-family:&quot;Arial Narrow&quot;,sans-serif">' . $hitosotp[0]->f_voc . '</span></b></div>' )) . '
                                ' . (($hitosotp[0]->estado_eoc == '') ? '' : (($hitosotp[0]->estado_eoc == 'PENDIENTE') ? '<div><b style="font-size:12pt; font-style:inherit; font-variant-ligatures:inherit; font-variant-caps:inherit"><span style="font-size:14.0pt; font-family:&quot;Arial Narrow&quot;,sans-serif">Ejecución Obra Civil</span></b><br><img src="' . URL::base() . "/assets/images/ejecucionObraCilvil_gris.png" . '"  width="auto" height="110"><br><b style="font-size:12pt; font-style:inherit; font-variant-ligatures:inherit; font-variant-caps:inherit"><span style="font-size:14.0pt; font-family:&quot;Arial Narrow&quot;,sans-serif">' . $hitosotp[0]->f_eoc . '</span></b></div>' : '<div><b style="font-size:12pt; font-style:inherit; font-variant-ligatures:inherit; font-variant-caps:inherit"><span style="font-size:14.0pt; font-family:&quot;Arial Narrow&quot;,sans-serif">Ejecución Obra Civil</span></b><br><img src="' . URL::base() . "/assets/images/ejecucionObraCivil_verde.png" . '"  width="auto" height="110"><br><b style="font-size:12pt; font-style:inherit; font-variant-ligatures:inherit; font-variant-caps:inherit"><span style="font-size:14.0pt; font-family:&quot;Arial Narrow&quot;,sans-serif">' . $hitosotp[0]->f_eoc . '</span></b></div>' )) . '
                                ' . (($hitosotp[0]->estado_em == '') ? '' : (($hitosotp[0]->estado_em == 'PENDIENTE') ? '<div><b style="font-size:12pt; font-style:inherit; font-variant-ligatures:inherit; font-variant-caps:inherit"><span style="font-size:14.0pt; font-family:&quot;Arial Narrow&quot;,sans-serif">Empalmes</span></b><br><img src="' . URL::base() . "/assets/images/Empalme_gris.png" . '"  width="auto" height="110"><br><b style="font-size:12pt; font-style:inherit; font-variant-ligatures:inherit; font-variant-caps:inherit"><span style="font-size:14.0pt; font-family:&quot;Arial Narrow&quot;,sans-serif">' . $hitosotp[0]->f_em . '</span></b></div>' : '<div><b style="font-size:12pt; font-style:inherit; font-variant-ligatures:inherit; font-variant-caps:inherit"><span style="font-size:14.0pt; font-family:&quot;Arial Narrow&quot;,sans-serif">Empalmes</span></b><br><img src="' . URL::base() . "/assets/images/Empalme_verde.png" . '"  width="auto" height="110"><br><b style="font-size:12pt; font-style:inherit; font-variant-ligatures:inherit; font-variant-caps:inherit"><span style="font-size:14.0pt; font-family:&quot;Arial Narrow&quot;,sans-serif">' . $hitosotp[0]->f_em . '</span></b></div>' )) . '
                                ' . (($hitosotp[0]->estado_entrega_servicio == '') ? '' : (($hitosotp[0]->estado_entrega_servicio == 'PENDIENTE') ? '<div><b style="font-size:12pt; font-style:inherit; font-variant-ligatures:inherit; font-variant-caps:inherit"><span style="font-size:14.0pt; font-family:&quot;Arial Narrow&quot;,sans-serif">Entrega Servicio</span></b><br><img src="' . URL::base() . "/assets/images/entregaServicio_gris.png" . '"  width="auto" height="110"><br><b style="font-size:12pt; font-style:inherit; font-variant-ligatures:inherit; font-variant-caps:inherit"><span style="font-size:14.0pt; font-family:&quot;Arial Narrow&quot;,sans-serif">' . $hitosotp[0]->f_entrega_servicio . '</span></b></div>' : '<div><b style="font-size:12pt; font-style:inherit; font-variant-ligatures:inherit; font-variant-caps:inherit"><span style="font-size:14.0pt; font-family:&quot;Arial Narrow&quot;,sans-serif">Entrega Servicio</span></b><br><img src="' . URL::base() . "/assets/images/entregaServicio_verde.png" . '"  width="auto" height="110"><br><b style="font-size:12pt; font-style:inherit; font-variant-ligatures:inherit; font-variant-caps:inherit"><span style="font-size:14.0pt; font-family:&quot;Arial Narrow&quot;,sans-serif">' . $hitosotp[0]->f_entrega_servicio . '</span></b></div>' )) . '
                            </div>
                        </div>
                        <br><br>
                        ';
           
        }

        $template .= '</tbody>
                </table>
            </div>
            <br>
            <div dir="ltr">
                <ul style="font-family:Calibri,Helvetica,sans-serif; margin-bottom:0cm">
                    <li>
                        <span style="font-size:10.0pt; color:black; border:none windowtext 1.0pt; padding:0cm">
                            ' . $observacionesEmail . '
                        </span>
                    </li>
                </ul>
            </div>
            <br><br>';
        
//        echo("<pre>"); print_r($template); echo("</pre>");exit();

        $encabezado = '
            <p class="x_MsoNormal"><span style="font-family: Arial, sans-serif, serif, EmojiFont;">Cordial Saludo</span></p>
            <!--<p class="x_MsoNormal"><span style="font-family: Arial, sans-serif, serif, EmojiFont;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></p>
            <p class="x_MsoNormal"><span style="font-family: Arial, sans-serif, serif, EmojiFont;">Señor(a):</span></p>
            <p class="x_MsoNormal" style="text-align:justify"><strong><span style="font-family: Arial, sans-serif, serif, EmojiFont;">' . $senior . '</span></strong></p>-->
            <p class="x_MsoNormal" style="text-align:justify"><span style="font-family: Arial, sans-serif, serif, EmojiFont;">&nbsp;</span></p>
            <p class="x_MsoNormal"><span style="font-family: Arial, sans-serif, serif, EmojiFont;">Comprometidos con el servicio y el cumplimiento de sus solicitudes me permito notificar los avances de los asuntos en curso. Es de suma importancia que sea revisado y nos retroalimente con &nbsp;sus comentarios, ya que al término de 2 días hábiles este reporte se dará por aceptado.</span></p>
            <p class="x_MsoNormal">&nbsp;</p>
            <!--<p class="x_MsoListParagraph" style="text-indent:-18.0pt; text-autospace:none"><span style="font-family: Symbol, serif, EmojiFont;"><span style=""><span style="font: 7pt &quot;Times New Roman&quot;, serif, EmojiFont;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </span></span></span><strong><span lang="EN-US" style="font-family: Arial, sans-serif, serif, EmojiFont;">&nbsp;OT DESTINO &nbsp;</span></strong><strong><span style="font-family: Arial, sans-serif, serif, EmojiFont;">' . substr($asunOtp, 0, -2) . '</span></strong><strong><span lang="EN-US" style="font-family: Arial, sans-serif, serif, EmojiFont;">: </span></strong><span lang="EN-US" style="font-family: Arial, sans-serif, serif, EmojiFont;">' . $infOtp->servicio . ' </span><span style="font-family: Arial, sans-serif, serif, EmojiFont;"><strong></strong></span></p>
            <p class="x_MsoNormal" style="text-autospace:none"><strong><span style="font-family: Arial, sans-serif, serif, EmojiFont;">Ciudad: </span></strong><span style="font-family: Arial, sans-serif, serif, EmojiFont;">' . $infOtp->ciudad . '</span><span style="font-family: Arial, sans-serif, serif, EmojiFont;"></span></p>
            <p class="x_MsoNormal" style="text-autospace:none"><strong><span style="font-family: Arial, sans-serif, serif, EmojiFont;">Dirección de servicio: </span></strong><span style="font-family: Arial, sans-serif, serif, EmojiFont;">' . $direccionCierreOtp . '&nbsp; </span><strong><span style="font-family: Arial, sans-serif, serif, EmojiFont;"></span></strong></p>
            <p class="x_MsoNormal"><strong><span style="font-family: Arial, sans-serif, serif, EmojiFont;">Cliente: &nbsp;</span></strong><span style="font-family: Arial, sans-serif, serif, EmojiFont;">' . $configuracion . '<strong></strong></span></p>
            <p class="x_MsoNormal"><strong><span style="font-family: Arial, sans-serif, serif, EmojiFont;">Entrega del servicio:</span></strong><span style="font-family: Arial, sans-serif, serif, EmojiFont;"> ' . $entregaServicio . ' </span><span lang="ES" style="font-family: Arial, sans-serif, serif, EmojiFont;">(Fecha sujeta a cambios en caso de tener algún inconveniente o adelantos en el proceso de instalación). </span><span style="font-family: Arial, sans-serif, serif, EmojiFont;">&nbsp;</span></p>
            <p class="x_MsoNormal" style="text-align:justify"><span style="text-decoration:underline"><span lang="ES" style="font-family: Arial, sans-serif, serif, EmojiFont;">De acuerdo a lo anterior, solicitamos de su colaboración confirmado la siguiente información:</span></span></p>
            <p class="x_MsoListParagraph" style="text-indent:-18.0pt"><span style="font-family: Symbol, serif, EmojiFont;"><span style="">·<span style="font: 7pt &quot;Times New Roman&quot;, serif, EmojiFont;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </span></span></span><span style="font-family: Arial, sans-serif, serif, EmojiFont;"><br><b>Observaciones: </b>&nbsp;' . $observacionesEmail . '</span></p>
            <p class="x_MsoListParagraph"><span style="font-family: Arial, sans-serif, serif, EmojiFont;">&nbsp;</span></p>-->
            <br><br>';

        $contacto = '
            <p class="x_MsoNormal"><span style="font-family: Arial, sans-serif, serif, EmojiFont;">Durante todo el Proceso de Instalación puede contactar a:</span></p>
            <p class="x_MsoNormal"><span style="font-family: Arial, sans-serif, serif, EmojiFont;">&nbsp;</span></p>
            <p class="x_MsoNormal"><strong><span style="font-family: Arial, sans-serif, serif, EmojiFont;">Nivel de Contacto 1:</span></strong><span style="font-family: Arial, sans-serif, serif, EmojiFont;"> Para cualquier duda o inquietud sobre el proceso.</span></p>
            <p class="x_MsoNormal"><span style="font-family: Arial, sans-serif, serif, EmojiFont;">Ingeniero Implementación Responsable Cuenta: &nbsp;' . $ingeniero . '</span></p>
            <p class="x_MsoNormal"><span style="font-family: Arial, sans-serif, serif, EmojiFont;">Ingeniero Aprovisionamiento Estándar</span></p>
            <p class="x_MsoNormal"><span style="font-family: Arial, sans-serif, serif, EmojiFont;">Celular: </span><span style="font-family: Arial, sans-serif, serif, EmojiFont;">' . $celIngeniero . '</span><span style="font-family: Arial, sans-serif, serif, EmojiFont;"></span></p>
            <p class="x_MsoNormal"><span style="font-family: Arial, sans-serif, serif, EmojiFont;">Correo electrónico: </span><span style="font-family: Arial, sans-serif, serif, EmojiFont; color: rgb(79, 129, 189);"><a href="mailto:' . $email . '" target="_blank" rel="noopener noreferrer" data-auth="NotApplicable"><span style="color:#4F81BD">' . $email . '</span></a></span><span style="font-family: Arial, sans-serif, serif, EmojiFont; color: black;">&nbsp;</span><span style="font-family: Arial, sans-serif, serif, EmojiFont;"></span></p>
            <p class="x_MsoNormal"><span style="font-family: Arial, sans-serif, serif, EmojiFont;">&nbsp;</span></p>


            <p class="m_-5751456617445139844xmsonormal" style="text-align:justify"><strong><span style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;">Nivel de Contacto 2:</span></strong><span style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;"> En caso de que no se obtenga respuesta por parte del Nivel de Contacto &nbsp;1.</span><u></u><u></u></p>

            <p class="m_-5751456617445139844xmsonormal"><span style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;">Coordinador Estándar: <span style="color:#1f497d">&nbsp;</span>Gustavo Gonzalez &nbsp;</span><u></u><u></u></p>
            <p class="MsoNormal"><span style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;">Celular:</span> 3142197626 - 3203278451<u></u><u></u></p>
            <p class="m_-5751456617445139844xmsonormal"><span style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;">Correo: <span style="color:#4f81bd"> <a href="mailto:gustavo.gonzalez.ext@claro.com.co" target="_blank"><span style="color:#4f81bd">gustavo.gonzalez.ext@claro.<wbr>com.co</span></a> </span></span><u></u><u></u></p>
            <p class="MsoNormal"><u></u>&nbsp;<u></u></p>
            <p class="m_-5751456617445139844xmsonormal" style="text-align:justify"><strong><span style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;">Nivel de Contacto 3:</span></strong><span style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;"> En caso de que no se obtenga respuesta por parte del Nivel de Contacto &nbsp;1.</span><u></u><u></u></p>
            <p class="m_-5751456617445139844xmsonormal"><span style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;">Coordinador Estándar: &nbsp;Alejandra Rendon Calderon<u></u><u></u></span></p>
            <p class="m_-5751456617445139844xmsonormal"><span style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;">Correo: <span style="color:#4f81bd"> <a href="mailto:alejandra.rendon.ext@claro.com.co" target="_blank"><span style="color:#4f81bd">alejandra.rendon.ext@claro.<wbr>com.co</span></a> </span></span><u></u><u></u></p>
            <p class="MsoNormal"><u></u>&nbsp;<u></u></p>

            <p class="x_MsoNormal"><span style="font-family: Arial, sans-serif, serif, EmojiFont; color: rgb(31, 73, 125);">&nbsp;</span></p>
            <p class="x_MsoNormal"><span style="font-family: Arial, sans-serif, serif, EmojiFont;">Gracias por la atención prestada y quedo atento a sus comentarios.</span></p>
            <p class="x_MsoNormal"><span style="font-family: Arial, sans-serif, serif, EmojiFont;">&nbsp;</span></p>';

        $res = $this->Dao_email_model->h_enviarCorreo($encabezado . $template . $encabezado_timeline .$timeline . $contacto, $email, 'REPORTE DE ACTUALIZACION DE ACTIVIDADES ' . strtoupper((isset($detCierreOtp->servicio) ? $detCierreOtp->servicio : $infOtp->servicio)) . ' - ' . $infOtp->n_nombre_cliente . ' / OT ' . substr($asunOtp, 0, -2));
        echo json_encode($res);
    }

}
