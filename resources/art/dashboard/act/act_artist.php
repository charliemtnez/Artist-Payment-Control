<?php
use App\TypeUser\ManArtist;
use App\MagImport\MagImport;

use App\ChunkReader\ChunkReadFilter;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Csv;

if(isset($_POST['action'])){

    switch($_POST['action']){

        case 'init':
           /* $response['table_art'] = tableArt();
            if($UserAuth->isAdmin()){
                $response['btn_imp'] = btn_imp();
            }
            
        break;*/

        /*case 'form_art':
            $response['showform']=(isset($_POST['idart']))?form_art($_POST['idart']):form_art();
        break;*/

        /*case 'add_art':
            $response['add_art']=$_POST;
            $objArt = new ManArtist;
            $idart = $objArt->addArt($_POST);
            if($idart){
                $response['add_art']=true;
                if($UserAuth->isAdmin()){
                    $response['btn_imp'] = btn_imp();
                }

            }
        break;*/
        /*case 'edt_art':
            $objArt = new ManArtist;
            $idart = $objArt->updArt($_POST);
            if($idart){
                $response['edt_art']=true;
                if($UserAuth->isAdmin()){
                    $response['btn_imp'] = btn_imp();
                }

            }
        break;*/

        /*case 'view_art':*/

            $objArt = new ManArtist;
            $totals = $objArt->getTotalsArt($_POST['idart']);
            $dataArt = prepareDataArt($objArt->getImpArtbyIdArt($_POST['idart']));

            /* if($UserAuth->isAdmin()){
                $response['btn_imp'] = btn_imp(false,$_POST['idart']);
            } */

            $response['view_art'] = datArt($_POST['idart'],$totals['totals']);

            $response['view_artperiod']['view_arttotalperiod'] = viewtotalperiod($totals['totals'][0]['month'].'-'.$totals['totals'][0]['year'],$totals['yearmonth']);

            $response['view_artperiod']['totalyear'] = reset($totals['yearmonth']);

            // $response['view_artperiod']['dataArt'] = $dataArt;

            $response['view_artperiod']['tableArtTracks'] = tableArtTracks($dataArt[$totals['totals'][0]['year']][$totals['totals'][0]['month']]['tracks'],$dataArt[$totals['totals'][0]['year']][$totals['totals'][0]['month']]['change_usd']);
            $response['view_artperiod']['tableArtRetail'] = tableArtRetail($dataArt[$totals['totals'][0]['year']][$totals['totals'][0]['month']]['retailers'],$dataArt[$totals['totals'][0]['year']][$totals['totals'][0]['month']]['change_usd']);
            $response['view_artperiod']['tableArtCountry'] = tableArtCountry($dataArt[$totals['totals'][0]['year']][$totals['totals'][0]['month']]['countries'],$dataArt[$totals['totals'][0]['year']][$totals['totals'][0]['month']]['change_usd']);

        break;

        case 'view_artperiod':

            $objArt = new ManArtist;
            $totals = $objArt->getTotalsArt($_POST['idart']);
            $dataArt = prepareDataArt($objArt->getImpArtbyIdArt($_POST['idart']));

            $arrayperiod = explode("-",$_POST['period']);

            $response['view_artperiod']['view_arttotalperiod'] = viewtotalperiod($_POST['period'],$totals['yearmonth']);

            $response['view_artperiod']['tableArtTracks'] = tableArtTracks($dataArt[$arrayperiod[1]][$arrayperiod[0]]['tracks'],$dataArt[$arrayperiod[1]][$arrayperiod[0]]['change_usd']);
            $response['view_artperiod']['tableArtRetail'] = tableArtRetail($dataArt[$arrayperiod[1]][$arrayperiod[0]]['retailers'],$dataArt[$arrayperiod[1]][$arrayperiod[0]]['change_usd']);
            $response['view_artperiod']['tableArtCountry'] = tableArtCountry($dataArt[$arrayperiod[1]][$arrayperiod[0]]['countries'],$dataArt[$arrayperiod[1]][$arrayperiod[0]]['change_usd']);

        break;

        default:
        $response = ['ERROR'=>'No existen acciones declaradas'];

    }
    die(json_encode($response,JSON_UNESCAPED_UNICODE | JSON_FORCE_OBJECT));
}else{
    die(json_encode(['ERROR'=>'No existe POST'],JSON_UNESCAPED_UNICODE | JSON_FORCE_OBJECT));
}

/*function btn_imp($init = true, $id='')
{
    $html ='';
    if ($init) {
        $html .= '<h2 class="mt-4">Artistas</h2>';
        $html .='<button type="button" name="crea_rol" id="crea_rol" class="btn btn-info btn-sm" onclick="manage_art({\'name\':\'form_art\'});">Crear artista</button>';
        
    }else{
        $objArt = new ManArtist;
        $art = $objArt->getUserbyId($id);
        $html .= '<h2 class="mt-4">Artista <b>'.$art[0]['name'] .' '. $art[0]['lastname'].'</b></h2>';
        $html .='<button type="button" name="volver" id="volver" class="btn btn-info btn-sm mr-2" onclick="manage_art({\'name\':\'init\'});"><i class="fas fa-arrow-left"></i> Volver</button>';
    }
    return $html;
}*/

function datArt($idart, $totals)
{

    // $acumulusd = array_sum(array_column($totals,'totalusd'));
    // $acumularg = array_sum(array_column($totals,'totalarg'));
    $acumulargartusd = array_sum(array_column($totals,'totalartistusd'));
    $acumulargart = array_sum(array_column($totals,'totalartist'));
    $acumulviews = array_sum(array_column($totals,'totalviews'));

    // $acumulargartusd = $acumulargart/$totals['change_usd'];

    $response = '';

    $response .= '<div class="row">';
    $response .= '  <div class="col-md-6 col-12">';
    $response .=        periodselect($totals, $idart, $totals[0]['month'].'-'.$totals[0]['year']);
    $response .= '      <div id="viewtotalperiod" class="bg-light">';
    $response .= '      </div>';
    $response .= '  </div>';
    $response .= '  <div class="col-md-6 col-12">';
    $response .= '      <p>';
    // $response .= '      Acumulado total hasta el momento (USD): $'.formatoarg($acumulusd).'<br />';
    // $response .= '      Acumulado total hasta el momento (ARG): $'.formatoarg($acumularg).'<br />';
    $response .= '      Acumulado total hasta el momento para el artista (USD): $'.formatoarg($acumulargartusd).'<br />';
    $response .= '      Acumulado total hasta el momento para el artista (ARG): $'.formatoarg($acumulargart).'<br />';
    $response .= '      Acumulado total de vistas: '.$acumulviews;
    $response .= '      </p>';
    $response .= '  </div>';
    $response .= '</div>';
    $response .= '<hr />';
    $response .= artNav();
    

    return $response;

}

function prepareDataArt($data)
{
    $response = [];
    if(is_array($data)){

        $tmpdate = '';
        foreach($data as $v){

            $keys = $v['year'].'-'.$v['month'];

            if($tmpdate == $keys){
                $tmpdate = $keys;
            }else{
                $tmpdate = $keys;
                $tracks = [];
                $retailers = [];
                $countries = [];

                $acumulusd = 0;
                $acumularg = 0;
                $acumulargart = 0;
                $acumulviews = 0;
            }

            $response[$v['year']][$v['month']]=[
                'tracks' => $tracks = array_merge(json_decode($v['data_track'],true),$tracks),
                'retailers' => $retailers = array_merge(json_decode($v['data_retailer'],true),$retailers),
                'countries' => $countries = array_merge(json_decode($v['data_country'],true),$countries),
                'totalusd' => $acumulusd = $acumulusd + $v['total_period'],
                'totalarg' => $acumularg = $acumularg + $v['total_period_arg'],
                'totalartist' => $acumulargart = $acumulargart + $v['total_period_artarg'],
                'totalviews' => $acumulviews = $acumulviews + $v['total_period_views'],
                'change_usd' => $v['change_usd'],
                'year' => $v['year'],
                'month' => $v['month'],
            ];
        }
    }

    return $response;
}

function viewtotalperiod($period,$data)
{
    $arrayperiod = explode("-",$period);
    // $acumulusd=$data[$arrayperiod[1]][$arrayperiod[0]]['totalusd'];
    // $acumularg=$data[$arrayperiod[1]][$arrayperiod[0]]['totalarg'];
    $acumulargartusd=$data[$arrayperiod[1]][$arrayperiod[0]]['totalartistusd'];
    $acumulargart=$data[$arrayperiod[1]][$arrayperiod[0]]['totalartist'];
    $acumulviews=$data[$arrayperiod[1]][$arrayperiod[0]]['totalviews'];

    $html = '  <div>';
    $html .= '      <p>';
    // $html .= '      Total del periodo (USD): $'.formatoarg($acumulusd).'<br />';
    // $html .= '      Total del periodo (ARG): $'.formatoarg($acumularg).'<br />';
    $html .= '      Total del periodo para el artista (USD): $'.formatoarg($acumulargartusd).'<br />';
    $html .= '      Total del periodo para el artista (ARG): $'.formatoarg($acumulargart).'<br />';
    $html .= '      Total de vistas en el Periodo: '.$acumulviews;
    $html .= '      </p>';
    $html .= '  </div>';

    return $html;

}

function periodselect($data, $idart, $period)
{

    $month = ['1'=>'Enero', '2'=>'Febrero', '3'=>'Marzo', '4'=>'Abril', '5'=>'Mayo', '6'=>'Junio', '7'=>'Julio', '8'=>'Agosto', '9'=>'Septiembre', '10'=>'Octubre', '11'=>'Noviembre', '12'=>'Diciembre'];

    if($data){
        $html = '';
        $html .= '<div class="form-group">';
        $html .= '  <label for="allperiod">Periodo:</label>';
        if(is_array($data)){
            $html .= '  <select name="allperiod" id="allperiod"  class="form-control" onchange="manage_art({\'name\':\'view_artperiod\',\'artist\':\''.$idart.'\'})">';
        
            foreach($data as $k => $v){
                $selected = ($v['month'].'-'.$v['year'] == $period)?'selected':'';
                // $aprob = ($v['act_import'] == 0)?' (no aprobado)':'';
                $html .= '<option data-year="'.$v['year'].'" value="'.$v['month'].'-'.$v['year'].'" '.$selected.'>'.$month[(int)$v['month']].'-'.$v['year'].'</option>';
            }

        }
        $html .= '  </select>';
        $html .= '</div>';
        return $html;
    }else{
        return false;
    }
}

function artNav()
{
    $html = '';
    $html .= '<ul class="nav nav-tabs mb-3" role="tablist">';
    $html .= '  <li class="nav-item"><a class="nav-link active" href="#chart" role="tab" data-toggle="tab">Ingresos anuales</a></li>';
    $html .= '  <li class="nav-item"><a class="nav-link" href="#tracks"role="tab" data-toggle="tab">Albun/Tracks</a></li>';
    $html .= '  <li class="nav-item"><a class="nav-link" href="#source"role="tab" data-toggle="tab">Tiendas</a></li>';
    $html .= '  <li class="nav-item"><a class="nav-link" href="#country"role="tab" data-toggle="tab">Países</a></li>';
    $html .= '</ul>';
    $html .= '<div class="tab-content" id="myTabContent">';
    $html .= '  <div class="tab-pane show fade active pt-4 pb-5" id="chart" role="tabpanel" aria-selected="true">';
    $html .= '      <div class="card mb-4">';
    $html .= '          <div class="card-header">';
    $html .= '              <i class="fas fa-chart-area mr-1"></i><sapn id="title_chart">Ingresos en Pesos Argentinos en el año</span>';
    $html .= '          </div>';
    $html .= '          <div class="card-body">';
    $html .= '              <canvas id="myAreaChart" width="100%" height="30"></canvas>';
    $html .= '          </div>';
    $html .= '      </div>';
    $html .= '  </div>';
    $html .= '  <div class="tab-pane fade pt-4 pb-5" id="tracks" role="tabpanel"></div>';
    $html .= '  <div class="tab-pane fade pt-4 pb-5" id="source" role="tabpanel"></div>';
    $html .= '  <div class="tab-pane fade pt-4 pb-5" id="country" role="tabpanel"></div>';
    $html .= '</div>';
    $html .= '';

    return $html;
}

function find_country($countries,$needer)
{
    
    if($k = array_search($needer, array_column($countries, 'en'))){
        return $countries[$k]['alpha_2'];
    }
    if($k = array_search($needer, array_column($countries, 'es'))){
        return $countries[$k]['alpha_2'];
    }
    if($k = array_search($needer, array_column($countries, 'alpha_3'))){
        return $countries[$k]['alpha_2'];
    }
    if($k = array_search($needer, array_column($countries, 'alpha_2'))){
        return $countries[$k]['alpha_2'];
    }

}

/*function form_art($idart = null, $artref =null)
{

    $name_form = ($idart || $idart != 0)?'edt_art':'add_art';
    $readonly = ($idart)?'readonly':'';
    $art = null;
    $imputidart = '';
    $refart = null;

    if($idart || $idart != 0){
        $objArt = new ManArtist;
        $art = $objArt->getUserbyId($idart);
        $imputidart = '<input type="hidden" id="idart" name="idart" class="form-control" value="'.$idart.'" />';
        $refart = $objArt->getRefArt($idart);
    }
    

    $user_usr = ($art)?$art[0]['username']:'';
    $email_usr = ($art)?$art[0]['email']:'';
    $nombre_usr = ($art)?$art[0]['name']:(($artref)?$artref:'');
    $apellido_usr = ($art)?$art[0]['lastname']:'';
    $birthday_usr = ($art)?$art[0]['bod']:'';
    $percent_usr = ($art)?$art[0]['percentart']:'';

    $disabled = 'disabled';
    $checked = '';

    if(!empty(trim($user_usr))){
        $disabled = '';
        $checked = 'checked';
    }

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

    $html .= '<div class="form-row"><div class="col-12"><hr />';
    $html .=                '<div class="form-check form-switch">';
    $html .=                    '<input class="form-check-input" type="checkbox" role="switch" id="canlog" name="canlog" value="canlog" onclick="activatelog(this)" '.$checked.' />';
    $html .=                    '<label class="form-check-label" for="hashead">Puede logearse</label>';
    $html .=                '</div>';
    $html .= '</div></div>';

    $html .= '<div class="form-row">';
    $html .= '  <div class="form-group col-md-6">';
    $html .= '      <label for="email" class="col-form-label">Correo: <i class="text-muted">(*)</i></label>';
    $html .= '      <input type="email" id="email" name="email" class="form-control" placeholder="Entre el correo" value="'.$email_usr.'" '.$disabled.' />';
    $html .= '  </div>';
    $html .= '  <div class="form-group col-md-3">';
    $html .= '      <label for="user" class="col-form-label">Usuario: <i class="text-muted">(*)</i></label>';
    $html .= '      <input type="text" id="user" name="user" class="form-control" placeholder="Entre el nombre de usuario" value="'.$user_usr.'" '.$readonly.' '.$disabled.' />';
    $html .= '  </div>';
    // $html .= '  <div class="form-group col-md-3">';
    // $html .= '      <label for="pass" class="col-form-label">Contraseña: <i class="text-muted">(*)</i></label>';
    // $html .= '      <input type="password" id="pass" name="pass" class="form-control" placeholder="Entre una contraseña" />';
    // $html .= '  </div>';
    $html .= '  <div class="form-group col-md-3">';
    $html .= '      <label for="pass" class="col-form-label">Contraseña: <i class="text-muted">(*)</i></label>';
    $html .= '      <div class="input-group" id="show_hide_password">';
    $html .= '          <input type="password" id="pass" name="pass" class="form-control" placeholder="Entre una contraseña" '.$disabled.' />';
    $html .= '          <div class="input-group-append">';
    $html .= '              <a href="#" class="btn btn-success"><i id="eyepass" class="fa fa-eye-slash" aria-hidden="true"></i></a>';
    $html .= '          </div>';
    $html .= '      </div>';
    $html .= '  </div>';
    $html .= '</div>';

    if($refart){
        $html .= '<hr />';
        $html .= '<div class="form-row">';
        $html .= '  <div class="form-group col-md-6">';
        $html .= '      <h5>Referencias para importar</h5>';
        $html .= '      <ul class="list-group">';
        foreach ($refart as $v) {
            $html .= '  <li class="list-group-item d-flex justify-content-between align-items-center">';
            $html .= $v['artist_import'];
            // $html .= '<button class="btn btn-icon btn-light mr-1" data-art="'.$v['artist_import'].'" data-idref="'.$v['id'].'" onclick="manage_art({\'name\':\'modal_delrefart\',\'info\':$(this)});"><i class="text-danger far fa-trash-alt"></i></button>';
            $html .= '  </li">';
        }
        $html .= '      </ul>';
        $html .= '  </div>';
        $html .= '</div>';
    }


    $html .= '  <div class="form-group mt-3 text-right">';

    if($artref){
        $html .= '  <input type="hidden" id="refart" name="refart" class="form-control" value="'.$artref.'" />';
    }
    $html .= $imputidart; 
    $html .= '      <button type="button" name="cancel_nav" id="cancel_nav" class="btn btn-danger btn-sm" onclick="$(`#panel`).slideUp();$(`html,body`).animate({ scrollTop: $(`body`).offset().top }, `slow`);$(`#crea_rol`).removeAttr(`disabled`);$(`#importxls`).removeAttr(`disabled`);">Cancelar</button>';
    $html .= '      <button type="button" class="btn btn-sm btn-primary" onclick="manage_art(this.form);"> Aceptar </button>';
    $html .= '  </div>';


    $html .= '</form>';

    return $html;
}*/

/*function tableArt()
{
    $html = '<table id="newartist" class="table table-sm table-striped dt-responsive mt-3 mb-4" style="width:100%">';
    $html .= '</table>';

    $objPrevImp = new ManArtist;
    $artists = $objPrevImp->getAllArt();

    $data = [];

    if($artists && is_array($artists)){
        
        for($i=0;$i<count($artists);$i++){
            $data[$i]['nombre'] = '<p>'.$artists[$i]['nombre_usr'].' '.$artists[$i]['apellido_usr'].'</p>';
            $data[$i]['user'] = '<p>'.$artists[$i]['user_usr'].'</p>';
            $data[$i]['email'] = '<p>'.$artists[$i]['email_usr'].'</p>';
            $data[$i]['created'] = '<p>'.$artists[$i]['created'].'</p>';
            $data[$i]['lastaccess'] = '<p>'.date("d/m/Y", strtotime($artists[$i]['lastaccess'])).'</p>';

            $data[$i]['actions_view'] = '<button class="btn btn-icon btn-light mr-1" data-art="'.$artists[$i]['id_usr'].'" onclick="manage_art({\'name\':\'view_art\',\'info\':$(this)});"><i class="far fa-file-excel"></i></button>';
            $data[$i]['actions_edt'] = '<button class="btn btn-icon btn-light mr-1" data-art="'.$artists[$i]['id_usr'].'" onclick="manage_art({\'name\':\'form_art\',\'info\':$(this)});"><i class="far fa-edit"></i></button>';

            // $data[$i]['actions_imp'] = '<button class="btn btn-icon btn-light mr-1" data-art="'.$artists[$i]['artist'].'" onclick="manage_art({\'name\':\'artprevimp\',\'info\':$(this)});"><i class="fas fa-arrow-down"></i></button>';

            // $data[$i]['actions_act'] = ($artists[$i]['act_usr'] == 1)?'<button class="btn btn-icon btn-light" data-user="'.$artists[$i]['id_usr'].'" data-act=0 onclick="manage_art({\'name\':\'activa_art\',\'info\':$(this)});"><i class="text-secondary far fa-eye"></i></button>':'<button class="btn btn-icon btn-light" data-user="'.$artists[$i]['id_usr'].'" data-act="1" onclick="manage_art({\'name\':\'activa_art\',\'info\':$(this)});"><i class="text-danger far fa-eye-slash"></i></button>';

            // $data[$i]['actions_del'] = '<button class="btn btn-icon btn-light mr-1" data-art="'.$artists[$i]['id_user'].'" onclick="manage_art({\'name\':\'modal_delprevart\',\'info\':$(this)});"><i class="text-danger far fa-trash-alt"></i></button>';
            
        }   

    }

    return array(
        'data'=>$data,
        'table'=>$html,
        'type'=>'art'
    );
}*/

function tableArtTracks($tracks, $change_usd /*, $idart,$prevImp = true*/)
{
    $data = [];

    if($tracks && is_array($tracks)){
        
        for($i=0;$i<count($tracks);$i++){
            $data[$i]['title_disk'] = $tracks[$i]['title_disk'];
            $data[$i]['title_track'] = $tracks[$i]['title_track'];
            $data[$i]['type_trans'] = $tracks[$i]['type_trans'];
            $data[$i]['qty'] = $tracks[$i]['qty'];            
            $data[$i]['receipts'] = $tracks[$i]['receipts'];                     
            $data[$i]['total_ar'] = $tracks[$i]['total_ar'];                     
            $data[$i]['totalart_ar'] = $tracks[$i]['totalart_ar'];                     
            $data[$i]['totalart_usd'] = $tracks[$i]['totalart_ar']/$change_usd;                     
        }   

    }

    return array(
        'data'=>$data,
        'table'=>'<table id="trackstable" class="table table-sm table-striped dt-responsive mt-3 mb-4" style="width:100%"></table>',
        'type'=>'tracks'
    );
}

function tableArtRetail($retail, $change_usd)
{
    $data = [];

    if($retail && is_array($retail)){
        
        for($i=0;$i<count($retail);$i++){
            // $data[$i]['year'] = $retail[$i]['year'];
            // $data[$i]['month'] = $retail[$i]['month'];
            $data[$i]['retailer'] = $retail[$i]['retailer'];
            $data[$i]['qty'] = $retail[$i]['qty'];            
            $data[$i]['receipts'] = $retail[$i]['receipts'];  
            $data[$i]['total_ar'] = $retail[$i]['total_ar'];                     
            $data[$i]['totalart_ar'] = $retail[$i]['totalart_ar'];                     
            $data[$i]['totalart_usd'] = $retail[$i]['totalart_ar']/$change_usd;           
        }   

    }

    return array(
        'data'=>$data,
        'table'=>'<table id="retailtable" class="table table-sm table-striped dt-responsive mt-3 mb-4" style="width:100%"></table>',
        'type'=>'retailer'
    );
}

function tableArtCountry($country, $change_usd)
{
    $data = [];

    if($country && is_array($country)){
        
        for($i=0;$i<count($country);$i++){
            // $data[$i]['year'] = $country[$i]['year'];
            // $data[$i]['month'] = $country[$i]['month'];
            $data[$i]['country'] = $country[$i]['country'];
            $data[$i]['qty'] = $country[$i]['qty'];            
            $data[$i]['receipts'] = $country[$i]['receipts'];  
            $data[$i]['total_ar'] = $country[$i]['total_ar'];                     
            $data[$i]['totalart_ar'] = $country[$i]['totalart_ar'];                     
            $data[$i]['totalart_usd'] = $country[$i]['totalart_ar']/$change_usd;           
        }   

    }

    return array(
        'data'=>$data,
        'table'=>'<table id="countrytable" class="table table-sm table-striped dt-responsive mt-3 mb-4" style="width:100%"></table>',
        'type'=>'country'
    );
}

?>