function manage_art(form){

    var loading = true; 

    if(form){

        $('input.form-control').css("border-color", "#ced4da");
        let reg = /^[a-z A-Z áéíóúÁÉÍÓÚÑñäëïöüÄËÏÖÜ 0-9 \. \- _ \\s]{2,60}$/;
        let num = /^[0-9\.]{1,8}$/;
        let email = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/; 
        let re = /(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{4,}/; 

        switch(form.name){
            case 'form_user':
                var formData = {'action':form.name};
                loading = false;
            break;
        }

    }

    proccess_ajax(formData,window.location.origin+'/art/act/act_art',loading).then((obj)=>{
        if(obj.ERROR){
            $('#msg_error').html(obj.ERROR).removeAttr('hidden');
            return false;
        }
        if (obj.hasOwnProperty("form_user")) {
            $('#crea_rol').attr("disabled", "disabled");
            $('#panel > div:first').html(obj.form_user);
            $("#panel").slideDown();

            $("#show_hide_password a").on('click', function(event) {
                event.preventDefault();
                $(this).toggleClass('btn-success btn-danger');
                // $(this).toggleClass('fa-eye fa-eye-slash');
                $("#eyepass").toggleClass('fa-eye fa-eye-slash');
                var input = document.getElementById("pass");
                if (input.type === "password") {
                    input.type = "text";
                } else {
                    input.type = "password";
                }
            });
        }
    });

}