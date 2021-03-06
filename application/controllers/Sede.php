<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sede extends CI_Controller {

	function __construct() {
		parent::__construct();
		$this->load->model('data/Dao_ot_padre_model');
		$this->load->model('data/Dao_ot_hija_model');
		$this->load->model('data/Dao_log_model');
		$this->load->model('data/Dao_sede_model');
		$this->load->model('data/Dao_control_cambios_model');
	}


	// carga en la nueva pestaña las distintas otp  de la sede
	public function otps_sede($id_sede){
        if (!Auth::check()) {
            Redirect::to(URL::base());
        }
		$otp['otp'] = $this->Dao_ot_padre_model->get_otp_by_idsede($id_sede);
		$otp['log'] = $this->Dao_log_model->get_log_by_idsede($id_sede);
        
		$otp['responsable'] = $this->Dao_control_cambios_model->getAllResponsable();
		$otp['causa'] = $this->Dao_control_cambios_model->getAllCausa();

        $data['cantidad'] = $this->Dao_ot_hija_model->getCantUndefined();
        $data['title'] = 'ots sede';
        $this->load->view('parts/headerF', $data);
        $this->load->view('sede_datail', $otp);
        $this->load->view('parts/footerF');
	}


	// Cargar la vista principal para el modulo de Control de cambios (sede)
	public function index(){
		if (!Auth::check()) {
            Redirect::to(URL::base());
        }
        $otp['responsable'] = $this->Dao_control_cambios_model->getAllResponsable();
        $otp['causa'] = $this->Dao_control_cambios_model->getAllCausa();

        $otp['list_sedes'] = $this->Dao_sede_model->get_list_sedes();
        $otp['list_otps'] = $this->Dao_sede_model->get_list_otps_sedes();

        $data['title'] = 'Control De Cambios'; // cargar el  titulo en la pestaña de la pagina para sede
        $data['cantidad'] = $this->Dao_ot_hija_model->getCantUndefined();
        $this->load->view('parts/headerF', $data);
        $this->load->view('contolDeCambios', $otp);
        $this->load->view('parts/footerF');
	}

	//carga el dao para mostrar la tabla de sede en el modulo de control de cambio
    public function c_getListofficesTable() {
        $data = $this->Dao_sede_model->getListoffices_Table();
        echo json_encode($data);
    }

    //carga el dao para mostrar la tabla de OTP para el modulo de control de cambio
    public function c_getListOTPTable() {
        $dataOtp = $this->Dao_sede_model->c_getListOTP_Table();
        echo json_encode($dataOtp);
    }
    
    // inserta un nuevo control de cambio
    public function insert_control(){

        $id_sede = $this->input->post('id_sede');
    	$faltantes_post = $this->input->post('faltantes');
    	$faltantes = "";
    	if ($faltantes_post) {
    		for ($i=0; $i < count($faltantes_post) - 1 ; $i++) { 
	    		$faltantes .= $faltantes_post[$i] . ", ";
    		}
    		$faltantes .= $faltantes_post[$i];
    	}

    	date_default_timezone_set("America/Bogota");
    	$fActual = date('Y-m-d H:i:s');

    	$data = array(
			'id_ot_padre'                => $this->input->post('id_ot_padre'),
			'id_responsable'             => $this->input->post('id_responsable'),
			'id_causa'                   => $this->input->post('id_causa'),
			// 'numero_control'             => $this->input->post('numero_control'),
			'fecha_compromiso'           => $this->input->post('fecha_compromiso'),
			'fecha_programacion_inicial' => $this->input->post('fecha_programacion_inicial'),
			'nueva_fecha_programacion'   => $this->input->post('nueva_fecha_programacion'),
			'narrativa_escalamiento'     => $this->input->post('narrativa_escalamiento'),
			'estado_cc'                  => $this->input->post('estado_cc'),
			'observaciones_cc'           => $this->input->post('observaciones_cc'),
			'faltantes'                  => $faltantes,
			'en_tiempos'                 => $this->input->post('en_tiempos'),
			'fecha_creacion_cc'          => $fActual
    	);
    	
        $this->load->library( 'user_agent' );
    	$ins = $this->Dao_control_cambios_model->insert_control_cambios($data);

        if ($ins) {
            $nombre_carpeta = $this->input->post('nombre_sede');
            $file_name = $_FILES['archivo']['name'];
            $file_size = $_FILES['archivo']['size'];
            $file_tmp = $_FILES['archivo']['tmp_name'];
            $file_type = $_FILES['archivo']['type'];
            
            $fp = fopen($file_tmp, 'r+b');
            $binario = fread($fp, filesize($file_tmp));
            fclose($fp);
            
            $explode_name = explode('.',$file_name);
            $ext = $explode_name[count($explode_name) - 1];
            $nombre_archivo = "ZCC$ins.$ext";

            $up_archivo = array(
                'archivo'           => $binario,
                'nombre_archivo'    => $nombre_archivo,
                'tipo_archivo'      => $file_type,
                'extension_archivo' => $ext
            );
            

            $up_ctrl = $this->Dao_control_cambios_model->update_control_cambios($up_archivo, $ins);



            
            if (!is_dir("uploads/$nombre_carpeta")) {
              mkdir("uploads/$nombre_carpeta");
            } 
             echo json_encode(rename("$file_tmp","uploads/$nombre_carpeta/$nombre_archivo"));

        }
        
        $msj = ($ins) ? 'ok' : 'error';

        $this->session->set_flashdata('ok', $msj);
        $this->session->set_flashdata('id', $ins);

        if ($this->agent->referrer () == URL::base()."/Sede") {
           header('location: ' .$this->agent->referrer ());
        }else{
            header('location: ' .URL::base()."/Sede/otps_sede/".$id_sede);
        }
    }

    // Inserta los controles de alta
    public function insert_control_high_impact(){



        date_default_timezone_set("America/Bogota");
        $fActual = date('Y-m-d H:i:s');
        $ots_combinadas = ($this->input->post('multi_ordenes'))? $this->input->post('multi_ordenes') : [] ;
        $faltantes_post = $this->input->post('faltantes');
        $faltantes = "";
        if ($faltantes_post) {
            for ($i=0; $i < count($faltantes_post) - 1 ; $i++) { 
                $faltantes .= $faltantes_post[$i] . ", ";
            }
            $faltantes .= $faltantes_post[$i];
        }


        if ($this->input->post('multi_sedes')) {
            $otsBySede = $this->Dao_sede_model->get_ots_by_idsede($this->input->post('multi_sedes'));
        }
        if (isset($otsBySede) && $otsBySede) {
            for ($j=0; $j < count($otsBySede); $j++) { 
                array_push($ots_combinadas, $otsBySede[$j]->k_id_ot_padre);
            }
        }

        $ots = array_values(array_unique($ots_combinadas));

        $data_cc = array(
            // 'id_ot_padre'                => $this->input->post('id_ot_padre'),
            'id_responsable'             => $this->input->post('id_responsable'),
            'id_causa'                   => $this->input->post('id_causa'),
            'fecha_compromiso'           => $this->input->post('fecha_compromiso'),
            'fecha_programacion_inicial' => $this->input->post('fecha_programacion_inicial'),
            'nueva_fecha_programacion'   => $this->input->post('nueva_fecha_programacion'),
            'narrativa_escalamiento'     => $this->input->post('narrativa_escalamiento'),
            'estado_cc'                  => $this->input->post('estado_cc'),
            'observaciones_cc'           => $this->input->post('observaciones_cc'),
            'faltantes'                  => $faltantes,
            'en_tiempos'                 => $this->input->post('en_tiempos'),
            'fecha_creacion_cc'          => $fActual
        );

        $nombre_carpeta = 'multi_sede';
        $file_name = $_FILES['archivo']['name'];
        $file_size = $_FILES['archivo']['size'];
        $file_tmp = $_FILES['archivo']['tmp_name'];
        $file_type = $_FILES['archivo']['type'];
        
        $fp = fopen($file_tmp, 'r+b');
        $binario = fread($fp, filesize($file_tmp));
        fclose($fp);
        
        $explode_name = explode('.',$file_name);
        $ext = $explode_name[count($explode_name) - 1];
        
        $up_archivo = array(
            'archivo'           => $binario,
            // 'nombre_archivo'    => $nombre_archivo,
            'tipo_archivo'      => $file_type,
            'extension_archivo' => $ext
        );

        $errores = '';
        $exito = '';

            echo '<pre>'; print_r($ots); echo '</pre>';
        for ($k=0; $k < count($ots); $k++) { 
            $data_cc['id_ot_padre'] = $ots[$k];
            $ins = $this->Dao_control_cambios_model->insert_control_cambios($data_cc);

            if ($ins) {
                $up_archivo['nombre_archivo'] = "ZCC$ins.$ext";
                $up_ctrl = $this->Dao_control_cambios_model->update_control_cambios($up_archivo, $ins);
                // subir archivo a carpetas del servidor
                /*if (!is_dir("uploads/$nombre_carpeta")) {
                  mkdir("uploads/$nombre_carpeta");
                } 
                 echo json_encode(rename("$file_tmp","uploads/$nombre_carpeta/$nombre_archivo"));*/

                 $exito .= 'ZCC' . $ins . '  ';

            } else {
                $errores .= $ots[$k] . '  ';
            }
        }



        $msj = $exito . "***" . $errores;

        $this->session->set_flashdata('success', $msj);

        $location = URL::base().'/Sede';
        header("location: $location");










    }

    // trae de la tabla control de cambios por id de ot padre
    public function getCCByOtp(){
        $otp = $this->input->post('otp');
        $cc = $this->Dao_control_cambios_model->get_cc_by_otp($otp);
        echo json_encode($cc);
    }

    //carga el dao para mostrar la tabla con todos los Controles de Cambios realizados para el modulo de control de cambio
    public function c_getList_All_Table() {
        $data_All_CC = $this->Dao_sede_model->c_getListAllCC_Table();
        echo json_encode($data_All_CC);
    }

}
