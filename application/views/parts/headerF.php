<!DOCTYPE html>
<html lang="en">
    <head>
        <title><?php echo $title ?></title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?php $this->load->helper('camilo'); ?>
        <!--   ICONO PAGINA    -->
        <link rel="icon" href="<?= URL::to('assets/img/logo_zte.png'); ?>">
        <!-- STYLES HEADER FOOTER  -->
        <link rel="stylesheet" type="text/css" href="<?= URL::to('assets/css/styles_header.css?v=' . validarEnProduccion()); ?>">
        <!-- BOOTSTRAP -->
        <link rel="stylesheet" href="<?= URL::to('assets/plugins/bootstrap/css/bootstrap.min.css') ?>"/>
        <link rel="stylesheet" href="<?= URL::to('assets/plugins/font-awesome/css/font-awesome.min.css') ?>"/>

        <!-- STYLES DATATABLES CAMILO -->
        <link rel="stylesheet" type="text/css" href="<?= URL::to('assets/css/datatables_camilo.css?v=' . validarEnProduccion()); ?>">
        <link rel="stylesheet" type="text/css" href="<?= URL::to("assets/css/util.css?v=" . validarEnProduccion()) ?>">
        <!-- STYLES MODULES PRINCIPAL -->
        <!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"> -->
        <!-- STYLES  FOOTER  -->
        <link rel="stylesheet" type="text/css" href="<?= URL::to('assets/css/styles_footer.css'); ?>">

        <?php if ($this->uri->segment(1) == 'type_restore'): ?>
            <!-- ************************************ type_restore ******************************************* -->
            <link rel="stylesheet" type="text/css" href="<?= URL::to("assets/plugins/bootstrap/css/bootstrap-select.min.css") ?>">
        <?php endif ?>

        <script src="<?= URL::to("assets/plugins/jquery/jquery.min.js") ?>"></script>
        <script src="<?= URL::to("assets/plugins/bootstrap/js/bootstrap.min.js") ?>"></script>

        <?php if ($this->uri->segment(1) == 'type_restore' || $this->uri->segment(1) == 'Type'): ?>
            <!-- ************************************** type_restore ********************************************* -->
            <link rel="stylesheet" type="text/css" href="<?= URL::to("assets/plugins/bootstrap/css/bootstrap-select.min.css") ?>">
            <link rel="stylesheet" type="text/css" href="<?= URL::to("assets/plugins/sweetalert2/animate.css") ?>">
        <?php endif ?>

        <?php if ($this->uri->segment(1) == 'managementOtp' || $this->uri->segment(1) == 'Sede'): ?>
            <!-- ************************************** WORK MANAGEMENT OTP CSS ********************************************* -->
            <link rel="stylesheet" type="text/css" href="<?= URL::to("assets/css/tooltip.css") ?>">
            <link rel="stylesheet" type="text/css" href="<?= URL::to("assets/plugins/multiselect/multi-select.css") ?>">
        <?php endif ?>

        <?php if ($this->uri->segment(1) == 'managementOtp'): ?>
            <!-- ************************************** timeLine CSS ********************************************* -->
            <link rel="stylesheet" type="text/css" href="<?= URL::to("assets/css/timeLine.css?v=" . validarEnProduccion()) ?>">
            <link rel="stylesheet" type="text/css" href="<?= URL::to("assets/plugins/selectize/dist/css/selectize.bootstrap2.css?v=" . validarEnProduccion()) ?>">
            <!-- css para el formulario con tabs seccionado -->
            <link rel="stylesheet" type="text/css" href="<?= URL::to("assets/css/vertical_tabs.css?v=" . validarEnProduccion()) ?>">
            <link rel="stylesheet" type="text/css" href="<?= URL::to("assets/css/interruptor.css?v=" . validarEnProduccion()) ?>">
            <link rel="stylesheet" type="text/css" href="<?= URL::to("assets/plugins/bootstrap/css/bootstrap-multiselect.css?v=" . validarEnProduccion()) ?>">
            <link rel="stylesheet" type="text/css" href="<?= URL::to("assets/plugins/bootstrap/css/prettify.min.css?v=" . validarEnProduccion()) ?>">
            <link rel="stylesheet" type="text/css" href="<?= URL::to("assets/css/util.css?v=" . validarEnProduccion()) ?>">


        <?php endif ?>

        <link rel="stylesheet" type="text/css" href="<?= URL::to('assets/vendor/select2/select2.min.css') ?>">

        <!-- ********************************VISTA VALIDADOR IP *********************************************-->
        <?php if ($this->uri->segment(1) == 'validadorIp'): ?>
            <!-- ASSESTS 2 -->
            <link rel="stylesheet" type="text/css" href="<?= URL::to('assets/css/main.css') ?>">
            <!--===============================================================================================-->
            <link rel="stylesheet" type="text/css" href="<?= URL::to('assets/css/util.css') ?>">
            <!--===============================================================================================-->
            <link rel="stylesheet" type="text/css" href="<?= URL::to('assets/vendor/css-hamburgers/hamburgers.min.css') ?>">
            <!--===============================================================================================-->
            <link rel="stylesheet" type="text/css" href="<?= URL::to('assets/vendor/animsition/css/animsition.min.css') ?>">
            <!--==============================================================================================-->

        <?php endif ?>
        <?php if ($this->uri->segment(1) == 'managementOtp' || $this->uri->segment(1) == 'paginaPrincipal' || $this->uri->segment(1) == 'OtHija' || $this->uri->segment(1) == 'Sede') { ?>
            <link rel="stylesheet" href="<?= URL::to('assets/css/styleModalCami.css?v=' . validarEnProduccion()) ?>" />
            <link rel="stylesheet" href="<?= URL::to('assets/css/helper-class.css?v=1.0') ?>">

        <?php } ?>
        <?php if ($this->uri->segment(1) == 'OTP' || $this->uri->segment(2) == 'loginUser') { ?>
            <link rel="stylesheet" href="<?= URL::to('assets/css/style_principal_otp.css?v=' . validarEnProduccion()) ?>" />
        <?php } ?>

        <?php if ($this->uri->segment(1) == 'Graphics' && $this->uri->segment(2) == 'view_load_graphics'): ?>
            <link rel="stylesheet" href="<?= URL::to('assets/css/input_file/component.css?v=' . validarEnProduccion()) ?>" />
            <link rel="stylesheet" href="<?= URL::to('assets/css/input_file/demo.css?v=' . validarEnProduccion()) ?>" />
        <?php endif ?>





    </head>

    <body style="padding: 0;" data-base="<?= URL::base() ?>" >
        <div class="telmexVIP_header ">
            <nav class="navbar navbar-inverse" role="navigation">
                <div class="container-fluid menu_nav_header" >
                    <div class="navbar-header">
                        <a class="navbar-brand">
                            <img class="logo_header" src="<?= URL::to('assets/img/LogoZTENav.png'); ?>">
                        </a>
                    </div>

                    <ul class="nav navbar-nav menu_nav_header">


                        <?php
                        if (Auth::user()->n_project == 'Gestion') {
                            ?>
                            <?php if (Auth::user()->n_role_user == 'administrador' || Auth::user()->n_role_user == 'ingeniero'): ?>

                                <li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="#">¿Cómo vamos?<span class="caret"></span></a>
                                    <ul class="dropdown-menu">
                                        <li><a href="<?= URL::to('reporteTiemposKo') ?>">Reporte Tiempos Kick Off</a></li>
                                        <li><a href="<?= URL::to('OTP') ?>">¿Cómo vamos OTP?</a></li>
                                        <li><a href="<?= URL::to('paginaPrincipal') ?>">¿Cómo vamos OTH?</a></li>
                                    </ul>
                                </li>

                                <li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="#">Management<span class="caret"></span></a>
                                    <ul class="dropdown-menu">
                                        <li><a href="<?= URL::to('managementOtp') ?>">Work Management</a></li>
                                        <!--<li><a href="<?= URL::to('editarOts') ?>">Work Management OTH</a></li>
                                        <li><a href="<?= URL::to('ReporteActualizacion') ?>">Reporte de Actualización <span class="badge"><?php echo $cantidad['afeterEigtDays'][0]->cant ?></span></a></li>-->

                                        <?php if (Auth::user()->n_role_user == 'administrador'): ?>
                                            <li><a href="<?= URL::to('cargarOts') ?>">load information</a></li>
                                            <li><a href="<?= URL::to('Graphics/view_load_graphics') ?>">Graficas</a></li>
                                            <li><a href="<?= URL::to('noCerradasZolid') ?>">No cerradas ZOLID</a></li>
                                        <?php endif ?>
                                    </ul>
                                </li>
                            <?php endif ?>

                            <!-- que el boton restore apareza solo en administrativo y que sea OTS Hija -->
                            <?php if (Auth::user()->n_role_user == 'administrador'): ?>
                                <li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="#"><span class="fa fa-exclamation-triangle"></span> restore <span class="badge"><?php echo $cantidad['indefinidos'] + $cantidad['nulos'] ?></span></a>
                                    <ul class="dropdown-menu">
                                        <li><a href="<?= URL::to('type_restore') ?>">Type restore <span class="badge"><?php echo $cantidad['new_types'] ?></span></a></li>
                                        <li><a href="<?= URL::to('status_restore') ?>">Status restore <span class="badge"><?php echo $cantidad['new_status'] ?></span></a></li>
                                        <li><a href="<?= URL::to('creacionoth') ?>">Creacion OTS <span class="badge"></span></a></li>
                                    </ul>
                                </li>

                                <li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="#"></span> Facturación <span class="caret"></span></a>
                                    <ul class="dropdown-menu">
                                        <li><a href="<?= URL::to('Cierre_ots') ?>">Cierre </a></li>
                                        <li><a href="<?= URL::to('facturacion') ?>">Facturados </a></li>
                                    </ul>
                                </li>

                                <li><a href="<?= URL::to('reportes') ?>">Generar reportes</a></li>

                            <?php endif ?>

                            <?php if (Auth::user()->n_role_user == 'administrador' || Auth::user()->n_role_user == 'clarocc' || (Auth::user()->n_role_user == 'ingeniero' && Auth::user()->n_code_user == 'TIPO_A')): ?>
                                <li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="#">C.Cambios<span class="caret"></span></a>
                                    <ul class="dropdown-menu">
                                        <li><a href="<?= URL::to('Sede') ?>">Control de Cambios</a></li>
                                    </ul>
                                </li>
                            <?php endif ?>

                            <?php
                        }
                        if (Auth::user()->n_project == 'Implementacion') {
                            ?>
                            <li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="#">Implementación Servicios <span class="caret"></span></a>
                                <ul class="dropdown-menu tamaniomin">
                                    <li><a href="<?= URL::to('generarMarcaciones') ?>">Marcaciones</a></li>
                                    <li><a href="<?= URL::to('validadorIp') ?>">Validación IP</a></li>
                                </ul>
                            </li>
                        <?php } ?>

                        <!--  <li><a href="#">agendamiento</a></li> -->
                        <!--  <li><a href="#">facturacion</a></li> -->
                    </ul>
                    <ul class="nav navbar-nav navbar-right">
                        <li><a href="#"><span class="glyphicon glyphicon-user"></span><b> Welcome  </b> <?php echo Auth::user()->n_name_user . ' ' . Auth::user()->n_last_name_user; ?><br>
                            </a>
                        </li>
                        <li><a href="<?= URL::to('User/logout') ?>"><span class="glyphicon glyphicon-log-in"></span> Sign out</a></li>
                    </ul>
                </div>
            </nav>
        </div>
        <div class="container" style="min-height: 513px;">
  
<script src="<?= URL::to('assets/plugins/datatables/DataTables-1.10.16/js/jquery.dataTables.min.js') ?>"></script>
<script src="<?= URL::to('assets/plugins/datatables/js/dataTables.bootstrap.js?v=1.0') ?>"></script>

<script type="text/javascript" src="<?= URL::to("assets/plugins/datatables/js/dataTables.buttons.min.js") ?>"></script>
<script type="text/javascript" src="<?= URL::to("assets/plugins/datatables/js/jszip.min.js") ?>"></script>
<script type="text/javascript" src="<?= URL::to("assets/plugins/datatables/js/pdfmake.min.js") ?>"></script>
<script type="text/javascript" src="<?= URL::to("assets/plugins/datatables/js/vfs_fonts.js") ?>"></script>
<script type="text/javascript" src="<?= URL::to("assets/plugins/datatables/js/buttons.html5.min.js") ?>"></script>
<script type="text/javascript" src="<?= URL::to("assets/plugins/datatables/js/buttons.print.min.js") ?>"></script>

<script type="text/javascript" src="<?= URL::to("assets/plugins/datatables/js/dataTables.select.min.js") ?>"></script>
<script type="text/javascript" src="<?= URL::to("assets/plugins/datatables/js/dataTables.scroller.js") ?>"></script>
