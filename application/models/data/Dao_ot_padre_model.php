<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Dao_ot_padre_model extends CI_Model {

    protected $session;

    public function __construct() {
        
    }

    // Retorna registro otp por id de ot padre
    public function exist_otp_by_id($id) {
        $query = $this->db->query(
                "SELECT k_id_ot_padre
                FROM ot_padre
				WHERE k_id_ot_padre = $id
			");
        return $query->row();
    }

    // Inserta datos a la tabla otp
    public function insert_data_otp($data_otp) {
        $this->db->insert('ot_padre', $data_otp);
    }

    // Actualiza ot padre
    public function update_ot_padre($data, $id_otp) {
        $this->db->where('k_id_ot_padre', $id_otp);
        $this->db->update('ot_padre', $data);
        // $this->db->last_query();
        $error = $this->db->error();
        if ($error['message']) {
            // print_r($error);
            return $error;
        } else {
            return 1;
        }
    }

    // Retorna ots de ingenieros sin estado cancelada, cerrada ni terminada
    public function get_otp_by_id_user($id) {
        $query = $this->db->query("
				SELECT
				k_id_ot_padre, estado_orden_trabajo
				FROM
				ot_padre
				WHERE
				k_id_user = '$id' AND
				estado_orden_trabajo != 'otp_cerrada'
		");
        return $query->result();
    }

    public function validateActiveGroup($tipoFiltro, $wherorand) {
        if ($tipoFiltro === 'GESTION OTS PROYECTOS') {
            return "$wherorand `user`.n_group = 'GESTION OTS PROYECTOS'";
        } else {
            if ($tipoFiltro === 'GESTION OTS ESTANDAR') {
                return "$wherorand `user`.n_group = 'GESTION OTS ESTANDAR'";
            } else {
                return ' ';
            }
        }
    }

    // tabla de lista de OTS Padre
    public function getListOtsOtPadre($fil) {
        $condicion = " ";
        if (Auth::user()->n_role_user == 'ingeniero') {
            $usuario_session = Auth::user()->k_id_user;
            $condicion = " WHERE otp.k_id_user = $usuario_session ";
        } else {
            $condicion = $this->validateActiveGroup($fil, 'WHERE');
        }



        $query = $this->db->query(
                "SELECT
            otp.k_id_ot_padre,
            otp.n_nombre_cliente,
            otp.orden_trabajo,
            ( SELECT COUNT( id_ot_padre ) FROM reporte_info WHERE id_ot_padre = otp.k_id_ot_padre ) AS MAIL_enviados,
            otp.servicio,
            REPLACE ( otp.estado_orden_trabajo, 'otp_cerrada', 'Cerrada' ) AS estado_orden_trabajo,
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
            CONCAT( '$ ', FORMAT( oth.monto_moneda_local_arriendo + oth.monto_moneda_local_cargo_mensual, 2 ) ) AS MRC,
            otp.lista_observaciones,
            (SELECT COUNT(oth2.nro_ot_onyx) FROM ot_hija oth2 WHERE otp.k_id_ot_padre = oth2.nro_ot_onyx ) AS cant_oths,
            (SELECT IF(oth2.direccion_origen = '', oth2.alias_enlace, oth2.direccion_origen) FROM ot_hija oth2 WHERE otp.k_id_ot_padre = oth2.nro_ot_onyx limit 1) AS direccion,
            (SELECT oth2.ciudad FROM ot_hija oth2 WHERE otp.k_id_ot_padre = oth2.nro_ot_onyx limit 1) AS ciudad
            FROM ot_hija oth
            RIGHT JOIN ot_padre otp ON oth.nro_ot_onyx = otp.k_id_ot_padre
            INNER JOIN `user` ON otp.k_id_user = `user`.k_id_user
            LEFT JOIN hitos ON hitos.id_ot_padre = otp.k_id_ot_padre
            $condicion
            GROUP BY
            otp.k_id_ot_padre
        ");
        // echo("<pre>"); print_r($this->db->last_query()); echo("</pre>");
        return $query;
    }

    // tabla que lista las OT Padre que tengan fecha de compromiso para hoy
    public function getListOtsOtPadreHoy($fil) {
        $condicion = " ";
        if (Auth::user()->n_role_user == 'ingeniero') {
            $usuario_session = Auth::user()->k_id_user;
            $condicion = " AND otp.k_id_user = $usuario_session ";
        } else {
            $condicion = $this->validateActiveGroup($fil, 'AND');
        }
        $query = $this->db->query(
                "SELECT
                    otp.k_id_ot_padre, otp.n_nombre_cliente, otp.orden_trabajo,
                    otp.servicio, REPLACE(otp.estado_orden_trabajo,'otp_cerrada','Cerrada') AS estado_orden_trabajo, otp.fecha_programacion,
                    otp.fecha_compromiso, otp.fecha_creacion, otp.k_id_user, user.n_name_user,
                    CONCAT(user.n_name_user, ' ' , user.n_last_name_user) AS ingeniero,
                    otp.lista_observaciones, otp.observacion, SUM(oth.c_email) AS cant_mails, hitos.id_hitos, otp.finalizo, otp.ultimo_envio_reporte,
                    CONCAT('$ ',FORMAT(oth.monto_moneda_local_arriendo + oth.monto_moneda_local_cargo_mensual,2)) AS MRC, otp.lista_observaciones,
                    (SELECT IF(oth2.direccion_origen = '', oth2.alias_enlace, oth2.direccion_origen) FROM ot_hija oth2 WHERE otp.k_id_ot_padre = oth2.nro_ot_onyx limit 1) AS direccion,
                    (SELECT oth2.ciudad FROM ot_hija oth2 WHERE otp.k_id_ot_padre = oth2.nro_ot_onyx limit 1) AS ciudad
                    FROM ot_hija oth
                    INNER JOIN ot_padre otp ON oth.nro_ot_onyx = otp.k_id_ot_padre
                    INNER JOIN user ON otp.k_id_user = user.k_id_user
                    LEFT JOIN hitos ON hitos.id_ot_padre = otp.k_id_ot_padre
                    WHERE otp.fecha_compromiso = CURDATE()
                $condicion
                GROUP BY nro_ot_onyx
        ");
        // echo("<pre>"); print_r($this->db->last_query()); echo("</pre>");
        return $query->result();
    }

    // tabla que lista las OT Padre que tengan fecha de compromiso vencida
    public function getListOtsOtPadreVencidas($fil) {
        $condicion = " ";
        if (Auth::user()->n_role_user == 'ingeniero') {
            $usuario_session = Auth::user()->k_id_user;
            $condicion = " AND otp.k_id_user = $usuario_session ";
        } else {
            $condicion = $this->validateActiveGroup($fil, 'AND');
        }
        $query = $this->db->query(
                "SELECT
                otp.k_id_ot_padre, otp.n_nombre_cliente, otp.orden_trabajo,
                otp.servicio, REPLACE(otp.estado_orden_trabajo,'otp_cerrada','Cerrada') AS estado_orden_trabajo, otp.fecha_programacion,
                otp.fecha_compromiso, otp.fecha_creacion, otp.k_id_user, user.n_name_user,
                CONCAT(user.n_name_user, ' ' , user.n_last_name_user) AS ingeniero,
                otp.lista_observaciones, otp.observacion, SUM(oth.c_email) AS cant_mails, hitos.id_hitos, otp.finalizo, otp.ultimo_envio_reporte,
                CONCAT('$ ',FORMAT(oth.monto_moneda_local_arriendo + oth.monto_moneda_local_cargo_mensual,2)) AS MRC, otp.lista_observaciones,
                (SELECT IF(oth2.direccion_origen = '', oth2.alias_enlace, oth2.direccion_origen) FROM ot_hija oth2 WHERE otp.k_id_ot_padre = oth2.nro_ot_onyx limit 1) AS direccion,
		(SELECT oth2.ciudad FROM ot_hija oth2 WHERE otp.k_id_ot_padre = oth2.nro_ot_onyx limit 1) AS ciudad
                FROM ot_hija oth
                INNER JOIN ot_padre otp ON oth.nro_ot_onyx = otp.k_id_ot_padre
                INNER JOIN user ON otp.k_id_user = user.k_id_user
                LEFT JOIN hitos ON hitos.id_ot_padre = otp.k_id_ot_padre
                WHERE otp.fecha_compromiso < CURDATE()
                $condicion
                GROUP BY nro_ot_onyx
        ");
        // echo("<pre>"); print_r($this->db->last_query()); echo("</pre>");
        return $query->result();
    }

    //Inserta la observaciones, usuario que lo hizo y fecha de la vista detalles
    public function update_new_data($data) {
        if (Auth::user()->n_role_user == 'administrador') {
            $this->db->where('k_id_ot_padre', $data['k_id_ot_padre']);
            $this->db->update('ot_padre', $data);
        } else {
            $this->db->where('k_id_user', Auth::user()->k_id_user);
            $this->db->where('k_id_ot_padre', $data['k_id_ot_padre']);
            $this->db->update('ot_padre', $data);
        }


        if ($this->db->affected_rows() > 0) {
            // print_r($this->db->last_query());
            return true;
        } else {
            // print_r($this->db->last_query());
            return false;
        }
    }

    // return $query->result();
    // trae otp segun opcion de ot padre
    public function getOtpByOpcList($opcion, $fil) {
        $condicion = " ";
        if (Auth::user()->n_role_user == 'ingeniero') {
            $usuario_session = Auth::user()->k_id_user;
            $condicion = " AND otp.k_id_user = $usuario_session ";
        } else {
            $condicion = $this->validateActiveGroup($fil, 'AND');
        }
        $query = $this->db->query(
                "SELECT
                otp.k_id_ot_padre, otp.n_nombre_cliente, otp.orden_trabajo,
                otp.servicio, REPLACE(otp.estado_orden_trabajo,'otp_cerrada','Cerrada') AS estado_orden_trabajo, otp.fecha_programacion,
                otp.fecha_compromiso, otp.fecha_creacion, otp.k_id_user, user.n_name_user,
                CONCAT(user.n_name_user, ' ' , user.n_last_name_user) AS ingeniero,
                otp.lista_observaciones, otp.observacion, SUM(oth.c_email) AS cant_mails, hitos.id_hitos, otp.finalizo, otp.ultimo_envio_reporte,
                CONCAT('$ ',FORMAT(oth.monto_moneda_local_arriendo + oth.monto_moneda_local_cargo_mensual,2)) AS MRC, otp.lista_observaciones,
                (SELECT IF(oth2.direccion_origen = '', oth2.alias_enlace, oth2.direccion_origen) FROM ot_hija oth2 WHERE otp.k_id_ot_padre = oth2.nro_ot_onyx limit 1) AS direccion,
		(SELECT oth2.ciudad FROM ot_hija oth2 WHERE otp.k_id_ot_padre = oth2.nro_ot_onyx limit 1) AS ciudad
                FROM ot_hija oth
                INNER JOIN ot_padre otp ON oth.nro_ot_onyx = otp.k_id_ot_padre
                INNER JOIN user ON otp.k_id_user = user.k_id_user
                LEFT JOIN hitos ON hitos.id_ot_padre = otp.k_id_ot_padre
                WHERE lista_observaciones = '$opcion'
                $condicion
                GROUP BY oth.nro_ot_onyx

        ");
        // echo("<pre>"); print_r($this->db->last_query()); echo("</pre>");
        return $query->result();
    }

    //trae la cantidad de ot hijas en ejecucion de una ot padre
    public function getCantOthInExecutionByIdOtp($idOtp) {
        $query = $this->db->query("
                SELECT COUNT(k_id_register) AS cant
                FROM ot_hija oth
                WHERE nro_ot_onyx = $idOtp
                AND estado_orden_trabajo_hija != 'Cerrada'
                AND estado_orden_trabajo_hija != 'Cancelada'
                AND estado_orden_trabajo_hija != '3- Terminada'
        ");
        return $query->row();
    }

    //trae las ot hijas en ejecucion de una ot padre
    public function getOthInExecutionByIdOtp($idOtp) {
        $query = $this->db->query("
                SELECT id_orden_trabajo_hija
                FROM ot_hija oth
                WHERE nro_ot_onyx = $idOtp
                AND estado_orden_trabajo_hija != 'Cerrada'
                AND estado_orden_trabajo_hija != 'Cancelada'
                AND estado_orden_trabajo_hija != '3- Terminada'
        ");
        return $query->result();
    }

    //Trae todas las othijas de una otp en especifico
    public function getothofothp($idOtp) {
        $query = $this->db->query("
            SELECT oth.id_orden_trabajo_hija, oth.ot_hija, oth.estado_orden_trabajo_hija, oth.c_email,
                CONCAT('$ ',FORMAT(oth.monto_moneda_local_arriendo + oth.monto_moneda_local_cargo_mensual,2)) AS MRC,
                otp.fecha_compromiso, otp.fecha_programacion
            FROM ot_hija oth
            INNER JOIN ot_padre otp
            ON otp.k_id_ot_padre = oth.nro_ot_onyx
            WHERE oth.nro_ot_onyx = $idOtp
        ");
        return $query->result();
    }

    //Trae todas las ot hijas que se encuentren en la tabla cierre de una otp en especifico
    public function getOthOfOtpCierre($idOtp) {
        $query = $this->db->query("
            SELECT cot.id_orden_trabajo_hija, cot.ot_hija, cot.estado_orden_trabajo_hija, cot.c_email,
                CONCAT('$ ',FORMAT(cot.monto_moneda_local_arriendo + cot.monto_moneda_local_cargo_mensual,2)) AS MRC,
                otp.fecha_compromiso, otp.fecha_programacion
            FROM cierre_ots cot
            INNER JOIN ot_padre otp
            ON otp.k_id_ot_padre = cot.nro_ot_onyx
            WHERE cot.nro_ot_onyx = $idOtp
        ");
        return $query->result();
    }

    // eliminar de tabla ot padre, pasar id otp o array con ids otp
    public function deleteById($otp) {
        $this->db->where_in('k_id_ot_padre', $otp);
        $this->db->delete('ot_padre');
        if ($this->db->affected_rows() > 0) {
            return $this->db->affected_rows();
        } else {
            return 0;
        }
    }

    // ots q tienen correos enviados
    public function getListOtsOtPadreEmail() {
        $condicion = " ";
        if (Auth::user()->n_role_user == 'ingeniero') {
            $usuario_session = Auth::user()->k_id_user;
            $condicion = " AND otp.k_id_user = $usuario_session ";
        }
        $query = $this->db->query("
                SELECT
                otp.k_id_ot_padre, otp.n_nombre_cliente, otp.orden_trabajo,
                otp.servicio, REPLACE(otp.estado_orden_trabajo,'otp_cerrada','Cerrada') AS estado_orden_trabajo, otp.fecha_programacion,
                otp.fecha_compromiso, otp.fecha_creacion, otp.k_id_user, user.n_name_user,
                CONCAT(user.n_name_user, ' ' , user.n_last_name_user) AS ingeniero,
                otp.lista_observaciones, otp.observacion, SUM(oth.c_email) AS cant_mails, hitos.id_hitos, otp.finalizo, otp.ultimo_envio_reporte,
                CONCAT('$ ',FORMAT(oth.monto_moneda_local_arriendo + oth.monto_moneda_local_cargo_mensual,2)) AS MRC
                FROM ot_hija oth
                INNER JOIN ot_padre otp ON oth.nro_ot_onyx = otp.k_id_ot_padre
                INNER JOIN user ON otp.k_id_user = user.k_id_user
                LEFT JOIN hitos ON hitos.id_ot_padre = otp.k_id_ot_padre
                $condicion
                GROUP BY nro_ot_onyx
                HAVING cant_mails > 0
                ORDER BY cant_mails DESC
        ");
        return $query->result();
    }

    // trae  todas las ots que tienen que enviar correo de actualizacion
    public function getOtsPtesPorEnvioActualizacion($fil) {
        $condicion = " ";
        $cond2 = " ";
        if (Auth::user()->n_role_user == 'ingeniero') {
            $usuario_session = Auth::user()->k_id_user;
            $condicion = " AND otp.k_id_user = $usuario_session ";
        } else {
            $cond2 = $this->validateActiveGroup($fil, 'AND');
        }
        $query = $this->db->query(
                "SELECT
            otp.k_id_ot_padre, otp.n_nombre_cliente, otp.orden_trabajo,
            (
                SELECT COUNT(idreporte_info) as cant FROM reporte_info where paquete_enviados >= 1 and id_ot_padre = otp.k_id_ot_padre
            ) AS MAIL_enviados,
            otp.servicio, REPLACE(otp.estado_orden_trabajo,'otp_cerrada','Cerrada') AS estado_orden_trabajo, otp.fecha_programacion,
            otp.fecha_compromiso, otp.fecha_creacion, otp.k_id_user, user.n_name_user,
            CONCAT(user.n_name_user, ' ' , user.n_last_name_user) AS ingeniero,
            otp.lista_observaciones, otp.observacion, SUM(oth.c_email) AS cant_mails, hitos.id_hitos, otp.finalizo, otp.ultimo_envio_reporte,
            CONCAT('$ ',FORMAT(oth.monto_moneda_local_arriendo + oth.monto_moneda_local_cargo_mensual,2)) AS MRC,
            (SELECT IF(oth2.direccion_origen = '', oth2.alias_enlace, oth2.direccion_origen) FROM ot_hija oth2 WHERE otp.k_id_ot_padre = oth2.nro_ot_onyx limit 1) AS direccion,
            (SELECT oth2.ciudad FROM ot_hija oth2 WHERE otp.k_id_ot_padre = oth2.nro_ot_onyx limit 1) AS ciudad
            FROM ot_hija oth
            INNER JOIN ot_padre otp ON oth.nro_ot_onyx = otp.k_id_ot_padre
            INNER JOIN user ON otp.k_id_user = user.k_id_user
            LEFT JOIN hitos ON hitos.id_ot_padre = otp.k_id_ot_padre
            WHERE
            DATEDIFF(CURDATE(), otp.ultimo_envio_reporte) > 6
            AND n_nombre_cliente NOT IN ('BANCO COLPATRIA RED MULTIBANCA COLPATRIA S.A', 'BANCO DAVIVIENDA S.A', 'SERVIBANCA S.A.')
            AND otp.orden_trabajo != 'Caso de Seguimiento'
            -- AND user.n_group = 'GESTION OTS ESTANDAR'
            $cond2
            $condicion
            GROUP BY nro_ot_onyx
        ");
        // echo("<pre>"); print_r($this->db->last_query()); echo("</pre>") ;
        return $query;
    }

    // obtiene las otp de una sede (pasarle el id de la sede)
    public function get_otp_by_idsede($idsede) {
        $query = $this->db->query("
            SELECT
            otp.k_id_ot_padre,
            otp.k_id_user,
            otp.id_cliente_onyx,
            otp.n_nombre_cliente,
            otp.orden_trabajo,
            otp.servicio,
            otp.estado_orden_trabajo,
            otp.fecha_creacion,
            otp.fecha_compromiso,
            otp.fecha_programacion,
            s.id_sede,
            s.nombre_sede,
            (
                SELECT COUNT(1) FROM control_cambios cc
                WHERE cc.id_ot_padre = otp.k_id_ot_padre
            ) AS num_ctrl
            FROM
            ot_padre otp
            INNER JOIN sede s ON otp.id_sede = s.id_sede
            WHERE
            otp.id_sede = $idsede
        ");

        return $query->result();
    }

    // retorna todos los hitos de la ot_padre
    public function getHitosOtp($idOtp) {
        $query = $this->db->query("
            SELECT f_compromiso_ko, estado_ko, observaciones_ko,
                f_compromiso_voc, estado_voc, observaciones_voc,
                f_compromiso_ec, estado_ec, observaciones_ec,
                f_compromiso_ac, estado_ac, observaciones_ac,
                f_compromiso_sit, estado_sit, observaciones_sit,
                f_compromiso_veoc, estado_veoc, observaciones_veoc,
                f_compromiso_crc, estado_crc, observaciones_crc,
                f_compromiso_empalmes, estado_empalmes, observaciones_empalmes,
                f_compromiso_veut, estado_veut, observaciones_veut,
                actividad_actual, tipo_voc, tipo_veoc
            FROM hitos WHERE id_ot_padre = $idOtp
        ");

        return $query->row();
    }

    //inserta y/o actualiza los hitos de una OTP
    public function saveHitosOtp($data) {
        $respuesta = array();
        $query = "";
        $exist = $this->db->select('id_hitos')
                ->from('hitos')
                ->where('id_ot_padre', $data['id_ot_padre'])
                ->get();
        if ($exist->num_rows() <= 0) {
            $query = $this->db->insert('hitos', $data);
            // echo("<pre>"); print_r($this->db->last_query()); echo("</pre>");
            // $query = "INSERT INTO hitos (id_ot_padre,
            //                 f_compromiso_ko,
            //                 estado_ko,
            //                 observaciones_ko,
            //                 f_compromiso_voc,
            //                 estado_voc,
            //                 observaciones_voc,
            //                 f_compromiso_ec,
            //                 estado_ec,
            //                 observaciones_ec,
            //                 f_compromiso_ac,
            //                 estado_ac,
            //                 observaciones_ac,
            //                 f_compromiso_sit,
            //                 estado_sit,
            //                 observaciones_sit,
            //                 f_compromiso_veoc,
            //                 estado_veoc,
            //                 observaciones_veoc,
            //                 f_compromiso_empalmes,
            //                 estado_empalmes,
            //                 observaciones_empalmes,
            //                 f_compromiso_crc,
            //                 estado_crc,
            //                 observaciones_crc,
            //                 f_compromiso_veut,
            //                 estado_veut,
            //                 observaciones_veut,
            //                 actividad_actual,
            //                 tipo_voc,
            //                 tipo_veoc)
            //             VALUES
            //                 ($idOtp,
            //                 '" . $formulario[1]['value'] . "',
            //                 '" . $formulario[2]['value'] . "',
            //                 '" . $formulario[3]['value'] . "',
            //                 '" . $formulario[5]['value'] . "',
            //                 '" . $formulario[6]['value'] . "',
            //                 '" . $formulario[7]['value'] . "',
            //                 '" . $formulario[8]['value'] . "',
            //                 '" . $formulario[9]['value'] . "',
            //                 '" . $formulario[10]['value'] . "',
            //                 '" . $formulario[11]['value'] . "',
            //                 '" . $formulario[12]['value'] . "',
            //                 '" . $formulario[13]['value'] . "',
            //                 '" . $formulario[14]['value'] . "',
            //                 '" . $formulario[15]['value'] . "',
            //                 '" . $formulario[16]['value'] . "',
            //                 '" . $formulario[18]['value'] . "',
            //                 '" . $formulario[19]['value'] . "',
            //                 '" . $formulario[20]['value'] . "',
            //                 '" . $formulario[21]['value'] . "',
            //                 '" . $formulario[22]['value'] . "',
            //                 '" . $formulario[23]['value'] . "',
            //                 '" . $formulario[24]['value'] . "',
            //                 '" . $formulario[25]['value'] . "',
            //                 '" . $formulario[26]['value'] . "',
            //                 '" . $formulario[27]['value'] . "',
            //                 '" . $formulario[28]['value'] . "',
            //                 '" . $formulario[29]['value'] . "',
            //                 '" . $formulario[0]['value'] . "',
            //                 '" . $formulario[4]['value'] . "',
            //                 '" . $formulario[17]['value'] . "')";
        } else {
            $this->db->where('id_ot_padre', $data['id_ot_padre']);
            unset($data['id_ot_padre']);
            $query = $this->db->update('hitos', $data);
            // $query = "
            // UPDATE hitos SET
            // f_compromiso_ko = '" . $formulario[1]['value'] . "',
            // estado_ko = '" . $formulario[2]['value'] . "',
            // observaciones_ko = '" . $formulario[3]['value'] . "',
            // f_compromiso_voc = '" . $formulario[5]['value'] . "',
            // estado_voc = '" . $formulario[6]['value'] . "',
            // observaciones_voc = '" . $formulario[7]['value'] . "',
            // f_compromiso_ec = '" . $formulario[8]['value'] . "',
            // estado_ec = '" . $formulario[9]['value'] . "',
            // observaciones_ec = '" . $formulario[10]['value'] . "',
            // f_compromiso_ac = '" . $formulario[11]['value'] . "',
            // estado_ac = '" . $formulario[12]['value'] . "',
            // observaciones_ac = '" . $formulario[13]['value'] . "',
            // f_compromiso_sit = '" . $formulario[14]['value'] . "',
            // estado_sit = '" . $formulario[15]['value'] . "',
            // observaciones_sit = '" . $formulario[16]['value'] . "',
            // f_compromiso_veoc =' " . $formulario[18]['value'] . "',
            // estado_veoc = '" . $formulario[19]['value'] . "',
            // observaciones_veoc = '" . $formulario[20]['value'] . "',
            // f_compromiso_empalmes = '" . $formulario[21]['value'] . "',
            // estado_empalmes = '" . $formulario[22]['value'] . "',
            // observaciones_empalmes = '" . $formulario[23]['value'] . "',
            // f_compromiso_crc = '" . $formulario[24]['value'] . "',
            // estado_crc = '" . $formulario[25]['value'] . "',
            // observaciones_crc = '" . $formulario[26]['value'] . "',
            // f_compromiso_veut = '" . $formulario[27]['value'] . "',
            // estado_veut = '" . $formulario[28]['value'] . "',
            // observaciones_veut = '" . $formulario[29]['value'] . "',
            // actividad_actual = '" . $formulario[0]['value'] . "',
            // tipo_voc = '" . $formulario[4]['value'] . "',
            // tipo_veoc = '" . $formulario[17]['value'] . "'
            // WHERE id_ot_padre = $idOtp";
        }

        if ($this->db->affected_rows()) {
            $respuesta['response'] = 'success';
            $respuesta['msg'] = 'Se actualizo correctamente';
        } else {
            $respuesta['response'] = 'warning';
            $respuesta['msg'] = 'No se actualizó ningún campo u ocurrió un error';
            // $respuesta['msg'] = 'No se actualizó ningún campo o podido actualizar correctamente la informacion';
        }
        return $respuesta;
    }

    // obtener listas de tipos de otp
    public function getListTypesOTP() {
        $query = $this->db->query("
            SELECT orden_trabajo FROM ot_padre GROUP BY orden_trabajo
        ");
        return $query->result();
    }

    // retorna listadode estados de otp
    public function getListStatusOTP() {
        $query = $this->db->query("
            SELECT estado_orden_trabajo FROM ot_padre GROUP BY estado_orden_trabajo
        ");
        return $query->result();
    }

    /* retorna:
     * nombre_cliente
     * servicio
     * ciudad
     * diereccion
     * de una OTP
     */

    public function getDetailsHitosOTP($idOtp) {
        $query = $this->db->query("
            SELECT otp.n_nombre_cliente, otp.servicio, oth.ciudad,
                (SELECT IF(oth2.direccion_origen = '', oth2.alias_enlace, oth2.direccion_origen) FROM ot_hija oth2 WHERE otp.k_id_ot_padre = oth2.nro_ot_onyx limit 1) AS  'direccion'
            FROM ot_padre otp
            INNER JOIN ot_hija oth ON oth.nro_ot_onyx = otp.k_id_ot_padre
            WHERE otp.k_id_ot_padre =$idOtp
            LIMIT 1
        ");
        return $query->row();
    }

    /* retorna:
     * la informacion del producto de una OTP
     */

    public function getProductByOtp($idOtp, $numServicio) {
        $tabla = '';
        $columWhere = 'id_ot_padre';
        switch ($numServicio) {
            /* formulario Internet */
            case '1': // internet dedicado empresarial
            case '2': // internet dedicado
                $tabla = 'pr_internet';
                break;
            /* formulario MPLS */
            case '3': // mpls_avanzado_intranet
            case '4': // mpls_avanzado_intranet_varios_puntos
            case '5': // MPLS Avanzado Intranet con Backup de Ultima Milla - NDS 2
            case '6': // MPLS Avanzado Intranet con Backup de Ultima Milla y Router - NDS1
            case '7': // MPLS Avanzado Extranet
            case '8': // Backend MPLS
            case '9': // MPLS Avanzado con Componente Datacenter Claro
            case '10': // MPLS Transaccional 3G
                $tabla = 'pr_mpls';
                $columWhere = 'id_ot_padre_ori';
                break;
            /* FORMULARIO NOVEDADES */
            case '12': // Cambio de Equipos Servicio
            case '13': // Cambio de Servicio Telefonia Fija Pública Linea Basica a Linea E1
            case '14': // Cambio de Servicio Telefonia Fija Pública Linea SIP a PBX Distribuida Linea SIP
            case '22': // Cambio de Última Milla
            case '23': // Cambio de Equipo
                $tabla = 'pr_novedades';
                break;
            /* TRASLADO_EXTERNO */
            case '15': // Traslado Externo Servicio
                $tabla = 'pr_traslado_externo';
                break;
            /* TRASLADO_INTERNO */
            case '16': // Traslado Interno Servicio
                $tabla = 'pr_traslado_interno';
                break;
            /* PVX_ADMINISTRADA */
            case '17': // SOLUCIONES ADMINISTRATIVAS - COMUNICACIONES UNIFICADAS PBX ADMINISTRADA
                $tabla = 'pr_pbx_administrada';
                break;
            /* TELEFONIA FIJA */
            case '18': // Instalación Servicio Telefonia Fija PBX Distribuida Linea E1
            case '19': // Instalación Servicio Telefonia Fija PBX Distribuida Linea SIP
            case '20': // Instalación Servicio Telefonia Fija PBX Distribuida Linea SIP con Gateway de Voz
            case '21': // Instalación Telefonía Publica Básica - Internet Dedicado
                $tabla = 'pr_telefonia_fija';
                break;

            /* NN HERFANITO */
            case '11': // Adición Marquillas Aeropuerto el Dorado Opain

                break;
        }

        $query = $this->db->query("
            SELECT * FROM $tabla WHERE $columWhere = $idOtp
        ");
        return $query->row();
    }

    // trae la cantidad de otp sin enviar correo
    // Por requerimiento se quitaron de los contadores los clientes 'BANCO COLPATRIA RED MULTIBANCA COLPATRIA S.A', y 'BANCO DAVIVIENDA S.A', 'SERVIBANCA S.A.
    public function getCountPtesPorEnvio($fil) {
        $condicion = " ";
        $cond2 = '';
        if (Auth::user()->n_role_user == 'ingeniero') {
            $usuario_session = Auth::user()->k_id_user;
            $condicion = " AND `user`.k_id_user = $usuario_session ";
        } else {
            $cond2 = $this->validateActiveGroup($fil, 'AND');
        }
        $query = $this->db->query(
                "SELECT `user`.k_id_user, CONCAT(`user`.n_name_user, ' ', `user`.n_last_name_user) AS ingeniero,
                (
                    SELECT COUNT(1) FROM ot_padre otp1
                    WHERE DATEDIFF(CURDATE(), otp1.ultimo_envio_reporte) <= 6
                    AND otp1.k_id_user = `user`.k_id_user
                    AND otp1.n_nombre_cliente NOT IN ('BANCO COLPATRIA RED MULTIBANCA COLPATRIA S.A', 'BANCO DAVIVIENDA S.A', 'SERVIBANCA S.A.')
                    AND otp1.orden_trabajo != 'Caso de Seguimiento'
                    -- si solo se quiere mostrar otp que tengan oth descomentariar esta seccion
                    AND EXISTS(
                        SELECT nro_ot_onyx FROM ot_hija AS oth1
                        WHERE otp1.k_id_ot_padre = oth1.nro_ot_onyx
                    )

                ) AS menor_7,
                (
                    SELECT COUNT(1) FROM ot_padre otp2
                    WHERE DATEDIFF(CURDATE(), otp2.ultimo_envio_reporte) >= 7
                    AND DATEDIFF(CURDATE(), otp2.ultimo_envio_reporte) <= 14
                    AND otp2.k_id_user = `user`.k_id_user
                    AND otp2.n_nombre_cliente NOT IN ('BANCO COLPATRIA RED MULTIBANCA COLPATRIA S.A', 'BANCO DAVIVIENDA S.A', 'SERVIBANCA S.A.')
                    AND otp2.orden_trabajo != 'Caso de Seguimiento'
                    -- si solo se quiere mostrar otp que tengan oth descomentariar esta seccion
                    AND EXISTS(
                        SELECT nro_ot_onyx FROM ot_hija AS oth2
                        WHERE otp2.k_id_ot_padre = oth2.nro_ot_onyx
                    )

                ) AS entre_8_15,
                (
                    SELECT COUNT(1) FROM ot_padre otp3
                    WHERE DATEDIFF(CURDATE(), otp3.ultimo_envio_reporte) >= 15
                    AND DATEDIFF(CURDATE(), otp3.ultimo_envio_reporte) <= 30
                    AND otp3.k_id_user = `user`.k_id_user
                    AND otp3.n_nombre_cliente NOT IN ('BANCO COLPATRIA RED MULTIBANCA COLPATRIA S.A', 'BANCO DAVIVIENDA S.A', 'SERVIBANCA S.A.')
                    AND otp3.orden_trabajo != 'Caso de Seguimiento'
                    -- si solo se quiere mostrar otp que tengan oth descomentariar esta seccion
                    AND EXISTS(
                        SELECT nro_ot_onyx FROM ot_hija AS oth3
                        WHERE otp3.k_id_ot_padre = oth3.nro_ot_onyx
                    )

                ) AS entre_16_30,
                (
                    SELECT COUNT(1) FROM ot_padre otp4
                    WHERE DATEDIFF(CURDATE(), otp4.ultimo_envio_reporte) > 30
                    AND otp4.k_id_user = `user`.k_id_user
                    AND otp4.n_nombre_cliente NOT IN ('BANCO COLPATRIA RED MULTIBANCA COLPATRIA S.A', 'BANCO DAVIVIENDA S.A', 'SERVIBANCA S.A.')
                    AND otp4.orden_trabajo != 'Caso de Seguimiento'
                    -- si solo se quiere mostrar otp que tengan oth descomentariar esta seccion
                    AND EXISTS(
                        SELECT nro_ot_onyx FROM ot_hija AS oth4
                        WHERE otp4.k_id_ot_padre = oth4.nro_ot_onyx
                    )

                ) AS mayor_30
            FROM user
            WHERE `user`.n_role_user = 'ingeniero'
            $cond2
            -- AND `user`.n_group='GESTION OTS ESTANDAR'
            $condicion
        ");
        // echo("<pre>"); print_r($this->db->last_query()); echo("</pre>");
        return $query->result();
    }

    //consulto si existe algo en la tabla reporte info
    public function getInfoEmailReport($id) {
        $this->db->where_in('id_ot_padre', $id);
        $this->db->select(["senior", "nombre_cliente", "f_entrega_servicio", "observaciones"]);
        $query = $this->db->get('reporte_info');
        return $query->row();
    }

    //guarda la informacion del form. en la tabla reporte info
    public function saveInfoEmailDB($data) {
        $this->db->insert('reporte_info', $data);
    }

    //actualiza la inf. de la tabla reporte_info
    // ya no sirve :'v
    // public function updateInfoEmailDB($data,$ids)
    // {
    //     $this->db->where('id_ot_padre',$ids);
    //     $this->db->update('reporte_info',$data);
    // }
    // trae registro de la tabla reporte_info mediante otp
    // public function get_email_report_by_otp($otp)
    // {
    //     $query = $this->db->get_where('reporte_info', array('id_ot_padre' => $otp));
    //     return $query->row();
    // }
    //extrae la fecha de linea base si no existe en la tabla reporte_info
    public function getFechaLineaBaseEmailReport($id) {
        $this->db->where_in('id_ot_padre', $id);
        $query = $this->db->get('linea_base');
        return $query->row();
    }

    // obtiene el mayor numero de paquete de envio de la tabla reporte info para poder aumentarlo cuando se guarde o actualice
    public function getMaxPaqueteEnvío() {
        $this->db->select_max("paquete_enviados");
        $query = $this->db->get('reporte_info');
        return $query->result();
    }

    public function getLastMailSent($ids) {
        $query = $this->db->query(
                "SELECT senior,nombre_cliente,f_entrega_servicio,observaciones
         FROM reporte_info
         WHERE id_ot_padre IN ($ids)
         ORDER BY senior DESC LIMIT 1;");
        //  echo("<pre>"); print_r($query->result()); echo("</pre>");
        return $query->row();
    }

    // trae  todas las ots que tienen que enviar correo de actualizacion
    public function downloadAllReportAct() {
        $condicion = " ";
        if (Auth::user()->n_role_user == 'ingeniero') {
            $usuario_session = Auth::user()->k_id_user;
            $condicion = " AND otp.k_id_user = $usuario_session ";
        }
        $query = $this->db->query(
                "SELECT
            otp.k_id_ot_padre, otp.n_nombre_cliente, otp.orden_trabajo,
            (
                SELECT COUNT(idreporte_info) as cant FROM reporte_info where paquete_enviados >= 1 and id_ot_padre = otp.k_id_ot_padre
            ) AS MAIL_enviados,
            otp.servicio, REPLACE(otp.estado_orden_trabajo,'otp_cerrada','Cerrada') AS estado_orden_trabajo, otp.fecha_programacion,
            otp.fecha_compromiso, otp.fecha_creacion, otp.k_id_user, user.n_name_user,
            CONCAT(user.n_name_user, ' ' , user.n_last_name_user) AS ingeniero,
            otp.lista_observaciones, otp.observacion,
            DATEDIFF(curdate(), otp.ultimo_envio_reporte) AS ultimo_envio,
            CONCAT('$ ',FORMAT(oth.monto_moneda_local_arriendo + oth.monto_moneda_local_cargo_mensual,2)) AS MRC
            FROM ot_hija oth
            INNER JOIN ot_padre otp ON oth.nro_ot_onyx = otp.k_id_ot_padre
            INNER JOIN user ON otp.k_id_user = user.k_id_user
            LEFT JOIN hitos ON hitos.id_ot_padre = otp.k_id_ot_padre
            WHERE
            DATEDIFF(CURDATE(), otp.ultimo_envio_reporte) >= 0 AND
            n_nombre_cliente NOT IN ('BANCO COLPATRIA RED MULTIBANCA COLPATRIA S.A', 'BANCO DAVIVIENDA S.A', 'SERVIBANCA S.A.')
            AND otp.orden_trabajo != 'Caso de Seguimiento'
            AND user.n_group = 'GESTION OTS ESTANDAR'
            $condicion
            GROUP BY nro_ot_onyx
        ");
        return $query->result();
    }

    public function getLineaBasePerOTP($id) {
        $query = $this->db->select('fecha_cierre_ko,fecha_visita_obra_civil,
                fecha_dcoc,fecha_aprobacion_coc,
                fecha_ingenieria_detalle,
                fecha_ejecucion_obra_civil,
                fecha_configuracion,
                fecha_empalmes,
                fecha_entrega_servicio')
                ->from('linea_base')
                ->where('id_ot_padre', $id)
                ->order_by('fecha_cierre_ko', 'DESC')
                ->limit(1)
                ->get();

        // echo("<pre>"); print_r($this->db->last_query()); echo("</pre>");
        // echo("<pre>"); print_r($query->row()); echo("</pre>");
        return $query->row_array();
    }
    
    public function getInfoHitosByOtp($idOtp) {
        $query = $this->db->query("
                SELECT f_voc, n_estado_voc, n_observaciones_voc, no_aplica_voc,
                    f_eoc, estado_eoc, observaciones_eoc, no_aplica_eoc,
                    f_em, estado_em, observaciones_em, no_aplica_em,
                    f_entrega_servicio, estado_entrega_servicio, no_aplica_entrega,
                    observaciones_genrales, actividad_actual, observaciones_entrega_servicio
                FROM hitos
                WHERE id_ot_padre = $idOtp
        ");
        return $query->result();
    }
    
    //inserta en la tabla de log-hitos los hitos de una OTP
    public function saveLogHitosOtp($data) {
        $respuesta = array();
        
        date_default_timezone_set("America/Bogota");
        $data['fehca_modificacion'] = date('Y-m-d H:i:s');
        $data['usuario_sesion'] = Auth::user()->k_id_user;
        
        $query = $this->db->insert('log_hitos', $data);

        if ($this->db->affected_rows()) {
            $respuesta['response'] = 'success';
            $respuesta['msg'] = 'Se actualizo correctamente';
        } else {
            $respuesta['response'] = 'warning';
            $respuesta['msg'] = 'No se actualizó ningún campo u ocurrió un error';
            // $respuesta['msg'] = 'No se actualizó ningún campo o podido actualizar correctamente la informacion';
        }
        return $respuesta;
    }
    
    public function getInfoHitosByOtpEmail($idOtp) {
        $query = $this->db->query("
            SELECT IF(no_aplica_voc = 'aplica', f_voc, '') AS f_voc,
                IF(no_aplica_voc = 'aplica', n_estado_voc, '') AS n_estado_voc,
                IF(no_aplica_voc = 'aplica', n_observaciones_voc, '') AS n_observaciones_voc,
                IF(no_aplica_eoc = 'aplica', f_eoc, '') AS f_eoc,
                IF(no_aplica_eoc = 'aplica', estado_eoc, '') AS estado_eoc,
                IF(no_aplica_eoc = 'aplica', observaciones_eoc, '') AS observaciones_eoc,
                IF(no_aplica_em = 'aplica', f_em, '') AS f_em,
                IF(no_aplica_em = 'aplica', estado_em, '') AS estado_em,
                IF(no_aplica_em = 'aplica', observaciones_em, '') AS observaciones_em,
                IF(no_aplica_entrega = 'aplica', f_entrega_servicio, '') AS f_entrega_servicio,
                IF(no_aplica_entrega = 'aplica', estado_entrega_servicio, '') AS estado_entrega_servicio,
                IF(no_aplica_entrega = 'aplica', observaciones_entrega_servicio, '') AS observaciones_entrega_servicio,
                observaciones_genrales, actividad_actual
            FROM hitos
            WHERE id_ot_padre = $idOtp
        ");
        return $query->result();
    }

}
