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
        $estados_arr = ['Cancelada','Cerrada','3- Terminada'];

        // Contadores generales
        $cont_total_in_otp = 0;
        $cont_total_out_otp = 0;
        $cont_total_hoy_otp = 0;
        $cont_total_ejec_otp = 0;
        $cont_total_otp = 0;
        // fin contadores generales
        $otp_ejecutadas = [];// array para contar las otp ejecutadas
        $ingenieros = [];// objetro final
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
                }

                else if ($ingenieros[$general[$i]->k_id_user]['hoy'] > 0) {
                    $ingenieros[$general[$i]->k_id_user]['color'] = "btn_orange";
                }

                else {
                    $ingenieros[$general[$i]->k_id_user]['color'] = "btn_green";
                }
                // *************************************fin calculo colores*************************************


                // Si entra en este bloque la ot padre no tiene que estar en el arreglo de ejecutadas
                if ( in_array($general[$i]->k_id_ot_padre , $ingenieros[$general[$i]->k_id_user]['ejecutadas'])) {
                    // si existe se elimina del array y se decrementas los contadores
                    $ingenieros[$general[$i]->k_id_user]['ejecutadas'] = array_values(array_diff($ingenieros[$general[$i]->k_id_user]['ejecutadas'], array($general[$i]->k_id_ot_padre)));
                    $cont_total_otp--;
                    $cont_total_ejec_otp--;

                }
            // Si está cerrada finalizada o cancelada
            } else {
                // si la otp no existe en el array de ejecutadas y tampoco se ha creado en el array del ingeniero la posicion de la otp
                if (!in_array($general[$i]->k_id_ot_padre ,$ingenieros[$general[$i]->k_id_user]['ejecutadas']) && !array_key_exists($general[$i]->k_id_ot_padre, $ingenieros[$general[$i]->k_id_user] )) {
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
        $otPadreList = $this->Dao_ot_padre_model->getOtpByOpcList($opcion,$filtro);

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
        $newFields = array();
        $newFields['id_ot_padre'] = $idOtp;
        $newFields['actividad_actual'] = $actividadActual;

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
                
                case 'CONFIGURACION RED CLARO':
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

        $res = $this->Dao_ot_padre_model->saveHitosOtp(array_filter($newFields));
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
        $direccionCierreOtp = implode(',' , $this->getDireccionCierreOTP($ids_in));
        $detCierreOtp = $this->Dao_cierre_ots_model->getDetailsCierreOTP($ids_in);

        /*print_r($direccionCierreOtp);
       exit();*/

        foreach ($ids_otp as $idOtp) {
            //actualizar la ultima fecha de envio de reporte de loa ot padre (CAMILO)
            $this->Dao_ot_padre_model->update_ot_padre(array('ultimo_envio_reporte' => date('Y-m-d')), $idOtp);

            $asunOtp .= $idOtp . ' - ';
            $hitosotp = $this->Dao_ot_padre_model->getHitosOtp($idOtp);
            $infOtp = $this->Dao_ot_padre_model->getDetailsHitosOTP($idOtp);
            $observaciones = $hitosotp->observaciones_ko . '<br><br>' .
                    $hitosotp->observaciones_voc . '<br><br>' .
                    $hitosotp->observaciones_ec . '<br><br>' .
                    $hitosotp->observaciones_ac . '<br><br>' .
                    $hitosotp->observaciones_sit . '<br><br>' .
                    $hitosotp->observaciones_veoc . '<br><br>' .
                    $hitosotp->observaciones_empalmes . '<br><br>' .
                    $hitosotp->observaciones_crc . '<br><br>' .
                    $hitosotp->observaciones_veut;
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
                            </tr>
                            <tr height="20" style="height:15pt">
                                <td height="15" class="m_-7809522729103588979gmail-xl65" style="height:15pt;border-top:none;border-right:0.5pt solid windowtext;border-bottom:0.5pt solid windowtext;border-left:0.5pt solid windowtext;padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:middle;white-space:nowrap"><div style="color: #fff; width: 30px; height: 30px; line-height: 30px; font-size: 22px; text-align: center; top: 18px; left: 50%; margin-left: -25px; border: 3px solid #ffffff; z-index: 100; border-top-right-radius: 50%; border-top-left-radius: 50%; border-bottom-right-radius: 50%; border-bottom-left-radius: 50%; background-color: ' . ($hitosotp->actividad_actual == 'KICK OFF' ? '#4bd605' : '#7c7c7c') . ';">1</div></td>
                                <td class="m_-7809522729103588979gmail-xl65" style="border-top:none;border-left:none;border-right:0.5pt solid windowtext;border-bottom:0.5pt solid windowtext;padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:middle;white-space:nowrap">KICK OFF</td>
                                <td class="m_-7809522729103588979gmail-xl65" style="border-top:none;border-left:none;box-sizing:content-box;border-right:0.5pt solid windowtext;border-bottom:0.5pt solid windowtext;padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:middle;white-space:nowrap">&nbsp;' . $hitosotp->f_compromiso_ko . '</td>
                                <td class="m_-7809522729103588979gmail-xl65" style="border-top:none;border-left:none;border-right:0.5pt solid windowtext;border-bottom:0.5pt solid windowtext;padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:middle;white-space:nowrap">&nbsp;' . $hitosotp->estado_ko . '</td>
                                <td colspan="2" rowspan="10" class="m_-7809522729103588979gmail-xl75" style="border-width:0.5pt;border-style:solid;border-color:windowtext black black windowtext;text-align:left;padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:middle;white-space:nowrap">&nbsp;' . $observaciones . '</td>
                            </tr>
                            <tr height="20" style="height:15pt">
                                <td height="15" class="m_-7809522729103588979gmail-xl67" style="height:30pt;border-top:none;text-align:center;border-right:0.5pt solid windowtext;border-bottom:0.5pt solid windowtext;border-left:0.5pt solid windowtext;padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:middle;white-space:nowrap"><div style="color: #fff; width: 30px; height: 30px; line-height: 30px; font-size: 22px; text-align: center; top: 18px; left: 50%; margin-left: -25px; border: 3px solid #ffffff; z-index: 100; border-top-right-radius: 50%; border-top-left-radius: 50%; border-bottom-right-radius: 50%; border-bottom-left-radius: 50%; background-color: ' . (($hitosotp->actividad_actual == 'VISITA OBRA CIVIL' || $hitosotp->actividad_actual == 'VISITA OBRA CIVIL TERCEROS') ? '#4bd605' : '#7c7c7c') . ';">2</div></td>
                                <td class="m_-7809522729103588979gmail-xl65" style="border-top:none;border-left:none;border-right:0.5pt solid windowtext;border-bottom:0.5pt solid windowtext;padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:middle;white-space:nowrap">' . $hitosotp->tipo_voc . '</td>
                                <td class="m_-7809522729103588979gmail-xl65" style="border-top:none;border-left:none;border-right:0.5pt solid windowtext;border-bottom:0.5pt solid windowtext;padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:middle;white-space:nowrap">&nbsp;' . $hitosotp->f_compromiso_voc . '</td>
                                <td class="m_-7809522729103588979gmail-xl65" style="border-top:none;border-left:none;border-right:0.5pt solid windowtext;border-bottom:0.5pt solid windowtext;padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:middle;white-space:nowrap">&nbsp;' . $hitosotp->estado_voc . '</td>
                            </tr>
                            <tr height="20" style="height:15pt">
                                <td height="15" class="m_-7809522729103588979gmail-xl65" style="height:15pt;border-top:none;border-right:0.5pt solid windowtext;border-bottom:0.5pt solid windowtext;border-left:0.5pt solid windowtext;padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:middle;white-space:nowrap"><div style="color: #fff; width: 30px; height: 30px; line-height: 30px; font-size: 22px; text-align: center; top: 18px; left: 50%; margin-left: -25px; border: 3px solid #ffffff; z-index: 100; border-top-right-radius: 50%; border-top-left-radius: 50%; border-bottom-right-radius: 50%; border-bottom-left-radius: 50%; background-color: ' . ($hitosotp->actividad_actual == 'ENVIO COTIZACION' ? '#4bd605' : '#7c7c7c') . ';">3</div></td>
                                <td class="m_-7809522729103588979gmail-xl65" style="border-top:none;border-left:none;border-right:0.5pt solid windowtext;border-bottom:0.5pt solid windowtext;padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:middle;white-space:nowrap">ENVIÓ COTIZACIÓN</td>
                                <td class="m_-7809522729103588979gmail-xl65" style="border-top:none;border-left:none;border-right:0.5pt solid windowtext;border-bottom:0.5pt solid windowtext;padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:middle;white-space:nowrap">&nbsp;' . $hitosotp->f_compromiso_ec . '</td>
                                <td class="m_-7809522729103588979gmail-xl65" style="border-top:none;border-left:none;border-right:0.5pt solid windowtext;border-bottom:0.5pt solid windowtext;padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:middle;white-space:nowrap">&nbsp;' . $hitosotp->estado_ec . '</td>
                            </tr>
                            <tr height="20" style="height:15pt">
                                <td height="15" class="m_-7809522729103588979gmail-xl65" style="height:15pt;border-top:none;border-right:0.5pt solid windowtext;border-bottom:0.5pt solid windowtext;border-left:0.5pt solid windowtext;padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:middle;white-space:nowrap"><div style="color: #fff; width: 30px; height: 30px; line-height: 30px; font-size: 22px; text-align: center; top: 18px; left: 50%; margin-left: -25px; border: 3px solid #ffffff; z-index: 100; border-top-right-radius: 50%; border-top-left-radius: 50%; border-bottom-right-radius: 50%; border-bottom-left-radius: 50%; background-color: ' . ($hitosotp->actividad_actual == 'APROBACION COTIZACION' ? '#4bd605' : '#7c7c7c') . ';">4</div></td>
                                <td class="m_-7809522729103588979gmail-xl65" style="border-top:none;border-left:none;border-right:0.5pt solid windowtext;border-bottom:0.5pt solid windowtext;padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:middle;white-space:nowrap">APROBACIÓN COTIZACIÓN</td>
                                <td class="m_-7809522729103588979gmail-xl65" style="border-top:none;border-left:none;border-right:0.5pt solid windowtext;border-bottom:0.5pt solid windowtext;padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:middle;white-space:nowrap">&nbsp;' . $hitosotp->f_compromiso_ac . '</td>
                                <td class="m_-7809522729103588979gmail-xl65" style="border-top:none;border-left:none;border-right:0.5pt solid windowtext;border-bottom:0.5pt solid windowtext;padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:middle;white-space:nowrap">&nbsp;' . $hitosotp->estado_ac . '</td>
                            </tr>
                            <tr height="20" style="height:15pt">
                                <td height="15" class="m_-7809522729103588979gmail-xl65" style="height:15pt;border-top:none;border-right:0.5pt solid windowtext;border-bottom:0.5pt solid windowtext;border-left:0.5pt solid windowtext;padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:middle;white-space:nowrap"><div style="color: #fff; width: 30px; height: 30px; line-height: 30px; font-size: 22px; text-align: center; top: 18px; left: 50%; margin-left: -25px; border: 3px solid #ffffff; z-index: 100; border-top-right-radius: 50%; border-top-left-radius: 50%; border-bottom-right-radius: 50%; border-bottom-left-radius: 50%; background-color: ' . ($hitosotp->actividad_actual == 'SOLICITUD INFORMACIÓN TECNICA' ? '#4bd605' : '#7c7c7c') . ';">5</div></td>
                                <td class="m_-7809522729103588979gmail-xl65" style="border-top:none;border-left:none;border-right:0.5pt solid windowtext;border-bottom:0.5pt solid windowtext;padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:middle;white-space:nowrap">SOLICITUD INFORMACIÓN TÉCNICA</td>
                                <td class="m_-7809522729103588979gmail-xl65" style="border-top:none;border-left:none;border-right:0.5pt solid windowtext;border-bottom:0.5pt solid windowtext;padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:middle;white-space:nowrap">&nbsp;' . $hitosotp->f_compromiso_sit . '</td>
                                <td class="m_-7809522729103588979gmail-xl65" style="border-top:none;border-left:none;border-right:0.5pt solid windowtext;border-bottom:0.5pt solid windowtext;padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:middle;white-space:nowrap">&nbsp;' . $hitosotp->estado_sit . '</td>
                            </tr>
                            <tr height="20" style="height:15pt">
                                <td height="15" class="m_-7809522729103588979gmail-xl73" style="border-bottom:0.5pt solid black;height:30pt;border-top:none;text-align:center;border-right:0.5pt solid windowtext;border-left:0.5pt solid windowtext;padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:middle;white-space:nowrap"><div style="color: #fff; width: 30px; height: 30px; line-height: 30px; font-size: 22px; text-align: center; top: 18px; left: 50%; margin-left: -25px; border: 3px solid #ffffff; z-index: 100; border-top-right-radius: 50%; border-top-left-radius: 50%; border-bottom-right-radius: 50%; border-bottom-left-radius: 50%; background-color: ' . ($hitosotp->actividad_actual == 'VISITA EJECUCION OBRA CIVIL' || $hitosotp->actividad_actual == 'VISITA EJECUCION OBRA CIVIL TERCERO' ? '#4bd605' : '#7c7c7c') . ';">6</div></td>
                                <td class="m_-7809522729103588979gmail-xl65" style="border-top:none;border-left:none;border-right:0.5pt solid windowtext;border-bottom:0.5pt solid windowtext;padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:middle;white-space:nowrap">' . $hitosotp->tipo_veoc . '</td>
                                <td class="m_-7809522729103588979gmail-xl65" style="border-top:none;border-left:none;border-right:0.5pt solid windowtext;border-bottom:0.5pt solid windowtext;padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:middle;white-space:nowrap">&nbsp;' . $hitosotp->f_compromiso_veoc . '</td>
                                <td class="m_-7809522729103588979gmail-xl65" style="border-top:none;border-left:none;border-right:0.5pt solid windowtext;border-bottom:0.5pt solid windowtext;padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:middle;white-space:nowrap">&nbsp;' . $hitosotp->estado_veoc . '</td>
                            </tr>
                            <tr height="20" style="height:15pt">
                            <td height="15" class="m_-7809522729103588979gmail-xl65" style="height:15pt;border-top:none;border-right:0.5pt solid windowtext;border-bottom:0.5pt solid windowtext;border-left:0.5pt solid windowtext;padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:middle;white-space:nowrap"><div style="color: #fff; width: 30px; height: 30px; line-height: 30px; font-size: 22px; text-align: center; top: 18px; left: 50%; margin-left: -25px; border: 3px solid #ffffff; z-index: 100; border-top-right-radius: 50%; border-top-left-radius: 50%; border-bottom-right-radius: 50%; border-bottom-left-radius: 50%; background-color: ' . ($hitosotp->actividad_actual == 'EMPALMES' ? '#4bd605' : '#7c7c7c') . ';">7</div></td>
                            <td class="m_-7809522729103588979gmail-xl65" style="border-top:none;border-left:none;border-right:0.5pt solid windowtext;border-bottom:0.5pt solid windowtext;padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:middle;white-space:nowrap">EMPALMES</td>
                            <td class="m_-7809522729103588979gmail-xl65" style="border-top:none;border-left:none;border-right:0.5pt solid windowtext;border-bottom:0.5pt solid windowtext;padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:middle;white-space:nowrap">&nbsp;' . $hitosotp->f_compromiso_empalmes . '</td>
                            <td class="m_-7809522729103588979gmail-xl65" style="border-top:none;border-left:none;border-right:0.5pt solid windowtext;border-bottom:0.5pt solid windowtext;padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:middle;white-space:nowrap">&nbsp;' . $hitosotp->estado_empalmes . '</td>
                            </tr>
                            <tr height="20" style="height:15pt">
                                <td height="15" class="m_-7809522729103588979gmail-xl65" style="height:15pt;border-top:none;border-right:0.5pt solid windowtext;border-bottom:0.5pt solid windowtext;border-left:0.5pt solid windowtext;padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:middle;white-space:nowrap"><div style="color: #fff; width: 30px; height: 30px; line-height: 30px; font-size: 22px; text-align: center; top: 18px; left: 50%; margin-left: -25px; border: 3px solid #ffffff; z-index: 100; border-top-right-radius: 50%; border-top-left-radius: 50%; border-bottom-right-radius: 50%; border-bottom-left-radius: 50%; background-color: ' . ($hitosotp->actividad_actual == 'CONFIGURACION RED CLARO' ? '#4bd605' : '#7c7c7c') . ';">8</div></td>
                                <td class="m_-7809522729103588979gmail-xl65" style="border-top:none;border-left:none;border-right:0.5pt solid windowtext;border-bottom:0.5pt solid windowtext;padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:middle;white-space:nowrap">CONFIGURACIÓN RED CLARO</td>
                                <td class="m_-7809522729103588979gmail-xl65" style="border-top:none;border-left:none;border-right:0.5pt solid windowtext;border-bottom:0.5pt solid windowtext;padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:middle;white-space:nowrap">&nbsp;' . $hitosotp->f_compromiso_crc . '</td>
                                <td class="m_-7809522729103588979gmail-xl65" style="border-top:none;border-left:none;border-right:0.5pt solid windowtext;border-bottom:0.5pt solid windowtext;padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:middle;white-space:nowrap">&nbsp;' . $hitosotp->estado_crc . '</td>
                            </tr>
                            <tr height="20" style="height:15pt">
                                <td height="15" class="m_-7809522729103588979gmail-xl65" style="height:15pt;border-top:none;border-right:0.5pt solid windowtext;border-bottom:0.5pt solid windowtext;border-left:0.5pt solid windowtext;padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:middle;white-space:nowrap"><div style="color: #fff; width: 30px; height: 30px; line-height: 30px; font-size: 22px; text-align: center; top: 18px; left: 50%; margin-left: -25px; border: 3px solid #ffffff; z-index: 100; border-top-right-radius: 50%; border-top-left-radius: 50%; border-bottom-right-radius: 50%; border-bottom-left-radius: 50%; background-color: ' . ($hitosotp->actividad_actual == 'VISITA ENTREGA UM TERCEROS' ? '#4bd605' : '#7c7c7c') . ';">9</div></td>
                                <td class="m_-7809522729103588979gmail-xl65" style="border-top:none;border-left:none;border-right:0.5pt solid windowtext;border-bottom:0.5pt solid windowtext;padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:middle;white-space:nowrap">ENTREGA SERVICIO</td>
                                <td class="m_-7809522729103588979gmail-xl65" style="border-top:none;border-left:none;border-right:0.5pt solid windowtext;border-bottom:0.5pt solid windowtext;padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:middle;white-space:nowrap">&nbsp;' . $hitosotp->f_compromiso_veut . '</td>
                                <td class="m_-7809522729103588979gmail-xl65" style="border-top:none;border-left:none;border-right:0.5pt solid windowtext;border-bottom:0.5pt solid windowtext;padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:middle;white-space:nowrap">&nbsp;' . $hitosotp->estado_veut . '</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <br><br>';
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
            <p class="m_-5751456617445139844xmsonormal"><span style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;">Coordinador Estándar: &nbsp;Maria Marcela Rojas<u></u><u></u></span></p>
            <p class="m_-5751456617445139844xmsonormal"><span style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;">Teléfono.7500300&nbsp; Ext 83037 &nbsp;&nbsp;Celular 3133337675<u></u><u></u></span></p>
            <p class="m_-5751456617445139844xmsonormal"><span style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;">Correo: <a href="mailto:maria.rojasa@claro.com.co" target="_blank"> maria.rojasa@claro.com.co</a><u></u><u></u></span></p>
            <p class="MsoNormal"><u></u>&nbsp;<u></u></p>

            <p class="x_MsoNormal"><span style="font-family: Arial, sans-serif, serif, EmojiFont; color: rgb(31, 73, 125);">&nbsp;</span></p>
            <p class="x_MsoNormal"><span style="font-family: Arial, sans-serif, serif, EmojiFont;">Gracias por la atención prestada y quedo atento a sus comentarios.</span></p>
            <p class="x_MsoNormal"><span style="font-family: Arial, sans-serif, serif, EmojiFont;">&nbsp;</span></p>';

        $res = $this->Dao_email_model->h_enviarCorreo($encabezado . $template . $contacto, $email, 'REPORTE DE ACTUALIZACION DE ACTIVIDADES ' . strtoupper((isset($detCierreOtp->servicio) ? $detCierreOtp->servicio : $infOtp->servicio)) . ' - ' . $infOtp->n_nombre_cliente . ' / OT ' . substr($asunOtp, 0, -2));
//        print_r($template);
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
        /*$detCierreOtp = $this->Dao_cierre_ots_model->getDetailsCierreOTP($ids_in);*/
        $DirCierreOTP = $this->Dao_ot_hija_model->get_direccionservicio($ids_in);

        return array_column($DirCierreOTP, 'direccion_destino');
    }


    public function c_getInfoEmailreport()
    {
        $idsOtp = $this->input->post("idsOtp");

        //cuenta cuantas filas están seleccionadas
        $seleccionadas = count($idsOtp);

        $exist = $this->Dao_ot_padre_model->getInfoEmailReport($idsOtp);

        if ($seleccionadas == 1) {
            //verifica si existe en la base de datos, si no, extraerá la info de la linea base
            if ($exist) {
                $ultimo_en_enviar = $this->Dao_ot_padre_model->getLastMailSent(implode(',',$idsOtp));
                echo json_encode($ultimo_en_enviar);
            }else{
                //si no existe en la tabla de reporte_info, buscará si existe en la linea base
                $fLineaBase = $this->Dao_ot_padre_model->getFechaLineaBaseEmailReport($idsOtp);
                if ($fLineaBase) {
                    $answer['fecha_compromiso'] = $fLineaBase->fecha_compromiso;
                    echo json_encode($answer);
                }else{

                    //si no retorna sin data
                    echo json_encode('sin data');
                }
            }
        }
        else{
            //entra si hay más de una seleccion

            $answer = array();
            //creo el arreglo que devolveré

            for ($i=0; $i < $seleccionadas; $i++) {
                if($this->Dao_ot_padre_model->getLastMailSent($idsOtp[$i])){
                    //si existe algo en la tabla reporte_info, lo pondrá en el arreglo
                    array_push($answer,$this->Dao_ot_padre_model->getLastMailSent($idsOtp[$i]));
                }else if($this->Dao_ot_padre_model->getFechaLineaBaseEmailReport($idsOtp[$i])){
                    //si no, intentará obtener si existe algo en la linea base
                    $flb['fecha_compromiso'] = $this->Dao_ot_padre_model->getFechaLineaBaseEmailReport($idsOtp[$i])->fecha_compromiso;
                    array_push($answer,$flb);
                }else{
                    // si no, retornará sin data y seguira iterando
                    array_push($answer,"sin data");
                }
            }
            echo json_encode($answer);
        }



    }


    //funcion que actualiza o ingresa la info. del formulario del reporte de act.
    public function saveInfoEmailReport()
    {
        $ots = $this->input->post("ids_otp");// ids seleccionadas;
        $servicios = $this->input->post("servicios");
        $last_sender = Auth::user()->k_id_user; //captura quién envió el reporte

        $last_f_evio = date('Y-m-d H:i:s'); //obtiene la fecha de envío del reporte
        $paquete_envio = $this->Dao_ot_padre_model->getMaxPaqueteEnvío();
        $paquete_envio = $paquete_envio[0]->paquete_enviados;

        $data = array(
            'senior' => $this->input->post("senior"),
            'nombre_cliente'=>  $this->input->post("configuracion"),
            'f_entrega_servicio' => $this->input->post("entregaServicio"),
            'observaciones' => $this->input->post("observaciones"),
            'last_sender' => $last_sender,
            'last_f_envio' => $last_f_evio,
            'paquete_enviados' => $paquete_envio+1
        );

        //ELIMINA LOS CAMPOS VACÍOS PARA QUE SI UN INPUT SE VA VACÍO, NO LO ACTUALICE A NULL
        foreach ($data as $key => $value) {
            if ($data[$key] == "" || $data[$key] == " " || $data[$key] == "  ") {
                unset($data[$key]);
            }
        }
        $cant_ots = count($ots); //cuenta cuantas selecciones hay
        for ($i=0; $i < $cant_ots; $i++) {
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

    public function c_getLinearBaseForHitos(){
        $id = $this->input->post('idOtp');
        $fechas = $this->Dao_ot_padre_model->getLineaBasePerOTP($id);
        echo json_encode($fechas);
    }

}