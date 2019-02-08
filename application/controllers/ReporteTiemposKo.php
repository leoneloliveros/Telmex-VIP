<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class ReporteTiemposKo extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->helper('date');
        $this->load->model('data/Dao_ot_hija_model');
        $this->load->model('data/Dao_reportes_model');
    }

    //Carga la vista del reporte de tiempos para las KickOff
    public function viewReporteTiemposKo() {
        date_default_timezone_set("America/Bogota");
        $anio_actual = date('Y');
        $mes_actual = date('m');
        $ultimo_dia_mes = days_in_month($mes_actual, $anio_actual);
//        $fecha_hasta = $anio_actual.'-'.$mes_actual.'-'.$ultimo_dia_mes;
//        $fecha_desde = $anio_actual.'-'.$mes_actual.'-01';
        $fecha_hasta = '2019-01-31';
        $fecha_desde = '2019-01-01';

        $data['cantidad'] = $this->Dao_ot_hija_model->getCantUndefined();
        $data['infoReporte'] = $this->getInfoReporteTiemposKo($fecha_desde, $fecha_hasta);
        $data['title'] = 'Reporte Tiempos KO';
        $this->load->view('parts/headerF', $data);
        $this->load->view('reporteTiemposKo');
        $this->load->view('parts/footerF');
    }

    //Procesa la informacion del reporte de tiempos KO
    public function getInfoReporteTiemposKo($desde, $hasta) {
        $infoReporte = $this->Dao_reportes_model->getInfoReporteTiemposKo($desde, $hasta);
        $reporte = Array();
        $reporte['1'] = Array(
                    "ingeniero" => 'KO ESTANDAR',
                    "total_cerradas" => 0,
                    "total_abiertas" => 0,
                    "dia_min_cerrado" => 0,
                    "dia_min_abierto" => 0,
                    "dia_max_cerrado" => 0,
                    "dia_max_abierto" => 0,
                    "dia_promedio_cerrado" => 0,
                    "dia_promedio_abierto" => 0,
                );

        foreach ($infoReporte as $key => $value) {
            //SE CREA LA PLANTILLA DE DATOS POR CADA ING
            if (!array_key_exists($value->k_id_user, $reporte)) {
                $reporte[$value->k_id_user] = Array(
                    "ingeniero" => $value->ingeniero,
                    "total_cerradas" => 0,
                    "total_abiertas" => 0,
                    "dia_min_cerrado" => 0,
                    "dia_min_abierto" => 0,
                    "dia_max_cerrado" => 0,
                    "dia_max_abierto" => 0,
                    "dia_promedio_cerrado" => 0,
                    "dia_promedio_abierto" => 0,
                );
            }

            // SOLO PAREA EL PROCESO DE CERRADAS
            if ($value->n_name_estado_ot == 'Cerrada') {

                // TOTAL DE CERRADAS DE ESE ING
                $reporte[$value->k_id_user]['total_cerradas'] ++;
                // PROMEDIO QUE SE TARDA EN CERRAR LAS OTS
                $reporte[$value->k_id_user]['dia_promedio_cerrado'] = $reporte[$value->k_id_user]['dia_promedio_cerrado'] + $value->dias_trascurridos;
                // CAPTURAR EL DIA MAXIMO QUE UNA OT SE DEMORO EN CERRAR DE CADA INGENIERO
                if ($value->dias_trascurridos > $reporte[$value->k_id_user]['dia_max_cerrado']) {
                    $reporte[$value->k_id_user]['dia_max_cerrado'] = $value->dias_trascurridos;
                }
                // CAPTURA EL DIA MINIMO QUE UNA OT SE DEMORO EN CERRAR DE CADA INGENIERO
                if($value->dias_trascurridos < $reporte[$value->k_id_user]['dia_max_cerrado']){
                    $reporte[$value->k_id_user]['dia_min_cerrado'] = $value->dias_trascurridos;
                }

                // TOTAL DE CERRADAS DEL GRUPO DE ESTANDAR
                $reporte[1]['total_cerradas'] ++;
                // PROMEDIO QUE SE TARDA EN CERRAR LAS OTS
                $reporte[1]['dia_promedio_cerrado'] = $reporte[1]['dia_promedio_cerrado'] + $value->dias_trascurridos;
                // CAPTURAR EL DIA MAXIMO QUE UNA OT SE DEMORO EN CERRAR DEL GRUPO DE ESTANDAR
                if ($reporte[$value->k_id_user]['dia_max_cerrado'] > $reporte[1]['dia_max_cerrado']) {
                    $reporte[1]['dia_max_cerrado'] = $reporte[$value->k_id_user]['dia_max_cerrado'];
                }
                // CAPTURA EL DIA MINIMO QUE UNA OT SE DEMORO EN CERRAR DEL GRUPO DE ESTANDAR
                if($value->dias_trascurridos < $reporte[1]['dia_max_cerrado']){
                    $reporte[1]['dia_min_cerrado'] = $value->dias_trascurridos;
                }

            }
            // SOLO PARA ABIERTAS
            else {
                // TOTAL DE ABIERTAS DE ESE ING
                $reporte[$value->k_id_user]['total_abiertas'] ++;
                // PROMEDIO EN DIAS QUE DURA UNA OT ABIERTA
                $reporte[$value->k_id_user]['dia_promedio_abierto'] = $reporte[$value->k_id_user]['dia_promedio_abierto'] + $value->dias_trascurridos;
                // CAPTURA EL DIA MAXIMO QUE UNA OT AH DURADO ABIERTA DE CADA INGENIERO
                if ($value->dias_trascurridos > $reporte[$value->k_id_user]['dia_max_abierto']) {
                    $reporte[$value->k_id_user]['dia_max_abierto'] = $value->dias_trascurridos;
                }

                // CAPTURA EL DIA MINIMO QUE UNA OT HA DURADO ABIERTA DE CADA INGENIERO
                if($value->dias_trascurridos < $reporte[$value->k_id_user]['dia_max_abierto']){
                    $reporte[$value->k_id_user]['dia_min_abierto'] = $value->dias_trascurridos;
                }

                // TOTAL DE ABIERTAS DEL GRUPO DE ESTANDAR
                $reporte[1]['total_abiertas'] ++;
                // CAPTURA EL DIA MAXIMO QUE UNA OT AH DURADO ABIERTA DEL GRUPO DE ESTANDAR
                if ($reporte[$value->k_id_user]['dia_max_abierto'] > $reporte[1]['dia_max_abierto']) {
                    $reporte[1]['dia_max_abierto'] = $reporte[$value->k_id_user]['dia_max_abierto'];
                }
                // CAPTURA EL DIA MINIMO QUE UNA OT SE DEMORO EN CERRAR DEL GRUPO DE ESTANDAR
                if($value->dias_trascurridos < $reporte[1]['dia_max_abierto']){
                    $reporte[1]['dia_min_abierto'] = $value->dias_trascurridos;
                }

            }

        }
//        echo("<pre>");
//        print_r($reporte);
//        echo("</pre>");
        return $reporte;
    }

    public function c_generateReportTimesKickOff() {
        $f_inicio = $this->input->post('f_inicio');
        $f_final = $this->input->post('f_final');

        $return = $this->getInfoReporteTiemposKo($f_inicio, $f_final);

        echo json_encode($return);
    }

}
