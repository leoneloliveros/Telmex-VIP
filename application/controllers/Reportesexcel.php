<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Reportesexcel extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('data/Dao_user_model');
        $this->load->model('data/Dao_ot_padre_model');
        $this->load->model('data/Dao_ot_hija_model');
        $this->load->model('data/Dao_email_model');
        $this->load->model('data/Dao_cierre_ots_model');
        $this->load->model('data/Dao_reportes_model');
    }


    //funciones para generar reportes de actualizacion y de inicio
    public function Reportes()
    {
        if (!Auth::check()) { Redirect::to(URL::base()); }
        $data['cantidad'] = $this->Dao_ot_hija_model->getCantUndefined();
        $data['title'] = 'Generar reportes'; // cargar el  titulo en la pestaña de la pagina
        $this->load->view('parts/headerF',$data);
        $this->load->view('GenerarReportes');
        $this->load->view('parts/footerF');
    }
    

    public function c_getInfoReportInit()
    {
        $answer = $this->Dao_reportes_model->getInfoReportInit();
        echo json_encode($answer);
    }

    public function c_getInfoReportAct()
    {
        $answer = $this->Dao_reportes_model->getInfoReportAct();
        echo json_encode($answer);
    }
}
?>