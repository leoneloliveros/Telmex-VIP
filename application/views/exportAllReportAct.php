<?php
header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
header("Content-Disposition: attachment;filename= zolid " . date('Y-m-d') . ".xls");
header("Pragma: no-cache");
header("Expires: 0");
?>

<table>
    <tr>
        <td>
            <table width="100%" border="1">
                <tr bgcolor="#084c6f" style="color: white;" align="center" >
                    <td>Ot Padre</td>
                    <td>Nombre Cliente</td>
                    <td>Tipo</td>
                    <td>Servicio</td>
                    <td>Estado OT Padre</td>
                    <td>Fecha Programaci&oacute;n</td>
                    <td>Fecha Compromiso</td>
                    <td>Fecha Creaci&oacute;n</td>
                    <td>Ingeniero</td>
                    <td>Lista</td>
                    <td>Observaci&oacute;nes dejadas</td>
                    <td>Recurrente</td>
                    <td>ultimo envio</td>
                </tr>
                <?php
                //se recorren los registros para pintarlos
                foreach ($registros as $key => $value) {
//                    print_r($value->k_id_ot_padre);
                    echo "<tr>";
                    echo "<td>" . elimina_acentos($value->k_id_ot_padre) . "</td>";
                    echo "<td>" . elimina_acentos($value->n_nombre_cliente) . "</td>";
                    echo "<td>" . elimina_acentos($value->orden_trabajo) . "</td>";
                    echo "<td>" . elimina_acentos($value->servicio) . "</td>";
                    echo "<td>" . elimina_acentos($value->estado_orden_trabajo) . "</td>";
                    echo "<td>" . elimina_acentos($value->fecha_programacion) . "</td>";
                    echo "<td>" . elimina_acentos($value->fecha_compromiso) . "</td>";
                    echo "<td>" . elimina_acentos($value->fecha_creacion) . "</td>";
                    echo "<td>" . elimina_acentos($value->ingeniero) . "</td>";
                    echo "<td>" . elimina_acentos($value->lista_observaciones) . "</td>";
                    echo "<td>" . elimina_acentos($value->observacion) . "</td>";
                    echo "<td>" . elimina_acentos($value->MRC) . "</td>";
                    echo "<td>" . elimina_acentos($value->ultimo_envio) . "</td>";
                    echo "</tr>";
                }
                ?>
            </table>
        </td>
    </tr>
</table>
