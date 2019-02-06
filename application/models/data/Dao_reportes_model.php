<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dao_reportes_model extends CI_Model {
    protected $session;

    
    public function getInfoReportInit()
    {
        $query = $this->db->query('SELECT lc.k_id_ot_padre,lc.id_orden_trabajo_hija,lc.nombre_cliente,lc.servicio,lc.fecha,CONCAT(u.n_name_user," ",u.n_last_name_user) as Nombres, lc.campo2 FROM log_correo lc INNER JOIN user u ON lc.usuario_sesion = u.k_id_user;');

        return $query->result();
    }

    public function getInfoReportAct()
    {
        $query = $this->db->query(
            'SELECT 
                ri.idreporte_info,
                (
                    SELECT COUNT(id_ot_padre) FROM reporte_info
                    WHERE id_ot_padre = ri.id_ot_padre
                ) AS mail_enviados, 
                ri.id_ot_padre, 
                ri.last_f_envio, 
                ri.nombre_cliente, 
                CONCAT(u.n_name_user," ",u.n_last_name_user) as last_sender
                -- u.n_name_user,
                -- u.n_last_name_user
                FROM reporte_info ri
                LEFT JOIN user u ON ri.last_sender = u.k_id_user
                WHERE ri.idreporte_info IN (SELECT MAX(idreporte_info) FROM reporte_info GROUP BY id_ot_padre) 
                GROUP BY ri.id_ot_padre
                ORDER BY ri.idreporte_info DESC;
        ');
        return $query->result();
    }
}

?>