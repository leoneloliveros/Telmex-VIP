<style type="text/css">
    .amarillito {
        background: #f8d94f8f;
    }
    .rojito {
        background: #ff4a0157 ;
    }
</style>

<h2>Reporte Tiempos Kick Off</h2>
<hr>
<div class="text-center m-b-21">
    <section class="form-inline">
        <div class="form-group">
            <label for="f_inicio">Fecha inicio</label>
            <input type="date" class="form-control" id="f_inicio" placeholder="Fecha inicio" value="<?= $f_inicio ?>">
        </div>
        <div class="form-group">
            <label for="f_final">Fecha final</label>
            <input type="date" class="form-control" id="f_final" placeholder="Fecha final" value="<?= $f_final ?>">
        </div>
        <button id="btnGenerarReporte" class="btn btn-success">Generar Reporte</button>
    </section>
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

    </tbody>
</table>


<br>
<br>
<br>






<div id="containerGraphics"></div>