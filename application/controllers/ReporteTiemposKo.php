<?php

defined('BASEPATH') or exit('No direct script access allowed');

class ReporteTiemposKo extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('date');
        $this->load->model('data/Dao_ot_hija_model');
        $this->load->model('data/Dao_reportes_model');
    }

    //Carga la vista del reporte de tiempos para las KickOff
    public function viewReporteTiemposKo()
    {
        if (!Auth::check()) {
            Redirect::to(URL::base());
        }
        date_default_timezone_set("America/Bogota");
        $anio_actual    = date('Y');
        $mes_actual     = date('m');
        $ultimo_dia_mes = days_in_month($mes_actual, $anio_actual);
        $fecha_hasta    = $anio_actual . '-' . $mes_actual . '-' . $ultimo_dia_mes;
        $fecha_desde    = $anio_actual . '-' . $mes_actual . '-01';

        $data['cantidad'] = $this->Dao_ot_hija_model->getCantUndefined();
        $data['f_inicio'] = $fecha_desde;
        $data['f_final']  = $fecha_hasta;
        $data['title']    = 'Reporte Tiempos KO';
        $this->load->view('parts/headerF', $data);
        $this->load->view('reporteTiemposKo');
        $this->load->view('parts/footerF');
    }

    //Procesa la informacion del reporte de tiempos KO
    public function getInfoReporteTiemposKo($desde, $hasta)
    {
        $infoReporte  = $this->Dao_reportes_model->getInfoReporteTiemposKo($desde, $hasta);
        $reporte      = array();
        $reporte['1'] = array(
            "ingeniero"            => 'KO ESTANDAR',
            "total_cerradas"       => 0,
            "total_abiertas"       => 0,
            "dia_min_cerrado"      => 0,
            "dia_min_abierto"      => 0,
            "dia_max_cerrado"      => 0,
            "dia_max_abierto"      => 0,
            "dia_promedio_cerrado" => 0,
            "dia_promedio_abierto" => 0,
        );

        foreach ($infoReporte as $key => $value) {
            //SE CREA LA PLANTILLA DE DATOS POR CADA ING
            if (!array_key_exists($value->k_id_user, $reporte)) {
                $reporte[$value->k_id_user] = array(
                    "ingeniero"            => $value->ingeniero,
                    "total_cerradas"       => 0,
                    "total_abiertas"       => 0,
                    "dia_min_cerrado"      => 0,
                    "dia_min_abierto"      => 0,
                    "dia_max_cerrado"      => 0,
                    "dia_max_abierto"      => 0,
                    "dia_promedio_cerrado" => 0,
                    "dia_promedio_abierto" => 0,
                );
            }

            // SOLO PAREA EL PROCESO DE CERRADAS
            if ($value->n_name_estado_ot == 'Cerrada') {

                // TOTAL DE CERRADAS DE ESE ING
                $reporte[$value->k_id_user]['total_cerradas']++;
                // PROMEDIO QUE SE TARDA EN CERRAR LAS OTS
                $reporte[$value->k_id_user]['dia_promedio_cerrado'] = $reporte[$value->k_id_user]['dia_promedio_cerrado'] + $value->dias_trascurridos;
                // CAPTURAR EL DIA MAXIMO QUE UNA OT SE DEMORO EN CERRAR DE CADA INGENIERO
                if ($value->dias_trascurridos > $reporte[$value->k_id_user]['dia_max_cerrado']) {
                    $reporte[$value->k_id_user]['dia_max_cerrado'] = $value->dias_trascurridos;
                }
                // CAPTURA EL DIA MINIMO QUE UNA OT SE DEMORO EN CERRAR DE CADA INGENIERO
                if ($value->dias_trascurridos < $reporte[$value->k_id_user]['dia_max_cerrado']) {
                    $reporte[$value->k_id_user]['dia_min_cerrado'] = $value->dias_trascurridos;
                }

                // TOTAL DE CERRADAS DEL GRUPO DE ESTANDAR
                $reporte[1]['total_cerradas']++;
                // PROMEDIO QUE SE TARDA EN CERRAR LAS OTS
                $reporte[1]['dia_promedio_cerrado'] = $reporte[1]['dia_promedio_cerrado'] + $value->dias_trascurridos;
                // CAPTURAR EL DIA MAXIMO QUE UNA OT SE DEMORO EN CERRAR DEL GRUPO DE ESTANDAR
                if ($reporte[$value->k_id_user]['dia_max_cerrado'] > $reporte[1]['dia_max_cerrado']) {
                    $reporte[1]['dia_max_cerrado'] = $reporte[$value->k_id_user]['dia_max_cerrado'];
                }
                // CAPTURA EL DIA MINIMO QUE UNA OT SE DEMORO EN CERRAR DEL GRUPO DE ESTANDAR
                if ($value->dias_trascurridos < $reporte[1]['dia_max_cerrado']) {
                    $reporte[1]['dia_min_cerrado'] = $value->dias_trascurridos;
                }
            }
            // SOLO PARA ABIERTAS
            else {
                // TOTAL DE ABIERTAS DE ESE ING
                $reporte[$value->k_id_user]['total_abiertas']++;
                // PROMEDIO EN DIAS QUE DURA UNA OT ABIERTA
                $reporte[$value->k_id_user]['dia_promedio_abierto'] = $reporte[$value->k_id_user]['dia_promedio_abierto'] + $value->dias_trascurridos;
                // CAPTURA EL DIA MAXIMO QUE UNA OT AH DURADO ABIERTA DE CADA INGENIERO
                if ($value->dias_trascurridos > $reporte[$value->k_id_user]['dia_max_abierto']) {
                    $reporte[$value->k_id_user]['dia_max_abierto'] = $value->dias_trascurridos;
                }

                // CAPTURA EL DIA MINIMO QUE UNA OT HA DURADO ABIERTA DE CADA INGENIERO
                if ($value->dias_trascurridos < $reporte[$value->k_id_user]['dia_max_abierto']) {
                    $reporte[$value->k_id_user]['dia_min_abierto'] = $value->dias_trascurridos;
                }

                // TOTAL DE ABIERTAS DEL GRUPO DE ESTANDAR
                $reporte[1]['total_abiertas']++;
                // CAPTURA EL DIA MAXIMO QUE UNA OT AH DURADO ABIERTA DEL GRUPO DE ESTANDAR
                if ($reporte[$value->k_id_user]['dia_max_abierto'] > $reporte[1]['dia_max_abierto']) {
                    $reporte[1]['dia_max_abierto'] = $reporte[$value->k_id_user]['dia_max_abierto'];
                }
                // CAPTURA EL DIA MINIMO QUE UNA OT SE DEMORO EN CERRAR DEL GRUPO DE ESTANDAR
                if ($value->dias_trascurridos < $reporte[1]['dia_max_abierto']) {
                    $reporte[1]['dia_min_abierto'] = $value->dias_trascurridos;
                }
            }
        }

        return $reporte;
    }

    public function c_generateReportTimesKickOff()
    {
        $f_inicio = $this->input->post('f_inicio');
        $f_final  = $this->input->post('f_final');

        $return = $this->getInfoReporteTiemposKo($f_inicio, $f_final);

        echo json_encode($return);
    }


    public function viewAssociatesOTs($id, $fInit, $fEnd, $dias,$estadoOT)
    {
        $estadoOT = ($estadoOT == 'Cerradas') ? 3 : 1;
        
        $data = $this->Dao_reportes_model->getInfoBetweenDates($id, $fInit, $fEnd,$estadoOT);
        foreach ($data as $key => $value) {
            if ($value->dias_trascurridos != $dias) {
                unset($data[$key]);
            }
        }
        $this->viewAssociatesOThs($data);
    }

    public function viewAssociatesOThs($oths)
    {
        $data['title'] = 'OTHs Asociadas';
        $data['oth'] = json_encode(array_values($oths));
        $data['cantidad'] = $this->Dao_ot_hija_model->getCantUndefined();
        $this->load->view('parts/headerF', $data);
        $this->load->view('oths_asociadas');
        $this->load->view('parts/footerF');
        
    }
}
