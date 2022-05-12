<?php
use App\TypeUser\TypeArt;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Csv;

if(isset($_POST['action'])){

    switch($_POST['action']){

        case 'form_user':
            $response['form_user']=(isset($_POST['artist']))?form_users($_POST['artist']):form_users();
        break;

        default:
        $response = ['ERROR'=>'No existen acciones declaradas'];

    }
    die(json_encode($response,JSON_UNESCAPED_UNICODE | JSON_FORCE_OBJECT));
}else{
    // header('Location: ' . filter_var(getPathUriVar('URI').'/art', FILTER_SANITIZE_URL));
    die(json_encode(['ERROR'=>'No existe POST'],JSON_UNESCAPED_UNICODE | JSON_FORCE_OBJECT));
}

function form_users($user = null){

    $name_form = ($user)?'edt_user':'add_user';
    $readonly = ($user)?'readonly':'';
    $art = null;
    if($user){
        $art = new TypeArt;
    }
    

    $user_usr = ($user)?$user['user_usr']:'';
    $email_usr = ($user)?$user['email_usr']:'';
    $nombre_usr = ($user)?$user['nombre_usr']:'';
    $apellido_usr = ($user)?$user['apellido_usr']:'';
    $birthday_usr = ($user)?$user['birthday_usr']:'';
    $percent_usr = ($user)?$user['percent_usr']:'';

    $html = '<form name="'.$name_form.'">';

    $html .= '<div class="row mb-3">';

    $html .= '  <div class="col-md-8">';
    $html .= 'Los campos con <code><i class="text-muted">(*)</i></code> son obligatorios.';
    $html .= '      <div id="act_msg"></div>';
    $html .= '      <input type="hidden" id="art" name="type" value="art" />';
    $html .= '  </div>';
    $html .= '</div>';

    $html .= '<div class="form-row">';
    $html .= '  <div class="form-group col-md-4">';
    $html .= '      <label for="nombre" class="col-form-label">Nombre: <i class="text-muted">(*)</i></label>';
    $html .= '      <input type="text" id="nombre" name="nombre" class="form-control" placeholder="Entre el nombre" value="'.$nombre_usr.'" />';
    $html .= '  </div>';
    $html .= '  <div class="form-group col-md-4">';
    $html .= '      <label for="apellido" class="col-form-label">Apellido:</label>';
    $html .= '      <input type="text" id="apellido" name="apellido" class="form-control" placeholder="Entre el apellido" value="'.$apellido_usr.'" />';
    $html .= '  </div>';
    $html .= '  <div class="form-group col-md-2">';
    $html .= '      <label for="percent" class="col-form-label">Porciento aplicado: <i class="text-muted">(*)</i></label>';
    $html .= '      <input type="numeric" id="percent" name="percent" class="form-control" placeholder="0" value="'.$percent_usr.'" />';
    $html .= '  </div>';
    $html .= '  <div class="form-group col-md-2">';
    $html .= '      <label for="birthday" class="col-form-label">Fecha Nacimiento:</label>';
    $html .= '      <input type="date" id="birthday" name="birthday" class="form-control" placeholder="Entre el correo" value="'.$birthday_usr.'" />';
    $html .= '  </div>';
    $html .= '</div>';

    $html .= '<div class="form-row">';
    $html .= '  <div class="form-group col-md-6">';
    $html .= '      <label for="email" class="col-form-label">Correo: <i class="text-muted">(*)</i></label>';
    $html .= '      <input type="email" id="email" name="email" class="form-control" placeholder="Entre el correo" value="'.$email_usr.'" />';
    $html .= '  </div>';
    $html .= '  <div class="form-group col-md-3">';
    $html .= '      <label for="user" class="col-form-label">Usuario: <i class="text-muted">(*)</i></label>';
    $html .= '      <input type="text" id="user" name="user" class="form-control" placeholder="Entre el nombre de usuario" value="'.$user_usr.'" '.$readonly.'/>';
    $html .= '  </div>';
    // $html .= '  <div class="form-group col-md-3">';
    // $html .= '      <label for="pass" class="col-form-label">Contraseña: <i class="text-muted">(*)</i></label>';
    // $html .= '      <input type="password" id="pass" name="pass" class="form-control" placeholder="Entre una contraseña" />';
    // $html .= '  </div>';
    $html .= '  <div class="form-group col-md-3">';
    $html .= '      <label for="pass" class="col-form-label">Contraseña: <i class="text-muted">(*)</i></label>';
    $html .= '      <div class="input-group" id="show_hide_password">';
    $html .= '          <input type="password" id="pass" name="pass" class="form-control" placeholder="Entre una contraseña" />';
    $html .= '          <div class="input-group-append">';
    $html .= '              <a href="#" class="btn btn-success"><i id="eyepass" class="fa fa-eye-slash" aria-hidden="true"></i></a>';
    $html .= '          </div>';
    $html .= '      </div>';
    $html .= '  </div>';
    $html .= '</div>';

    $html .= '<div class="form-group mt-3 text-right">';
    
    $html .= '  <button type="button" name="cancel_nav" id="cancel_nav" class="btn btn-danger btn-sm" onclick="$(`#panel`).slideUp();$(`html,body`).animate({ scrollTop: $(`body`).offset().top }, `slow`);$(`#crea_rol`).removeAttr(`disabled`);">Cancelar</button>';
    $html .= '  <button type="button" class="btn btn-sm btn-primary" onclick="manage_art(this.form);"> Aceptar </button>';
    $html .= '</div>';
    $html .= '</form>';

    return $html;
}

function showform_import(){
    $html = '';
    $html .= '<form class="form-row" id="imp_data" name="imp_data">';
    $html .=         '<div class="form-group col-sm-6 col-md-4 col-lg-3">';
    // $html .=         '<div class="d-flex">';

    // $html .=         '<div class="form-group col-12 col-sm-6 mr-3">';

    // $html .= '  <div class="form-group col-md-2">';
    $html .= '      <label for="change_usd" class="col-form-label">Valor del Cambio USD: ';
    $html .= '      <input type="numeric" id="change_usd" name="change_usd" class="form-control" placeholder="$0" />';
    $html .= '  <hr />';
    // $html .= '  </div>';

    $html .=             '<div id="datepicker" data-date=""></div>';
    $html .=             '<input type="hidden" id="period" name="period">';
    $html .=         '</div>';
    // $html .=         '<div class="form-group col-sm-6 col-md-6 mt-1">';
    $html .=         '<div class="form-group col-12 col-sm-6 mt-1">';
    $html .=             '<div class="custom-file mb-2">';
    $html .=                 '<input type="file" class="custom-file-input" id="channel" name="channel" accept=".xls,.xlsx, .csv" required>';
    $html .=                 '<label class="custom-file-label" for="channel">XLS Canales</label>';
    $html .=             '</div>';
    $html .=             '<div class="custom-file mb-2">';
    $html .=                 '<input type="file" class="custom-file-input" id="country" name="country" accept=".xls,.xlsx, .csv" required>';
    $html .=                 '<label class="custom-file-label" for="country">XLS Paises</label>';
    $html .=             '</div>';
    $html .=             '<div class="custom-file mb-2">';
    $html .=                 '<input type="file" class="custom-file-input" id="product" name="product" accept=".xls,.xlsx, .csv" required>';
    $html .=                 '<label class="custom-file-label" for="product">XLS Productos</label>';
    $html .=             '</div>';
    $html .=             '<div class="custom-file mb-2">';
    $html .=                 '<input type="file" class="custom-file-input" id="source" name="source" accept=".xls,.xlsx, .csv" required>';
    $html .=                 '<label class="custom-file-label" for="source">XLS Fuentes</label>';
    $html .=             '</div>';
    $html .=             '<div class="custom-file mb-2">';
    $html .=                 '<input type="file" class="custom-file-input" id="track" name="track" accept=".xls,.xlsx, .csv" required>';
    $html .=                 '<label class="custom-file-label" for="track">XLS Tracks</label>';
    $html .=             '</div>';
    $html .=         '</div>';

    // $html .=         '</div>';

    $html .=         '<div class="form-group col-12 mt-3 text-right">';
    $html .=             '<input type="hidden" id="artista" name="artista" value ="">';
    $html .=             '<button type="button" name="cancel_nav" id="cancel_nav" class="btn btn-danger btn-sm mr-2" onclick="$(`#panel`).slideUp();$(`html,body`).animate({ scrollTop: $(`body`).offset().top }, `slow`);$(`#import_xls`).removeAttr(`disabled`);">Cancelar</button>';
    $html .=             '<button type="button" class="btn btn-sm btn-primary" onclick="manage_art(this.form);"> Aceptar </button>';

    $html .=         '</div>';
    $html .=     '</form>';

    return $html;
}

?>