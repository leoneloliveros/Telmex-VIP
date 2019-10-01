<?php defined('BASEPATH') OR exit('No direct script access allowed');

  /**
  * CIgniter DataTables
  * CodeIgniter library for Datatables server-side processing / AJAX, easy to use :3
  *
  * @package    CodeIgniter
  * @subpackage libraries
  * @version    1.5
  *
  * @author     Izal Fathoni (izal.fat23@gmail.com)
  * @link 		https://github.com/nacasha/CIgniter-Datatables
  */
class DatatablesBuilder
{
    private $initComplete = 'function(){}';
    private $script = "";
    private $delimitador = "";
    private $order_by = "";
    private $query = "";
    private $CI;
    private $total = 10000;
    private $searchable 	= array();
    private $style 			= '';
	   private $connection 	= 'local';

	private $dt_options		= array(
		'searchDelay' 	=> '500',
        'autoWidth' 	=> 'true'
	);
	private $ax_options 	= '';

    /**
     * Load the necessary library from codeigniter and caching the query
     * We use Codeigniter Active Record to generate query
     */
    public function __construct()
    {
        $this->CI =& get_instance();

        $this->_db = $this->CI->load->database($this->connection, TRUE);
        $this->CI->load->helper('url');
        $this->CI->load->library('table');

        $this->_db->start_cache();
    }

    public function __destruct()
    {
        $this->_db->stop_cache();
        $this->_db->flush_cache();
    }

    /**
     * Select column want to fetch from database
     *
     * @param  string
     * @return object
     */
    public function select($columns)
    {
        $this->_db->select($columns);

        $this->searchable = $columns;
        return $this;
    }

    public function query($query,$columns)
    {
      // $this->total = $this->_db->query($query)->num_rows();
      $this->query = $query;
      $this->searchable = $columns;
      return $this;
    }
    public function from($table)
    {
        $this->_db->from($table);

        $this->table = $table;
        return $this->_db;
    }

    public function style($data)
    {
        foreach ($data as $option => $value) {
            $this->style .= "$option=\"$value\"";
        }

        return $this;
    }

    /**
     * Set heading for the table
     *
     * @param  string $label    heading label
     * @param  string $source   column names
     * @param  method $function formatting the output
     * @return object
     */
    public function column($label, $source, $function = null)
    {
        $this->table_heading[] 		= $label;
        $this->columns[] 			= array($label, $source, $function);

        return $this;
    }

    /**
     * Initialize Datatables
     */
    public function init($dt_name)
    {
        if (isset($_REQUEST['dt_name'])) {
            if ($_REQUEST['dt_name'] == $dt_name) {
                if(isset($_REQUEST['draw']) && isset($_REQUEST['length']) && isset($_REQUEST['start']))
                {
                    $this->json();
                    exit;
                }
            }
        }
    }

    /**
     * Set searchable columns from table
     *
     * @param  string $data columns name
     * @return object
     */
    public function searchable($data)
    {
        $this->searchable = $data;
        return $this;
    }

    public function script($data)
    {
        $this->script = $data;
        return $this;
    }

    public function delimitador($data)
    {
        $this->delimitador = $data;
        return $this;
    }

    public function order($data)
    {
        $this->order_by = $data;
        return $this;
    }

    public function initComplete($data)
    {
        $this->initComplete = $data;
        return $this;
    }



    /**
     *	Add options to datatables jquery
     *
     * @param array / string 	$option options name
     * @param string 			$value  value
     */
    public function set_options($option, $value = null)
    {
		if ($option == 'ajax.data') {
			$this->ax_options .= $value;
		} else {
			if(is_array($option)) {
				foreach ($option as $opt => $value) {
					$this->dt_options[$opt] = $value;
				}
			} else {
				$this->dt_options[$option] = $value;
			}
		}

        return $this;
	}

	/**
     * Generate the datatables table (lol)
     *
     * @return html table
     */
    public function generate($id)
    {
		$this->CI->table->set_template(array(
            'table_open' => "<table id=\"$id\" $this->style>"
        ));
        $this->CI->table->set_heading($this->table_heading);

        echo $this->CI->table->generate();
    }

    /**
     * Jquery for datatables
     *
     * @return javascript
     */
    public function jquery($id)
    {
		$dt_options	= '';
		$ax_options = $this->ax_options;

		foreach ($this->dt_options as $opt => $value) {
			$dt_options .= "$opt: $value, \n";
		}

        $initComplete = $this->initComplete;

		$output = "
            <script type=\"text/javascript\" defer=\"defer\">
                function createDatatable() {
                    $('#{$id} thead').prepend('<tr id=\"searchZone\"></tr>');
                    $('#{$id} thead th').clone().appendTo( '#searchZone' );
                    var count = 0;
                    $('#searchZone th').each( function () {
                        $(this).html( '<input type=\"text\" class=\"input_search {$id}_search_'+count+'\" id=\"'+count+'\" placeholder=\"Buscar..\" />' );
                        count++;
                    } );
                    erTable_{$id} = $(\"#{$id}\").DataTable({
                        serverSide: true,
                        \"ordering\": true,
                        {$dt_options}
                        ajax: {
                            url: \"". site_url(uri_string()) ."\",
                            type: \"POST\",
                            data: function (d, dt) {
                                d.dt_name = \"{$id}\"
                                {$ax_options}
                            }
                        }
                    });
                    $( '.input_search' ).on( 'keypress', function (e) {
                        console.log(e.keyCode);
                        if (e.keyCode == 13) {
                            var id = this.id;
                            erTable_{$id}
                            .column(id).search(this.value)
                            .draw();
                        }
                    } );
                };

                createDatatable();
            </script>";
        $script = $this->script;
        echo $script.$output;
    }

    /**
     * Generate JSON for datatables
     *
     * @return json
     */
    public function json()
    {
        $draw		= $_REQUEST['draw'];
        $length		= $_REQUEST['length'];
        $start		= $_REQUEST['start'];
        $order_by	= $_REQUEST['order'][0]['column'];
        // $order_by	= $this->order_by;
        $order_dir	= $_REQUEST['order'][0]['dir'];
        $search		= $_REQUEST['search']['value'];
        $output['data'] 	= array();

        if($this->searchable == '*') {
            $field = $this->_db->list_fields($this->table);
            $this->searchable = implode("*", $field);
		}

        $column = explode("*", $this->searchable);
		$this->searchable = array();

        foreach($column as $key => $col) {
            $col = strtolower($col);
            $col = strstr($col, ' as ', true) ?: $col;
            $this->searchable[] = $col;
		}

    $delimitador = "";
    for($i=0; $i< count($this->searchable);$i++){
        if ($_REQUEST['columns'][$i]['search']['value'] != "") {
            $query = $this->query;
             $this->query = substr($query, 0, -1);
            if($delimitador=="" && $this->delimitador ==""){
                $delimitador = "WHERE";
            }
            else{
                $delimitador = "AND";
            }
            $this->query .= " ".$delimitador." ".$this->searchable[$i]." LIKE '%".$_REQUEST['columns'][$i]['search']['value']."%' ";
        }
    }


    if($search != "") {
        $query = $this->query;
        $this->query = substr($query, 0, -1);
        for($i=0; $i< count($this->searchable);$i++){
            if($delimitador=="" && $this->delimitador ==""){
                $delimitador = "WHERE";
            }
            else{
                $delimitador = "AND";
            }
            if($i==0){
              $this->query .= " ".$delimitador." (".$this->searchable[$i]." LIKE '%$search%' ";
            } else {
              if ($i == count($this->searchable) -1) {
                $this->query .= " OR ".$this->searchable[$i]." LIKE '%$search%') ";
              }else {
                $this->query .= " OR ".$this->searchable[$i]." LIKE '%$search%' ";
              }
            }
        }
    }

        /** ---------------------------------------------------------------------- */
        /** Count records in database */
        /** ---------------------------------------------------------------------- */
        // $query = $this->query;
        //
        // $total  = $this->_db->query($query)->result_array();

        // $total  = 25800;
        // //
        // // $total = $this->_db->count_all_results();
        //
        // $output['query_count'] 	= $this->_db->last_query();
        // $output['recordsTotal'] = $output['recordsFiltered'] = $total;

        /** ---------------------------------------------------------------------- */
        /** Generate JSON */
		/** ---------------------------------------------------------------------- */

		if ($length != -1) {
            $query = $this->query;
            $this->query = substr($query, 0, -1);
//            echo '<pre>';var_dump($this->columns[0][1]);echo '</pre>';exit();
            if (!empty($this->columns[8])) {
                $this->query .= " order by ".$this->columns[8][1]. " ". $order_dir . " "; //Cambiar el uno por la fila de la tabla que haga falta
            } else {
                $this->query .= " order by ".$this->columns[0][1]. " ". $order_dir . " "; //Cambiar el uno por la fila de la tabla que haga falta
            }
           
            $this->query .= " LIMIT $start, $length ;";
		}

//        echo '<pre>';var_dump($this->query);echo '</pre>';exit();

        $query = $this->query;
        // print_r( $query);
        // exit();
        $output['query'] 	=  $query;
        $result  = $this->_db->query($query)->result_array();
        foreach ($result as $row) {
            $arr = array();
            foreach ($this->columns as $key => $column) {
//                echo '<pre>';var_dump($column);echo '</pre>';
                $row_output = $row[$column[1]];
                if(isset($this->columns[$key][2])){
                    $row_output = call_user_func_array($this->columns[$key][2], array($row_output, $row));
                }
                $arr[] = $row_output;
            }
            $output['data'][] = $arr;
        }

        echo json_encode($output);
    }

}
