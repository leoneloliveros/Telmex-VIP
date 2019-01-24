<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dao_reportes_model extends CI_Model {
    protected $session;

    public function getInfoReportAct()
    {
        $query = $this->db->query('SELECT rf.id_ot_padre,rf.contador_reportes,rf.last_f_envio, CONCAT(u.n_name_user," ",u.n_last_name_user) AS enviador  FROM reporte_info rf  INNER JOIN user u ON rf.last_sender = u.k_id_user;');

        return $query->result();
    }
    
    public function getInfoReportInit()
    {
        $query = $this->db->query('SELECT lc.k_id_ot_padre,lc.id_orden_trabajo_hija,lc.usuario_sesion,lc.nombre_cliente,lc.servicio,lc.fecha,CONCAT(u.n_name_user," ",u.n_last_name_user) as Nombres FROM log_correo lc INNER JOIN user u ON lc.usuario_sesion = u.k_id_user;');
        return $query->result();
    }
}


?>