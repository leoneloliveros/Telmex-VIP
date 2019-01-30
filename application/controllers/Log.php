<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Log extends CI_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('data/Dao_log_model');
    $this->load->model('data/Dao_log_correo_model');
  }
  
 	// Llama los log por id 
	public function getLogById(){
		$id = $this->input->post('id');
		// $data['title'] = 'Email'; // cargar el  titulo en la pestaña de la pagina para otp
        // $this->load->view('moduleOtp', $data);
		$data['log'] = $this->Dao_log_model->getLogById($id);
		$data['mail'] = $this->Dao_log_correo_model->getLogMailById($id);
		echo json_encode($data);
	}

	// obtengo la información que se pintará en la tabla log y historial mail
	public function c_getLogsByOTP()
	{
		$OTHs = $this->input->post("valOTHs");
		$result['log'] = $this->Dao_log_model->getLogsByOTP($OTHs);
		$result['mail'] = $this->Dao_log_correo_model->getLogsMailsByOTP(implode($OTHs,","));

		echo json_encode($result);
	}

	//
	public function view_email(){
		$fun['cuerpo'] = $this->input->post('txt_template');

		$this->load->view('print_mail', $fun);
		// print_r($fun);
	}

}		