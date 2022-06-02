/*!
    * Start Bootstrap - SB Admin v6.0.1 (https://startbootstrap.com/templates/sb-admin)
    * Copyright 2013-2020 Start Bootstrap
    * Licensed under MIT (https://github.com/StartBootstrap/startbootstrap-sb-admin/blob/master/LICENSE)
    */
(function($) {
    "use strict";

    // Add active state to sidbar nav links
    var path = window.location.href; // because the 'href' property of the DOM element is the absolute path
        $("#layoutSidenav_nav .sb-sidenav a.nav-link").each(function() {
            if (this.href === path) {
                $(this).addClass("active");
            }
        });

    // Toggle the side navigation
    $("#sidebarToggle").on("click", function(e) {
        e.preventDefault();
        $("body").toggleClass("sb-sidenav-toggled");
    });

})(jQuery);


function proccess_modal(body,title,footer,close = true){

    let modal = $('<div class="modal fade" id="event-modal" tabindex="-1"></div>');
    let modal_dialog = $('<div class="modal-dialog"></div>');
    let modal_content = $('<div class="modal-content"></div>');
    let modal_header = $('<div class="modal-header border-bottom-0 d-block bg-light"></div>');
    let modal_header_content = $('<div class="d-flex justify-content-between"></div>');

    let modal_title = (title)?title:'';

    if(modal_title)
        modal_header_content.append('<h5 class="modal-title" style="float: left;">'+modal_title+'</h5>');

    if(close){
        modal_header_content.append('<button type="button" class="btn btn-danger text-white btn-outline-light btn-rounded waves-effect" style="float: right;" onclick="CloseModal();"> <i class="fa fa-times"></i> </button>');
    }


    modal_header.append(modal_header_content);

    let modal_body = $('<div class="modal-body"></div>');
    modal_body.append('<div class="contenido">'+body+'</div>');
    let modal_footer = $('<div class="modal-footer border-0 pt-0 d-block"></div>');
    modal_footer.append(footer);

    modal_content.append(modal_header);
    modal_content.append(modal_body);
    modal_content.append(modal_footer);

    modal_dialog.append(modal_content);

    modal.append(modal_dialog);

    $('#event-modal').remove();

    $('body').append(modal);

    $('#event-modal').modal({
        backdrop: 'static'
    });
}

function CloseModal() {
    $("#event-modal").modal('hide');//ocultamos el modal
    $('body').removeClass('modal-open');//eliminamos la clase del body para poder hacer scroll
    $('.modal-backdrop').remove();//eliminamos el backdrop del modal
    $('#event-modal').remove();
}

function proccess_ajax(data_post, uri, loading = false){

    var path_act;

    if(window.location.hostname == 'test.moob.club'){
        path_act = window.location.origin+'/magenta/v1';
    }else{
        path_act = window.location.origin;
    }    

    if(loading)
        loadingSpinner();
    
    return $.ajax({
        type: 'POST',
        url: uri,
        data: data_post,
        dataType: 'json',
        beforeSend: function(objeto){
            console.log(objeto);
            // $('#calendar').html('<h5 class="lead">Cargando la información de los calendarios, por favor espere....</h5>');
        },
        success:function(response){
            //console.log(response);
           // return response;

        //    if(loading)
        //     setTimeout(function(){loadingSpinner(false);}, 1000);

        },
        error:function(xhr, status, error){
            // console.log(error);
        }
    });
}

function proccess_ajaxfile(data_post, uri, loading = false, titleloading = 'Loading...'){

    if(loading)
        loadingSpinner(true,titleloading);
    
    return $.ajax({
        type: 'POST',
        url: uri,
        cache: false,
        contentType: false,
        processData: false,
        data: data_post,
        dataType: 'json',
        beforeSend: function(obj){
            // console.log(obj);
        },
        success:function(response){
            // console.log(response);
        },
        error:function(xhr, status, error){
            // console.log(error);
        }
    });
}

function loadingSpinner(open = true, title){

    if (open) {
        let msg = (title)?title:'Loading...';
        let loading = '<div class="text-center" ><button class="btn btn-outline-secondary" type="button" onclick="CloseModal()">'+
                            '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>'+
                            '<span class="ml-3 loadmsg">'+msg+'</span>'+
                        '</button></div>';

        proccess_modal(loading,'','',false);

    }else{
        CloseModal();
    }

}
