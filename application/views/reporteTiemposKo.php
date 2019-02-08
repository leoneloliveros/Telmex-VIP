<?php
date_default_timezone_set("America/Bogota");
$anio_actual = date('Y');
$mes_actual = date('m');
$ultimo_dia_mes = days_in_month($mes_actual, $anio_actual);
$f_final = $anio_actual . '-' . $mes_actual . '-' . $ultimo_dia_mes;
$f_inicio = $anio_actual . '-' . $mes_actual . '-01';

function colorCelda($cantidad) {
    $color = '#f9f9f9';

    if ($cantidad == 2) {
        $color = '#ffd94f';
    }

    if ($cantidad > 2) {
        $color = '#ff4a01';
    }

    return $color;
}
?>
<h2>Reporte Tiempos Kick Off</h2>
<hr>
<div class="text-center m-b-21">
    <form class="form-inline">
        <div class="form-group">
            <label for="f_inicio">Fecha inicio</label>
            <input type="date" class="form-control" id="f_inicio" placeholder="Fecha inicio" value="<?= $f_inicio ?>">
        </div>
        <div class="form-group">
            <label for="f_final">Fecha final</label>
            <input type="date" class="form-control" id="f_final" placeholder="Fecha final" value="<?= $f_final ?>">
        </div>
    </form>
    <button id="btnGenerarReporte" class="btn btn-success m-t-15">Generar Reporte</button>
</div>

<table id="tablereportinit" class="table table-hover table-bordered table-striped dataTable_camilo" width="100%">
    <thead>
        <tr>
            <th rowspan="2">Kick Off</th>
            <th colspan="2">Cantidad</th>
            <th colspan="2">Días Min</th>
            <th colspan="2">Días Max</th>
            <th colspan="2">Días Promedio</th>
        </tr>
        <tr>
            <th>CERRADO</th>
            <th>ABIERTO</th>
            <th>CERRADO</th>
            <th>ABIERTO</th>
            <th>CERRADO</th>
            <th>ABIERTO</th>
            <th>CERRADO</th>
            <th>ABIERTO</th>
        </tr>
    </thead>
    <tbody id="bodyInfo">
        <?php
        foreach ($infoReporte as $key => $value) {
            $clase = ($key == '1') ? 'grupo-info-tiempo-ko' : 'text-right';
            $promedio_cerradas = round(($value['total_cerradas'] != 0) ? $value['dia_promedio_cerrado'] / $value['total_cerradas'] : 0, 2, PHP_ROUND_HALF_UP);
            $promedio_abiertas = round(($value['total_abiertas'] != 0) ? $value['dia_promedio_abierto'] / $value['total_abiertas'] : 0, 2, PHP_ROUND_HALF_UP);
            echo '
                <tr>
                    <td class="' . $clase . '">' . $value['ingeniero'] . '</td>
                    <td>' . $value['total_cerradas'] . '</td>
                    <td>' . $value['total_abiertas'] . '</td>
                    <td style="background:'.colorCelda($value['dia_min_cerrado']).' ">' . $value['dia_min_cerrado'] . '</td>
                    <td style="background:'.colorCelda($value['dia_min_abierto']).' ">' . $value['dia_min_abierto'] . '</td>
                    <td style="background:'.colorCelda($value['dia_max_cerrado']).' ">' . $value['dia_max_cerrado'] . '</td>
                    <td style="background:'.colorCelda($value['dia_max_abierto']).' ">' . $value['dia_max_abierto'] . '</td>
                    <td style="background:'.colorCelda($promedio_cerradas).' ">' . $promedio_cerradas . '</td>
                    <td style="background:'.colorCelda($promedio_abiertas).' ">' . $promedio_abiertas . '</td>
                </tr>
            ';
        }
        ?>
    </tbody>
</table>