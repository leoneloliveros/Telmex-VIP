<link href="<?= URL::to('assets/css/inputFile.css?v=' . validarEnProduccion()) ?>" rel="stylesheet" />

<!--<div class="container divSelectArchivo">
    <div class="form-group">
        <label for="cantArchivos" class="labelArchivo">Cantidad de archivos a subir</label>
        <select class="form-control" id="cantArchivos">
        <option>1</option>
        <option>2</option>
    </select>
    </div>
</div>-->
<form method="post" enctype="multipart/form-data" id="formFileUpload">
    <input type="file" name="idarchivo" id="idarchivo">
    <p>Arrastra su archivo aquí o haz clic en esta área.</p>
    <button id="btnUploadFile" type="submit" class="btn btn-primary" >Upload  <span class="glyphicon glyphicon-ok"></span></button>
    <img src="assets/images/loguito.png" class="logo">
</form>
<?php $this->load->view('parts/generic/scripts'); ?>
<!-- CUSTOM SCRIPT   -->
<script>
    $(document).ready(function() {
        $('form input').change(function() {
            $('form p').text(this.files.length + " file(s) selected");
        });
    });
</script>
<script src="<?= URL::to("assets/js/modules/loadInformation.js?v=" . validarEnProduccion()) ?>" type="text/javascript"></script >