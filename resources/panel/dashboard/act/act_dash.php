<?php
use App\TypeUser\ManArtist;
use App\MagImport\MagImport;

if(isset($_POST['action'])){

    switch($_POST['action']){

        case 'init':
            $objArt = new ManArtist();
            $response['chart_years'] = $objArt->getTotalsbyYears();
            
        break;

        default:
        $response = ['ERROR'=>'No existen acciones declaradas'];

    }
    die(json_encode($response,JSON_UNESCAPED_UNICODE | JSON_FORCE_OBJECT));
}else{
    die(json_encode(['ERROR'=>'No existe POST'],JSON_UNESCAPED_UNICODE | JSON_FORCE_OBJECT));
}


?>