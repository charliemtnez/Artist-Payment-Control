<?php
use App\TypeUser\ManArtist;

if(isset($_POST['action'])){

    switch($_POST['action']){

        case 'init':

            $objArt = new ManArtist;
            $totals = $objArt->getTotalsArt($_POST['idart']);
            $dataArt = prepareDataArt($objArt->getImpArtbyIdArt($_POST['idart']));

            $response['view_art'] = datArt($_POST['idart'],$totals['totals']);

            $response['view_artperiod']['view_arttotalperiod'] = viewtotalperiod($totals['totals'][0]['month'].'-'.$totals['totals'][0]['year'],$totals['yearmonth']);

            $response['view_artperiod']['totalyear'] = reset($totals['yearmonth']);

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

function datArt($idart, $totals)
{

    $acumulargartusd = array_sum(array_column($totals,'totalartistusd'));
    $acumulargart = array_sum(array_column($totals,'totalartist'));
    $acumulviews = array_sum(array_column($totals,'totalviews'));

    $response = '';

    $response .= '<div class="row">';
    $response .= '  <div class="col-md-6 col-12">';
    $response .=        periodselect($totals, $idart, $totals[0]['month'].'-'.$totals[0]['year']);
    $response .= '      <div id="viewtotalperiod" class="bg-light">';
    $response .= '      </div>';
    $response .= '  </div>';
    $response .= '  <div class="col-md-6 col-12">';
    $response .= '      <p>';
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
    $acumulargartusd=$data[$arrayperiod[1]][$arrayperiod[0]]['totalartistusd'];
    $acumulargart=$data[$arrayperiod[1]][$arrayperiod[0]]['totalartist'];
    $acumulviews=$data[$arrayperiod[1]][$arrayperiod[0]]['totalviews'];

    $html = '  <div>';
    $html .= '      <p>';
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


function tableArtTracks($tracks, $change_usd)
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