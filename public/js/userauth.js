function userauth(form){
    "use strict";

    $('#msg_error').attr('hidden',true);

    let user = $.trim(form.user.value);
    let pass = $.trim(form.password.value);
    let remenberme = form.remenberme.checked;

    if (!user){
        $('#msg_error').html('Debe indicar un usuario').removeAttr('hidden');
        return false;
    }
    if (!pass){
        $('#msg_error').html('Debe colocar la contraseña').removeAttr('hidden');
        return false;
    }
    
    let p = hex_sha512(pass);

    let datapost = {'user':user,'pass':p,'act':'login','remenberme':remenberme};

    proccess_ajax(datapost,form.action,true).then((obj)=>{
        if(obj.ERROR){
            $('#msg_error').html(obj.ERROR).removeAttr('hidden');
            return false;
        }

        if(obj.status === 'OK'){
            location.href = window.location.origin;
        }
            
    });

}

function show_pass(obj){
    let btn = $(obj);

    if(btn.data('type') == 'hide'){
        btn.removeClass('text-danger');
        btn.html('<i class="fa fa-eye" ></i>');
        btn.data('type','show');
        $('#password').attr('type','password');
        $('#re_passw').attr('type','password');
    }else if(confirm('Mostrara la contraseña. Por favor tenga cuidado.') && btn.data('type') == 'show'){
        btn.html('<i class="fa fa-eye-slash" ></i>');
        btn.addClass('text-danger');
        btn.data('type', 'hide');
        $('#password').attr('type','text');
        $('#re_passw').attr('type','text');
    }

}