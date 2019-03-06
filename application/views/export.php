<?php 
	$nombre=(isset($registros['otp'][0]->nombre)?$registros['otp'][0]->nombre:date("Y-m-d"));

	header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
	header("Content-Disposition: attachment;filename= ".$nombre.".xls");
	header("Pragma: no-cache");
	header("Expires: 0");
	if (!empty($registros['otp'])) {
?>

<table>
	<tr>
		<td>
			<table width="100%" border="1">
				<h3 align="center" style="color: #084c6f;">Tabla de Detalles OTP</h3>
					<tr bgcolor="#084c6f" style="color: white;" align="center" >
						<td>OTP</td>
						<td>Nombre cliente</td>
						<td>Tipo OTP</td>
						<td>Servicio</td>
						<td>Estado OTP</td>
						<td>Programado</td>
						<td>Compromiso</td>
						<td>Creacion</td>
						<td>Ciudad</td>
						<td>Recurrente</td>
						<td>Lista observaciones</td>
						<td>Observacion</td>
					</tr>
				<?php 
				//$registros
				for ($i=0; $i < count($registros['otp']); $i++) { 
					echo "<tr>";
						echo "<td>".elimina_acentos($registros['otp'][$i]->k_id_ot_padre)."</td>";
						echo "<td>".elimina_acentos($registros['otp'][$i]->n_nombre_cliente)."</td>";
						echo "<td>".elimina_acentos($registros['otp'][$i]->orden_trabajo)."</td>";
						echo "<td>".elimina_acentos($registros['otp'][$i]->servicio)."</td>";
						echo "<td>".elimina_acentos($registros['otp'][$i]->estado_orden_trabajo)."</td>";
						echo "<td>".elimina_acentos($registros['otp'][$i]->fecha_programacion)."</td>";
						echo "<td>".elimina_acentos($registros['otp'][$i]->fecha_compromiso)."</td>";
						echo "<td>".elimina_acentos($registros['otp'][$i]->fecha_creacion)."</td>";
						echo "<td>".elimina_acentos($registros['otp'][$i]->ciudad)."</td>";
						echo "<td>".elimina_acentos($registros['otp'][$i]->recurrente)."</td>";
						echo "<td>".elimina_acentos($registros['otp'][$i]->lista_observaciones)."</td>";
						echo "<td>".elimina_acentos($registros['otp'][$i]->observacion)."</td>";
					echo "</tr>";
							
				}
				?>
			</table>
		</td>
		<td>   </td>
		<td>
			<table width="100%" border="1">
				<h3 align="center" style="color: #084c6f;">Tabla de Detalles OTH</h3>
				<tr bgcolor="#084c6f" style="color: white;" align="center" >
					<td>OTH</td>
					<td>Tipo OTH</td>
					<td>Estado OTH</td>
					<td>Creacion OTH</td>
					<td>Ingeniero</td>
				</tr>
			<?php 
			//$registros
			for ($i=0; $i < count($registros['oth']); $i++) { 
				echo "<tr>";
					echo "<td>".elimina_acentos($registros['oth'][$i]->id_orden_trabajo_hija)."</td>";
					echo "<td>".elimina_acentos($registros['oth'][$i]->ot_hija)."</td>";
					echo "<td>".elimina_acentos($registros['oth'][$i]->estado_orden_trabajo_hija)."</td>";
					echo "<td>".elimina_acentos($registros['oth'][$i]->fecha_creacion_ot_hija)."</td>";
					echo "<td>".elimina_acentos($registros['oth'][$i]->nombre)."</td>";
				echo "</tr>";
						
			}
			?>
		</table>
		</td>
	</tr>
</table>

<?php

} else{?>
    
    <table>
	<tr>
		<td>
			<table width="100%" border="1">
				<h3 align="center" style="color: #084c6f;">Tabla de Detalles OTP</h3>
					<tr bgcolor="#084c6f" style="color: white;" align="center" >
						<td>OTP</td>
						<td>Nombre cliente</td>
						<td>Tipo OTP</td>
						<td>Servicio</td>
						<td>Estado OTP</td>
						<td>Programado</td>
						<td>Compromiso</td>
						<td>Creacion</td>
						<td>Ciudad</td>
						<td>Recurrente</td>
						<td>Lista observaciones</td>
						<td>Observacion</td>
					</tr>
					<tr>
						<td colspan="12">El ingeniero seleccionado no posee OTP asignadas</td>
					</tr>
			</table>
		</td>
		<td>   </td>
		<td>
			<table width="100%" border="1">
				<h3 align="center" style="color: #084c6f;">Tabla de Detalles OTH</h3>
				<tr bgcolor="#084c6f" style="color: white;" align="center" >
					<td>OTH</td>
					<td>Tipo OTH</td>
					<td>Estado OTH</td>
					<td>Creacion OTH</td>
					<td>Ingeniero</td>
				</tr>
				<tr>
					<td colspan="5">El ingeniero seleccionado no posee OTP asignadas</td>
				</tr>
		</table>
		</td>
	</tr>
</table>

<?php
}
?>
