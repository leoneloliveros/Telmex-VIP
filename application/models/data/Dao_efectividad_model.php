<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Dao_efectividad_model extends CI_Model {

    public function __construct() {
        
    }

    // elimina todos los datos de la tabla efectividad
    public function delete_efectividad_table() {
        if ($this->db->truncate('efectividad')) {
            return 1;
        } else {
            return 0;
        }
    }

    //
    public function insert_data_efectividad($data) {
        if ($this->db->insert('efectividad', $data)) {
            return $this->db->insert_id();
        } else {
            return false;
        }
    }

    //
    public function get_estado_voc_vs_tipo_estado() {
        $query = $this->db->query("
			SELECT COUNT(efectividad) AS cant, estado_voc_primario AS nombre
                        FROM efectividad
                        WHERE fecha >= '2018-10-05' AND estado_voc_1 IS NULL
                        GROUP BY estado_voc_primario
                        ORDER BY cant DESC
		");

        return $query->result();
    }

     //
    public function get_estado_voc_vs_tipo_estado_all() {
        $query = $this->db->query("
			SELECT COUNT(efectividad) AS cant, estado_voc_primario AS nombre
                        FROM efectividad
                        GROUP BY estado_voc_primario
                        ORDER BY cant DESC
		");

        return $query->result();
    }
    
    //
    public function get_estado_voc_vs_tipo_sede() {
        $query = $this->db->query("
			SELECT COUNT(tipo_sede) AS cant, estado_voc_primario AS nombre
			FROM efectividad
			WHERE estado_voc_primario <> ''
			GROUP BY estado_voc_primario
			ORDER BY cant DESC
		");

        return $query->result();
    }

    // retorna valores de dicha columna y  fecha
    public function get_names_by_col($columna, $fecha = '0000-00-00'){
    	$query = $this->db->query("
				SELECT DISTINCT($columna) FROM efectividad WHERE 
				fecha >= '$fecha' 
				AND estado_voc_1 is NULL 

    		");
    	return $query->result_array();
    }


    // 
    public function get_cant_section_in_name($name_seccion, $value_seccion, $name_nombre, $name_value){
    	$query = $this->db->query("
			SELECT count(1) as cant FROM 
			efectividad
			where
			$name_nombre = '$name_value'
			and
			$name_seccion = '$value_seccion'
    	");
    	return $query->row();
    }

}
