<link rel="stylesheet" type="text/css" href="<?= URL::to('assets/css/MenuStickyLeft.css') ?>">
<!------------------------------- menu sticky para las columnas ------------------------------->
<div class="contenedor_sticky">
    <div id="btn_sticky_open" class="rotar">
        <div class="rotar">
            <i class="glyphicon glyphicon-chevron-up">columnas</i>
        </div>
    </div>
</div>

<div class="contenedor_menu_sticky" style="display: none">
    <div class="btn_cerrar_sticky" align="center">
        <i class="glyphicon glyphicon-chevron-left"></i> Cerrar
    </div>
    <div class="menu-fixed">
        <ul>
            <li class="toggle-vis" data-column="0">OTP<i class="f-r glyphicon glyphicon-eye-open"></i></li>
            <li class="toggle-vis" data-column="1">Cliente<i class="f-r glyphicon glyphicon-eye-open"></i></li>
            <li class="toggle-vis" data-column="2">Tipo<i class="f-r glyphicon glyphicon-eye-open"></i></li>
            <li class="toggle-vis" data-column="3">Servicio<i class="f-r glyphicon glyphicon-eye-open"></i></li>
            <li class="toggle-vis" data-column="4">Estado OTP<i class="f-r glyphicon glyphicon-eye-open"></i></li>
            <li class="toggle-vis" data-column="5">F Programa<i class="f-r glyphicon glyphicon-eye-open"></i></li>
            <li class="toggle-vis" data-column="6">F Compromiso<i class="f-r glyphicon glyphicon-eye-open"></i></li>
            <li class="toggle-vis" data-column="7">f Creación<i class="f-r glyphicon glyphicon-eye-open"></i></li>
            <li class="toggle-vis" data-column="8">Ingeniero<i class="f-r glyphicon glyphicon-eye-open"></i></li>
            <li class="toggle-vis inactive" data-column="9">Lista<i class="f-r glyphicon glyphicon-eye-close"></i></li>
            <li class="toggle-vis inactive" data-column="10">Observaciónes<i class="f-r glyphicon glyphicon-eye-close"></i></li>
            <li class="toggle-vis inactive" data-column="11">Recurrente<i class="f-r glyphicon glyphicon-eye-close"></i></li>
            <li class="toggle-vis inactive" data-column="12">ultimo envio<i class="f-r glyphicon glyphicon-eye-close"></i></li>
        </ul>
    </div>
</div>

<!-- fin menu sticky de columnas -->

<!-- boton para "guardar codigos de resolucion" falso  -->
<a class="btn btn_datatable_cami" id="reload" title="Guardar Código de Resolución" style="float: right;"><span class="glyphicon glyphicon-save"></span></a>
<!--*********************  MODULO PESTAÑAS  *********************-->
<ul class="nav nav-tabs" id="pestania">
    <li tabla="table_otPadreList" class="active"><a id="pestana_cant_total" data-toggle="tab" href="#total">Total <span class="badge" style="background: white;color: #7a00ff;border: 1.2px solid #7a00ff;" id="badge_cant_total_OTP"></span></a></li>
    <li tabla="table_otPadreListHoy" class=""><a data-toggle="tab" href="#hoy">Hoy</a></li>
    <li tabla="table_otPadreListVencidas" class=""><a data-toggle="tab" href="#vencidas">Vencida</a></li>
    <li tabla="table_list_opc" class=""><a data-toggle="tab" href="#por_lista">Por Lista</a></li>
    <li tabla="table_reporte_actualizacion" class=""><a class="clr_red" data-toggle="tab" href="#reporte_actualizacion" id="pestana_cant_report"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Enviar Reporte <span class="badge border_red bg-white" id="badge_cant_report"></span></a></li>
</ul>
<!--*********************  CONTENIDO PESTAÑAS  *********************-->
<div class="tab-content" id="contenido_tablas">
    <?php if (Auth::user()->n_role_user === 'administrador') : ?>
    <div class="row" style="text-align:center; margin-bottom:1em; margin-top:unset;">
        <h5><b>Filtrar grupo de ingenieros por...</b></h5>
        <select id="filterGroupIng" class="sel-cris-cool">
            <option value="all">TODOS</option>
            <option value="GESTION OTS PROYECTOS">GESTION OTS PROYECTOS</option>
            <option value="GESTION OTS ESTANDAR">GESTION OTS ESTANDAR</option>
        </select>
    </div>
    <hr>
    <?php endif ?>

    <div id="total" class="tab-pane fade in active">
        <h3 style="margin-bottom:unset;">OT Padre</h3>
        <?php
//        $this->datatables->generate('table_otPadreList');
//
//        // Add this line after you load jquery from code.jquery.com
//        $this->datatables->jquery('table_otPadreList');
        ?>
        <table id="table_otPadreList" class="table table-hover table-bordered table-striped dataTable_camilo" style="width: 100%;">
            <tfoot>
                <tr>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th id="opciones"></th>
                </tr>
            </tfoot>
        </table>
    </div>

    <div id="hoy" class="tab-pane fade">
        <h3>Hoy</h3>
        <table id="table_otPadreListHoy" class="table table-hover table-bordered table-striped dataTable_camilo" style="width: 100%;">
            <tfoot>
                <tr>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
            </tfoot>
        </table>
    </div>

    <div id="vencidas" class="tab-pane fade">
        <h3>Vencidas</h3>
        <table id="table_otPadreListVencidas" class="table table-hover table-bordered table-striped dataTable_camilo" style="width: 100%;">
            <tfoot>
                <tr>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
            </tfoot>
        </table>
    </div>

    <div id="por_lista" class="tab-pane fade">

        <div align="center">
            <select class='btn-cami_cool' name='opc_lista' id="select_filter">
                <optgroup class="cod_nuev" label="CODIGOS NUEVOS">
                    <option value="CLIENTE - SIN FECHA PARA RECIBIR EL SERVICIO">CLIENTE - SIN FECHA PARA RECIBIR EL SERVICIO</option>
                    <option value="CLIENTE/SIN FECHA ADECUACIONES EN SEDE (ELEC/FIS)">CLIENTE/SIN FECHA ADECUACIONES EN SEDE (ELEC/FIS)</option>
                    <option value="CLIENTE/SIN DISPONIBILIDAD INFRA (PTA TELEF/LAN)">CLIENTE/SIN DISPONIBILIDAD INFRA (PTA TELEF/LAN)</option>
                    <option value="CLIENTE/CAMBIO DE ALCANCE (CBIO  TIPO SERVICIO)">CLIENTE/CAMBIO DE ALCANCE (CBIO TIPO SERVICIO)</option>
                    <option value="CLIENTE/CAMBIO DE UBICACIÓN DE ULTIMA MILLA">CLIENTE/CAMBIO DE UBICACIÓN DE ULTIMA MILLA</option>
                    <option value="CLIENTE/NO APRUEBA COSTOS DE OBRA CIVIL">CLIENTE/NO APRUEBA COSTOS DE OBRA CIVIL</option>
                    <option value="CLIENTE/NO PERMITE CIERRE DE KO">CLIENTE/NO PERMITE CIERRE DE KO</option>
                    <option value="CLIENTE/SIN DEFINICIÓN DIR DE UBICACIÓN SERVICIO">CLIENTE/SIN DEFINICIÓN DIR DE UBICACIÓN SERVICIO</option>
                    <option value="CLIENTE/NO PERMITE PROG ACT ETAPA INICIAL VOC">CLIENTE/NO PERMITE PROG ACT ETAPA INICIAL VOC</option>
                    <option value="CLIENTE/NO PERMITE PROG ACT ETAPA INTERMEDIA EOC">CLIENTE/NO PERMITE PROG ACT ETAPA INTERMEDIA EOC</option>
                    <option value="CLIENTE/NO PERMITE PROG ACT ETAPA INTERMEDIA EMP">CLIENTE/NO PERMITE PROG ACT ETAPA INTERMEDIA EMP</option>
                    <option value="CLIENTE/NO PERMITE PROG ACT  VOC TERCERO">CLIENTE/NO PERMITE PROG ACT VOC TERCERO</option>
                    <option value="CLIENTE/NO PERMITE PROG ACT ETAP INTERMEDIA UM TER">CLIENTE/NO PERMITE PROG ACT ETAP INTERMEDIA UM TER</option>
                    <option value="CLIENTE/NO PERMITE PROG ACT ETAPA FINAL ES">CLIENTE/NO PERMITE PROG ACT ETAPA FINAL ES</option>
                    <option value="CLIENTE/NO PERMITE PROG ACT ETAPA FINAL ES REQ VM">CLIENTE/NO PERMITE PROG ACT ETAPA FINAL ES REQ VM</option>
                    <option value="CLIENTE/SIN CONTRATO FIRMADO">CLIENTE/SIN CONTRATO FIRMADO</option>
                    <option value="CLIENTE/PROGRAMADA_PROXIMO PERIODO">CLIENTE/PROGRAMADA_PROXIMO PERIODO</option>
                    <option value="PL_ EXT/PERMISO MUNI - PERMISO ARREND INFRAESTRUC">PL_ EXT/PERMISO MUNI - PERMISO ARREND INFRAESTRUC</option>
                    <option value="PL_ EXT/NO VIABLE EN FACTIBILIDAD POR TERCEROS">PL_ EXT/NO VIABLE EN FACTIBILIDAD POR TERCEROS</option>
                    <option value="PL_ EXT/ETAPA INTERMEDIA/SIN PERSONAL  EOC/EMP">PL_ EXT/ETAPA INTERMEDIA/SIN PERSONAL EOC/EMP</option>
                    <option value="PL_ EXT/SIN APROBACIÓN COSTOS TENDIDO EXTERNO">PL_ EXT/SIN APROBACIÓN COSTOS TENDIDO EXTERNO</option>
                    <option value="PL_ EXT/NO VIABLE EN FO - EN INSTALACIÓN POR HFC">PL_ EXT/NO VIABLE EN FO - EN INSTALACIÓN POR HFC</option>
                    <option value="PLANTA EXTERNA - ERROR EN LA EJECUCIÓN DE EOC">PLANTA EXTERNA - ERROR EN LA EJECUCIÓN DE EOC</option>
                    <option value="PL_ EXT/INCUMPLIMIENTO FE DE UM/CANCELO/REPR ES">PL_ EXT/INCUMPLIMIENTO FE DE UM/CANCELO/REPR ES</option>
                    <option value="PL_ EXT/EN CURSO SIN INCONVENIENTE REPORTADO">PL_ EXT/EN CURSO SIN INCONVENIENTE REPORTADO</option>
                    <option value="PL_ EXT/ESCALADO_IFO_RESULTADO DE ACTIVIDAD">PL_ EXT/ESCALADO_IFO_RESULTADO DE ACTIVIDAD</option>
                    <option value="PL_ EXT/ESCALADO_IFO_SOLICITUD DE DESBORDE">PL_ EXT/ESCALADO_IFO_SOLICITUD DE DESBORDE</option>
                    <option value="PL_ EXT/ESCALADO_IFO_SOLICITUD DE PERSONAL">PL_ EXT/ESCALADO_IFO_SOLICITUD DE PERSONAL</option>
                    <option value="PLANTA EXTERNA - EN CURSO SOBRE OTP PYMES">PLANTA EXTERNA - EN CURSO SOBRE OTP PYMES</option>
                    <option value="PLANTA EXTERNA - EN CURSO SOBRE OTP ASOCIADA">PLANTA EXTERNA - EN CURSO SOBRE OTP ASOCIADA</option>
                    <option value="TERCEROS/NO VIABLE/EN PROC CANCELACIÓN">TERCEROS/NO VIABLE/EN PROC CANCELACIÓN</option>
                    <option value="TERCEROS/INCUMPLIMIENTO FECHA ENTREGA UM">TERCEROS/INCUMPLIMIENTO FECHA ENTREGA UM</option>
                    <option value="TERCEROS/SIN AVANCE SOBRE LA FECHA ENTREGA UM">TERCEROS/SIN AVANCE SOBRE LA FECHA ENTREGA UM</option>
                    <option value="TERCEROS - EN CURSO SIN INCONVENIENTE REPORTADO">TERCEROS - EN CURSO SIN INCONVENIENTE REPORTADO</option>
                    <option value="ALIADO/SIN INFORM ENTREGADA A TERC PARA INICIAR">ALIADO/SIN INFORM ENTREGADA A TERC PARA INICIAR</option>
                    <option value="PREVENTA - SIN ID  FACTIBILIDAD PARA TERCEROS">PREVENTA - SIN ID FACTIBILIDAD PARA TERCEROS</option>
                    <option value="PREVENTA - NO ES CLARA LA SOLUCIÓN A IMPLEMENTAR">PREVENTA - NO ES CLARA LA SOLUCIÓN A IMPLEMENTAR</option>
                    <option value="IMPLEMENTACIÓN - SOLUCIÓN NO ESTANDAR">IMPLEMENTACIÓN - SOLUCIÓN NO ESTANDAR</option>
                    <option value="COMERCIAL - ESCALADO ORDEN DE REEMPLAZO">COMERCIAL - ESCALADO ORDEN DE REEMPLAZO</option>
                    <option value="EQUIPOS - EN COMPRAS">EQUIPOS - EN COMPRAS</option>
                    <option value="EQUIPOS - DEFECTUOSOS">EQUIPOS - DEFECTUOSOS</option>
                    <option value="EQUIPOS - SIN CODIGO SAP PARA SOLICITUD DE EQUIPOS">EQUIPOS - SIN CODIGO SAP PARA SOLICITUD DE EQUIPOS</option>
                    <option value="GPC/PENDIENTE INFOR DEL CLIENTE PARA CONFIGURAR">GPC/PENDIENTE INFOR DEL CLIENTE PARA CONFIGURAR</option>
                    <option value="GPC/PENDIENTE ACEPTACIÓN CRONOGRAMA POR CLIENTE">GPC/PENDIENTE ACEPTACIÓN CRONOGRAMA POR CLIENTE</option>
                    <option value="GPC - CAMBIO DE ALCANCE ORDEN DE PEDIDO">GPC - CAMBIO DE ALCANCE ORDEN DE PEDIDO</option>
                    <option value="GPC - EN PROCESO DE CANCELACIÓN">GPC - EN PROCESO DE CANCELACIÓN</option>
                    <option value="GPC/PENDIENTE ACEPTACIÓN CRONOGRAMA POR CLIENTE">GPC/PENDIENTE ACEPTACIÓN CRONOGRAMA POR CLIENTE</option>
                    <option value="GPC - SIN ALCANCE PARA FABRICA">GPC - SIN ALCANCE PARA FABRICA</option>
                    <option value="LIDER TECNICO - PENDIENTE PLAN TECNICO">LIDER TECNICO - PENDIENTE PLAN TECNICO</option>
                    <option value="LIDER TECNICO - CAMBIO DE ALCANCE PLAN TECNICO">LIDER TECNICO - CAMBIO DE ALCANCE PLAN TECNICO</option>
                    <option value="LIDER TECNICO/SOLUCIÓN NO ESTANDAR SIN DEFINICIÓN">LIDER TECNICO/SOLUCIÓN NO ESTANDAR SIN DEFINICIÓN</option>
                    <option value="CONTROL DE CAMBIOS - RFC NO ESTANDAR EN APROBACIÓN">CONTROL DE CAMBIOS - RFC NO ESTANDAR EN APROBACIÓN</option>
                    <option value="COEX - EN PROCESO DE CONFIGURACIÓN BACKEND">COEX - EN PROCESO DE CONFIGURACIÓN BACKEND</option>
                    <option value="COEX -ATRASO CONFIGURACIÓN BACKEND">COEX -ATRASO CONFIGURACIÓN BACKEND</option>
                    <option value="ESCALADO/EN PROCESO PASO A PENDIENTE CLIENTE">ESCALADO/EN PROCESO PASO A PENDIENTE CLIENTE</option>
                    <option value="ENTREGA - SERVICIO_ENTREGADO_PROCESO DE CIERRE">ENTREGA - SERVICIO_ENTREGADO_PROCESO DE CIERRE</option>
                    <option value="ENTREGA/SIN DISPONIBILIDAD AGENDA">ENTREGA/SIN DISPONIBILIDAD AGENDA</option>
                    <option value="ENTREGA Y/O SOPORTE PROGRAMADO">ENTREGA Y/O SOPORTE PROGRAMADO</option>
                    <option value="PENDIENTE SOLICITAR ENTREGA DEL SERVICIO">PENDIENTE SOLICITAR ENTREGA DEL SERVICIO</option>
                    <option value="DATACENTER CLARO- CABLEADO EN CURSO">DATACENTER CLARO- CABLEADO EN CURSO</option>
                    <option value="DATACENTER  CLARO- CABLEADO SIN EJECUTAR">DATACENTER CLARO- CABLEADO SIN EJECUTAR</option>
                    <option value="DATACENTER  CLARO- SIN CONSUMIBLES EN DATACENTER">DATACENTER CLARO- SIN CONSUMIBLES EN DATACENTER</option>
                    <option value="EN PROCESO DE PASO A ESTADO PENDIENTE CLIENTE">EN PROCESO DE PASO A ESTADO PENDIENTE CLIENTE</option>
                    <option value="EN PROCESO DE PASO A ESTADO CANCELADO ">EN PROCESO DE PASO A ESTADO CANCELADO </option>
                    <option value="INCONVENIENTE TECNICO">INCONVENIENTE TECNICO</option>
                    <option value="KO PENDIENTE">KO PENDIENTE</option>
                    <option value="EN CONFIGURACIÓN">EN CONFIGURACIÓN</option>
                    <option value="GPC/CAMBIO DE ALCANCE ORDEN DE PEDIDO">GPC/CAMBIO DE ALCANCE ORDEN DE PEDIDO</option>
                    <option value="GPC/EN PROCESO DE CANCELACIÓN">GPC/EN PROCESO DE CANCELACIÓN</option>
                    <option value="GPC/PENDIENTE INFORM DEL CLIENTE PARA CONFIGURAR">GPC/PENDIENTE INFORM DEL CLIENTE PARA CONFIGURAR</option>
                    <option value="GPC/SIN ALCANCE PARA FABRICA">GPC/SIN ALCANCE PARA FABRICA</option>
                    <option value="ESTADO CANCELADO">ESTADO CANCELADO</option>
                    <option value="ESTADO PENDIENTE CLIENTE">ESTADO PENDIENTE CLIENTE</option>
                </optgroup>
            </select>
        </div>
        <table id="table_list_opc" class="table table-hover table-bordered table-striped dataTable_camilo" style="width: 100%;">
            <tfoot>
                <tr>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
            </tfoot>
        </table>
    </div>


    <div id="reporte_actualizacion" class="tab-pane fade">
        <h3>Reportes de Actualización</h3>
        <div class="container m-b-40">
            <table id="tableCountReporteActualizacion" class="table table-hover table-bordered table-striped dataTable_camilo fs-10">
                <thead>
                    <tr>
                        <th width="210">Ingeniero</th>
                        <th>Menor 8 días</th>
                        <th>Entre 8 y 15 días</th>
                        <th>Entre 16 y 30 días</th>
                        <th>Mayor 30 días</th>
                        <th width="210">Ingeniero</th>
                        <th>Menor 8 días</th>
                        <th>Entre 8 y 15 días</th>
                        <th>Entre 16 y 30 días</th>
                        <th>Mayor 30 días</th>
                    </tr>
                </thead>
                <tbody id="tbodyCount"></tbody>
            </table>
        </div>

        <table id="table_reporte_actualizacion" class="table table-hover table-bordered table-striped dataTable_camilo" style="width: 100%;">
            <tfoot>
                <tr>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<!------------------------------------------ MODAL QUE MUESTRA TODAS LAS OTS HIJA DE LAS OTS PADRES -------------------------->
<div id="modalOthDeOtp" class="modal fade" tabindex="-1" data-backdrop="static" role="dialog" style="overflow: auto">
    <div class="modal-dialog modal-lg" width='100%'>
        <div class="modal-content">
            <div class="modal-header csstypesubtitle">
                <button type="button" class="close cerrar" data-dismiss="modal" aria-label="Close"><img src="<?= URL::to('assets/images/cerrar (7).png'); ?>"></button>
                <h3 class="modal-title" id="myModalLabel"> Orden Ot Hija N <label id="id_ot_modal"></label></h3>
            </div>
            <div class="modal-body">
                <form class="well form-horizontal" id="formModalOTHS" method="post" novalidate="novalidate">
                    <div class="col-xs-4 col-xs-offset-4 ver-log-general">
                        <span class="fa fa-info-circle" aria-hidden="true"></span>
                        <span>Ver historial</span>
                    </div>
                    <table class="table table-hover table-bordered  dataTable_camilo table-striped  " id="table_oths_otp" cellspacing="2"></table>
                </form>
            </div>

            <div class="modal-footer cssnewtypem">
                <button type="button" class="btn btn-default cerrar" id="mbtnCerrarModal" data-dismiss="modal"><i class='glyphicon glyphicon-remove'></i>&nbsp;Cancelar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal tabla log -->
<div class="modal fade" id="ModalHistorialLog" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header csstypesubtitle">
                <button type="button" class="close cerrar" data-dismiss="modal" aria-label="Close"><span class="glyphicon glyphicon-remove-sign"></span></button>
                <h4 class="modal-title" id="titleEventHistory">Modal Historial</h4>
            </div>
            <div class="modal-body" id="cuerpoModal">
                <div class="container2">
                    <!--*********************  MODULO PESTAÑAS  *********************-->
                    <ul class="nav nav-tabs">
                        <li id="liLogHistory" class="active"><a data-toggle="tab" href="#tab_log">Historial Log</a></li>
                        <li id="liLogReportInit" class=""><a data-toggle="tab" href="#tabLogReportInit">Historial Reporte Inicio <span style='color: #2dea29;' class='fa fa-check-circle' aria-hidden='true'></span></a></li>
                        <li id="liLogReporAct" class=""><a data-toggle="tab" href="#tabLogReportAct">Historial Reporte Act. <span class='fa fa-envelope' aria-hidden='true' style='color: #e2f105;'></span></a></li>
                    </ul>

                    <!--*********************  CONTENIDO PESTAÑAS  *********************-->
                    <div class="tab-content">
                        <div id="tab_log" class="tab-pane fade in active">
                            <h3>Tabla Log</h3>
                            <table id="tableHistorialLog" class='table table-bordered table-striped  col-sm-12' width='100%'>
                                <thead>
                                    <th>ORDEN</th>
                                    <th>ANTES</th>
                                    <th>AHORA</th>
                                    <th>COLUMNA CAMBIADA</th>
                                    <th>FECHA MODIFICACION</th>
                                </thead>
                            </table>
                        </div>

                        <div id="tabLogReportInit" class="tab-pane fade">
                            <h3>Historial Reporte Inicio</h3>
                            <table id="tableLogReportInit" class='table table-bordered table-striped' width='100%'>
                                <thead>
                                    <th>FECHA</th>
                                    <th>CLASE</th>
                                    <th>SERVICIO</th>
                                    <th>ENVIADO POR</th>
                                    <th>DIRIGIDO A</th>
                                    <th>Opc</th>
                                </thead>
                            </table>
                        </div>
                        <div id="tabLogReportAct" class="tab-pane fade">
                            <h3>Historial Reporte de Inicio</h3>
                            <table id="tableLogReportAct" class="table table-bordered table-striped" width="100%">
                                <thead>
                                    <!-- <th>id_ot_padre</th> -->
                                    <th>Destinatario</th>
                                    <th>Nombre Cliente</th>
                                    <!-- <th>observaciones</th> -->
                                    <th>Enviado por</th>
                                    <th>Fecha Envío</th>
                                    <!-- <th>paquete_enviados</th> -->
                                    <th>opc.</th>
                                    <!-- <th>FECHA</th>
                                    <th>CLASE</th>
                                    <th>SERVICIO</th>
                                    <th>ENVIADO POR</th>
                                    <th>ING. 2</th>
                                    <th>DIRIGIDO A</th> -->
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer cssnewtypem">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar <i class="glyphicon glyphicon-chevron-up"></i></button>
            </div>
        </div>
    </div>
</div>

<!------------------------------------------ MODAL QUE MUESTRA LOS HITOS DE UNA OT PADRES -------------------------->
<div id="modalHitosOtp" class="modal fade" tabindex="-1" data-backdrop="static" role="dialog" style="overflow: auto">
    <div class="modal-dialog modal-lg" width='100%'>
        <div class="modal-content">
            <div class="modal-header csstypesubtitle">
                <button type="button" class="close cerrar" data-dismiss="modal" aria-label="Close"><img src="<?= URL::to('assets/images/cerrar (7).png'); ?>"></button>
                <h3 class="modal-title" id="myModalLabelHitos"> Orden Ot Hija N <label id="id_ot_modal"></label></h3>
            </div>
            <div class="modal-body">
                <form class="well form-horizontal" id="formModalHitosOTP" method="post" novalidate="novalidate">
                    <div class="form-group col-md-6">
                        <label for="email">Actividad Actual:</label>
                        <select name="actividad_actual" id="actividad_actual" class="form-control" required>
                            <option value="">SELECCIONE...</option>
                            <option class="optH" value="VISITA OBRA CIVIL">VISITA OBRA CIVIL (VOC)</option>
                            <option class="optH" value="VISITA EJECUCION OBRA CIVIL">VISITA EJECUCION OBRA CIVIL (EOC)</option>
                            <option class="optH" value="EMPALMES">EMPALMES (EM)</option>
                            <option class="optH" value="ENTREGA SERVICIO">ENTREGA SERVICIO</option>
                            <option class="optH" value="PENDIENTE CLIENTE">PENDIENTE CLIENTE</option>
                        </select>
                    </div>
                    <table class="table table-hover table-bordered  dataTable_camilo table-striped  ">
                        <tr>
                            <td><label id="servivio_hito"></label></td>
                            <td><label id="cliente_hito"></label></td>
                            <td><label id="ciudad_hito"></label></td>
                        </tr>
                    </table>
                    <table class="table table-hover table-bordered  dataTable_camilo table-striped  " id="table_hitos_otp" cellspacing="2">
                        <thead>
                            <tr>
                                <th></th>
                                <th>ACTIVIDAD</th>
                                <th>FECHA</th>
                                <th>ESTADO</th>
                                <th>OBSERVACIONES</th>
                                <th>NO APLICA</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <ul class="timeline timeline-jhon">
                                        <li class="timeline-item">
                                            <div class="timeline-badge" id="act_voc">1</div>
                                        </li>
                                    </ul>
                                </td>
                                <td><span>VISITA OBRA CIVIL</span></td>
                                <td>
                                    <input type="date" name="f_compromiso" id="f_voc" class="form-control fechas_hitos">
                                </td>
                                <td>
                                    <select name="estado" id="n_estado_voc" class="form-control" onchange="eventos.onChangeStatusHitos('voc');">
                                        <option value="">SELECCIONE...</option>
                                        <option value="EJECUTADA">EJECUTADA</option>
                                        <option value="PROGRAMADA">PROGRAMADA</option>
                                        <option value="APROBADA">APROBADA</option>
                                        <option value="CONFIGURADO">CONFIGURADO</option>
                                        <option value="FECHA TENTATIVA">FECHA TENTATIVA</option>
                                        <option value="CERRADA">CERRADA</option>
                                        <option value="NO APLICA">NO APLICA</option>
                                    </select>
                                </td>
                                <td>
                                    <textarea name="observaciones" id="n_observaciones_voc" rows="2"></textarea>
                                </td>
                                <td>
                                    <input type="checkbox" name="check_na" id="no_aplica_voc" class="form-control" data-check="f_voc">
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <ul class="timeline timeline-jhon">
                                        <li class="timeline-item">
                                            <div class="timeline-badge" id="act_eoc">2</div>
                                        </li>
                                    </ul>
                                </td>
                                <td><span>VISITA EJECUCION OBRA CIVIL</span></td>
                                <td>
                                    <input type="date" name="f_compromiso" id="f_eoc" class="form-control fechas_hitos">
                                </td>
                                <td>
                                    <select name="estado" id="estado_eoc" class="form-control" onchange="eventos.onChangeStatusHitos('eoc');">
                                        <option value="">SELECCIONE...</option>
                                        <option value="EJECUTADA">EJECUTADA</option>
                                        <option value="PROGRAMADA">PROGRAMADA</option>
                                        <option value="APROBADA">APROBADA</option>
                                        <option value="CONFIGURADO">CONFIGURADO</option>
                                        <option value="FECHA TENTATIVA">FECHA TENTATIVA</option>
                                        <option value="CERRADA">CERRADA</option>
                                        <option value="NO APLICA">NO APLICA</option>
                                    </select>
                                </td>
                                <td>
                                    <textarea name="observaciones" id="observaciones_eoc" rows="2"></textarea>
                                </td>
                                <td>
                                    <input type="checkbox" name="check_na" id="no_aplica_eoc" class="form-control" data-check="f_eoc">
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <ul class="timeline timeline-jhon">
                                        <li class="timeline-item">
                                            <div class="timeline-badge" id="act_em">3</div>
                                        </li>
                                    </ul>
                                </td>
                                <td><span>EMPALMES</span></td>
                                <td>
                                    <input type="date" name="f_compromiso" id="f_em" class="form-control fechas_hitos">
                                </td>
                                <td>
                                    <select name="estado" id="estado_em" class="form-control" onchange="eventos.onChangeStatusHitos('em');">
                                        <option value="">SELECCIONE...</option>
                                        <option value="EJECUTADA">EJECUTADA</option>
                                        <option value="PROGRAMADA">PROGRAMADA</option>
                                        <option value="APROBADA">APROBADA</option>
                                        <option value="CONFIGURADO">CONFIGURADO</option>
                                        <option value="FECHA TENTATIVA">FECHA TENTATIVA</option>
                                        <option value="CERRADA">CERRADA</option>
                                        <option value="NO APLICA">NO APLICA</option>
                                    </select>
                                </td>
                                <td>
                                    <textarea name="observaciones" id="observaciones_em" rows="2"></textarea>
                                </td>
                                <td>
                                    <input type="checkbox" name="check_na" id="no_aplica_em" class="form-control" data-check="f_em">
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <ul class="timeline timeline-jhon">
                                        <li class="timeline-item">
                                            <div class="timeline-badge" id="act_en_ser">4</div>
                                        </li>
                                    </ul>
                                </td>
                                <td><span>ENTREGA SERVICIO</span></td>
                                <td>
                                    <input type="date" name="f_compromiso" id="f_entrega_servicio" class="form-control fechas_hitos">
                                </td>
                                <td>
                                    <select name="estado" id="estado_entrega_servicio" class="form-control" onchange="eventos.onChangeStatusHitos('entrega');">
                                        <option value="">SELECCIONE...</option>
                                        <option value="EJECUTADA">EJECUTADA</option>
                                        <option value="PROGRAMADA">PROGRAMADA</option>
                                        <option value="APROBADA">APROBADA</option>
                                        <option value="CONFIGURADO">CONFIGURADO</option>
                                        <option value="FECHA TENTATIVA">FECHA TENTATIVA</option>
                                        <option value="CERRADA">CERRADA</option>
                                        <option value="NO APLICA">NO APLICA</option>
                                    </select>
                                </td>
                                <td>
                                    <textarea name="observaciones" id="observaciones_entrega_servicio" rows="2"></textarea>
                                </td>
                                <td>
                                    <input type="checkbox" name="check_na" id="no_aplica_entrega" class="form-control" data-check="f_entrega_servicio">
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" class="timeline-badge"><span>OBSERVACIONES PENDIENTE CLIENTE</span></td>
                                <td colspan="4">
                                    <textarea name="observaciones" id="observaciones_genrales" rows="2" cols="100"></textarea>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </form>
            </div>
            <div class="modal-footer cssnewtypem">
                <button type="button" class="btn btn-default cerrar" id="mbtnCerrarModal" data-dismiss="modal"><i class='glyphicon glyphicon-remove'></i>&nbsp;Cancelar</button>
                <button type="button" class="btn btn-success" id="btnGuardarModalHitos"><i class='glyphicon glyphicon-save'></i>&nbsp;Guardar</button>
            </div>
        </div>
    </div>
</div>

<!-------------------------------------- MODAL QUE MUESTRA TODAS LAS OTS HIJA DE LAS OTS PADRES -------------------------->

<!------------------------------------------ Modal editar OTs hijas ------------------------------------------>
<div id="modalEditTicket" class="modal fade" data-backdrop="static" data-keyboard="false" role="dialog">
    <div class="modal-dialog modal-lg2" style="width: 1200px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close cerrar" data-dismiss="modal" aria-label="Close"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button>
                <h3 class="modal-title" id="myModalLabelEditOth"> Orden Ot Hija N <label id="id_ot_modal_edit_oth"></label></h3>
            </div>
            <div class="modal-body">
                <button class="btn m-b-10" id="btn_mostrar_detalle"> ver detalle &nbsp;<img src="<?= URL::to('assets/images/plus.png'); ?>" data-simbol="plus" class="rigth"></span></button>

                <div>
                    <form class="well form-horizontal f-s-12" id="formModal" action="Templates/c_updateStatusOt" method="post">
                        <input name="id_orden_trabajo_hija" id="id_orden_trabajo_hija" type="hidden">
                        <input name="estado_orden_trabajo_hija" id="estado_orden_trabajo_hija" type="hidden">
                        <input name="k_id_estado_ot_value" id="k_id_estado_ot_value" type="hidden">
                        <input name="c_email" id="c_email" type="hidden">
                        <input name="num_servicio" id="num_servicio" type="hidden">
                        <input name="is_origen" id="is_origen" value="0" type="hidden">

                        <fieldset>
                            <div class="toggle_info_detail" style="display: none">
                                <div class="widget bg_white m-t-25 display-block">
                                    <fieldset class="col-md-6">
                                        <div class="form-group">
                                            <label for="id_cliente_onyx" class="col-md-3 control-label">Id cliente onyx: &nbsp;</label>
                                            <div class="col-md-8 selectContainer">
                                                <div class="input-group">
                                                    <span class="input-group-addon"><i class="glyphicon glyphicon-home"></i></span>
                                                    <input name="id_cliente_onyx" id="id_cliente_onyx" class="form-control" type="text" disabled="true">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="nombre_cliente" class="col-md-3 control-label">Nombre cliente: &nbsp;</label>
                                            <div class="col-md-8 selectContainer">
                                                <div class="input-group">
                                                    <span class="input-group-addon"><i class="glyphicon glyphicon-dashboard"></i></span>
                                                    <input name="n_nombre_cliente" id="n_nombre_cliente" class="form-control" type="text" disabled="true">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="grupo_objetivo" class="col-md-3 control-label">Grupo Objetivo: &nbsp;</label>
                                            <div class="col-md-8 selectContainer">
                                                <div class="input-group">
                                                    <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
                                                    <input name="grupo_objetivo" id="grupo_objetivo" class="form-control" type="text" disabled="true">
                                                </div>
                                            </div>
                                        </div>
                                    </fieldset>
                                    <!--  fin seccion izquierda form-->

                                    <!--  inicio seccion derecha form-->
                                    <fieldset>
                                        <div class="form-group">
                                            <label for="segmento" class="col-md-3 control-label">Segmento: &nbsp;</label>
                                            <div class="col-md-8 selectContainer">
                                                <div class="input-group">
                                                    <span class="input-group-addon" id="statusColor"><i class="glyphicon glyphicon-hand-right"></i></span>
                                                    <input name="segmento" id="segmento" class="form-control" type="text" disabled="true">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="nivel_atencion" class="col-md-3 control-label">Nivel Atención: &nbsp;</label>
                                            <div class="col-md-8 selectContainer">
                                                <div class="input-group">
                                                    <span class="input-group-addon"><i class="glyphicon glyphicon-time"></i></span>
                                                    <input name="nivel_atencion" id="nivel_atencion" class="form-control" type="text" disabled="true">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="ciudad" class="col-md-3 control-label">ciudad: &nbsp;</label>
                                            <div class="col-md-8 selectContainer">
                                                <div class="input-group">
                                                    <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
                                                    <input name="ciudad" id="ciudad" class="form-control" type="text" disabled="true">
                                                </div>
                                            </div>
                                        </div>
                                    </fieldset>
                                </div>
                                <!--  fin seccion derecha form -->

                                <div class="widget bg_white m-t-25 display-block">
                                    <fieldset class="col-md-6 control-label">
                                        <div class="form-group">
                                            <label for="departamento" class="col-md-3 control-label">departamento: &nbsp;</label>
                                            <div class="col-md-8 selectContainer">
                                                <div class="input-group">
                                                    <span class="input-group-addon"><i class='glyphicon glyphicon-user'></i></span>
                                                    <input name="departamento" id="departamento" class="form-control" type="text" disabled="true">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="grupo" class="col-md-3 control-label">grupo: &nbsp;</label>
                                            <div class="col-md-8 selectContainer">
                                                <div class="input-group">
                                                    <span class="input-group-addon"><i class='glyphicon glyphicon-calendar'></i></span>
                                                    <input name="grupo" id="grupo" class="form-control" type="text" disabled="true">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="consultor_comercial" class="col-md-3 control-label">Consultor Comercial: &nbsp;</label>
                                            <div class="col-md-8 selectContainer">
                                                <div class="input-group">
                                                    <span class="input-group-addon"><i class='glyphicon glyphicon-calendar'></i></span>
                                                    <input name="consultor_comercial" id="consultor_comercial" class="form-control" type="text" disabled="true">
                                                </div>
                                            </div>
                                        </div>
                                    </fieldset>
                                    <!--  fin seccion izquierda form -->

                                    <!--  inicio seccion derecha form -->
                                    <fieldset>
                                        <div class="form-group">
                                            <label for="grupo2" class="col-md-3 control-label">Grupo 2: &nbsp;</label>
                                            <div class="col-md-8 selectContainer">
                                                <div class="input-group">
                                                    <span class="input-group-addon"><i class='glyphicon glyphicon-user'></i></span>
                                                    <input name="grupo2" id="grupo2" class="form-control" type="text" disabled="true">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="consultor_postventa" class="col-md-3 control-label">Consultor Postventa: &nbsp;</label>
                                            <div class="col-md-8 selectContainer">
                                                <div class="input-group">
                                                    <span class="input-group-addon"><i class='glyphicon glyphicon-calendar'></i></span>
                                                    <input name="consultor_postventa" id="consultor_postventa" class="form-control" type="text" disabled="true">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="proy_instalacion" class="col-md-3 control-label">Proy Instalacion: &nbsp;</label>
                                            <div class="col-md-8 selectContainer">
                                                <div class="input-group">
                                                    <span class="input-group-addon"><i class='glyphicon glyphicon-calendar'></i></span>
                                                    <input name="proy_instalacion" id="proy_instalacion" class="form-control" type="text" disabled="true">
                                                </div>
                                            </div>
                                        </div>
                                    </fieldset>
                                </div>

                                <div class="widget bg_white m-t-25 display-block">
                                    <fieldset class="col-md-6 control-label">
                                        <div class="form-group">
                                            <label for="ing_responsable" class="col-md-3 control-label">Ingeniero Responsable: &nbsp;</label>
                                            <div class="col-md-8 selectContainer">
                                                <div class="input-group">
                                                    <span class="input-group-addon"><i class='glyphicon glyphicon-user'></i></span>
                                                    <input name="ing_responsable" id="ing_responsable" class="form-control" type="text" disabled="true">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="id_enlace" class="col-md-3 control-label">Id Enlace: &nbsp;</label>
                                            <div class="col-md-8 selectContainer">
                                                <div class="input-group">
                                                    <span class="input-group-addon"><i class='glyphicon glyphicon-calendar'></i></span>
                                                    <input name="id_enlace" id="id_enlace" class="form-control" type="text" disabled="true">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="alias_enlace" class="col-md-3 control-label">Alias Enlace: &nbsp;</label>
                                            <div class="col-md-8 selectContainer">
                                                <div class="input-group">
                                                    <span class="input-group-addon"><i class='glyphicon glyphicon-calendar'></i></span>
                                                    <input name="alias_enlace" id="alias_enlace" class="form-control" type="text" disabled="true">
                                                </div>
                                            </div>
                                        </div>
                                    </fieldset>
                                    <!--  fin seccion izquierda form-->

                                    <!--  inicio seccion derecha form-->
                                    <fieldset>
                                        <div class="form-group">
                                            <label for="orden_trabajo" class="col-md-3 control-label">Orden Trabajo: &nbsp;</label>
                                            <div class="col-md-8 selectContainer">
                                                <div class="input-group">
                                                    <span class="input-group-addon"><i class='glyphicon glyphicon-user'></i></span>
                                                    <input name="orden_trabajo" id="orden_trabajo" class="form-control" type="text" disabled="true">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="nro_ot_onyx" class="col-md-3 control-label">Nro Ot Onyx: &nbsp;</label>
                                            <div class="col-md-8 selectContainer">
                                                <div class="input-group">
                                                    <span class="input-group-addon"><i class='glyphicon glyphicon-calendar'></i></span>
                                                    <input name="nro_ot_onyx" id="nro_ot_onyx" class="form-control" type="" readonly>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="servicio" class="col-md-3 control-label">Servicio: &nbsp;</label>
                                            <div class="col-md-8 selectContainer">
                                                <div class="input-group">
                                                    <span class="input-group-addon"><i class='glyphicon glyphicon-calendar'></i></span>
                                                    <input name="servicio" id="servicio" class="form-control" type="text" disabled="true">
                                                </div>
                                            </div>
                                        </div>
                                    </fieldset>
                                </div>

                                <div class="widget bg_white m-t-25 display-block">
                                    <fieldset class="col-md-6 control-label">
                                        <div class="form-group">
                                            <label for="familia" class="col-md-3 control-label">Familia: &nbsp;</label>
                                            <div class="col-md-8 selectContainer">
                                                <div class="input-group">
                                                    <span class="input-group-addon"><i class='glyphicon glyphicon-user'></i></span>
                                                    <input name="familia" id="familia" class="form-control" type="text" disabled="true">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="producto" class="col-md-3 control-label">Producto: &nbsp;</label>
                                            <div class="col-md-8 selectContainer">
                                                <div class="input-group">
                                                    <span class="input-group-addon"><i class='glyphicon glyphicon-calendar'></i></span>
                                                    <input name="producto" id="producto" class="form-control" type="text" disabled="true">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="fecha_creacion" class="col-md-3 control-label">Fecha Creacion: &nbsp;</label>
                                            <div class="col-md-8 selectContainer">
                                                <div class="input-group">
                                                    <span class="input-group-addon"><i class='glyphicon glyphicon-calendar'></i></span>
                                                    <input name="fecha_creacion" id="fecha_creacion" class="form-control" type="text" disabled="true">
                                                </div>
                                            </div>
                                        </div>
                                    </fieldset>
                                    <!--  fin seccion izquierda form -->

                                    <!--  inicio seccion derecha form -->
                                    <fieldset>
                                        <div class="form-group">
                                            <label for="tiempo_incidente" class="col-md-3 control-label">Tiempo Incidente: &nbsp;</label>
                                            <div class="col-md-8 selectContainer">
                                                <div class="input-group">
                                                    <span class="input-group-addon"><i class='glyphicon glyphicon-user'></i></span>
                                                    <input name="tiempo_incidente" id="tiempo_incidente" class="form-control" type="text" disabled="true">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="estado_orden_trabajo" class="col-md-3 control-label">Estado Orden Trabajo: &nbsp;</label>
                                            <div class="col-md-8 selectContainer">
                                                <div class="input-group">
                                                    <span class="input-group-addon"><i class='glyphicon glyphicon-calendar'></i></span>
                                                    <input name="estado_orden_trabajo" id="estado_orden_trabajo" class="form-control" type="text" disabled="true">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="tiempo_estado" class="col-md-3 control-label">Tiempo Estado: &nbsp;</label>
                                            <div class="col-md-8 selectContainer">
                                                <div class="input-group">
                                                    <span class="input-group-addon"><i class='glyphicon glyphicon-calendar'></i></span>
                                                    <input name="tiempo_estado" id="tiempo_estado" class="form-control" type="text" disabled="true">
                                                </div>
                                            </div>
                                        </div>
                                    </fieldset>
                                </div>

                                <div class="widget bg_white m-t-25 display-block">
                                    <fieldset class="col-md-6 control-label">
                                        <div class="form-group">
                                            <label for="ano_ingreso_estado" class="col-md-3 control-label">Año Ingreso Estado: &nbsp;</label>
                                            <div class="col-md-8 selectContainer">
                                                <div class="input-group">
                                                    <span class="input-group-addon"><i class='glyphicon glyphicon-user'></i></span>
                                                    <input name="ano_ingreso_estado" id="ano_ingreso_estado" class="form-control" type="text" disabled="true">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="mes_ngreso_estado" class="col-md-3 control-label">Mes Ingreso Estado: &nbsp;</label>
                                            <div class="col-md-8 selectContainer">
                                                <div class="input-group">
                                                    <span class="input-group-addon"><i class='glyphicon glyphicon-calendar'></i></span>
                                                    <input name="mes_ngreso_estado" id="mes_ngreso_estado" class="form-control" type="text" disabled="true">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="fecha_ingreso_estado" class="col-md-3 control-label">Fecha Ingreso Estado: &nbsp;</label>
                                            <div class="col-md-8 selectContainer">
                                                <div class="input-group">
                                                    <span class="input-group-addon"><i class='glyphicon glyphicon-calendar'></i></span>
                                                    <input name="fecha_ingreso_estado" id="fecha_ingreso_estado" class="form-control" type="text" disabled="true">
                                                </div>
                                            </div>
                                        </div>
                                    </fieldset>
                                    <!--  fin seccion izquierda form -->

                                    <!--  inicio seccion derecha form -->
                                    <fieldset>
                                        <div class="form-group">
                                            <label for="usuario_asignado" class="col-md-3 control-label">Usuario Asignado: &nbsp;</label>
                                            <div class="col-md-8 selectContainer">
                                                <div class="input-group">
                                                    <span class="input-group-addon"><i class='glyphicon glyphicon-user'></i></span>
                                                    <input name="usuario_asignado" id="usuario_asignado" class="form-control" type="text" disabled="true">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="grupo_asignado" class="col-md-3 control-label">Grupo Asignado: &nbsp;</label>
                                            <div class="col-md-8 selectContainer">
                                                <div class="input-group">
                                                    <span class="input-group-addon"><i class='glyphicon glyphicon-calendar'></i></span>
                                                    <input name="grupo_asignado" id="grupo_asignado" class="form-control" type="text" disabled="true">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="ingeniero_provisioning" class="col-md-3 control-label">Ingeniero Provisioning: &nbsp;</label>
                                            <div class="col-md-8 selectContainer">
                                                <div class="input-group">
                                                    <span class="input-group-addon"><i class='glyphicon glyphicon-calendar'></i></span>
                                                    <input name="ingeniero_provisioning" id="ingeniero_provisioning" class="form-control" type="text" disabled="true">
                                                </div>
                                            </div>
                                        </div>
                                    </fieldset>
                                </div>

                                <div class="widget bg_white m-t-25 display-block">
                                    <fieldset class="col-md-6 control-label">
                                        <div class="form-group">
                                            <label for="cargo_arriendo" class="col-md-3 control-label">Cargo Arriendo: &nbsp;</label>
                                            <div class="col-md-8 selectContainer">
                                                <div class="input-group">
                                                    <span class="input-group-addon"><i class='glyphicon glyphicon-user'></i></span>
                                                    <input name="cargo_arriendo" id="cargo_arriendo" class="form-control" type="text" disabled="true">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="cargo_mensual" class="col-md-3 control-label">Cargo Mensual: &nbsp;</label>
                                            <div class="col-md-8 selectContainer">
                                                <div class="input-group">
                                                    <span class="input-group-addon"><i class='glyphicon glyphicon-calendar'></i></span>
                                                    <input name="cargo_mensual" id="cargo_mensual" class="form-control" type="text" disabled="true">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="monto_moneda_local_arriendo" class="col-md-3 control-label">Monto Moneda Local Arriendo: &nbsp;</label>
                                            <div class="col-md-8 selectContainer">
                                                <div class="input-group">
                                                    <span class="input-group-addon"><i class='glyphicon glyphicon-calendar'></i></span>
                                                    <input name="monto_moneda_local_arriendo" id="monto_moneda_local_arriendo" class="form-control" type="text" disabled="true">
                                                </div>
                                            </div>
                                        </div>
                                    </fieldset>
                                    <!--  fin seccion izquierda form -->

                                    <!--  inicio seccion derecha form -->
                                    <fieldset>
                                        <div class="form-group">
                                            <label for="monto_moneda_local_cargo_mensual" class="col-md-3 control-label">Monto Moneda Local Cargo Mensual: &nbsp;</label>
                                            <div class="col-md-8 selectContainer">
                                                <div class="input-group">
                                                    <span class="input-group-addon"><i class='glyphicon glyphicon-user'></i></span>
                                                    <input name="monto_moneda_local_cargo_mensual" id="monto_moneda_local_cargo_mensual" class="form-control" type="text" disabled="true">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="cargo_obra_civil" class="col-md-3 control-label">Cargo Obra Civil: &nbsp;</label>
                                            <div class="col-md-8 selectContainer">
                                                <div class="input-group">
                                                    <span class="input-group-addon"><i class='glyphicon glyphicon-calendar'></i></span>
                                                    <input name="cargo_obra_civil" id="cargo_obra_civil" class="form-control" type="text" disabled="true">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="descripcion" class="col-md-3 control-label">Descripcion: &nbsp;</label>
                                            <div class="col-md-8 selectContainer">
                                                <div class="input-group">
                                                    <span class="input-group-addon"><i class='glyphicon glyphicon-calendar'></i></span>
                                                    <input name="descripcion" id="descripcion" class="form-control" type="text" disabled="true">
                                                </div>
                                            </div>
                                        </div>
                                    </fieldset>
                                </div>

                                <div class="widget bg_white m-t-25 display-block">
                                    <fieldset class="col-md-6 control-label">
                                        <div class="form-group">
                                            <label for="direccion_origen" class="col-md-3 control-label">Dirección Origen: &nbsp;</label>
                                            <div class="col-md-8 selectContainer">
                                                <div class="input-group">
                                                    <span class="input-group-addon"><i class='glyphicon glyphicon-user'></i></span>
                                                    <input name="direccion_origen" id="direccion_origen" class="form-control" type="text" disabled="true">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="ciudad_incidente" class="col-md-3 control-label">ciudad Incidente: &nbsp;</label>
                                            <div class="col-md-8 selectContainer">
                                                <div class="input-group">
                                                    <span class="input-group-addon"><i class='glyphicon glyphicon-calendar'></i></span>
                                                    <input name="ciudad_incidente" id="ciudad_incidente" class="form-control" type="text" disabled="true">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="direccion_destino" class="col-md-3 control-label">Dirección Destino: &nbsp;</label>
                                            <div class="col-md-8 selectContainer">
                                                <div class="input-group">
                                                    <span class="input-group-addon"><i class='glyphicon glyphicon-calendar'></i></span>
                                                    <input name="direccion_destino" id="direccion_destino" class="form-control" type="text" disabled="true">
                                                </div>
                                            </div>
                                        </div>
                                    </fieldset>
                                    <!--  fin seccion izquierda form-->

                                    <!--  inicio seccion derecha form-->
                                    <fieldset>
                                        <div class="form-group">
                                            <label for="ciudad_incidente3" class="col-md-3 control-label">Ciudad Incidente 3: &nbsp;</label>
                                            <div class="col-md-8 selectContainer">
                                                <div class="input-group">
                                                    <span class="input-group-addon"><i class='glyphicon glyphicon-user'></i></span>
                                                    <input name="ciudad_incidente3" id="ciudad_incidente3" class="form-control" type="text" disabled="true">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="fecha_compromiso" class="col-md-3 control-label">Fecha Compromiso: &nbsp;</label>
                                            <div class="col-md-8 selectContainer">
                                                <div class="input-group">
                                                    <span class="input-group-addon"><i class='glyphicon glyphicon-calendar'></i></span>
                                                    <input name="fecha_compromiso" id="fecha_compromiso" class="form-control" type="text" disabled="true">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="fecha_programacion" class="col-md-3 control-label">Fecha Programación: &nbsp;</label>
                                            <div class="col-md-8 selectContainer">
                                                <div class="input-group">
                                                    <span class="input-group-addon"><i class='glyphicon glyphicon-calendar'></i></span>
                                                    <input name="fecha_programacion" id="fecha_programacion" class="form-control" type="text" disabled="true">
                                                </div>
                                            </div>
                                        </div>
                                    </fieldset>
                                </div>

                                <div class="widget bg_white m-t-25 display-block">
                                    <fieldset class="col-md-6 control-label">
                                        <div class="form-group">
                                            <label for="fecha_realizacion" class="col-md-3 control-label">Fecha Realización: &nbsp;</label>
                                            <div class="col-md-8 selectContainer">
                                                <div class="input-group">
                                                    <span class="input-group-addon"><i class='glyphicon glyphicon-user'></i></span>
                                                    <input name="fecha_realizacion" id="fecha_realizacion" class="form-control" type="text" disabled="true">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="resolucion_1" class="col-md-3 control-label">Resolución 1: &nbsp;</label>
                                            <div class="col-md-8 selectContainer">
                                                <div class="input-group">
                                                    <span class="input-group-addon"><i class='glyphicon glyphicon-calendar'></i></span>
                                                    <input name="resolucion_1" id="resolucion_1" class="form-control" type="text" disabled="true">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="resolucion_2" class="col-md-3 control-label">Resolución 2: &nbsp;</label>
                                            <div class="col-md-8 selectContainer">
                                                <div class="input-group">
                                                    <span class="input-group-addon"><i class='glyphicon glyphicon-calendar'></i></span>
                                                    <input name="resolucion_2" id="resolucion_2" class="form-control" type="text" disabled="true">
                                                </div>
                                            </div>
                                        </div>
                                    </fieldset>
                                    <!--  fin seccion izquierda form-->

                                    <!--  inicio seccion derecha form-->
                                    <fieldset>
                                        <div class="form-group">
                                            <label for="resolucion_3" class="col-md-3 control-label">Resolución 3: &nbsp;</label>
                                            <div class="col-md-8 selectContainer">
                                                <div class="input-group">
                                                    <span class="input-group-addon"><i class='glyphicon glyphicon-user'></i></span>
                                                    <input name="resolucion_3" id="resolucion_3" class="form-control" type="text" disabled="true">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="resolucion_4" class="col-md-3 control-label">Resolución 4: &nbsp;</label>
                                            <div class="col-md-8 selectContainer">
                                                <div class="input-group">
                                                    <span class="input-group-addon"><i class='glyphicon glyphicon-calendar'></i></span>
                                                    <input name="resolucion_4" id="resolucion_4" class="form-control" type="text" disabled="true">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="fecha_creacion_ot_hija" class="col-md-3 control-label">Fecha Creación Ot Hija: &nbsp;</label>
                                            <div class="col-md-8 selectContainer">
                                                <div class="input-group">
                                                    <span class="input-group-addon"><i class='glyphicon glyphicon-calendar'></i></span>
                                                    <input name="fecha_creacion_ot_hija" id="fecha_creacion_ot_hija" class="form-control" type="text" disabled="true">
                                                </div>
                                            </div>
                                        </div>
                                    </fieldset>
                                </div>

                                <div class="widget bg_white m-t-25 display-block">
                                    <fieldset class="col-md-6 control-label">
                                        <div class="form-group">
                                            <label for="proveedor_ultima_milla" class="col-md-3 control-label">Proveedor Ultima Milla: &nbsp;</label>
                                            <div class="col-md-8 selectContainer">
                                                <div class="input-group">
                                                    <span class="input-group-addon"><i class='glyphicon glyphicon-user'></i></span>
                                                    <input name="proveedor_ultima_milla" id="proveedor_ultima_milla" class="form-control" type="text" disabled="true">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="usuario_asignado4" class="col-md-3 control-label">Usuario Asignado 4: &nbsp;</label>
                                            <div class="col-md-8 selectContainer">
                                                <div class="input-group">
                                                    <span class="input-group-addon"><i class='glyphicon glyphicon-calendar'></i></span>
                                                    <input name="usuario_asignado4" id="usuario_asignado4" class="form-control" type="text" disabled="true">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="resolucion_15" class="col-md-3 control-label">Resolución 15: &nbsp;</label>
                                            <div class="col-md-8 selectContainer">
                                                <div class="input-group">
                                                    <span class="input-group-addon"><i class='glyphicon glyphicon-calendar'></i></span>
                                                    <input name="resolucion_15" id="resolucion_15" class="form-control" type="text" disabled="true">
                                                </div>
                                            </div>
                                        </div>
                                    </fieldset>
                                    <!--  fin seccion izquierda form-->

                                    <!--  inicio seccion derecha form-->
                                    <fieldset>
                                        <div class="form-group">
                                            <label for="resolucion_26" class="col-md-3 control-label">Resolución 26: &nbsp;</label>
                                            <div class="col-md-8 selectContainer">
                                                <div class="input-group">
                                                    <span class="input-group-addon"><i class='glyphicon glyphicon-user'></i></span>
                                                    <input name="resolucion_26" id="resolucion_26" class="form-control" type="text" disabled="true">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="resolucion_37" class="col-md-3 control-label">Resolución 37: &nbsp;</label>
                                            <div class="col-md-8 selectContainer">
                                                <div class="input-group">
                                                    <span class="input-group-addon"><i class='glyphicon glyphicon-calendar'></i></span>
                                                    <input name="resolucion_37" id="resolucion_37" class="form-control" type="text" disabled="true">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="resolucion_48" class="col-md-3 control-label">Resolución 48: &nbsp;</label>
                                            <div class="col-md-8 selectContainer">
                                                <div class="input-group">
                                                    <span class="input-group-addon"><i class='glyphicon glyphicon-calendar'></i></span>
                                                    <input name="resolucion_48" id="resolucion_48" class="form-control" type="text" disabled="true">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="fec_actualizacion_onyx_hija" class="col-md-3 control-label">Fecha Actualizacion Onyx Hija: &nbsp;</label>
                                            <div class="col-md-8 selectContainer">
                                                <div class="input-group">
                                                    <span class="input-group-addon"><i class='glyphicon glyphicon-calendar'></i></span>
                                                    <input name="fec_actualizacion_onyx_hija" id="fec_actualizacion_onyx_hija" class="form-control" type="text" disabled="true">
                                                </div>
                                            </div>
                                        </div>
                                    </fieldset>
                                </div>


                            </div>
                            <div>
                                <div class="widget bg_white m-t-25 display-block">
                                    <fieldset class="col-md-12 control-label">
                                        <div class="form-group">
                                            <label for="n_observacion_cierre" class="col-md-3 control-label">Observaciones: &nbsp;</label>
                                            <div class="col-md-8 selectContainer">
                                                <div class="input-group">
                                                    <textarea name="n_observacion_cierre" id="n_observacion_cierre" class="form-control" rows="3" cols="100"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </fieldset>

                                    <fieldset>
                                        <div class="form-group m-t-40 p-b-40"></div>
                                    </fieldset>
                                </div>
                                <div class="widget bg_white m-t-25 display-block">
                                    <fieldset class="col-md-6 control-label">
                                        <div class="form-group">
                                            <label for="ot_hija" class="col-md-3 control-label">Ot Hija: &nbsp;</label>
                                            <div class="col-md-8 selectContainer">
                                                <div class="input-group">
                                                    <span class="input-group-addon"><i class='glyphicon glyphicon-user'></i></span>
                                                    <input name="ot_hija" id="ot_hija" class="form-control" type="text" disabled="true">
                                                </div>
                                            </div>
                                        </div>
                                    </fieldset>
                                    <!--  fin seccion izquierda form-->

                                    <!--  inicio seccion derecha form-->
                                    <fieldset>
                                        <fieldset class="col-md-12 control-label">
                                            <div class="form-group">
                                                <label for="k_id_estado_ot" class="col-md-3 control-label">Estado Orden Trabajo Hija: &nbsp;</label>
                                                <div class="col-md-8 selectContainer">
                                                    <div class="input-group">
                                                        <span class="input-group-addon"><i class='glyphicon glyphicon-calendar'></i></span>
                                                        <select name="k_id_estado_ot" id="k_id_estado_ot" class="form-control llenarEstadosJS">
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group ins_servicio">
                                                <label for="t_servicio" class="col-md-3 control-label">Instalacion De Servicio: &nbsp;</label>
                                                <div class="col-md-8 selectContainer">
                                                    <div class="input-group">
                                                        <span class="input-group-addon"><i class="fa fa-briefcase" aria-hidden="true"></i></span>
                                                        <select class="form-control" id="ins_servicio" class="form-control">
                                                            <option value="">select...</option>
                                                            <option value="1">Internet Dedicado Empresarial</option>
                                                            <option value="2">Internet Dedicado </option>
                                                            <option value="3">MPLS Avanzado Intranet</option>
                                                            <option value="4">MPLS Avanzado Intranet - Varios Puntos</option>
                                                            <option value="5">MPLS Avanzado Intranet con Backup de Ultima Milla - NDS 2</option>
                                                            <option value="6">MPLS Avanzado Intranet con Backup de Ultima Milla y Router - NDS1</option>
                                                            <option value="7">MPLS Avanzado Extranet</option>
                                                            <option value="8">Backend MPLS </option>
                                                            <option value="9">MPLS Avanzado con Componente Datacenter Claro</option>
                                                            <option value="10">MPLS Transaccional 3G</option>
                                                            <!-- servicios nuevos -->
                                                            <option value="11">Adición Marquillas Aeropuerto el Dorado Opain</option>
                                                            <option value="12">Cambio de Equipos Servicio</option>
                                                            <option value="13">Cambio de Servicio Telefonia Fija Pública Linea Basica a Linea E1</option>
                                                            <option value="14">Cambio de Servicio Telefonia Fija Pública Linea SIP a PBX Distribuida Linea SIP</option>
                                                            <option value="15">Traslado Externo Servicio</option>
                                                            <option value="16">Traslado Interno Servicio</option>
                                                            <option value="17">SOLUCIONES ADMINISTRATIVAS - COMUNICACIONES UNIFICADAS PBX ADMINISTRADA</option>
                                                            <option value="18">Instalación Servicio Telefonia Fija PBX Distribuida Linea E1</option>
                                                            <option value="19">Instalación Servicio Telefonia Fija PBX Distribuida Linea SIP</option>
                                                            <option value="20">Instalación Servicio Telefonia Fija PBX Distribuida Linea SIP con Gateway de Voz</option>
                                                            <option value="21">Instalación Telefonía Publica Básica - Internet Dedicado</option>
                                                            <option value="22">Cambio de Última Milla</option>
                                                            <option value="23">Cambio de Equipo</option>
                                                            <option value=24>PL ETHERNET</option>
                                                            <option value=25>Cambio de velocidad – Ampliación y Saturación</option>
                                                            <optgroup label="SIN REPORTE INICIO">
                                                                <option value="30">PRIVATE LINE</option>
                                                                <option value="31">LAN ADMINISTRADA</option>
                                                            </optgroup>
                                                        </select>

                                                    </div>
                                                </div>
                                            </div>
                                        </fieldset>
                                    </fieldset>
                                </div>
                            </div>
                        </fieldset>

                        <!-- Tabs vertical -->
                        <div class="widget bg_white m-t-25 display-block" id="tabs_form" style="display: none">
                            <section class="content container-fluid p-t-0">
                                <div class="container autoheight">
                                    <div class="row">
                                        <div class="col-xs-11 bhoechie-tab-container">
                                            <div class="col-xs-2 bhoechie-tab-menu">
                                                <div class="list-group">
                                                    <a href="#" class="list-group-item active text-center">
                                                        <h2 class="glyphicon glyphicon-resize-horizontal"></h2><br />Linea Base
                                                    </a>
                                                    <a href="#" class="list-group-item text-center">
                                                        <h2 class="glyphicon glyphicon-list"></h2><br />Cierre de Kickoff
                                                    </a>
                                                    <a href="#" class="list-group-item text-center" id="reporteInicioFormModal">
                                                        <h2 class="glyphicon glyphicon-folder-open"></h2><br />Reporte de inicio
                                                    </a>
                                                    <a href="#" class="list-group-item text-center" id="contentAll">
                                                        <h2 class="glyphicon glyphicon-eye-open"></h2><br />Ver Todo
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="col-xs-10 bhoechie-tab">

                                                <!-- tab1  LINEA BASE-->
                                                <div class="bhoechie-tab-content active" id="contentTab1">
                                                    <h2 class="h4"><i class="fa fa-eye"></i> &nbsp; Línea Base – Formulario de Fechas</h2>
                                                    <!-- formulario de linea base -->
                                                    <div id="general_linea_base">
                                                        <div>
                                                            <div class="widget bg_white m-t-25 d-inline-b cliente">
                                                                <div class="d-inline-b">
                                                                    <fieldset class="col-md-6 m-l-200">

                                                                        <div class="form-group">
                                                                            <label for="lb_fecha_cierreKo" class="col-md-3 control-label">CIERRE KICKOFF(KO):</label>
                                                                            <!-- dias para OTP  #5 -->
                                                                            <input type="text" class="form-control numeroDias" placeholder="Numero De Dias" id="consultaDias"> <br>
                                                                            <div class="col-md-9 selectContainer">
                                                                                <div class="input-group">
                                                                                    <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
                                                                                    <input type="date" name="lb_fecha_cierreKo" id="lb_fecha_cierreKo" class="form-control validar_required">
                                                                                    <div class="input-group-btn">
                                                                                        <button type="button" id="btnTodayDateCalculate" class="btn btn-primary" title="Fecha Actual"><i class="glyphicon glyphicon-calendar"></i></button>

                                                                                    </div>
                                                                                </div><i class="m-l-40">Calcular linea base <span class="glyphicon glyphicon-question-sign"></span></i>
                                                                            </div>
                                                                        </div>
                                                                    </fieldset>
                                                                </div>
                                                                <div class="d-inline-b">
                                                                    <!-- <input type="text" class="comodin"> -->
                                                                    <fieldset class="col-md-6">
                                                                        <!--*********************  INPUT DATE  *********************-->
                                                                        <div class="form-group">
                                                                            <label for="lb_fecha_compromiso" class="control-label">COMPROMISO:</label>
                                                                            <div class="col-md-9 selectContainer">
                                                                                <div class="input-group">
                                                                                    <span class="input-group-addon activeDate span_fecha_compromiso"><i class="fa fa-calendar-check-o"></i></span>
                                                                                    <input type="date" name="lb_fecha_compromiso" id="lb_fecha_compromiso" class="form-control validar_required">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <!--*********************  INPUT DATE  *********************-->
                                                                        <div class="form-group">
                                                                            <label for="lb_fecha_voc" class="control-label">VISITA OBRA CIVIL (VOC): </label>
                                                                            <div class="col-md-9 selectContainer">
                                                                                <div class="input-group">
                                                                                    <span class="input-group-addon activeDate span_fecha_voc"><i class="fa fa-calendar-check-o"></i></span>
                                                                                    <input type="date" name="lb_fecha_voc" id="lb_fecha_voc" class="form-control validar_required">
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                    </fieldset>
                                                                    <fieldset class="col-md-6">
                                                                        <!--*********************  INPUT DATE  *********************-->
                                                                        <div class="form-group">
                                                                            <label for="lb_fecha_programacion" class="control-label">PROGRAMACIÓN: </label>
                                                                            <div class="col-md-9 selectContainer">
                                                                                <div class="input-group">
                                                                                    <span class="input-group-addon activeDate span_fecha_programacion"><i class="fa fa-calendar-check-o"></i></span>
                                                                                    <input type="date" name="lb_fecha_programacion" id="lb_fecha_programacion" class="form-control validar_required">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <!--*********************  INPUT DATE  *********************-->
                                                                        <div class="form-group">
                                                                            <label for="lb_fecha_dcoc" class="control-label">DOCUMENTACIÓN COTIZACIÓN OBRA CIVIL: </label>
                                                                            <div class="col-md-9 selectContainer">
                                                                                <div class="input-group">
                                                                                    <span class="input-group-addon activeDate span_fecha_dcoc"><i class="fa fa-calendar-check-o"></i></span>
                                                                                    <input type="date" name="lb_fecha_dcoc" id="lb_fecha_dcoc" class="form-control validar_required">
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                    </fieldset>
                                                                </div>

                                                                <div class="d-inline-b">
                                                                    <fieldset class="col-md-6">
                                                                        <!--*********************  INPUT DATE  *********************-->
                                                                        <div class="form-group">
                                                                            <label for="lb_fecha_aprobacion_coc" class="control-label">APROBACIÓN COTIZACIÓN OC: </label>
                                                                            <div class="col-md-9 selectContainer">
                                                                                <div class="input-group">
                                                                                    <span class="input-group-addon activeDate span_fecha_aprobacion_coc"><i class="fa fa-calendar-check-o"></i></span>
                                                                                    <input type="date" name="lb_fecha_aprobacion_coc" id="lb_fecha_aprobacion_coc" class="form-control validar_required">
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <!--*********************  INPUT DATE  *********************-->
                                                                        <div class="form-group">
                                                                            <label for="lb_fecha_configuracion" class="control-label">CONFIGURACIÓN:</label>
                                                                            <div class="col-md-9 selectContainer">
                                                                                <div class="input-group">
                                                                                    <span class="input-group-addon activeDate span_fecha_configuracion"><i class="fa fa-calendar-check-o"></i></span>
                                                                                    <input type="date" name="lb_fecha_configuracion" id="lb_fecha_configuracion" class="form-control validar_required">
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                    </fieldset>
                                                                    <fieldset class="col-md-6">
                                                                        <!--*********************  INPUT DATE  *********************-->
                                                                        <div class="form-group">
                                                                            <label for="lb_fecha_ingenieria_detalle" class="control-label">INGENIERÍA DETALLE:</label>
                                                                            <div class="col-md-9 selectContainer">
                                                                                <div class="input-group">
                                                                                    <span class="input-group-addon activeDate span_fecha_ingenieria_detalle"><i class="fa fa-calendar-check-o"></i></span>
                                                                                    <input type="date" name="lb_fecha_ingenieria_detalle" id="lb_fecha_ingenieria_detalle" class="form-control validar_required">
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <!--*********************  INPUT DATE  *********************-->
                                                                        <div class="form-group">
                                                                            <label for="lb_fecha_equipos" class="control-label">EQUIPOS:</label>
                                                                            <div class="col-md-9 selectContainer">
                                                                                <div class="input-group">
                                                                                    <span class="input-group-addon activeDate span_fecha_equipos"><i class="fa fa-calendar-check-o"></i></span>
                                                                                    <input type="date" name="lb_fecha_equipos" id="lb_fecha_equipos" class="form-control validar_required">
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                    </fieldset>
                                                                </div>

                                                                <div class="d-inline-b">
                                                                    <fieldset class="col-md-6">
                                                                        <!--*********************  INPUT DATE  *********************-->
                                                                        <div class="form-group">
                                                                            <label for="lb_fecha_ejecucion_obra_civil" class="control-label">VISITA EJECUCION OBRA CIVIL (EOC): </label>
                                                                            <div class="col-md-9 selectContainer">
                                                                                <div class="input-group">
                                                                                    <span class="input-group-addon activeDate span_fecha_ejecucion_obra_civil"><i class="fa fa-calendar-check-o"></i></span>
                                                                                    <input type="date" name="lb_fecha_ejecucion_obra_civil" id="lb_fecha_ejecucion_obra_civil" class="form-control validar_required">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <!--*********************  INPUT DATE  *********************-->
                                                                        <div class="form-group">
                                                                            <label for="lb_fecha_entrega_servicio" class="control-label">ENTREGA DE SERVICIO:</label>
                                                                            <div class="col-md-9 selectContainer">
                                                                                <div class="input-group">
                                                                                    <span class="input-group-addon activeDate span_fecha_entrega_servicio"><i class="fa fa-calendar-check-o"></i></span>
                                                                                    <input type="date" name="lb_fecha_entrega_servicio" id="lb_fecha_entrega_servicio" class="form-control validar_required">
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                    </fieldset>
                                                                    <fieldset class="col-md-6">
                                                                        <!--*********************  INPUT DATE  *********************-->
                                                                        <div class="form-group">
                                                                            <label for="lb_fecha_empalmes" class="control-label">EMPALMES:</label>
                                                                            <div class="col-md-9 selectContainer">
                                                                                <div class="input-group">
                                                                                    <span class="input-group-addon activeDate span_fecha_empalmes"><i class="fa fa-calendar-check-o"></i></span>
                                                                                    <input type="date" name="lb_fecha_empalmes" id="lb_fecha_empalmes" class="form-control validar_required">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </fieldset>
                                                                </div>

                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>


                                                <!--tab 2 -->
                                                <div class="bhoechie-tab-content" id="contentTab2">
                                                    <div id="general_producto">
                                                        <!-- <h2 class="h4"><i class="fa fa-eye"></i> &nbsp; Formulario de producto</h2> -->
                                                        <!-- llenar con formulario de producto -->
                                                    </div>
                                                </div>
                                                <!--tab 3 -->
                                                <div class="bhoechie-tab-content" id="contentTab3">
                                                    <h2 class="h4"><i class="fa fa-dot-circle-o"></i> Reporte de inicio</h2>
                                                    <!-- llenar esta seccion con el form de servicio -->
                                                    <div id="general_servicio"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </section>
                        </div>

                    </form>
                </div>
            </div>


            <div class="modal-footer">
                <button type="button" class="btn btn-default cerrar" id="mbtnCerrarModal" data-dismiss="modal"><i class='glyphicon glyphicon-remove'></i>&nbsp;Cancelar</button>
                <?php if (Auth::user()->n_role_user != 'claro') : ?>
                <button type="button" form="formModal" class="btn btn-info" id="btnUpdOt"><i class='glyphicon glyphicon-save'></i>&nbsp;Actualizar</button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<!------------------------------------------ Fin Modal editar OTs -------------------------------------->

<!------------------------------------------ modal ordenes seleccionadas ------------------------------->
<div id="mdl_cierre" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">X</button>
                <h3 class="modal-title" id="mdl-title-cierre"></h3>
            </div>
            <div class="modal-body">
                <table id="table_selected" class="table table-hover table-bordered table-striped dataTable_camilo" width="100%"></table>

                <form class="form-horizontal" id="formEmail">
                    <div class="form-group">
                        <label for="seniorHitos" class="col-sm-2 control-label">Señor(a)</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="seniorHitos">
                        </div>
                    </div>
                    <!--                    <div class="form-group">
                        <label for="direccionHitos" class="col-sm-2 control-label">Dirección de servicio</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="direccionHitos">
                        </div>
                    </div>-->
                    <div class="form-group">
                        <label for="configuracionHitos" class="col-sm-2 control-label">Nombre del cliente</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="configuracionHitos">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="entregaServicioHitos" class="col-sm-2 control-label">Entrega del servicio</label>
                        <div class="col-sm-10">
                            <input type="date" class="form-control" id="entregaServicioHitos">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="observacionesHitos" class="col-sm-2 control-label">Observaciones</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" rows="3" id="observacionesHitos"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" id="mdl-cierre-cerrar" data-dismiss="modal"><i class="fa fa-ban" aria-hidden="true"></i>&nbsp;Cancelar</button>
                <button type="button" class="btn btn-success" id="mdl-enviar-reporte" style="width: 13%;"><i class="fa fa-paper-plane-o" aria-hidden="true"></i>&nbsp;Enviar correo&nbsp;&nbsp;</button>
            </div>
        </div>
    </div>
</div>

<!------------------------------------------ modal detalle cierre de KO ------------------------------->
<div id="mdl_cierreKo" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg2" style="width: 1200px;">
        <div class="modal-content">
            <!--<div class="row">-->
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">X</button>
                <h3 class="modal-title" id="mdl-title-cierre"></h3>
            </div>
            <div class="modal-body">
                <div id="form_cierreKo" class="container autoheight">

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" id="mdl-cierre-cerrar" data-dismiss="modal"><i class="fa fa-ban" aria-hidden="true"></i>&nbsp;Cancelar</button>
            </div>
            <!--</div>-->
        </div>
    </div>
</div>


<script src="<?= URL::to("assets/plugins/sweetalert2/sweetalert2.all.js"); ?> "></script>
<!--<script src="https://cdn.jsdelivr.net/npm/promise-polyfill"></script>-->
<?php if (isset($_GET['msj'])) : ?>
<script>
    var urlbase = "<?php echo URL::base(); ?>";
    swal('OK', 'se actualizó correctamente', 'success');
</script>
<?php endif; ?>

<?php
$msj = $this->session->flashdata('msj');
if ($msj == 'error') {
    ?>
<script>
    swal('Intenta de nuevo', `error al actualizar los datos`, 'error');
</script>
<?php 
} else if ($msj == 'ok') { ?>
<script>
    swal('OK', 'se actualizó correctamente', 'success');
</script>
<?php

}
