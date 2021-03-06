<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class LoadInformation extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('data/Dao_user_model');
        $this->load->model('data/Dao_ot_hija_model');
        $this->load->model('data/Dao_tipo_ot_hija_model');
        $this->load->model('data/Dao_estado_ot_model');
        $this->load->model('data/Dao_log_model');
        $this->load->model('data/Dao_ot_padre_model');
        $this->load->model('data/Dao_cierre_ots_model');
    }

    public function uploadfile() {
        // $tam_max = 10; //PARA PRUEBAS
        $tam_max = 6500000;
        // $tam_max = 6414336;
        $MBexcel = $_FILES['file']['size'];
        if ($MBexcel > $tam_max) {
            $MBexcel /= 1000000;
            // echo("El tamaño del archivo es de ".$MBexcel."MB, y el maximo es de 6.41MB");
            // echo("El tamaño del archivo es de $_FILES['file']['size'], y el maximo es de 6,11MB");
            $response = new Response(EMessages::ERROR, "El tamaño del archivo que intenta subir es de <b>".$MBexcel."MB</b>, y el tamaño máximo permitido es de <b>6.41MB</b>");
            $this->json($response);
        }else{
            // echo("El tamaño del archivo ESTA BIEN ");
            $request = $this->request;
            $storage = new Storage();
            //Se activa la asignación de un prefijo para nuestro archivo...
            $storage->setPrefix(true);
            //Seteamos las extenciones válidas...
            $storage->setValidExtensions("xlsx", "xls");
            //Subimos el archivo...
            $storage->process($request);
            //Obtenemos el log de los archivos subidos...
            $files    = $storage->getFiles();
            $response = null;
            if (count($files) > 0) {
                $project  = $files[0];
                $response = new Response(EMessages::SUCCESS, "Se ha subido el archivo correctamente", $project);
            } else {
                $response = new Response(EMessages::ERROR_ACTION, "No se pudo subir el archivo.");
            }
        $this->json($response);
        }
    }

    public function countLinesFile() {
        error_reporting(E_ERROR);
        $request = $this->request;
        $file    = $request->file;
        //fecha Actual
        date_default_timezone_set("America/Bogota");
        $f_actual_hora = date('Y-m-d H:i:s');
        $user_session  = Auth::user()->n_name_user . ' ' . Auth::user()->n_last_name_user;
        $pesoKb        = filesize($file) / 1000;

        $response = new Response(EMessages::SUCCESS);
        try {
            //Se procesa el archivo de comentarios...
            // Solo para insertar la ultima hora de actualizacion
            $this->Dao_log_model->insert_last_update($f_actual_hora, $user_session, $pesoKb);


            set_time_limit(-1);
            ini_set('memory_limit', '1500M');
            require_once APPPATH . 'models/bin/PHPExcel-1.8.1/Classes/PHPExcel/Settings.php';
            $cacheMethod   = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
            $cacheSettings = array(' memoryCacheSize ' => '15MB');
            PHPExcel_Settings::setZipClass(PHPExcel_Settings::PCLZIP);
            PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
            $this->load->model('bin/PHPExcel-1.8.1/Classes/PHPExcel');

            $inputFileType = PHPExcel_IOFactory::identify($file);
            $objReader     = PHPExcel_IOFactory::createReader($inputFileType);
            $objReader->setReadDataOnly(true);

            $objPHPExcel = $objReader->load($file);
            //
            $sheet     = $objPHPExcel->getSheet(0);
            $row       = 1;
            $validator = new Validator();
            while ($validator->required("", $this->getValueCell($sheet, "AW" . $row))) {
                $row++;
            }
            $highestRowSheet1 = $row;

            $lines = [
                "sheet1" => $highestRowSheet1,
                "export" => $this->Dao_ot_hija_model->get_actualizado_row() //Last_export
            ];

            $response->setData($lines);
            $this->json($response);
        } catch (DeplynException $ex) {
            $this->json($ex);
        }
    }

    public function processData() {
        error_reporting(E_ERROR);
        $request  = $this->request;
        $response = new Response(EMessages::SUCCESS);
        $file     = $request->file;

        //Verificamos si el archivo existe...
        if (file_exists($file)) {
            //Iniciamos el procedimiento de carga de datos...
            set_time_limit(-1);
            ini_set('memory_limit', '1500M');
            require_once APPPATH . 'models/bin/PHPExcel-1.8.1/Classes/PHPExcel/Settings.php';
            require_once APPPATH . 'models/bin/PHPExcel-1.8.1/Classes/PHPExcel/IOFactory.php';
            $cacheMethod   = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
            $cacheSettings = array(' memoryCacheSize ' => '15MB');
            PHPExcel_Settings::setZipClass(PHPExcel_Settings::PCLZIP);
            PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

            try {
                //se envia el reporte automatico
                // $this->enviar_correo_cant_reportes_actualizacion();

                // envio de reportte automatico semanal...
                // $res_mail = $this->enviar_correo_cant_reportes_actualizacion();

                $inputFileType = PHPExcel_IOFactory::identify($file);
                $objReader     = PHPExcel_IOFactory::createReader($inputFileType);
                $objReader->setReadDataOnly(true);
                //Leer el archivo...
                $objPHPExcel = $objReader->load($file);

                //Cambiar el archivo...
                // $objWriter  = PHPExcel_IOFactory::createWriter($objPHPExce, $inputFileTypel);
                $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

                //Obtenemos la página.
                $sheet = $objPHPExcel->getSheet(0);
                //Obtenemos el highestRow...
                $highestRow    = 0;
                $row           = $request->index;
                $limit         = $row + $request->limit;
                $inserts       = 0;
                $errorInsert   = [];
                $errorUpdate   = [];
                $errorNoChange = [];
                $actualizar    = 0;
                $actualizados  = 0;
                $cantArchivos = $request->cantArchivos;


                $export = $request->export;

                //fecha Actual
                date_default_timezone_set("America/Bogota");
                $fActual      = date('Y-m-d');
                $fActual_hora = date('Y-m-d H:i:s');
                $list_inges   = $this->Dao_user_model->getArrayAllEngineers();
                // fecha de carga anterior
                // $fecha_max = $this->Dao_ot_hija_model->getUltimaFechaCarga()->fecha;
                //Inicializamos un objeto de PHPExcel para escritura...
                //while para recorrer filas del excel...
                while ($this->getValueCell($sheet, 'AW' . $row) > 0 && ($row < $limit)) {
                    $data = array();
                    //valido si el id del excel existe en la base de datos
                    $exist = $this->Dao_ot_hija_model->getExistIdOtHija($this->getValueCell($sheet, 'AW' . $row));

                    /******************validacion para remplazar una oth creada manualmente******************/
                    if ($exist) {
                        if ($exist['b_flag'] == 1) {
                            $this->actualizar_ot_padre($sheet, $row, $list_inges);
                            $this->eliminar_oth_excel($exist['id_orden_trabajo_hija']);
                            $exist = false;
                        }
                    }
                    /****************************fin validacion****************************/

                    // si existe...
                    if ($exist) {
                        $cambioStatusMod = [];
                        $updates         = [];

                        $arrayBD = $exist;

                        // SELECCIONO LAS COLUMNAS DEL EXCEL A COMPARAR
                        $dataExcel = array(
                            // 'usuario_asignado'                 => $this->getValueCell($sheet, 'AB'. $row),
                            // comparacion para ot_padre
                            'estado_orden_trabajo'        => $this->getValueCell($sheet, 'W' . $row),
                            'fecha_compromiso'            => $this->getDatePHPExcel($sheet, 'AO' . $row),
                            'fecha_programacion'          => $this->getDatePHPExcel($sheet, 'AP' . $row),
                            'lista_observaciones'         => $this->getValueCell($sheet, 'AR' . $row),
                            'k_id_user'                   => $this->cedula_del_inegeniero(str_replace(array("ñ", "Ñ"), 'N', $this->getValueCell($sheet, 'AB' . $row)), $list_inges),
                            // comparacion para ot_hija
                            // 'tiempo_estado'               => $this->getValueCell($sheet, 'X' . $row),
                            'descripcion'                 => $this->getValueCell($sheet, 'AJ' . $row),
                            'fecha_realizacion'           => $this->getDatePHPExcel($sheet, 'AQ' . $row),
                            'estado_orden_trabajo_hija'   => $this->getValueCell($sheet, 'AZ' . $row),
                            'fec_actualizacion_onyx_hija' => $this->getDatePHPExcel($sheet, 'BF' . $row),
                            'tipo_trascurrido'            => $this->getValueCell($sheet, 'BG' . $row),
                            'usuario_asignado'            => $this->getValueCell($sheet, 'AB' . $row),
                        );

                        // Se hace la comparacion antes del foreach para el tratamiento de cambio de estados
                        if ($arrayBD['estado_orden_trabajo_hija'] != $dataExcel['estado_orden_trabajo_hija']) {
                            // Se calcula el tipo-estado que viene del excel
                            $nombre_ref           = $this->Dao_tipo_ot_hija_model->get_tipo_ot_hija_by_name($this->getValueCell($sheet, 'AV' . $row));
                            $id_estado_camb_excel = $this->Dao_estado_ot_model->getStatusByTypeAndStatusName($nombre_ref->id_tipo, $this->getValueCell($sheet, 'AZ' . $row));
                            // si el orden del cambio de estado es ascendente se debe efectuar el cambio
                            if ($arrayBD['i_orden'] < $id_estado_camb_excel->i_orden) {
                                $dataExcel['k_id_estado_ot'] = $id_estado_camb_excel->k_id_estado_ot;
                            } else {
                                unset($dataExcel['estado_orden_trabajo_hija']);
                            }
                        }

                        // Recorro el array de lo q viene en excel
                        foreach ($dataExcel as $key => $value) {
                            // si la celda de db es != a la celda de excel
                            if ($arrayBD[$key] != $dataExcel[$key]) {

                                $insert = array(
                                    'id_ot_hija' => $arrayBD['id_orden_trabajo_hija'],
                                    'antes'      => $arrayBD[$key],
                                    'ahora'      => $dataExcel[$key],
                                    'columna'    => $key,
                                    'fecha_mod'  => $fActual,
                                );

                                if ($insert['columna'] != 'k_id_estado_ot') {
                                    $this->Dao_log_model->insertLogRow($insert);
                                }
                                //voy creando el arreglo con las modificaciones que hayan
                                $updates[$key] = $dataExcel[$key];
                            }

                            // Si en la comparacion hubo alguna modificacion
                            if (count($updates) > 0) {
                                $updates['estado_mod']            = 1;
                                $updates['fecha_actual']          = $fActual;
                                $updates['id_orden_trabajo_hija'] = $arrayBD['id_orden_trabajo_hija'];
                                $updates['ot_hija']               = $arrayBD['ot_hija'];
                                // $updates['k_id_estado_ot']        = $estadoMod->k_id_estado_ot;
                            }
                            // si son iguales
                            else {
                                if ($arrayBD['estado_mod'] != 2) {
                                    $cambioStatusMod['estado_mod']            = 2;
                                    $cambioStatusMod['fecha_actual']          = $fActual;
                                    $cambioStatusMod['id_orden_trabajo_hija'] = $arrayBD['id_orden_trabajo_hija'];
                                }
                            }
                        }
                        // si no hay ningun cambio
                        if ($cambioStatusMod) {
                            $sinCambios = $this->Dao_ot_hija_model->update_ot_hija_status_mod($cambioStatusMod);
                            // capturo el error si no se actualizo + el id
                            if ($sinCambios != 1) {
                                array_push($errorNoChange, array($sinCambios, $this->getValueCell($sheet, 'AW' . $row)));
                            }
                        }
                        //print_r($updates);
                        if ($updates) {
                            $up_otp = [];
                            $up_oth = [];

                            foreach ($updates as $indice => $value) {
                                if ($indice == 'estado_orden_trabajo' || $indice == 'fecha_compromiso' || $indice == 'fecha_programacion' || $indice == 'k_id_user' || $indice == 'lista_observaciones') {
                                    $up_otp[$indice] = $value;
                                } else {
                                    $up_oth[$indice] = $value;
                                }
                            }

                            $actualizar_oth = $this->Dao_ot_hija_model->update_ot_hija_mod($up_oth);

                            if ($up_otp) {
                                $actualizar_otp = $this->Dao_ot_padre_model->update_ot_padre($up_otp, $this->getValueCell($sheet, 'Q' . $row));
                            }

                            // Si se actualizó  el estado a sin cambios retorna 1
                            if (($actualizar_oth === 1) || ($actualizar_oth === 1 && $actualizar_otp === 1)) {
                                $actualizados++;
                            }
                            // si retorna error lo captura, + el id
                            else {
                                array_push($errorUpdate, array($actualizar, $this->getValueCell($sheet, 'AW' . $row)));
                            }
                        }

                        $col_actualizar = $this->Dao_ot_hija_model->update_ot_hija_mod(array('id_orden_trabajo_hija' => $arrayBD['id_orden_trabajo_hija'], 'actualizado' => $export + 1));

                        if ($this->oth_cerrada_zolid($this->getValueCell($sheet, 'AV' . $row), $this->getValueCell($sheet, 'AZ' . $row)) && $arrayBD['ultimo_envio_reporte'] == null) {
                            $data = array(
                                'cerrado_zolid' => 'NO',
                                'ultimo_envio_reporte'  => $this->getDatePHPExcel($sheet, 'BF' . $row),
                            );
                            $this->Dao_ot_padre_model->update_ot_padre($data, $this->getValueCell($sheet, 'Q' . $row));
                        }
                    }
                    //si no existe lo inserto en la db tabla ot_hija
                    else {
                        //*******************VALIDACION DE OT PADRE*******************
                        $existe_otp = $this->Dao_ot_padre_model->exist_otp_by_id($this->getValueCell($sheet, 'Q' . $row));
                        // verificamos si existe la ot padre
                        if (!$existe_otp) {
                            // Se debe insertar en tabla ot_padre
                            $dataotp = array(
                                'k_id_ot_padre'         => $this->getValueCell($sheet, 'Q' . $row),
                                'k_id_user'             => $this->cedula_del_inegeniero(str_replace(array("ñ", "Ñ"), 'N', $this->getValueCell($sheet, 'AB' . $row)), $list_inges),
                                'id_cliente_onyx'       => $this->getValueCell($sheet, 'A' . $row),
                                'n_nombre_cliente'      => $this->getValueCell($sheet, 'B' . $row),
                                'orden_trabajo'         => $this->getValueCell($sheet, 'P' . $row),
                                'servicio'              => $this->getValueCell($sheet, 'R' . $row),
                                'estado_orden_trabajo'  => $this->getValueCell($sheet, 'W' . $row),
                                'fecha_creacion'        => $this->getDatePHPExcel($sheet, 'U' . $row),
                                'fecha_compromiso'      => $this->getDatePHPExcel($sheet, 'AO' . $row),
                                'fecha_programacion'    => $this->getDatePHPExcel($sheet, 'AP' . $row),
                                'lista_observaciones'   => $this->getValueCell($sheet, 'AR' . $row),
                                'observacion'           => NULL,
                                'fecha_actualizacion'   => NULL,
                                'usuario_actualizacion' => NULL,
                                'ultimo_envio_reporte'  => ($this->oth_cerrada_zolid($this->getValueCell($sheet, 'AV' . $row), $this->getValueCell($sheet, 'AZ' . $row))) ? $this->getDatePHPExcel($sheet, 'BF' . $row) : NULL,
                                'cerrado_zolid'         => ($this->oth_cerrada_zolid($this->getValueCell($sheet, 'AV' . $row), $this->getValueCell($sheet, 'AZ' . $row))) ? 'NO' : 'SI',
                            );
                            //print_r('data'.$dataotp);
                            // funcion para insertar datos otp
                            $this->Dao_ot_padre_model->insert_data_otp($dataotp);
                        } elseif ($this->oth_cerrada_zolid($this->getValueCell($sheet, 'AV' . $row), $this->getValueCell($sheet, 'AZ' . $row))) {
                            $data = array(
                                'cerrado_zolid' => 'NO',
                                'ultimo_envio_reporte'  => $this->getDatePHPExcel($sheet, 'BF' . $row),
                            );
                            $this->Dao_ot_padre_model->update_ot_padre($data, $this->getValueCell($sheet, 'Q' . $row));
                        }

                        $id_estado = $this->get_estado_by_name_ot_hiha($this->getValueCell($sheet, 'AV' . $row), $this->getValueCell($sheet, 'AZ' . $row));
                        //LLENO EL ARRAY LETRAS CON LOS VARORES DE LA FILA DEL EXCEL EN LA QUE VA EL WHILE
                        $data = array(
                            'nro_ot_onyx'                      => $this->getValueCell($sheet, 'Q' . $row),
                            'id_orden_trabajo_hija'            => $this->getValueCell($sheet, 'AW' . $row),
                            'k_id_estado_ot'                   => $id_estado->k_id_estado_ot,
                            'grupo_objetivo'                   => $this->getValueCell($sheet, 'C' . $row),
                            'segmento'                         => $this->getValueCell($sheet, 'D' . $row),
                            'nivel_atencion'                   => $this->getValueCell($sheet, 'E' . $row),
                            'ciudad'                           => $this->getValueCell($sheet, 'F' . $row),
                            'departamento'                     => $this->getValueCell($sheet, 'G' . $row),
                            'grupo'                            => $this->getValueCell($sheet, 'H' . $row),
                            'consultor_comercial'              => $this->getValueCell($sheet, 'I' . $row),
                            'grupo2'                           => $this->getValueCell($sheet, 'J' . $row),
                            'consultor_postventa'              => $this->getValueCell($sheet, 'K' . $row),
                            'proy_instalacion'                 => $this->getValueCell($sheet, 'L' . $row),
                            'ing_responsable'                  => $this->getValueCell($sheet, 'M' . $row),
                            'id_enlace'                        => $this->getValueCell($sheet, 'N' . $row),
                            'alias_enlace'                     => $this->getValueCell($sheet, 'O' . $row),
                            'familia'                          => $this->getValueCell($sheet, 'S' . $row),
                            'producto'                         => $this->getValueCell($sheet, 'T' . $row),
                            'tiempo_incidente'                 => $this->getValueCell($sheet, 'V' . $row),
                            'tiempo_estado'                    => $this->getValueCell($sheet, 'X' . $row),
                            'ano_ingreso_estado'               => $this->getValueCell($sheet, 'Y' . $row),
                            'mes_ngreso_estado'                => $this->getValueCell($sheet, 'Z' . $row),
                            'fecha_ingreso_estado'             => $this->getDatePHPExcel($sheet, 'AA' . $row),
                            'usuario_asignado'                 => $this->getValueCell($sheet, 'AB' . $row),
                            'grupo_asignado'                   => $this->getValueCell($sheet, 'AC' . $row),
                            'ingeniero_provisioning'           => $this->getValueCell($sheet, 'AD' . $row),
                            'cargo_arriendo'                   => $this->getValueCell($sheet, 'AE' . $row),
                            'cargo_mensual'                    => $this->getValueCell($sheet, 'AF' . $row),
                            'monto_moneda_local_arriendo'      => $this->getValueCell($sheet, 'AG' . $row),
                            'monto_moneda_local_cargo_mensual' => $this->getValueCell($sheet, 'AH' . $row),
                            'cargo_obra_civil'                 => $this->getValueCell($sheet, 'AI' . $row),
                            'descripcion'                      => $this->getValueCell($sheet, 'AJ' . $row),
                            'direccion_origen'                 => $this->getValueCell($sheet, 'AK' . $row),
                            'ciudad_incidente'                 => $this->getValueCell($sheet, 'AL' . $row),
                            'direccion_destino'                => $this->getValueCell($sheet, 'AM' . $row),
                            'ciudad_incidente3'                => $this->getValueCell($sheet, 'AN' . $row),
                            'fecha_realizacion'                => $this->getDatePHPExcel($sheet, 'AQ' . $row),
                            'resolucion_1'                     => NULL,
                            'resolucion_2'                     => $this->getValueCell($sheet, 'AS' . $row),
                            'resolucion_3'                     => $this->getValueCell($sheet, 'AT' . $row),
                            'resolucion_4'                     => $this->getValueCell($sheet, 'AU' . $row),
                            'ot_hija'                          => $this->getValueCell($sheet, 'AV' . $row),
                            'fecha_creacion_ot_hija'           => $this->getDatePHPExcel($sheet, 'AX' . $row),
                            'proveedor_ultima_milla'           => $this->getValueCell($sheet, 'AY' . $row),
                            'estado_orden_trabajo_hija'        => $this->getValueCell($sheet, 'AZ' . $row),
                            'usuario_asignado4'                => $this->getValueCell($sheet, 'BA' . $row),
                            'resolucion_15'                    => $this->getValueCell($sheet, 'BB' . $row),
                            'resolucion_26'                    => $this->getValueCell($sheet, 'BC' . $row),
                            'resolucion_37'                    => $this->getValueCell($sheet, 'BD' . $row),
                            'resolucion_48'                    => $this->getValueCell($sheet, 'BE' . $row),
                            'fec_actualizacion_onyx_hija'      => $this->getDatePHPExcel($sheet, 'BF' . $row),
                            'tipo_trascurrido'                 => $this->getValueCell($sheet, 'BG' . $row),
                            'fecha_actual'                     => $fActual,
                            'fecha_insercion_zolid'            => $fActual_hora,
                            'estado_mod'                       => 0,
                            'fecha_compromiso'                 => $this->getDatePHPExcel($sheet, 'AO' . $row),
                            'actualizado'                      => $export + 1,
                        );

                        //inserto la fila en la base de datos
                        $insert = $this->Dao_ot_hija_model->insert_ot_hija($data);

                        //si retorna 1 se insertó bien y lo sumo en contador
                        if ($insert === 1) {
                            $inserts++;
                        }
                        // si no se insertó retorno el error y lo guardo en un array multidimencional + el id de la orden
                        else {
                            array_push($errorInsert, array($insert, $this->getValueCell($sheet, 'AW' . $row)));
                        }
                    }
                    $row++;
                }

                /***********FUNCIONES QUE SE EJECUTAN AL TERMINAR DE CARGAR LA ULTIMA LINEA CON DATOS DEL EXCEL***********/
                if (($limit - $row) >= 2) {
                    $response->setCode(2);

                    // NO BORRAR
                    // $this->insertar_cierre_ots(); // FUNCION PARA ENVIAR A CIERRE LO DE FECHA ANTERIOR

                        //ENVIAR A CIERRE LO QUE NO ESTE EN EL ULTIMO ARCHIVO SUBIDO
                        $this->enviar_a_cierre($export);
                        // Si no hay cambios ni inserciones se deja
                        if ($inserts > 0 || $actualizados > 0) {
                            $this->Dao_log_model->insertNuevaFecha();
                        }

                }

                $response->setData([
                    "nuevos"                  => $inserts,
                    "Actualizados"            => $actualizados,
                    "No hay cambio"           => ($row - $request->index) - $actualizados - $inserts,
                    "error de insercion"      => $errorInsert,
                    "error al Actualizar"     => $errorUpdate,
                    "error act a sin cambios" => $errorNoChange,
                    "row"                     => ($row - $request->index),
                    "data"                    => $this->objs,
                    "correo_enviado"          => $res_mail,
                    "cantArchivos"            => $cantArchivos
                ]);
            } catch (DeplynException $ex) {
                $response = new Response(EMessages::ERROR, "Error al procesar el archivo.");
            }
        } else {
            $response = new Response(EMessages::ERROR, "No se encontró el archivo " . $file);
        }

        $this->json($response);
        // $this->load->view('viewRF');
    }

    private function getValueCell(&$sheet, $cell) {
        $string = str_replace(array("\n", "\r", "\t"), '', $sheet->getCell($cell)->getValue());
        $string = str_replace(array("_x000D_"), "<br/>", $sheet->getCell($cell)->getValue());
        return $string;
    }

    private function getDatePHPExcel($sheet, $colum) {
        $cell      = $sheet->getCell($colum);
        $validator = new Validator();
        $date      = DB::NULLED;
        if ($validator->required("", $cell->getValue())) {
            $date = $cell->getValue();
            $date = date("Y-m-d H:i:s", PHPExcel_Shared_Date::ExcelToPHP($date));
            $date = Hash::addHours($date, 5);
        }
        if ($date == "NULLED") {
            $date = "0000-00-00 00:00:00";
        }
        if ($date == "1970-01-01 00:00:00") {
            $date = "1900-01-02 00:00:00";
        }
        return $date;
    }

    //calcula la cedula de los ingenieros a base del nombre dado en el excel
    private function cedula_del_inegeniero($nombre, $ingenieros) {
        $nombreP = strtoupper(str_replace(array("\n", "\r", "\t", ".", " "), '', $nombre));
        for ($i = 0; $i < count($ingenieros); $i++) {
            if ($nombreP == strtoupper(str_replace(array("\n", "\r", "\t", ".", " "), '', $ingenieros[$i]['name']))) {
                return $ingenieros[$i]['id'];
            }
        }

        $this->load->helper('camilo');

        $posible = comparationsNames($ingenieros, $nombre);
        if ($posible) {
            $inge = $this->Dao_user_model->get_user_by_concat_name($posible);
            return $inge->k_id_user;
        } else {
            return '3';
        }
    }

    // retorna el id del estado segun el nombre del tipo y estado
    private function get_estado_by_name_ot_hiha($name_type, $status) {

        $id_tipo = $this->Dao_tipo_ot_hija_model->get_tipo_ot_hija_by_name($name_type);
        if ($id_tipo) {
            $id_estado = $this->Dao_estado_ot_model->get_status_by_idtipo_and_name_status($id_tipo->id_tipo, $status);
        } else {
            $id_estado = $this->Dao_estado_ot_model->get_status_by_idtipo_and_name_status(62, 'indefinido');
        }

        return $id_estado;
    }

    // inserta registros antiguos de tabla ot hija en cierre ots
    private function insertar_cierre_ots() {
        // Se usa para eliminar los registros que vienen de dias anteriores a la actual data subida
        $penultima = $this->Dao_ot_hija_model->getPenultimaFechaCarga()->fecha;
        if ($penultima) {
            // Para pasar las ots pasadas de dias anteriores a la tabla de cierre
            $traslado = $this->Dao_cierre_ots_model->trasladar_oth($penultima);
            if ($traslado > 0) {
                // si se trasladaron con exito se eliminan de la tabla ot_hija
                $delete = $this->Dao_ot_hija_model->delete_oth_by_fecha($penultima);
                // funcion para eliminar los registro duplicados de la tabla de cierre
                $duplicadas_borradas = $this->Dao_cierre_ots_model->delete_duplicates();
                // funcion para eliminar de cierre las ots que ya estan activas en tabla ot_hija
                $eliminar_activas = $this->Dao_cierre_ots_model->delete_actives();
            }
        }
    }


    // Enviar a cierre todas las actividades que sean menor a la ultima actualizacion
    public function enviar_a_cierre($export){
        // Para pasar las ots pasadas de dias anteriores a la tabla de cierre
        $traslado = $this->Dao_cierre_ots_model->trasladar_oth_by_last_export($export);
        if ($traslado > 0) {
            // si se trasladaron con exito se eliminan de la tabla ot_hija
            $delete = $this->Dao_ot_hija_model->delete_oth_by_last_export($export);
            // funcion para eliminar los registro duplicados de la tabla de cierre
            $duplicadas_borradas = $this->Dao_cierre_ots_model->delete_duplicates();
            // funcion para eliminar de cierre las ots que ya estan activas en tabla ot_hija
            $eliminar_activas = $this->Dao_cierre_ots_model->delete_actives();
        }
    }

    // funcion para actualizar una ot padre desde el excel
    private function actualizar_ot_padre($sheet, $row, $list_inges) {
        // Se debe insertar en tabla ot_padre
        $dataotp = array(
            'k_id_user'            => $this->cedula_del_inegeniero(str_replace(array("ñ", "Ñ"), 'N', $this->getValueCell($sheet, 'AB' . $row)), $list_inges),
            'id_cliente_onyx'      => $this->getValueCell($sheet, 'A' . $row),
            'n_nombre_cliente'     => $this->getValueCell($sheet, 'B' . $row),
            'orden_trabajo'        => $this->getValueCell($sheet, 'P' . $row),
            'servicio'             => $this->getValueCell($sheet, 'R' . $row),
            'estado_orden_trabajo' => $this->getValueCell($sheet, 'W' . $row),
            'fecha_creacion'       => $this->getDatePHPExcel($sheet, 'U' . $row),
            'fecha_compromiso'     => $this->getDatePHPExcel($sheet, 'AO' . $row),
            'fecha_programacion'   => $this->getDatePHPExcel($sheet, 'AP' . $row),
        );
        // funcion para actualizar datos otp
        $this->Dao_ot_padre_model->update_ot_padre($dataotp, $this->getValueCell($sheet, 'Q' . $row));
    }

    // funcion para eliminar oth
    private function eliminar_oth_excel($id_oth) {
        $delete = $this->Dao_ot_hija_model->delete_oth($id_oth);
    }

    /******************CREAR OT MANUALMENTE******************/

    // Cargar vistas manualmente
    public function crear_orden() {
        if (!Auth::check()) {
            Redirect::to(URL::base());
        }
        $data['title']    = 'Creación de OTH';
        $data['cantidad'] = $this->Dao_ot_hija_model->getCantUndefined();

        $list['inenieros']   = $this->Dao_user_model->getAllEngineers();
        $list['tipos_otp']   = $this->Dao_ot_padre_model->getListTypesOTP();
        $list['estados_otp'] = $this->Dao_ot_padre_model->getListStatusOTP();
        $list['tipos_oth']   = $this->Dao_tipo_ot_hija_model->getListTypesOTH();

        $data['cantidad'] = $this->Dao_ot_hija_model->getCantUndefined();
        $this->load->view('parts/headerF', $data);
        $this->load->view('creacionOTS', $list);
        $this->load->view('parts/footerF');
    }

    // crear ots manualmente
    public function create_ot() {
        $fActual = date('Y-m-d');

        $id_otp     = $this->input->post('id_otp');
        $tipo_otp   = $this->input->post('tipo_otp');
        $estado_otp = $this->input->post('estado_otp');
        $id_user    = $this->input->post('ing_responsable');

        $tipo_oth   = $this->input->post('tipo_oth');
        $estado_oth = $this->input->post('estado_oth');

        //validar si ya existe la otp
        $existe_otp = $this->Dao_ot_padre_model->exist_otp_by_id($id_otp);

        if (!$existe_otp) {
            // Se debe insertar en tabla ot_padre
            $data_otp = array(
                'k_id_ot_padre'        => $id_otp,
                'k_id_user'            => $id_user,
                'n_nombre_cliente'     => $this->input->post('nombre_cliente'),
                'orden_trabajo'        => $tipo_otp,
                'servicio'             => 'servicio',
                'estado_orden_trabajo' => $estado_otp,
                'fecha_creacion'       => $fActual,
                'fecha_compromiso'     => $this->input->post('fecha_compromiso'),
                'fecha_programacion'   => $this->input->post('fecha_programacion'),
            );
            // funcion para insertar datos otp
            $this->Dao_ot_padre_model->insert_data_otp($data_otp);
        }

        $id_estado_oth = $this->get_estado_by_name_ot_hiha($tipo_oth, $estado_oth)->k_id_estado_ot;

        $name_inge = $this->Dao_user_model->getUserById($id_user);

        $data_oth = array(
            'id_orden_trabajo_hija'     => $this->input->post('id_oth'),
            'k_id_estado_ot'            => $id_estado_oth,
            'nro_ot_onyx'               => $id_otp,
            'usuario_asignado'          => $name_inge->n_name_user . " " . $name_inge->n_last_name_user,
            'fecha_creacion_ot_hija'    => $fActual,
            'ot_hija'                   => $tipo_oth,
            'estado_orden_trabajo_hija' => $estado_oth,
            'fecha_insercion_zolid'     => $fActual,
            'fecha_actual'              => $fActual,
            'estado_mod'                => 0,
            'c_email'                   => 0,
            'b_flag'                    => '1',
        );

        //inserto la fila en la base de datos
        $insert = $this->Dao_ot_hija_model->insert_ot_hija($data_oth);

        $msj = ($insert === 1) ? 'ok' : $insert['message'];

        $this->session->set_flashdata('msj', $msj);

        header('location: ' . URL::base() . "/creacionoth");
    }

    // editar ots
    public function edit_ot() {
        $fActual = date('Y-m-d H:i:s');

        $id_otp     = $this->input->post('id_otp');
        $tipo_otp   = $this->input->post('tipo_otp');
        $estado_otp = $this->input->post('estado_otp');
        $id_user    = $this->input->post('ing_responsable');

        $tipo_oth   = $this->input->post('tipo_oth');
        $estado_oth = $this->input->post('estado_oth');

        // Se debe insertar en tabla ot_padre
        $data_otp = array(
            'k_id_user'            => $id_user,
            'n_nombre_cliente'     => $this->input->post('nombre_cliente'),
            'orden_trabajo'        => $tipo_otp,
            'servicio'             => 'servicio',
            'estado_orden_trabajo' => $estado_otp,
            'fecha_creacion'       => $fActual,
            'fecha_compromiso'     => $this->input->post('fecha_compromiso'),
            'fecha_programacion'   => $this->input->post('fecha_programacion'),
        );
        // funcion para insertar datos otp
        $update = $this->Dao_ot_padre_model->update_ot_padre($data_otp, $id_otp);

        $id_estado_oth = $this->get_estado_by_name_ot_hiha($tipo_oth, $estado_oth)->k_id_estado_ot;
        $name_inge     = $this->Dao_user_model->getUserById($id_user);

        $data_oth = array(
            'id_orden_trabajo_hija'     => $this->input->post('id_oth'),
            'k_id_estado_ot'            => $id_estado_oth,
            'usuario_asignado'          => $name_inge->n_name_user . " " . $name_inge->n_last_name_user,
            'fecha_creacion_ot_hija'    => $fActual,
            'ot_hija'                   => $tipo_oth,
            'estado_orden_trabajo_hija' => $estado_oth,
            'fecha_insercion_zolid'     => $fActual,
            'fecha_actual'              => $fActual,
            'estado_mod'                => 0,
            'c_email'                   => 0,
            'b_flag'                    => '1',
        );

        //actualizo la fila en la base de datos
        $update_otp = $this->Dao_ot_hija_model->update_ot_hija_mod($data_oth);

        $msj = ($update_otp === 1) ? 'ok' : $update_otp['message'];

        $this->session->set_flashdata('msj', $msj);

        header('location: ' . URL::base() . "/creacionoth");
    }

    // Enviar correo a los administradores con el cuadro de cantidades de reportes de actualizacion enviados por los ingenieros
    public function enviar_correo_cant_reportes_actualizacion() {
        // Variables actuales
        $this->load->model('data/Dao_email_model');
        $fecha  = new DateTime();
        $semana = $fecha->format('W') - 1;
        $ano    = $fecha->format('Y');
        $date   = $fecha->format('Y-m-d H:i:s');
        // Valido si ya existe un registro en la tabla de la semana actual
        $existe = $this->Dao_email_model->get_reporteautomatico_by_week($ano, $semana);
        // Si no existe se envia el correo y se guarda el registro, si existe no se hace nada
        if (!$existe) {
            // consulta para las cantidades que van a ir en el cuadro
            $cants = $this->Dao_ot_padre_model->getCountPtesPorEnvio();
            // retorna la plantilla del correo que se va a enviar
            $template = $this->template_mail_reporte_actualizacion($cants);
            // retornar los destinatarios (administradores)
            $users         = $this->Dao_user_model->get_mail_administrators();
            $destinatarios = array_column($users, 'n_mail_user');
            // eliminar espacios en blanco de un array
            array_walk($destinatarios, array($this, 'trim_value'));

            // enviar correo...
            $se_envio = $this->Dao_email_model->h_enviarCorreo($template, 'zolid.telmex.vip@gmail.com', 'TELMEX-VIP Informe automatico reportes de actualización', $destinatarios);
            // Si se envio el correo se actualiza la tabla de reporte_actualizacion con el nuevo registro
            if ($se_envio['success']) {
                $report = array(
                    'semana' => $semana,
                    'ano'    => $ano,
                    'fecha'  => $date,
                );


                $insert = $this->Dao_email_model->insert_reporte_automatico($report);
            }
            return (isset($se_envio['error'])) ? $se_envio['error'] : 'ok';
        } else {
            return 'enviado anteriormente';
        }

    }

    // callback de un array_walk... para quitar espacios en blanco de un array
    public function trim_value(&$value) {
        $value = trim($value);
    }

    // retorna la tabla armada en html para ser enviada
    public function template_mail_reporte_actualizacion($data) {
        $plantilla = '

            <div dir="ltr">
                <p style="background: #e4ff0085"><i>Ingenieros con todos los contadores en cero indica que no estan usando la plataforma</i></p>
                <table class="m_848243451567401046gmail-MsoNormalTable" border="0" cellpadding="0" align="left" width="963" style="border:1pt solid rgb(8,76,112);margin-left:4.8pt;margin-right:4.8pt">
                 <tbody>

                    <tr style="height:25.55pt;color:#fff;">
                      <td width="224" style="width:167.85pt;border:1pt solid rgb(221,221,221);background:rgb(8,76,111);padding:0cm 7.5pt;height:25.55pt">
                      <p class="MsoNormal" align="center" style="margin:0cm 0cm 0.0001pt;text-align:center;line-height:normal;font-size:11pt;font-family:Calibri,sans-serif"><b><span style="font-size:8pt;font-family:Helvetica,sans-serif">Ingeniero</span></b><b><span style="font-size:8pt;font-family:Helvetica,sans-serif"></span></b></p>
                      </td>
                      <td width="64" style="width:47.75pt;border:1pt solid rgb(221,221,221);background:rgb(8,76,111);padding:0cm 7.5pt;height:25.55pt">
                      <p class="MsoNormal" align="center" style="margin:0cm 0cm 0.0001pt;text-align:center;line-height:normal;font-size:11pt;font-family:Calibri,sans-serif"><b><span style="font-size:8pt;font-family:Helvetica,sans-serif">Menor 8 días</span></b></p>
                      </td>
                      <td width="55" style="width:41pt;border:1pt solid rgb(221,221,221);background:rgb(8,76,111);padding:0cm 7.5pt;height:25.55pt">
                      <p class="MsoNormal" align="center" style="margin:0cm 0cm 0.0001pt;text-align:center;line-height:normal;font-size:11pt;font-family:Calibri,sans-serif"><b><span style="font-size:8pt;font-family:Helvetica,sans-serif">Entre 8 y 15 días</span></b></p>
                      </td>
                      <td width="55" style="width:41.05pt;border:1pt solid rgb(221,221,221);background:rgb(8,76,111);padding:0cm 7.5pt;height:25.55pt">
                      <p class="MsoNormal" align="center" style="margin:0cm 0cm 0.0001pt;text-align:center;line-height:normal;font-size:11pt;font-family:Calibri,sans-serif"><b><span style="font-size:8pt;font-family:Helvetica,sans-serif">Entre 16 y 30 días</span></b></p>
                      </td>
                      <td width="55" style="width:41pt;border:1pt solid rgb(221,221,221);background:rgb(8,76,111);padding:0cm 7.5pt;height:25.55pt">
                      <p class="MsoNormal" align="center" style="margin:0cm 0cm 0.0001pt;text-align:center;line-height:normal;font-size:11pt;font-family:Calibri,sans-serif"><b><span style="font-size:8pt;font-family:Helvetica,sans-serif">Mayor 30 días</span></b></p>
                      </td>
                      <td width="244" style="width:182.8pt;border:1pt solid rgb(221,221,221);background:rgb(8,76,111);padding:0cm 7.5pt;height:25.55pt">
                      <p class="MsoNormal" align="center" style="margin:0cm 0cm 0.0001pt;text-align:center;line-height:normal;font-size:11pt;font-family:Calibri,sans-serif"><b><span style="font-size:8pt;font-family:Helvetica,sans-serif">Ingeniero</span></b><b><span style="font-size:8pt;font-family:Helvetica,sans-serif"></span></b></p>
                      </td>
                      <td width="64" style="width:48.1pt;border:1pt solid rgb(221,221,221);background:rgb(8,76,111);padding:0cm 7.5pt;height:25.55pt">
                      <p class="MsoNormal" align="center" style="margin:0cm 0cm 0.0001pt;text-align:center;line-height:normal;font-size:11pt;font-family:Calibri,sans-serif"><b><span style="font-size:8pt;font-family:Helvetica,sans-serif">Menor 8 días</span></b></p>
                      </td>
                      <td width="55" colspan="2" style="width:41pt;border:1pt solid rgb(221,221,221);background:rgb(8,76,111);padding:0cm 7.5pt;height:25.55pt">
                      <p class="MsoNormal" align="center" style="margin:0cm 0cm 0.0001pt;text-align:center;line-height:normal;font-size:11pt;font-family:Calibri,sans-serif"><b><span style="font-size:8pt;font-family:Helvetica,sans-serif">Entre 8 y 15 días</span></b></p>
                      </td>
                      <td width="64" colspan="2" style="width:48.15pt;border:1pt solid rgb(221,221,221);background:rgb(8,76,111);padding:0cm 7.5pt;height:25.55pt">
                      <p class="MsoNormal" align="center" style="margin:0cm 0cm 0.0001pt;text-align:center;line-height:normal;font-size:11pt;font-family:Calibri,sans-serif"><b><span style="font-size:8pt;font-family:Helvetica,sans-serif">Entre 16 y 30 días</span></b></p>
                      </td>
                      <td width="63" style="width:47.35pt;border:1pt solid rgb(221,221,221);background:rgb(8,76,111);padding:0cm 7.5pt;height:25.55pt">
                      <p class="MsoNormal" align="center" style="margin:0cm 0cm 0.0001pt;text-align:center;line-height:normal;font-size:11pt;font-family:Calibri,sans-serif"><b><span style="font-size:8pt;font-family:Helvetica,sans-serif">Mayor 30 días</span></b></p>
                      </td>
                    </tr>';

        for ($i = 0; $i < count($data); $i += 2) {
            $plantilla .= '
                        <tr style="height:8.55pt">
                            <td width="224" style="width:200.85pt;border-bottom:1pt solid rgb(221,221,221);padding:0cm 7.5pt;height:8.55pt">
                            <p class="MsoNormal" style="margin:0cm 0cm 0.0001pt;line-height:normal;font-size:11pt;font-family:Calibri,sans-serif"><span style="font-size:9pt;font-family:Helvetica,sans-serif"><b>' . strtolower($data[$i]->ingeniero) . '</b></span><span style="font-size:9pt;font-family:Helvetica,sans-serif"></span></p>
                            </td>
                            <td width="64" style="width:47.75pt;border:1pt solid rgb(221,221,221);padding:0cm 7.5pt;height:8.55pt';
            if ($data[$i]->menor_7 > 0) {
                $plantilla .= ';background:#00800075';
            }

            $plantilla .= '">
                            <p class="MsoNormal" align="center" style="margin:0cm 0cm 0.0001pt;text-align:center;line-height:normal;font-size:11pt;font-family:Calibri,sans-serif"><span style="font-size:9pt;font-family:Helvetica,sans-serif">' . strtolower($data[$i]->menor_7) . '</span></p>
                            </td>
                            <td width="55" style="width:41pt;border:1pt solid rgb(221,221,221);padding:0cm 7.5pt;height:8.55pt';
            if ($data[$i]->entre_8_15 > 0) {
                $plantilla .= ';background:#fcff02a6';
            }

            $plantilla .= '">
                            <p class="MsoNormal" align="center" style="margin:0cm 0cm 0.0001pt;text-align:center;line-height:normal;font-size:11pt;font-family:Calibri,sans-serif"><span style="font-size:9pt;font-family:Helvetica,sans-serif">' . strtolower($data[$i]->entre_8_15) . '</span></p>
                            </td>
                            <td width="55" style="width:41.05pt;border:1pt solid rgb(221,221,221);padding:0cm 7.5pt;height:8.55pt';
            if ($data[$i]->entre_16_30 > 0) {
                $plantilla .= ';background:#f2a404c7';
            }

            $plantilla .= '">
                            <p class="MsoNormal" align="center" style="margin:0cm 0cm 0.0001pt;text-align:center;line-height:normal;font-size:11pt;font-family:Calibri,sans-serif"><span style="font-size:9pt;font-family:Helvetica,sans-serif">' . strtolower($data[$i]->entre_16_30) . '</span></p>
                            </td>
                            <td width="55" style="width:41pt;border:1pt solid rgb(221,221,221);padding:0cm 7.5pt;height:8.55pt';
            if ($data[$i]->mayor_30 > 0) {
                $plantilla .= ';background:#ed220073';
            }

            $plantilla .= '">
                            <p class="MsoNormal" align="center" style="margin:0cm 0cm 0.0001pt;text-align:center;line-height:normal;font-size:11pt;font-family:Calibri,sans-serif"><span style="font-size:9pt;font-family:Helvetica,sans-serif">' . strtolower($data[$i]->mayor_30) . '</span></p>
                            </td>';

            if (isset($data[$i + 1])) {
                $plantilla .= '
                            <td width="244" style="width:200.8pt;border-bottom:1pt solid rgb(221,221,221);padding:0cm 7.5pt;height:8.55pt">
                            <p class="MsoNormal" style="margin:0cm 0cm 0.0001pt;line-height:normal;font-size:11pt;font-family:Calibri,sans-serif"><span style="font-size:9pt;font-family:Helvetica,sans-serif"><b>' . strtolower($data[$i + 1]->ingeniero) . '</b></span><span style="font-size:9pt;font-family:Helvetica,sans-serif"></span></p>
                            </td>
                            <td width="64" style="width:48.1pt;border:1pt solid rgb(221,221,221);padding:0cm 7.5pt;height:8.55pt';
                if ($data[$i + 1]->menor_7 > 0) {
                    $plantilla .= ';background:#00800075';
                }

                $plantilla .= '">
                            <p class="MsoNormal" align="center" style="margin:0cm 0cm 0.0001pt;text-align:center;line-height:normal;font-size:11pt;font-family:Calibri,sans-serif"><span style="font-size:9pt;font-family:Helvetica,sans-serif">' . strtolower($data[$i + 1]->menor_7) . '</span></p>
                            </td>
                            <td width="55" colspan="2" style="width:41pt;border:1pt solid rgb(221,221,221);padding:0cm 7.5pt;height:8.55pt';
                if ($data[$i + 1]->entre_8_15 > 0) {
                    $plantilla .= ';background:#fcff02a6';
                }

                $plantilla .= '">
                            <p class="MsoNormal" align="center" style="margin:0cm 0cm 0.0001pt;text-align:center;line-height:normal;font-size:11pt;font-family:Calibri,sans-serif"><span style="font-size:9pt;font-family:Helvetica,sans-serif">' . strtolower($data[$i + 1]->entre_8_15) . '</span></p>
                            </td>
                            <td width="64" colspan="2" style="width:48.15pt;border:1pt solid rgb(221,221,221);padding:0cm 7.5pt;height:8.55pt';
                if ($data[$i + 1]->entre_16_30 > 0) {
                    $plantilla .= ';background:#f2a404c7';
                }

                $plantilla .= '">
                            <p class="MsoNormal" align="center" style="margin:0cm 0cm 0.0001pt;text-align:center;line-height:normal;font-size:11pt;font-family:Calibri,sans-serif"><span style="font-size:9pt;font-family:Helvetica,sans-serif">' . strtolower($data[$i + 1]->entre_16_30) . '</span></p>
                            </td>
                            <td width="63" style="width:47.35pt;border:1pt solid rgb(221,221,221);padding:0cm 7.5pt;height:8.55pt';
                if ($data[$i + 1]->mayor_30 > 0) {
                    $plantilla .= ';background:#ed220073';
                }

                $plantilla .= '">
                            <p class="MsoNormal" align="center" style="margin:0cm 0cm 0.0001pt;text-align:center;line-height:normal;font-size:11pt;font-family:Calibri,sans-serif"><span style="font-size:9pt;font-family:Helvetica,sans-serif">' . strtolower($data[$i + 1]->mayor_30) . '</span></p>
                            </td>';
            }

            $plantilla .= '</tr>';

        }
        $plantilla .= '
                            </tbody>
                        </table>

                            <p class="MsoNormal" style="margin:15pt 0cm 7.5pt;line-height:normal;font-size:11pt;font-family:Calibri,sans-serif"><span style="font-size:18pt;color:rgb(51,51,51)">&nbsp;</span></p>

                            <p><i>Este reporte automático se genera semanalmente, muestra el listado de ingenieros de la operación, la cantidad de otp y el rango de días que tienen sin ser enviado reporte de actualización.</i></p>
                            <p class="MsoNormal" style="margin:0cm 0cm 8pt;line-height:107%;font-size:11pt;font-family:Calibri,sans-serif"><span lang="EN-US">&nbsp;</span></p></div>
                        ';
        return $plantilla;

    }

    //valida si una oth es de tipo kick off y se encuentra cerrada
    public function oth_cerrada_zolid($name_type, $status_order) {
        $id_tipo = $this->Dao_tipo_ot_hija_model->get_tipo_ot_hija_by_name($name_type);
        if ($id_tipo->id_tipo == 1 && $status_order == 'Cerrada') {
            $return = true;
        } else {
            $return = false;
        }

        return $return;
    }

}
