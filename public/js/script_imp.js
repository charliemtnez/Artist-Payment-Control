function activatelog(chk){
    if($(chk).is(":checked")){
        $('#email').attr('disabled',false);
        $('#user').attr('disabled',false);
        $('#pass').attr('disabled',false);
    }else if($(chk).is(":not(:checked)")){
        $('#email').attr('disabled',true);
        $('#user').attr('disabled',true);
        $('#pass').attr('disabled',true);
    }
}

function manage_imp(form){

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

            case 'add_art':
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

            case 'asociateart':
                var asoci = $('select[name=artref] option').filter(':selected').val();
                var art = form.info.data('art');
                var formData = new FormData();
                formData.append('action',form.name);
                formData.append('idartasoc',asoci);
                formData.append('artref',art);
                titleloading = 'Asociando artrista...';
            break;

            case 'modal_delprevart':

                btn_ok = '<button type="button" data-art="'+form.info.data('art')+'" onclick="manage_imp({\'name\':\'del_prevartimp\',\'info\':$(this)})" class="btn btn-success">Confirmar</button>';
                btn_cancel = '<button type="button" onclick="CloseModal()" class="btn btn-danger" style="margin-right:10px;">Cancelar</button>';
                footer = '<div class="form-group text-right">'+btn_cancel+btn_ok+'</div>';
                proccess_modal('<div class="alert alert-danger" role="alert">Está a punto de borrar el artista '+form.info.data('art')+'.¿Confirma esta acción?</div>','Borrar Artista',footer,true);
                return false;

            break;
            case 'modaldelallimp':

                btn_ok = '<button type="button" data-art="all" onclick="manage_imp({\'name\':\'del_allprevartimp\',\'info\':$(this)})" class="btn btn-success">Confirmar</button>';
                btn_cancel = '<button type="button" onclick="CloseModal()" class="btn btn-danger" style="margin-right:10px;">Cancelar</button>';
                footer = '<div class="form-group text-right">'+btn_cancel+btn_ok+'</div>';
                proccess_modal('<div class="alert alert-danger" role="alert">Está a punto de borrar toda la información previa a importar.¿Confirma esta acción?</div>','Borrar Información',footer,true);
                return false;

            break;

            case 'del_prevartimp':
            case 'del_allprevartimp':

                var formData = new FormData();
                formData.append('action',form.name);
                formData.append('art',form.info.data('art'));
                titleloading = 'Borrando Artista...';

            break;

            case 'imp_data':
                if(!num.test(form.change_usd.value.trim())) { 
                    form.change_usd.focus();
                    $('#'+form.change_usd.id).css("border-color", "#dc3545");
                    proccess_modal('<div class="alert alert-danger" role="alert">Es necesario que indique el valor del cambio USD/ARG con el cual se hará la entrada de los datos.</div>','Error en el Campo Valor del Cambio USD');
                    return false; 
                }
                if(!form.importfile.value.trim()) { 
                    proccess_modal('<div class="alert alert-danger" role="alert">Es Necesario que seleccione el fichero a importar</div>','Error para Importar');
                    return false; 
                }

                loading = true;
                var formData = new FormData(form);
                formData.append('action',form.name);

            break;
            case 'artprevimp':
                var formData = new FormData();
                formData.append('action',form.name);
                formData.append('artist',form.info.data('art'));
                titleloading = 'Cargando Artista antes de importar...';
            break;
            case 'imp_artinfo':
                var formData = new FormData();
                formData.append('action',form.name);
                formData.append('artref',form.info.data('art'));
                formData.append('idartref',form.info.data('idartref'));
                titleloading = 'Importando información del Artista...';
            break;
            case 'impArtLote':
                var formData = new FormData();
                formData.append('action',form.name);
                titleloading = 'Importando información de los artistas...';
            break;
            default:
                return false;
            
        }

    }

    proccess_ajaxfile(formData,window.location.origin+'/imp/act/act_imp',loading, titleloading).then((obj)=>{

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

        if (obj.hasOwnProperty("imp_file")) {
            
            manage_imp({'name':'init'});

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

        if (obj.hasOwnProperty("table_prevartimp")) {
            
            $('#artistas').html(obj.table_prevartimp.table);

            $('#newartist').DataTable({
                destroy: true,
                order: [],
                responsive: true,
                processing: true,
                lengthChange: false,
                pageLength: 25,
                language:{"url":'js/datatable_spanish.json'},
                data:Object.values(obj.table_prevartimp.data),
                columns: [
                    { title: "Artista",data:"nombre"},
                    { title: "Tracks",data:"tracks"},
                    { title: "Vistas",data:"vistas"},
                    { title: "Recibido",data:"monto"},
                    // { title: "",data:"actions_edt", orderable: false },
                    { title: "",data:"actions_imp", orderable: false },
                    { title: "",data:"actions_del", orderable: false }
                ]
            });

        }

        if (obj.hasOwnProperty("tableArtTracks")) {
            
            $('#tracks').html(obj.tableArtTracks.table);

            $('#trackstable').DataTable({
                destroy: true,
                order: [],
                responsive: true,
                processing: true,
                lengthChange: false,
                pageLength: 25,
                language:{"url":'js/datatable_spanish.json'},
                data:Object.values(obj.tableArtTracks.data),
                columns: [
                    // { title: "Año",data:"year"},
                    // { title: "Mes",data:"month"},
                    { title: "Discos",data:"title_disk"},
                    { title: "Tracks",data:"title_track"},
                    { title: "Tipo Trans",data:"type_trans"},
                    { title: "Vistas",data:"qty"},
                    { title: "Recibido",data:"receipts"}
                ]
            });

        }

        if (obj.hasOwnProperty("tableArtRetail")) {
            
            $('#source').html(obj.tableArtRetail.table);

            $('#retailtable').DataTable({
                destroy: true,
                order: [],
                responsive: true,
                processing: true,
                lengthChange: false,
                pageLength: 25,
                language:{"url":'js/datatable_spanish.json'},
                data:Object.values(obj.tableArtRetail.data),
                columns: [
                    // { title: "Año",data:"year"},
                    // { title: "Mes",data:"month"},
                    { title: "Tiendas",data:"retailer"},
                    { title: "Vistas",data:"qty"},
                    { title: "Recibido",data:"receipts"}
                ]
            });

        }

        if (obj.hasOwnProperty("tableArtCountry")) {
            
            $('#country').html(obj.tableArtCountry.table);

            $('#countrytable').DataTable({
                destroy: true,
                order: [],
                responsive: true,
                processing: true,
                lengthChange: false,
                pageLength: 25,
                language:{"url":'js/datatable_spanish.json'},
                data:Object.values(obj.tableArtCountry.data),
                columns: [
                    // { title: "Año",data:"year"},
                    // { title: "Mes",data:"month"},
                    { title: "Paises",data:"country"},
                    { title: "Vistas",data:"qty"},
                    { title: "Recibido",data:"receipts"}
                ]
            });

        }

    });

}