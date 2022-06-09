<?php
use App\TypeUser\ManArtist;
use App\MagImport\MagImport;

use App\ChunkReader\ChunkReadFilter;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Csv;

if(isset($_POST['action'])){

    switch($_POST['action']){

        case 'init':
            $response['table_prevartimp'] = tableArtprevImp();
            if($UserAuth->isAdmin()){
                $response['btn_imp'] = btn_imp();
            }
            
        break;

        case 'form_art':
            $response['showform']=(isset($_POST['idart']))?form_artref($_POST['idart']):form_artref();
        break;

        case 'artprevimp':
            if($UserAuth->isAdmin()){
                $response['btn_imp'] = btn_imp(false);
            }
            $response['artprevimp'] = artprevimp($_POST['artist']);
            $response['tableArtTracks'] = tableArtTracks($_POST['artist']);
            $response['tableArtRetail'] = tableArtRetail($_POST['artist']);
            $response['tableArtCountry'] = tableArtCountry($_POST['artist']);
        break;

        case 'asociateart':

            if($UserAuth->isAdmin()){
                $response['btn_imp'] = btn_imp(false);
            }

            if($_POST['idartasoc'] == 0){
                $response['showform']=form_artref(0,$_POST['artref']);
            }else{

                if(artAsociate($_POST['idartasoc'],$_POST['artref'])){

                    $response['artprevimp'] = artprevimp($_POST['artref']);
                    $response['tableArtTracks'] = tableArtTracks($_POST['artref']);
                    $response['tableArtRetail'] = tableArtRetail($_POST['artref']);
                    $response['tableArtCountry'] = tableArtCountry($_POST['artref']);

                }

            }


        break;

        case 'add_art':
            $response['add_art']=$_POST;
            $objArt = new ManArtist;
            $idart = $objArt->addArt($_POST);
            if($idart){

                if($UserAuth->isAdmin()){
                    $response['btn_imp'] = btn_imp(false);
                }

                if(artAsociate($idart,$_POST['refart'])){

                    $response['artprevimp'] = artprevimp($_POST['refart']);
                    $response['tableArtTracks'] = tableArtTracks($_POST['refart']);
                    $response['tableArtRetail'] = tableArtRetail($_POST['refart']);
                    $response['tableArtCountry'] = tableArtCountry($_POST['refart']);

                }
            }
        break;
        case 'edt_art':
            $objArt = new ManArtist;
            $idart = $objArt->updArt($_POST);
            if($idart){

                if($UserAuth->isAdmin()){
                    $response['btn_imp'] = btn_imp(false);
                }

                if(artAsociate($idart,$_POST['refart'])){

                    $response['artprevimp'] = artprevimp($_POST['refart']);
                    $response['tableArtTracks'] = tableArtTracks($_POST['refart']);
                    $response['tableArtRetail'] = tableArtRetail($_POST['refart']);
                    $response['tableArtCountry'] = tableArtCountry($_POST['refart']);

                }
            }
        break;

        case 'showform_import':
            $response['showform']=showform_import();
        break;

        case 'del_prevartimp':

            $tmpArt = new MagImport;
            if($tmpArt->delArtImport($_POST['art'])){
                $response['del_prevartimp']=['status'=>'OK'];
            }else{
                $response['ERROR']='Hubo problemas para borrar el artista '.$_POST['art']. '. '.$this->get_error();
            }
            
        break;

        case 'del_allprevartimp':

            $tmpArt = new MagImport;
            if($tmpArt->delAllArtImport()){
                $response['del_allprevartimp']=['status'=>'OK'];
            }else{
                $response['ERROR']='Hubo problemas para borrar la información. '.$this->get_error();
            }
            
        break;

        case 'imp_data':

            $allowedFileType = [
                'text/csv',
                'text/comma-separated-values',
                'text/plain',
                'application/vnd.ms-excel',
                'application/msexcel',
                'application/x-msexcel',
                'application/x-ms-excel',
                'application/x-excel',
                'application/x-dos_ms_excel',
                'application/xls',
                'application/x-xls',
                'application/octet-stream',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/xlsx'
            ];

            if(in_array($_FILES['importfile']["type"],$allowedFileType)){

                $response['imp_file'] = [];

                $targetPath = getPathUriVar('PATH_RESOURCES').'panel/art/tmpupload/'.$_FILES['importfile']["name"];
                move_uploaded_file($_FILES['importfile']['tmp_name'], $targetPath);

                $tmpImport = new MagImport;

                $fileimported = $tmpImport->getFileImport($_FILES['importfile']["name"]);

                if(!$fileimported){
                    $countries = $tmpImport->getAllCountries();

                    $success = file_get_contents_chunked(
                        $_POST['typexls'], 
                        $targetPath, 2048,
                        $countries,$_POST['change_usd'],
                        $tmpImport, 
                        function ($chunk, &$handle, &$countries, &$i, &$count, &$queryValuePrefix, $type, &$usd_ar, &$tmpImport) 
                        {
                        
                        $lineArray = preg_replace('/("\d+),(\d+")/','$1.$2',$chunk);
                        $lineArray = str_replace('"','',$lineArray);
                        $lineArray = explode(',',$lineArray);

                        $lineImport = [];

                        if($type === 'orchad'){
                            if(!empty($lineArray[25]) && ((float)$lineArray[25] != 0 && (int)$lineArray[21] != 0 && !empty(trim($lineArray[12])))){

                                $lineImport = [
                                    'year'=>(int)substr($lineArray[0],0,4),
                                    'month'=>(int)substr($lineArray[0],5,2),
                                    'retailer'=>$lineArray[2],
                                    'territory'=>find_country($countries,$lineArray[3]),
                                    'label'=>$lineArray[8],
                                    'title_disk'=>$lineArray[10],
                                    'title_track'=>$lineArray[11],
                                    'artist'=>$lineArray[12],
                                    'type_trans'=>$lineArray[17],
                                    'quantity'=>(int)$lineArray[21],
                                    'receipts'=>$lineArray[25],
                                    'percentbytrack'=>0,
                                    'usd_ar'=>$usd_ar,
                                ];

                            }
                        }else if($type === 'youtube'){
                            if(!empty($lineArray[26]) && ((float)$lineArray[26] != 0 && (int)$lineArray[17] != 0 && !empty(trim($lineArray[11])))){

                                $lineImport = [
                                    'year'=>(int)substr($lineArray[1],0,4),
                                    'month'=>(int)substr($lineArray[1],4,2),
                                    'retailer'=>'YouTube',
                                    'territory'=>find_country($countries,$lineArray[2]),
                                    'label'=>$lineArray[13],
                                    'title_disk'=>$lineArray[12],
                                    'title_track'=>$lineArray[4],
                                    'artist'=>$lineArray[11],
                                    'type_trans'=>$lineArray[6],
                                    'quantity'=>(int)$lineArray[17],
                                    'receipts'=>$lineArray[26],
                                    'percentbytrack'=>0,
                                    'usd_ar'=>$usd_ar,
                                ];

                            }
                        }                        

                        if(!empty($lineImport)){
                            // $save = $tmpImport->addTmpImport($lineImport);
                            // if(!$save){
                            //     return false;
                            // }

                            $queryValuePrefix = $queryValuePrefix.'("'.implode('","',$lineImport).'")';

                            if($count == 1000){
                                $save = $tmpImport->addTmpImportEXP($queryValuePrefix);
                                $count = 0;
                                $queryValuePrefix = '';
                            }

                            if($count > 0){
                                $queryValuePrefix .= ',';
                            }

                            $i++;
                            $count++;
                        }
                        
                    });

                    if($success){

                        if(!empty(trim($success['query']))){
                            $query = trim($success['query'],',');
                            $tmpImport->addTmpImportEXP($query);
                        }

                        $fimp = [
                            'name_file'=>$_FILES['importfile']["name"],
                            'date_imp'=>date("Y-m-d H:i:s"),
                            'user_imp'=>$UserAuth->getFullName(),
                            'usd_ar'=>$_POST['change_usd']
                        ];

                        $idimpfile = $tmpImport->addFileImport($fimp);

                        $response = ['imp_file'=>$idimpfile, 'total_rows'=>$success['totalrows']];

                    }else{
                        $response = ['ERROR'=>'Hubo un problema al importar','msgerror'=>$tmpImport->get_error()];
                    }

                    unlink($targetPath);

                }else{
                    $response = ['ERROR'=>'Ya fue importado el archivo '.$_FILES['importfile']["name"]. ' el día '.$fileimported[0]['date_imp']];
                }
              
            }else{
                $response = ['ERROR'=>'El tipo de archivo no es el adecuado.'];
            }

        break;

        case 'imp_artinfo':
            $imp = impArtInfo($_POST,$UserAuth->getId());
            if($imp['success'] == 'OK'){
                $response['impArtInfo'] = ['success'=>'OK'];
            }else{
                $response = ['ERROR'=>'Existe un error al importar la información. '.$imp['msgerror']];
            }

        break;

        case 'impArtLote':
            $imp = impArtLote($UserAuth->getId());
            if($imp['success'] == 'OK'){
                $response['impArtLote'] = responseImplote($imp);
            }else{
                $response = ['ERROR'=>'Existe un error al importar la información. '.$imp['msgerror']];
            }
        break;

        default:
        $response = ['ERROR'=>'No existen acciones declaradas'];

    }
    die(json_encode($response,JSON_UNESCAPED_UNICODE | JSON_FORCE_OBJECT));
}else{
    die(json_encode(['ERROR'=>'No existe POST'],JSON_UNESCAPED_UNICODE | JSON_FORCE_OBJECT));
}

function responseImplote($data)
{
 
    $html = '';
    $html .= '<section>';
    if(!empty($data['artsuccess'])){
        $html .= '<div class="">';
        $html .= '  <h6 class="bg-success p-2 text-white">Se logró importar '.count($data['artsuccess']).' artistas:</h6>';
        $html .= '  <ul>';
        foreach($data['artsuccess'] as $v){
            $html .= '<li>'.$v['refimp'].'</li>';
        }
        $html .= '  </ul>';
        $html .= '</div>';
    }else{
        $html .= '<div class="">';
        $html .= '  <h6 class="p-2">NO se importaron Artistas.</h6>';
        $html .= '</div>';
    }
    $html .= '<hr />';
    if(!empty($data['artfailed'])){
        $html .= '<div class="">';
        $html .= '  <h6 class="bg-danger p-2 text-white">NO se logró importar '.count($data['artfailed']).' artistas:</h6>';
        $html .= '  <ul>';
        foreach($data['artfailed'] as $v){
            $html .= '<li>'.$v['refimp'].'</li>';
        }
        $html .= '  </ul>';
        $html .= '</div>';
    }else{
        $html .= '<div class="">';
        $html .= '  <h6 class="p-2">NO hubo errores al importar.</h6>';
        $html .= '</div>';
    }
    $html .= '</section>';

    return $html;
}

function impArtLote($user_id)
{
    $objArt = new ManArtist;
    $objPrevArt = new MagImport;

    $artref = $objArt->getRefArt();
    if($artref){
        $failed=[];
        $success=[];
        foreach($artref as $v){
            $data = [
                'artref'=>$v['artist_import'],
                'idartref'=>$v['id_user']
            ];
            $imp = impArtInfo($data,$user_id);
            if(!empty($imp) && $imp['success'] == 'NOK'){
                $failed[] = ['id_art'=>$v['id_user'],'refimp'=>$v['artist_import']];
            }elseif(!empty($imp) && $imp['success'] == 'OK'){
                $success[] = ['id_art'=>$v['id_user'],'refimp'=>$v['artist_import']];
            }
        }
        return ['success'=>'OK','artsuccess'=>$success,'artfailed'=>$failed];
    }
    return ['success'=>'NOK','msgerror'=>$objPrevArt->get_error()];
}

function impArtInfo($data,$userid)
{
    $objArt = new ManArtist;
    $objPrevArt = new MagImport;

    $art = $objArt->getUserbyId($data['idartref']);

    $percentArt = ($art)?$art[0]['percentart'] :0;

    $imp = $objPrevArt->addImpDatArt($data['artref'],$data['idartref'],$percentArt,$userid);

    return ($imp)?$imp:false;

}

function artAsociate($idart, $artref)
{
    $objRef = new MagImport;
    return $objRef->addRefImp($idart,$artref);
}

function btn_imp($init = true)
{
    $html ='';
    if ($init) {
            
        $html .='<button type="button" name="importxls" id="importxls" class="btn btn-info btn-sm" onclick="manage_imp({\'name\':\'showform_import\'});"><i class="fas fa-arrow-down"></i> Importar Fichero</button>';
        $html .='<button type="button" name="importart" id="importart" class="btn btn-success ml-2 btn-sm" onclick="manage_imp({\'name\':\'impArtLote\'});"><i class="fas fa-arrow-down"></i> Importar Artistas</button>';
        $html .='<button type="button" name="modaldelallimp" id="modaldelallimp" class="btn btn-danger btn-sm ml-2" onclick="manage_imp({\'name\':\'modaldelallimp\'});"><i class="far fa-trash-alt"></i> Borrar Todo</button>';
        
    }else{
        $html .='<button type="button" name="volver" id="volver" class="btn btn-info btn-sm mr-2" onclick="manage_imp({\'name\':\'init\'});"><i class="fas fa-arrow-left"></i> Volver</button>';
        //$html .='<button type="button" name="crea_rol" id="crea_rol" class="btn btn-info btn-sm" onclick="manage_imp({\'name\':\'form_user\'});">Crear artista</button>';
    }
    return $html;
}

function artprevimp($art)
{
    $objPrevImp = new MagImport;
    $objArt = new ManArtist;
    $artref = $objArt->hasArtRef($art);

    if(!empty($artref)){
        $response = $art;
    }else{
        $response = '<span class="badge bg-danger">Nuevo</span> '.$art;
        $selectRef  = '<div class="input-group">';
        $selectRef  .= '<select name = "artref" class="form-select form-select-sm" aria-label=".form-select-sm">';
        $selectRef .= '<option value = "0" selected>Nuevo Artista</option>';
        $allart = $objArt->getAllArt();

        if($allart){
            foreach($allart as $v){
                $selectRef .= '<option value="'.$v['id_usr'].'">'.$v['nombre_usr'].'</option>';
            }
        }
        
        $selectRef  .= '</select>';
        $selectRef  .= '<button class="btn btn-outline-secondary" type="button" onclick="manage_imp({\'name\':\'asociateart\',\'info\':$(this)})" data-art="'.$art.'" >Asociar</button>';
        $selectRef  .= '</div>';
    }

    $response .= '<button class="btn btn-icon btn-light ml-1" data-art="'.$art.'" onclick="manage_imp({\'name\':\'modal_delprevart\',\'info\':$(this)});"><i class="text-danger far fa-trash-alt"></i></button>';

    $response .= '<hr />';

    $response .= (!empty($artref))?'Asociado con '.$artref['name']:'Asociar con: '. $selectRef;

    if (!empty($artref)) {
        // $response .= '<button class="btn btn-icon btn-light ml-2" data-art="'.$artref['id'].'" onclick="manage_imp({\'name\':\'form_art\',\'info\':$(this)});"><i class="far fa-edit"></i></button>';
        $response .= '<button class="btn btn-icon btn-success ml-1" data-idartref="'.$artref['id'].'" data-art="'.$art.'" onclick="manage_imp({\'name\':\'imp_artinfo\',\'info\':$(this)});"><i class="fas fa-arrow-down"></i></button>';
    }

    $response .= '<hr />';
    $response .= artNav();
    

    return $response;

}

function artNav()
{
    $html = '';
    $html .= '<ul class="nav nav-tabs mb-3" role="tablist">';
    $html .= '  <li class="nav-item"><a class="nav-link" href="#tracks"role="tab" data-toggle="tab">Albun/Tracks</a></li>';
    $html .= '  <li class="nav-item"><a class="nav-link" href="#source"role="tab" data-toggle="tab">Tiendas</a></li>';
    $html .= '  <li class="nav-item"><a class="nav-link" href="#country"role="tab" data-toggle="tab">Países</a></li>';
    $html .= '</ul>';
    $html .= '<div class="tab-content" id="myTabContent">';
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

function file_get_contents_chunked($type, $file, $chunk_size,$countries,&$usd_ar,&$tmpImport,$callback)
{
    try {
        $handle = fopen($file, "r");
        $i = 0;
        $count = 1;
        $queryValuePrefix = '';

        while (! feof($handle)) {
            call_user_func_array($callback, array(
                // fread($handle, $chunk_size),
                fgets($handle,$chunk_size),
                // fgetcsv($handle,$chunk_size),
                &$handle,
                &$countries,
                &$i,
                &$count,
                &$queryValuePrefix,
                $type,
                &$usd_ar,
                &$tmpImport
            ));
            // $i ++;
        }
        fclose($handle);
    } catch (Exception $e) {
        trigger_error("file_get_contents_chunked::" . $e->getMessage(), E_USER_NOTICE);
        return false;
    }

    return ['totalrows'=>$i,'query'=>&$queryValuePrefix];
}

function form_artref($idart = null, $artref =null)
{

    $name_form = ($idart || $idart != 0)?'edt_art':'add_art';
    $readonly = ($idart)?'readonly':'';
    $art = null;

    if($idart || $idart != 0){
        $objArt = new ManArtist;
        $art = $objArt->getUserbyId($idart);

    }
    

    $user_usr = ($art)?$art[0]['username']:'';
    $email_usr = ($art)?$art[0]['email']:'';
    $nombre_usr = ($art)?$art[0]['name']:(($artref)?$artref:'');
    $apellido_usr = ($art)?$art[0]['lastname']:'';
    $birthday_usr = ($art)?$art[0]['bod']:'';
    $percent_usr = ($art)?$art[0]['percentart']:'';

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
    // $html .= '  <div class="form-group col-md-2">';
    // $html .= '      <label for="birthday" class="col-form-label">Fecha Nacimiento:</label>';
    // $html .= '      <input type="date" id="birthday" name="birthday" class="form-control" placeholder="Entre el correo" value="'.$birthday_usr.'" />';
    // $html .= '  </div>';
    $html .= '</div>';

    $html .= '<div class="form-row"><div class="col-12"><hr />';
    $html .=                '<div class="form-check form-switch">';
    $html .=                    '<input class="form-check-input" type="checkbox" role="switch" id="canlog" name="canlog" value="canlog" onclick="activatelog(this)" />';
    $html .=                    '<label class="form-check-label" for="hashead">Puede logearse</label>';
    $html .=                '</div>';
    $html .= '</div></div>';

    $html .= '<div class="form-row">';
    $html .= '  <div class="form-group col-md-6">';
    $html .= '      <label for="email" class="col-form-label">Correo: <i class="text-muted">(*)</i></label>';
    $html .= '      <input type="email" id="email" name="email" class="form-control" placeholder="Entre el correo" value="'.$email_usr.'" disabled />';
    $html .= '  </div>';
    $html .= '  <div class="form-group col-md-3">';
    $html .= '      <label for="user" class="col-form-label">Usuario: <i class="text-muted">(*)</i></label>';
    $html .= '      <input type="text" id="user" name="user" class="form-control" placeholder="Entre el nombre de usuario" value="'.$user_usr.'" '.$readonly.' disabled />';
    $html .= '  </div>';
    // $html .= '  <div class="form-group col-md-3">';
    // $html .= '      <label for="pass" class="col-form-label">Contraseña: <i class="text-muted">(*)</i></label>';
    // $html .= '      <input type="password" id="pass" name="pass" class="form-control" placeholder="Entre una contraseña" />';
    // $html .= '  </div>';
    $html .= '  <div class="form-group col-md-3">';
    $html .= '      <label for="pass" class="col-form-label">Contraseña: <i class="text-muted">(*)</i></label>';
    $html .= '      <div class="input-group" id="show_hide_password">';
    $html .= '          <input type="password" id="pass" name="pass" class="form-control" placeholder="Entre una contraseña" disabled />';
    $html .= '          <div class="input-group-append">';
    $html .= '              <a href="#" class="btn btn-success"><i id="eyepass" class="fa fa-eye-slash" aria-hidden="true"></i></a>';
    $html .= '          </div>';
    $html .= '      </div>';
    $html .= '  </div>';
    $html .= '</div>';

    $html .= '<div class="form-group mt-3 text-right">';

    if($artref){
        $html .= '<input type="hidden" id="refart" name="refart" class="form-control" value="'.$artref.'" />';
    }
    
    $html .= '  <button type="button" name="cancel_nav" id="cancel_nav" class="btn btn-danger btn-sm" onclick="$(`#panel`).slideUp();$(`html,body`).animate({ scrollTop: $(`body`).offset().top }, `slow`);$(`#crea_rol`).removeAttr(`disabled`);$(`#importxls`).removeAttr(`disabled`);">Cancelar</button>';
    $html .= '  <button type="button" class="btn btn-sm btn-primary" onclick="manage_imp(this.form);"> Aceptar </button>';
    $html .= '</div>';
    $html .= '</form>';

    return $html;
}

function showform_import()
{
    $html = '';
    $html .= '<form id="imp_data" name="imp_data">';

    $html .=        '<div class="row">';
    $html .=            '<div class="col-12 col-sm-6 mt-1">';
    $html .=                '<h5>Tipo de archivo</h5>';
    $html .=                '<div class="form-check">';
    $html .=                    '<input class="form-check-input" type="radio" name="typexls" id="orchad" value="orchad" checked>';
    $html .=                    '<label class="form-check-label" for="orchad">Orchad</label>';
    $html .=                '</div>';
    $html .=                '<div class="form-check">';
    $html .=                    '<input class="form-check-input" type="radio" name="typexls" id="youtube" value="youtube">';
    $html .=                    '<label class="form-check-label" for="youtube">Youtube</label>';
    $html .=                '</div>';
    $html .=            '</div>';
    // $html .=            '<div class="col-12 col-sm-6 mt-1">';
    // $html .=                '<h5>Características iniciales</h5>';
    // $html .=                '<div class="form-check form-switch">';
    // $html .=                    '<input class="form-check-input" type="checkbox" role="switch" id="hashead" name="hashead" value="hashead" checked>';
    // $html .=                    '<label class="form-check-label" for="hashead">Tiene Encabezado</label>';
    // $html .=                '</div>';
    // $html .=                '<div class="form-check form-switch">';
    // $html .=                    '<input class="form-check-input" type="checkbox" role="switch" id="secondline" name="secondline" value="secondline">';
    // $html .=                    '<label class="form-check-label" for="secondline">Comienza 2da fila</label>';
    // $html .=                '</div>';
    // $html .=            '</div>';
    $html .=        '</div>';

    $html .='<hr />';

    $html .=        '<div class="row">';

    $html .=            '<div class="mt-1 mb-1 col-12 col-sm-6">';

    $html .=    '<div class="input-group">';
    $html .=        '<span class="input-group-text" id="basic-addon3">Cambio a USD:</span>';
    $html .=        '<input type="numeric" class="form-control" id="change_usd" name="change_usd" placeholder="$0" aria-describedby="basic-addon3">';
    $html .=    '</div>';

    $html .=        '</div>';

    $html .='<div class="col-12 col-sm-6 mt-1 mb-1">';
    $html .=    '<div class="input-group mb-3">';
    // $html .=        '<label class="input-group-text" for="importfile">XLS,CSV: </label>';
    $html .=        '<input type="file" class="form-control" id="importfile" name="importfile" accept=".xls,.xlsx, .csv" required>';
    $html .=    '</div>';
    $html .='</div>';

    // $html .='<div class="col-12 col-sm-6 mt-1 mb-1">';
    // $html .=    '<div class="custom-file">';
    // $html .=        '<input type="file" class="custom-file-input" id="importfile" name="importfile" accept=".xls,.xlsx, .csv" required>';
    // $html .=        '<label class="custom-file-label" for="importfile">cargar XLS</label>';
    // $html .=    '</div>';
    // $html .='</div>';

    $html .=         '</div>';

    $html .='<hr />';

    $html .=         '<div class="form-group col-12 mt-3 text-right">';
    $html .=             '<button type="button" name="cancel_nav" id="cancel_nav" class="btn btn-danger btn-sm mr-2" onclick="$(`#panel`).slideUp();$(`html,body`).animate({ scrollTop: $(`body`).offset().top }, `slow`);$(`#crea_rol`).removeAttr(`disabled`);$(`#importxls`).removeAttr(`disabled`);">Cancelar</button>';
    $html .=             '<button type="button" class="btn btn-sm btn-primary" onclick="manage_imp(this.form);"> Aceptar </button>';
    $html .=         '</div>';



    $html .=     '</form>';

    return $html;
}

function tableArtprevImp()
{
    $html = '<table id="newartist" class="table table-sm table-striped dt-responsive mt-3 mb-4" style="width:100%">';
    $html .= '</table>';

    $objPrevImp = new MagImport;
    $artists = $objPrevImp->getprevArt();

    $data = [];

    if($artists && is_array($artists)){

        for($i=0;$i<count($artists);$i++){
            $data[$i]['nombre'] = '<p>'.$artists[$i]['artist'].'</p>';
            $data[$i]['tracks'] = '<p>'.$artists[$i]['tracks'].'</p>';
            $data[$i]['vistas'] = '<p>'.$artists[$i]['qty'].'</p>';
            $data[$i]['monto'] = '<p>'.$artists[$i]['receipts'].'</p>';
            $classbtn = ($artists[$i]['flag'] == 1)?'btn-success':'btn-light';

            // $data[$i]['actions_edt'] = '<button class="btn btn-icon btn-light mr-1" data-art="'.$artists[$i]['artist'].'" onclick="manage_imp({\'name\':\'form_user\',\'info\':$(this)});"><i class="far fa-edit"></i></button>';

            $data[$i]['actions_imp'] = '<button class="btn btn-icon '.$classbtn.' mr-1" data-art="'.$artists[$i]['artist'].'" onclick="manage_imp({\'name\':\'artprevimp\',\'info\':$(this)});"><i class="fas fa-arrow-down"></i></button>';

            $data[$i]['actions_del'] = '<button class="btn btn-icon btn-light mr-1" data-art="'.$artists[$i]['artist'].'" onclick="manage_imp({\'name\':\'modal_delprevart\',\'info\':$(this)});"><i class="text-danger far fa-trash-alt"></i></button>';
            
        }   

    }

    return array(
        'data'=>$data,
        'table'=>$html,
        'type'=>'art'
    );
}

function tableArtTracks($art,$prevImp = true)
{
    $tracks = null;
    $data = [];

    if ($prevImp) {
        $objPrevImp = new MagImport;
        $tracks = $objPrevImp->getPrevArtTracks($art);

    }

    if($tracks && is_array($tracks)){
        
        for($i=0;$i<count($tracks);$i++){
            // $data[$i]['year'] = $tracks[$i]['year'];
            // $data[$i]['month'] = $tracks[$i]['month'];
            $data[$i]['title_disk'] = $tracks[$i]['title_disk'];
            $data[$i]['title_track'] = $tracks[$i]['title_track'];
            $data[$i]['type_trans'] = $tracks[$i]['type_trans'];
            $data[$i]['qty'] = $tracks[$i]['qty'];            
            $data[$i]['receipts'] = $tracks[$i]['receipts'];            
        }   

    }

    return array(
        'data'=>$data,
        'table'=>'<table id="trackstable" class="table table-sm table-striped dt-responsive mt-3 mb-4" style="width:100%"></table>',
        'type'=>'tracks'
    );
}

function tableArtRetail($art,$prevImp = true)
{
    $retail = null;
    $data = [];

    if ($prevImp) {
        $objPrevImp = new MagImport;
        $retail = $objPrevImp->getPrevArtRetailer($art);
        
    }

    if($retail && is_array($retail)){
        
        for($i=0;$i<count($retail);$i++){
            // $data[$i]['year'] = $retail[$i]['year'];
            // $data[$i]['month'] = $retail[$i]['month'];
            $data[$i]['retailer'] = $retail[$i]['retailer'];
            $data[$i]['qty'] = $retail[$i]['qty'];            
            $data[$i]['receipts'] = $retail[$i]['receipts'];            
        }   

    }

    return array(
        'data'=>$data,
        'table'=>'<table id="retailtable" class="table table-sm table-striped dt-responsive mt-3 mb-4" style="width:100%"></table>',
        'type'=>'retailer'
    );
}

function tableArtCountry($art,$prevImp = true)
{
    $country = null;
    $data = [];

    if ($prevImp) {
        $objPrevImp = new MagImport;
        $country = $objPrevImp->getPrevArtCountries($art);
        
    }

    if($country && is_array($country)){
        
        for($i=0;$i<count($country);$i++){
            // $data[$i]['year'] = $country[$i]['year'];
            // $data[$i]['month'] = $country[$i]['month'];
            $data[$i]['country'] = $country[$i]['country'];
            $data[$i]['qty'] = $country[$i]['qty'];            
            $data[$i]['receipts'] = $country[$i]['receipts'];            
        }   

    }

    return array(
        'data'=>$data,
        'table'=>'<table id="countrytable" class="table table-sm table-striped dt-responsive mt-3 mb-4" style="width:100%"></table>',
        'type'=>'country'
    );
}


?>