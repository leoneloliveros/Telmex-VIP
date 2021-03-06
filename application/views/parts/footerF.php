<!-- CIERRE CONTAINER-->
</div>
<!-- INICIO FOOTER-->
<div class="footerF">
    <p class="margenDelFooter">©1998-2018 ZTE Corporation - ZTE Colombia. All rights reserved</p>
</div>
<script>
    const get_hoy = "<?php echo date('Y-m-d'); ?>";
    var baseurl = "<?php echo URL::base(); ?>";
    const meses_anual = new Array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
    var formato_fecha = new Date();
    var fecha_actual = formato_fecha.getDate() + " de " + meses_anual[formato_fecha.getMonth()] + " de " + formato_fecha.getFullYear();
    var role_session = "<?php echo Auth::user()->n_role_user ?>";
    var id_session = "<?php echo Auth::user()->k_id_user ?>";
</script>


<script src="<?= URL::to("assets/js/utils/helper.js?v=" . validarEnProduccion()) ?>"></script>

<?php if ($this->uri->segment(1) == 'cargarOts' || $this->uri->segment(2) == 'view_load_graphics'): ?>
    <!-- **********************************************VISTA EDITAR OTS *********************************************-->
    <script src="<?= URL::to("assets/js/utils/app.global.js?v=1.2") ?>" type="text/javascript"></script>
    <script src="<?= URL::to("assets/js/utils/app.dom.js?v=" . validarEnProduccion()) ?>" type="text/javascript"></script>
<?php endif ?>

<?php if ($this->uri->segment(1) == 'editarOts' || $this->uri->segment(1) == 'status_restore' || $this->uri->segment(1) == 'type_restore'): ?>
    <!-- **********************************************VISTA EDITAR OTS *********************************************-->
    <script src="<?= URL::to('assets/plugins/datatables/DataTables-1.10.16/js/jquery.dataTables.min.js') ?>"></script>
    <script src="<?= URL::to('assets/plugins/datatables/js/dataTables.bootstrap.js?v=1.0') ?>"></script>
    <script src="<?= URL::to("assets/js/modules/moduleOts.js?v=" . validarEnProduccion()) ?>"></script>

<?php endif ?>

<?php if ($this->uri->segment(1) == 'generarMarcaciones'): ?>
    <!-- **********************************************GENERAR MARCACIONES********************************************-->
    <script src="<?= URL::to("assets/js/utils/app.global.js?v=1.2") ?>" type="text/javascript"></script>
    <script src="<?= URL::to("assets/js/utils/app.dom.js?v=" . validarEnProduccion()) ?>" type="text/javascript"></script>
    <script type="text/javascript" src="<?= URL::to("assets/js/modules/markings.js?v=" . validarEnProduccion()) ?>"></script>

<?php endif ?>

<?php if ($this->uri->segment(1) == 'User'): ?>
    <script src="<?= URL::to('assets/plugins/datatables/DataTables-1.10.16/js/jquery.dataTables.min.js') ?>"></script>
    <script src="<?= URL::to('assets/plugins/datatables/js/dataTables.bootstrap.js?v=1.0') ?>"></script>

<?php endif ?>

<?php if ($this->uri->segment(1) == 'paginaPrincipal'): ?>
    <script src="<?= URL::to('assets/plugins/datatables/DataTables-1.10.16/js/jquery.dataTables.min.js') ?>"></script>
    <script src="<?= URL::to('assets/plugins/datatables/js/dataTables.bootstrap.js?v=1.0') ?>"></script>
    <script src="<?= URL::to('assets/plugins/charjs/chart.min.js'); ?>"></script>
    <script src="<?= URL::to('assets/js/modules/principal.js?v=' . validarEnProduccion()) ?>"></script>

<?php endif ?>

<?php if ($this->uri->segment(1) == 'type_restore' || $this->uri->segment(1) == 'Type'): ?>
    <!-- ***********************************REPARAR TIPOS Y ESTADOS*****************************************-->
    <script src="<?= URL::to('assets/plugins/datatables/DataTables-1.10.16/js/jquery.dataTables.min.js') ?>"></script>
    <script src="<?= URL::to('assets/plugins/datatables/js/dataTables.bootstrap.js?v=1.0') ?>"></script>
    <script src="<?= URL::to('assets/js/modules/type_restore.js?v=' . validarEnProduccion()) ?>"></script>
    <script src="<?= URL::to('assets/plugins/bootstrap/js/bootstrap-selet.min.js') ?>"></script>
<?php endif ?>

<?php if ($this->uri->segment(1) == 'status_restore' || $this->uri->segment(1) == 'Status'): ?>
    <script src="<?= URL::to('assets/plugins/datatables/DataTables-1.10.16/js/jquery.dataTables.min.js') ?>"></script>
    <script src="<?= URL::to('assets/plugins/datatables/js/dataTables.bootstrap.js?v=1.0') ?>"></script>
    <script src="<?= URL::to('assets/js/modules/status_restore.js?v=' . validarEnProduccion()) ?>"></script>
<?php endif ?>

<?php if ($this->uri->segment(1) == 'OTP' || $this->uri->segment(2) == 'loginUser'): ?>
    <!-- ***********************************JS PARA ACORDEON OT PADRE*****************************************-->
    <script src="<?= URL::to('assets/plugins/charjs/chart.min.js'); ?>"></script>
    <script src="<?= URL::to('assets/js/modules/acordeon_otp.js?v=' . validarEnProduccion()) ?>"></script>
<?php endif ?>

<?php if ($this->uri->segment(1) == 'managementOtp'): ?>
    <!-- **********************************************VISTA OTPADRE *********************************************-->
    <script src="<?= URL::to('assets/plugins/datatables/DataTables-1.10.16/js/jquery.dataTables.min.js') ?>"></script>
    <script src="<?= URL::to('assets/plugins/datatables/js/dataTables.bootstrap.js?v=1.0') ?>"></script>

    <script type="text/javascript" src="<?= URL::to('assets/plugins/bootstrap/js/prettify.min.js') ?>"></script>
    <script type="text/javascript" src="<?= URL::to('assets/plugins/bootstrap/js/bootstrap-multiselect.js') ?>"></script>
    <script type="text/javascript" src="<?= URL::to('assets/plugins/selectize/dist/js/standalone/selectize.js') ?>"></script>

    <script src="<?= URL::to("assets/js/modules/setForms.js?v=" . validarEnProduccion()) ?>"></script>
    <script src="<?= URL::to("assets/js/modules/formulario.js?v=" . validarEnProduccion()) ?>"></script>
    <script src="<?= URL::to("assets/js/modules/moduleOtpadre.js?v=" . validarEnProduccion()) ?>"></script>

<?php endif ?>

<?php if ($this->uri->segment(1) == 'Cierre_ots'): ?>
    <!-- **********************************************VISTA CIERRE OTS *********************************************-->
    <script src="<?= URL::to('assets/plugins/datatables/DataTables-1.10.16/js/jquery.dataTables.min.js') ?>"></script>
    <script src="<?= URL::to('assets/plugins/datatables/js/dataTables.bootstrap.js?v=1.0') ?>"></script>
    <script src="<?= URL::to("assets/js/modules/cierre_ots.js?v=" . validarEnProduccion()) ?>"></script>

<?php endif ?>

<?php if ($this->uri->segment(1) == 'Sede' && $this->uri->segment(2) != 'otps_sede'): ?>
    <!-- **********************************************VISTA FACTURACION OTS *********************************************-->
    <script src="<?= URL::to('assets/plugins/datatables/DataTables-1.10.16/js/jquery.dataTables.min.js') ?>"></script>
    <script src="<?= URL::to('assets/plugins/datatables/js/dataTables.bootstrap.js?v=1.0') ?>"></script>


    <script src="<?= URL::to('assets/plugins/multiselect/jquery.multi-select.js?v=1.0') ?>"></script>
    <script src="<?= URL::to('assets/plugins/multiselect/jquery.quicksearch.js?v=1.0') ?>"></script>



    <script src="<?= URL::to("assets/js/modules/cambiosControl.js?v=" . time()) ?>"></script>
<?php endif ?>




<?php if ($this->uri->segment(1) == 'creacionoth'): ?>
    <!-- **********************************************VISTA FACTURACION OTS *********************************************-->

    <script src="<?= URL::to('assets/plugins/datatables/DataTables-1.10.16/js/jquery.dataTables.min.js') ?>"></script>
    <script src="<?= URL::to('assets/plugins/datatables/js/dataTables.bootstrap.js?v=1.0') ?>"></script>
    <script src="<?= URL::to("assets/js/modules/crud_oth.js?v=" . time()) ?>"></script>
    <!-- <script src="https://code.jquery.com/jquery-3.3.1.js"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script> -->

<?php endif ?>

<?php if ($this->uri->segment(2) == 'otps_sede'): ?>
    <script src="<?= URL::to('assets/plugins/datatables/DataTables-1.10.16/js/jquery.dataTables.min.js') ?>"></script>
    <script src="<?= URL::to('assets/plugins/datatables/js/dataTables.bootstrap.js?v=1.0') ?>"></script>
    <script src="<?= URL::to("assets/js/modules/sede_detail.js?v=" . time()) ?>"></script>
<?php endif ?>

<!-- ***********************faber*********************************** -->
<?php if ($this->uri->segment(1) == 'editarOts' || $this->uri->segment(1) == 'paginaPrincipal' || $this->uri->segment(1) == 'User' || $this->uri->segment(1) == 'managementOtp' || $this->uri->segment(1) == 'Cierre_ots'  || $this->uri->segment(1) == 'Sede'): ?>
    <!-- <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script> -->

    <script src="<?= URL::to('assets/plugins/datatables/js/dataTables.buttons.min.js') ?>"></script>
    <!-- <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script> -->
    <script src="<?= URL::to('assets/plugins/datatables/js/jszip.min.js') ?>"></script>
    <!-- <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script> -->
    <script src="<?= URL::to('assets/plugins/datatables/js/pdfmake.min.js') ?>"></script>
    <!-- <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script> -->
    <script src="<?= URL::to('assets/plugins/datatables/js/vfs_fonts.js') ?>"></script>
    <!-- <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.html5.min.js"></script> -->
    <script src="<?= URL::to('assets/plugins/datatables/js/buttons.html5.min.js') ?>"></script>
    <!-- <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.print.min.js"></script> -->
    <script src="<?= URL::to('assets/plugins/datatables/js/buttons.print.min.js') ?>"></script>
    <!-- <script type="text/javascript" src="https://cdn.datatables.net/select/1.2.7/js/dataTables.select.min.js"></script> -->
    <script src="<?= URL::to('assets/plugins/datatables/js/dataTables.select.min.js') ?>"></script>
    <!-- COLVIs PARA MOSTRAR U OCULTAR COLUMNAS -->
    <!-- <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.colVis.min.js"></script> -->
    <script src="<?= URL::to('assets/plugins/datatables/js/buttons.colVis.min.js') ?>"></script>
    <!-- ***********************faber*********************************** -->
<?php endif ?>

<?php if ($this->uri->segment(1) == 'ReporteActualizacion'): ?>
    <!-- ***********************************REPORTE ACTUALIZACIONES *****************************************-->
    <script src="<?= URL::to('assets/plugins/datatables/DataTables-1.10.16/js/jquery.dataTables.min.js') ?>"></script>
    <!-- <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script> -->
    <script src="<?= URL::to('assets/plugins/datatables/js/dataTables.bootstrap.js?v=1.0') ?>"></script>
    <script src="<?= URL::to('assets/js/modules/reporteActualizacion.js?v=' . validarEnProduccion()) ?>"></script>
<?php endif ?>

<?php if ($this->uri->segment(1) == 'OtHija'): ?>
    <script src="<?= URL::to('assets/plugins/datatables/DataTables-1.10.16/js/jquery.dataTables.min.js') ?>"></script>
    <script src="<?= URL::to('assets/plugins/datatables/js/dataTables.bootstrap.js?v=1.0') ?>"></script>
    <script src="<?= URL::to('assets/js/modules/detalle_otp.js?v=' . validarEnProduccion()) ?>"></script>
<?php endif ?>

<?php if ($this->uri->segment(1) == 'facturacion'): ?>
    <!-- **********************************************VISTA FACTURACION OTS *********************************************-->
    <script src="<?= URL::to('assets/plugins/datatables/DataTables-1.10.16/js/jquery.dataTables.min.js') ?>"></script>
    <script src="<?= URL::to('assets/plugins/datatables/js/dataTables.bootstrap.js?v=1.0') ?>"></script>
    <script src="<?= URL::to("assets/js/modules/facturacion_ots.js?v=" . validarEnProduccion()) ?>"></script>
<?php endif ?>

<?php if ($this->uri->segment(1) == 'noCerradasZolid'): ?>
    <script src="<?= URL::to('assets/plugins/datatables/DataTables-1.10.16/js/jquery.dataTables.min.js') ?>"></script>
    <script src="<?= URL::to('assets/plugins/datatables/js/dataTables.bootstrap.js?v=1.0') ?>"></script>
    <script type="text/javascript" src="<?= URL::to('assets/plugins/bootstrap/js/prettify.min.js') ?>"></script>
    <script type="text/javascript" src="<?= URL::to('assets/plugins/bootstrap/js/bootstrap-multiselect.js') ?>"></script>
    <script type="text/javascript" src="<?= URL::to('assets/plugins/selectize/dist/js/standalone/selectize.js') ?>"></script>
    <script src="<?= URL::to('assets/plugins/datatables/js/dataTables.buttons.min.js') ?>"></script>
    <script src="<?= URL::to('assets/plugins/datatables/js/jszip.min.js') ?>"></script>
    <script src="<?= URL::to('assets/plugins/datatables/js/pdfmake.min.js') ?>"></script>
    <script src="<?= URL::to('assets/plugins/datatables/js/vfs_fonts.js') ?>"></script>
    <script src="<?= URL::to('assets/plugins/datatables/js/buttons.html5.min.js') ?>"></script>
    <script src="<?= URL::to('assets/plugins/datatables/js/buttons.print.min.js') ?>"></script>
    <script src="<?= URL::to('assets/plugins/datatables/js/dataTables.select.min.js') ?>"></script>
    <script src="<?= URL::to('assets/plugins/datatables/js/buttons.colVis.min.js') ?>"></script>

    <script src="<?= URL::to("assets/js/modules/cerradas_zolid.js?v=" . validarEnProduccion()) ?>"></script>

<?php endif ?>

<?php if ($this->uri->segment(1) == 'Graphics' && $this->uri->segment(2) == 'view_load_graphics'): ?>
    <script src="<?= URL::to("assets/js/modules/graficas/custom-file-input.js?v=" . validarEnProduccion()) ?>"></script>
<?php endif ?>

<?php if ($this->uri->segment(1) == 'Graphics' && $this->uri->segment(2) == 'view_graphics'): ?>
    <script src="<?= URL::to("assets/js/modules/graficas/view_graphics_efectividad.js?v=" . validarEnProduccion()) ?>"></script>
<?php endif ?>

<?php if ($this->uri->segment(1) == 'reportes'): ?>

    <script src="<?= URL::to('assets/plugins/datatables/DataTables-1.10.16/js/jquery.dataTables.min.js') ?>"></script>
    <script src="<?= URL::to('assets/plugins/datatables/js/dataTables.bootstrap.js?v=1.0') ?>"></script>

    <script src="<?= URL::to('assets/plugins/datatables/js/dataTables.buttons.min.js') ?>"></script>
    <!-- <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script> -->
    <script src="<?= URL::to('assets/plugins/datatables/js/jszip.min.js') ?>"></script>
    <!-- <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script> -->
    <script src="<?= URL::to('assets/plugins/datatables/js/pdfmake.min.js') ?>"></script>
    <!-- <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script> -->
    <script src="<?= URL::to('assets/plugins/datatables/js/vfs_fonts.js') ?>"></script>
    <!-- <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.html5.min.js"></script> -->
    <script src="<?= URL::to('assets/plugins/datatables/js/buttons.html5.min.js') ?>"></script>
    <!-- <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.print.min.js"></script> -->
    <script src="<?= URL::to('assets/plugins/datatables/js/buttons.print.min.js') ?>"></script>
    <!-- <script type="text/javascript" src="https://cdn.datatables.net/select/1.2.7/js/dataTables.select.min.js"></script> -->
    <script src="<?= URL::to('assets/plugins/datatables/js/dataTables.select.min.js') ?>"></script>
    <!-- COLVIs PARA MOSTRAR U OCULTAR COLUMNAS -->
    <!-- <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.colVis.min.js"></script> -->
    <script src="<?= URL::to('assets/plugins/datatables/js/buttons.colVis.min.js') ?>"></script>

    <script src="<?= URL::to("assets/js/generateRepor.js?v=" . validarEnProduccion()) ?>"></script>

<?php endif ?>

<?php if ($this->uri->segment(1) == 'reporteTiemposKo'): ?>
    <script src="<?= URL::to('assets/plugins/datatables/DataTables-1.10.16/js/jquery.dataTables.min.js') ?>"></script>
    <script src="<?= URL::to('assets/plugins/datatables/js/dataTables.bootstrap.js?v=1.0') ?>"></script>

    <script src="<?= URL::to('assets/plugins/hightchart/code/highcharts.js') ?>"></script>
    <script src="<?= URL::to('assets/plugins/hightchart/code/highcharts-3d.js') ?>"></script>
    <script src="<?= URL::to('assets/plugins/hightchart/code/modules/exporting.js') ?>"></script>
    <script src="<?= URL::to('assets/plugins/hightchart/code/modules/export-data.js') ?>"></script>

    <script src="<?= URL::to("assets/js/modules/reporteTiemposKo.js?v=" . validarEnProduccion()) ?>"></script>
    <script src="<?= URL::to('assets/plugins/hightchart/code/themes/grid-light.js') ?>"></script>
    <script src="<?= URL::to("assets/plugins/sweetalert2/sweetalert2.all.js"); ?> "></script>


<?php endif ?>

<?php if ($this->uri->segment(1) == 'reporteTiemposKo' && $this->uri->segment(2) == 'viewAssociatesOTs'): ?>
<script src="<?= URL::to('assets/plugins/datatables/DataTables-1.10.16/js/jquery.dataTables.min.js') ?>"></script>
    <script src="<?= URL::to('assets/plugins/datatables/js/dataTables.bootstrap.js?v=1.0') ?>"></script>

    <script src="<?= URL::to('assets/plugins/datatables/js/dataTables.buttons.min.js') ?>"></script>
    <!-- PARA CARGAR BOTON DE IMPORTAR A UN EXCEL -->
    <script src="<?= URL::to('assets/plugins/datatables/js/jszip.min.js') ?>"></script>
    <!-- ========================= -->
    <script src="<?= URL::to('assets/plugins/datatables/js/buttons.html5.min.js') ?>"></script>
    <script src="<?= URL::to('assets/plugins/datatables/js/buttons.print.min.js') ?>"></script>
    <script src="<?= URL::to('assets/plugins/datatables/js/dataTables.select.min.js') ?>"></script>
    <script src="<?= URL::to('assets/plugins/datatables/js/buttons.colVis.min.js') ?>"></script>

    <script src="<?= URL::to("assets/js/oths_asc.js?v=" . validarEnProduccion()) ?> "></script>
<?php endif ?>

<script src="<?= URL::to('assets/plugins/select2/select2.js') ?>"></script>

</body>
</html>
