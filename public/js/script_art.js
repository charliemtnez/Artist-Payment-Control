function activatelog(chk){
    if($(chk).is(":checked")){
        $('#email').attr('disabled',false);
        $('#user').attr('disabled',false);
        $('#user').attr('readonly',false);
        $('#pass').attr('disabled',false);
    }else if($(chk).is(":not(:checked)")){
        $('#email').attr('disabled',true);
        $('#user').attr('disabled',true);
        $('#user').attr('readonly',true);
        $('#pass').attr('disabled',true);
    }
}

function manage_art(form){

    var loading = true; 
    var titleloading = 'Espere. Puede tardar...'; 

    if(form){

        $('input.form-control').css("border-color", "#ced4da");
        let reg = /^[a-z A-Z áéíóúÁÉÍÓÚÑñäëïöüÄËÏÖÜ 0-9 \. \- _ \\s]{2,250}$/;
        let num = /^[0-9\.]{1,8}$/;
        let email = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/; 
        let re = /(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{4,}/; 

        let btn_ok,btn_cancel,footer;

        switch(form.name){
            case 'init':
                var formData = new FormData();
                formData.append('action',form.name);
                loading = true;
                titleloading = 'Cargando datos. Puede tardar...';
            break;
            case 'form_art':
            case 'showform_import':
                var formData = new FormData();
                formData.append('action',form.name);

                if (form.hasOwnProperty("info")) {
                    formData.append('idart',form.info.data('art'));
                }

                loading = false;
            break;
            case 'view_art':
                var formData = new FormData();
                formData.append('action',form.name);

                if (form.hasOwnProperty("info")) {
                    formData.append('idart',form.info.data('art'));
                }

                titleloading = 'Cargando datos. Puede tardar...';
            break;
            case 'view_artperiod':
                var formData = new FormData();
                formData.append('action',form.name);
                formData.append('idart',form.artist);
                formData.append('period',$('select[name=allperiod] option').filter(':selected').val());

                titleloading = 'Cargando datos. Puede tardar...';
            break;

            case 'add_art':
            case 'edt_art':
                var formData = new FormData(form);
                formData.append('action',form.name);

                if(!reg.test(form.nombre.value.trim())) { 
                    form.nombre.focus();
                    $('#'+form.nombre.id).css("border-color", "#dc3545");
                    proccess_modal('<div class="alert alert-danger" role="alert">Los campos Marcados con <i>(*)</i> son obligatorios. Por favor revise que esté bien el dato a entrar</div>','Error en el Campo Nombre');
                    return false; 
                }

                if(!num.test(form.percent.value.trim())) { 
                    form.percent.focus();
                    $('#'+form.percent.id).css("border-color", "#dc3545");
                    proccess_modal('<div class="alert alert-danger" role="alert">Los campos Marcados con <i>(*)</i> son obligatorios. Por favor indique el valor del Porciento aplicado al artista</div>','Error en el Campo Porciento aplicado');
                    return false; 
                }

                if($("#canlog").is(":checked")){
                    if(!email.test(form.email.value.trim())) { 
                        form.email.focus();
                        $('#'+form.email.id).css("border-color", "#dc3545");
                        proccess_modal('<div class="alert alert-danger" role="alert">Los campos Marcados con <i>(*)</i> son obligatorios. Por favor revise que esté bien el dato a entrar</div>','Error en el Campo Correo');
                        return false; 
                    }
                    if(!reg.test(form.user.value.trim())) { 
                        form.user.focus();
                        $('#'+form.user.id).css("border-color", "#dc3545");
                        proccess_modal('<div class="alert alert-danger" role="alert">Los campos Marcados con <i>(*)</i> son obligatorios. Por favor revise que esté bien el dato a entrar</div>','Error en el Campo Usuario');
                        return false; 
                    }
                    if(form.name == 'add_art' || form.pass.value.trim() != ''){
                        if (!re.test(form.pass.value.trim())) {
                            form.pass.focus();
                            $('#'+form.pass.id).css("border-color", "#dc3545");
                            proccess_modal('<div class="alert alert-danger" role="alert">Los campos Marcados con <i>(*)</i> son obligatorios. La contraseña debe contener al menos una Mayuscula, minusculas, números y no menos de 4 caracteres.<br />Por favor revise que esté bien el dato a entrar</div>','Error en el Campo Contraseña');
                            return false;
                        }
    
                        let p = hex_sha512(form.pass.value);
                        form.pass.value = "";
                        formData.append('password',p);
                    }
                }

            break;

            default:
                return false;
            
        }

    }

    proccess_ajaxfile(formData,window.location.origin+'/art/act/act_art',loading, titleloading).then((obj)=>{

        loadingSpinner(false);

        if(obj.ERROR){
            
            proccess_modal('<div class="alert alert-danger" role="alert">'+obj.ERROR+'</div>','Error');
            return false;
        }

        if (obj.hasOwnProperty("showform")) {
            
            $('#crea_rol').attr("disabled", "disabled");
            $('#importxls').attr("disabled", "disabled");
            $('#panel > div:first').html(obj.showform);
            $("#panel").slideDown();

            $("#show_hide_password a").on('click', function(event) {
                event.preventDefault();
                $(this).toggleClass('btn-success btn-danger');
                $("#eyepass").toggleClass('fa-eye fa-eye-slash');
                var input = document.getElementById("pass");
                if (input.type === "password") {
                    input.type = "text";
                } else {
                    input.type = "password";
                }
            });
        }
        
        if (obj.hasOwnProperty("btn_imp")) {
            $('#btn_imp').html(obj.btn_imp);
        }

        if (obj.hasOwnProperty("edt_art") || obj.hasOwnProperty("add_art")) {

            $(`#panel`).slideUp();
            $(`html,body`).animate({ scrollTop: $(`body`).offset().top }, `slow`);
            $(`#crea_rol`).removeAttr(`disabled`);
            
            manage_art({'name':'init'});

        }

        if (obj.hasOwnProperty("view_art")) {
            
            $('#artistas').html(obj.view_art);

        }

        if (obj.hasOwnProperty("view_artperiod")) {
            
            $('#viewtotalperiod').html(obj.view_artperiod.view_arttotalperiod);

            create_chart(obj.view_artperiod.totalyear);

            if (obj.view_artperiod.hasOwnProperty("tableArtTracks")) {

                create_table(obj.view_artperiod.tableArtTracks,'tracks');

            }

            if (obj.view_artperiod.hasOwnProperty("tableArtRetail")) {

                create_table(obj.view_artperiod.tableArtRetail,'source');
                
            }

            if (obj.view_artperiod.hasOwnProperty("tableArtCountry")) {

                create_table(obj.view_artperiod.tableArtCountry,'country');

            }

        }


        if (obj.hasOwnProperty("impArtInfo")) {
            
            manage_imp({'name':'init'});

        }

        if (obj.hasOwnProperty("impArtLote")) {

            btn_footer = '<button type="button" onclick="CloseModal()" class="btn btn-success" style="margin-right:10px;">OK</button>';
            
            proccess_modal(obj.impArtLote,'Resultados de importar en lote:',btn_footer);
            manage_imp({'name':'init'});

        }

        if (obj.hasOwnProperty("del_prevartimp") || obj.hasOwnProperty("del_allprevartimp")) {
            
            manage_imp({'name':'init'});

        }

        if (obj.hasOwnProperty("artprevimp")) {
            $('#artistas').html(obj.artprevimp);
            $(`#panel`).slideUp();
            $(`html,body`).animate({ scrollTop: $(`body`).offset().top }, `slow`);
            $(`#crea_rol`).removeAttr(`disabled`);$(`#importxls`).removeAttr(`disabled`);
        }

        if (obj.hasOwnProperty("table_art")) {
            
            $('#artistas').html(obj.table_art.table);

            $('#newartist').DataTable({
                destroy: true,
                order: [],
                responsive: true,
                processing: true,
                lengthChange: false,
                pageLength: 25,
                language:{"url":'js/datatable_spanish.json'},
                data:Object.values(obj.table_art.data),
                columns: [
                    { title: "Usuario",data:"user"},
                    { title: "Artista",data:"nombre"},
                    { title: "Correo",data:"email"},
                    { title: "Creado",data:"created"},
                    { title: "Ult. Accesso",data:"lastaccess"},
                    { title: "",data:"actions_view", orderable: false },
                    { title: "",data:"actions_edt", orderable: false },
                    // { title: "",data:"actions_act", orderable: false },
                    // { title: "",data:"actions_imp", orderable: false },
                    // { title: "",data:"actions_del", orderable: false }
                ]
            });

        }

    });

}

function create_chart(data)
{
    let month_period = [];
    let period_value = [];
    let period_value_art = [];

    let month = {1:'Enero', 2:'Febrero', 3:'Marzo', 4:'Abril', 5:'Mayo', 6:'Junio', 7:'Julio', 8:'Agosto', 9:'Septiembre', 10:'Octubre', 11:'Noviembre', 12:'Diciembre'};

    var elemento = document.getElementById("myAreaChart");
    var ctx = elemento.getContext('2d');

    Chart.defaults.global.defaultFontFamily = '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
    Chart.defaults.global.defaultFontColor = '#292b2c';

    try {
        /* Object.keys(data).map((key, index) => {
            period_value.push(parseFloat(data[key].total));
            period_value_art.push(parseFloat(data[key].total_art));
            month_period.push(data[key].month);
        }); */

        Object.keys(month).map((key, index) => {
            if(data.hasOwnProperty(key)){
                period_value.push(parseFloat(data[key].totalarg));
                period_value_art.push(parseFloat(data[key].totalartist)); 
            }else{
                period_value.push(0);
                period_value_art.push(0);
            } 
            month_period.push(month[key]);
        });  

        let Chart_Mag = {
            label: "Magenta",
            lineTension: 0.3,
            backgroundColor: "rgba(139,0,139,0.2)",
            borderColor: "rgba(139,0,139,1)",
            pointRadius: 5,
            pointBackgroundColor: "rgba(139,0,139,1)",
            pointBorderColor: "rgba(255,255,255,0.8)",
            pointHoverRadius: 5,
            pointHoverBackgroundColor: "rgba(139,0,139,1)",
            pointHitRadius: 30,
            pointBorderWidth: 2,
            data: period_value,
            };
        let Chart_Art = {
            label: "Artista",
            lineTension: 0.3,
            backgroundColor: "rgba(2,117,216,0.2)",
            borderColor: "rgba(2,117,216,1)",
            pointRadius: 5,
            pointBackgroundColor: "rgba(2,117,216,1)",
            pointBorderColor: "rgba(255,255,255,0.8)",
            pointHoverRadius: 5,
            pointHoverBackgroundColor: "rgba(2,117,216,1)",
            pointHitRadius: 30,
            pointBorderWidth: 2,
            data: period_value_art,
            };

        var DataChart = {
            // labels: month,
            labels: month_period,
            datasets: [Chart_Mag,Chart_Art]
        };

        new Chart(ctx, {
            data: DataChart,
            type: 'line',
            options: {
                legend: {
                    display: true,
                    position: 'bottom'
                }
            }
        });

    } catch (error) {
        console.log('chart tiene un error: '+ error);
    }
}

function create_table(data,idtable)
{
    
    $('#'+idtable).html(data.table);

    let columnstodo = Object.values(data.columns).map(function(value,index){
        let obj = {title:value['title'],data:value['data']};
        
        if(value.hasOwnProperty("render")){
            if(value['render'] == 'number'){
                obj['render'] = $.fn.dataTable.render.number( '.', ',', 0, '' )
            }
            if(value['render'] == 'currency'){
                obj['render'] = $.fn.dataTable.render.number( '.', ',', 6, '$ ' )
            }
        }

        return obj
    });

    let columnstotals =  Object.values(data.columnstotals);

    $('#'+data.tablaid).DataTable({
        destroy: true,
        order: [],
        responsive: true,
        processing: true,
        lengthChange: false,
        pageLength: 25,
        language:{"url":'js/datatable_spanish.json'},
        data:Object.values(data.data),
        columns:columnstodo,
        showFooter:false,
        footerCallback:function(row, data, start, end, display) {

            var api = this.api(),data;

            var intVal = function(i) {
                return typeof i === 'string' ?
                i.replace(/[\$,]/g, '') * 1 :
                typeof i === 'number' ?
                i : 0;
            };

            if(columnstotals){
                let totals  = columnstotals.map(function(value,index){
                    return api.column(value).data().reduce(function(a,b){
                            return intVal(a) + intVal(b);
                        },0);
                    });

                let pagetotals  = columnstotals.map(function(value,index){
                    return api.column(value,{
                                page: 'current'
                            }).data().reduce(function(a,b){
                                    return intVal(a) + intVal(b);
                                },0);
                            });

                if (end == display.length) {
                    columnstotals.map(function(value,index){
                        let n = Math.round((totals[index]+ Number.EPSILON) * 10000) / 10000;
                        $( api.column( value ).footer() ).html(n.toLocaleString("es-AR",{maximumFractionDigits:4}));
                    });
                }else{
                    columnstotals.map(function(value,index){
                        $( api.column( value ).footer() ).html('');
                    });
                }
            }

        }

    });

}