<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Dao_log_correo_model extends CI_Model {

    protected $session;

    public function __construct() {
        // $this->load->model('dto/UserModel');
    }

    // Inserta un registro nuevo a la base de datos
    public function insert_data($data) {
        $this->db->insert('log_correo', $data);
    }

    // Obtiene datos de log_correo por id ot hija
    public function getLogMailById($id) {
        // $query = $this->db->order_by('k_id_tipo ASC, i_orden ASC');
        $query = $this->db->query("
                SELECT

                k_id_log_correo, k_id_ot_padre, id_orden_trabajo_hija, clase, destinatarios, usuario_sesion, nombre, nombre_cliente, servicio, fecha, direccion_instalacion, direccion_instalacion_des1, direccion_instalacion_des2, direccion_instalacion_des3, direccion_instalacion_des4, existente, nuevo, ancho_banda, interfaz_entrega, equipos_intalar_camp1, equipos_intalar_camp2, equipos_intalar_camp3, fecha_servicio, ingeniero1, ingeniero1_tel, ingeniero1_email, ingeniero2, ingeniero2_tel, ingeniero2_email, ingeniero3, ingeniero3_tel, ingeniero3_email, ots_nombre, ampliacion_enlaces, vista_obra_civil, envio_cotizacion_obra_civil, aprobacion_cotizacion_obra_civil, ejecucion_obra_civil, empalmes, configuracion, entrega_servicio, direccion_servicio, campo1, campo2, campo3, campo4, campo5, campo6, campo7, campo8, campo9, campo10, campo11, campo12, campo13, campo14, campo15, campo16, campo17, campo18, campo19, campo20, campo21, campo22, campo23, campo24, campo25, campo26, campo27, campo28, campo29, campo30, campo31, campo32, campo33, campo34, campo35, campo36, campo37, campo38, campo39, campo40, campo41, campo42, campo43, campo44, campo45, campo46, campo47, campo48, campo49, campo50, campo51, campo52, campo53, campo54, campo55, campo56, campo57, campo58, CONCAT(u.n_name_user, ' ', u.n_last_name_user) AS usuario_en_sesion

                FROM
                log_correo lc
                INNER JOIN user u
                ON lc.usuario_sesion = u.k_id_user
                WHERE
                lc.id_orden_trabajo_hija = $id
            ");
        return $query->result();
    }

    //obtengo la info de los Mails enviados
    public function getInitReportMailsByOTP($oths)
    {
        $query = $this->db->query(
            "SELECT
                k_id_log_correo, k_id_ot_padre, id_orden_trabajo_hija, clase, destinatarios, usuario_sesion, nombre, nombre_cliente, servicio, fecha, direccion_instalacion, direccion_instalacion_des1, direccion_instalacion_des2, direccion_instalacion_des3, direccion_instalacion_des4, existente, nuevo, ancho_banda, interfaz_entrega, equipos_intalar_camp1, equipos_intalar_camp2, equipos_intalar_camp3, fecha_servicio, ingeniero1, ingeniero1_tel, ingeniero1_email, ingeniero2, ingeniero2_tel, ingeniero2_email, ingeniero3, ingeniero3_tel, ingeniero3_email, ots_nombre, ampliacion_enlaces, vista_obra_civil, envio_cotizacion_obra_civil, aprobacion_cotizacion_obra_civil, ejecucion_obra_civil, empalmes, configuracion, entrega_servicio, direccion_servicio, campo1, campo2, campo3, campo4, campo5, campo6, campo7, campo8, campo9, campo10, campo11, campo12, campo13, campo14, campo15, campo16, campo17, campo18, campo19, campo20, campo21, campo22, campo23, campo24, campo25, campo26, campo27, campo28, campo29, campo30, campo31, campo32, campo33, campo34, campo35, campo36, campo37, campo38, campo39, campo40, campo41, campo42, campo43, campo44, campo45, campo46, campo47, campo48, campo49, campo50, campo51, campo52, campo53, campo54, campo55, campo56, campo57, campo58, CONCAT(u.n_name_user, ' ', u.n_last_name_user) AS usuario_en_sesion
                FROM
                log_correo lc
                INNER JOIN user u
                ON lc.usuario_sesion = u.k_id_user
                WHERE
                lc.id_orden_trabajo_hija IN ($oths)
            "
        );
        return $query->result_array();
    }


    //obtengo la info de los Mails enviados
    public function getLogsMailsByOTP($otp)
    {
        $query = $this->db->query(
            "SELECT ri.id_ot_padre, ri.senior, ri.nombre_cliente, ri.f_entrega_servicio, ri.observaciones, CONCAT(u.n_name_user,' ',u.n_last_name_user) AS last_enviador , ri.last_f_envio, ri.paquete_enviados
            FROM reporte_info ri INNER JOIN user u ON u.k_id_user = ri.last_sender
            WHERE id_ot_padre = $otp
            ");
        return $query->result_array();
    }


    
    //obtengo la info de los correos que se enviaron juntos o sólos si es el caso
    public function getPaqueteEnviados($pe)
    {
        $this->db->select(['id_ot_padre','servicio','paquete_enviados']);
        $this->db->where("paquete_enviados",$pe);
        $query = $this->db->get("reporte_info");
        // echo("<pre>"); print_r($this->db->last_query()); echo("</pre>");
        // echo("<pre>"); print_r($query->result()); echo("</pre>");
        return array("data" => $query->result() , "cant" => $query->num_rows());
    }


}
