$(function(){
    oth = {
        init : function () {
            var data = JSON.parse(oths);
            console.log('data:', data)
            oth.tableOTHSAsc = $("#tableOTHSAsc").DataTable(helper.configTableSearchColumn(data,[
                {title: 'OTP' , data: 'k_id_ot_padre'},
                {title: 'Cliente' , data: 'n_nombre_cliente'},
                {title: 'Orden de Trabajo OTP' , data: 'orden_trabajo'},
                {title: 'Estado OTP' , data: 'estado_orden_trabajo'},
                {title: 'OTH' , data: 'id_orden_trabajo_hija'},
                {title: 'ESTADO OTH' , data: 'n_name_estado_ot'},
                {title: 'ultima_fecha_modificacion' , data: 'ultima_fecha_modificacion'},
                {title: 'Ing' , data: 'nombreIng'},
                {title: 'Dias Habiles Transcurridos' , data: 'dias_trascurridos'},
            ],'tableOTHSAsc',1));
            
        },
    }
    oth.init()
});