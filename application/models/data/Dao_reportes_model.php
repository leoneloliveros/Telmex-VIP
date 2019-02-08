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

    //Trae la informacion del reporte de tiempos Kick Off de acuerdo a un rango de fechas
    public function getInfoReporteTiemposKo($fecha_ini, $fecha_fin){
        $query = $this->db->query("
                SELECT info.k_id_user, info.ingeniero,
                    info.k_id_ot_padre, info.id_orden_trabajo_hija, info.n_name_estado_ot, info.fecha_creacion_ot_hija,
                    info.fec_actualizacion_onyx_hija, info.ultima_fecha_modificacion,
                    habiles_rango(info.fecha_creacion_ot_hija, info.ultima_fecha_modificacion) AS dias_trascurridos # la fecha del primer parametro tiene que ser menor que la segunda
                FROM (
                        SELECT u.k_id_user, CONCAT(u.n_name_user, ' ', u.n_last_name_user) AS ingeniero,
                                otp.k_id_ot_padre, oth.id_orden_trabajo_hija, et.n_name_estado_ot, oth.fecha_creacion_ot_hija,
                                oth.fec_actualizacion_onyx_hija,
                                IF(et.n_name_estado_ot = 'Cerrada', oth.fec_actualizacion_onyx_hija, CURDATE()) AS ultima_fecha_modificacion
                        FROM user u
                        INNER JOIN ot_padre otp
                        ON otp.k_id_user = u.k_id_user
                        INNER JOIN ot_hija oth
                        ON oth.nro_ot_onyx = otp.k_id_ot_padre
                        INNER JOIN estado_ot et
                        ON et.k_id_estado_ot = oth.k_id_estado_ot
                        WHERE u.n_role_user = 'ingeniero'
                        AND u.n_group = 'GESTION OTS ESTANDAR'
                        AND et.k_id_tipo = 1
                ) info
                WHERE info.ultima_fecha_modificacion BETWEEN '$fecha_ini' AND '$fecha_fin'
                ORDER BY info.ingeniero ASC, dias_trascurridos DESC
                ");
        return $query->result();
    }
}

?>