<h1 id="como_vamos">¿Cómo vamos?</h1>
<div class="container_accordeon">
	<div class="fechasCargueUpdate" style="padding:1em;">
		<h4> <small> Último cargue: </small>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>
			<?php 
				date_default_timezone_set("America/Bogota");
				$f_cargue = new DateTime($last_time->fecha);
				// print_r($f_cargue->format('Y-m-d'));
				// print_r($last_time->fecha);

				if ($f_cargue->format('Y-m-d') == date('Y-m-d')) {
					$last_time->fecha = "HOY a las  " . $f_cargue->format('H:i:s');
				}
			?>
			<?= $last_time->fecha ?></strong></h4>
		<h4> <small> Última actualización: </small> <strong>
		<?php 
			date_default_timezone_set("America/Bogota");
			if ($last_time->fecha_last_update) {
				$f_update = new DateTime($last_time->fecha_last_update);
				if ($f_update->format('Y-m-d') == date('Y-m-d')) {
					$last_time->fecha_last_update = "HOY a las  " . $f_update->format('H:i:s');
				}
			}else{
				$last_time->fecha_last_update = "Sin fecha";
			}
			
		?>
		<?= $last_time->fecha_last_update ?></strong></h4>
	</div>
	<h4 align="center" class="con_semaf">Total OTP: <span id="all_otp"class="badge all">...</span> &nbsp;&nbsp;&nbsp;En tiempo: <span id="in_time_otp" class="badge in_time">...</span>&nbsp;&nbsp;&nbsp;Fuera de tiempo: <span id="out_time_otp" class="badge out_time">...</span>&nbsp;&nbsp;&nbsp;Hoy: <span id="today_otp" class="badge today">...</span></h4><br>
	<?php 
		for ($i=0; $i < count($ingenieros); $i++) { 
		    echo "<button class='accordion btn_ingeniero' data-iduser='".$ingenieros[$i]->k_id_user."'>".$ingenieros[$i]->nombre." <img src='".URL::base()."/assets/images/plus.png' class='rigth'> <a class='rigth fontsize10' target='_blank' href='".URL::base()."/OtHija/detalle/".$ingenieros[$i]->k_id_user."'><span class='glyphicon glyphicon-eye-open' title='ver detalle'></span></a> <a class='rigth fontsize10' target='_blank' href='".URL::base()."/OtHija/exportar/".$ingenieros[$i]->k_id_user."'><span class='glyphicon glyphicon-export' title='exportar a excel'></span></a>
				<span class='h4_for' aling='center' id='".$ingenieros[$i]->k_id_user."'></span>	
					

		    </button>";
		    echo "<div class='panel' ></div>";
		}
	 ?>
</div>
<br><br><br>
<h1 id="como_vamos" align="center"> Grafica actividades en proceso </h1>
<div class="cont_graf">
<canvas id="graficsTotal" width="400" height="180"></canvas>
</div>

