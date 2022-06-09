<?php

namespace App\MagImport;

use App\DbConn\Opdb;
use Exception;

class MagImport extends Opdb
{
    private $tableTmpImport = 'prev_import';
    private $tablefileimport = 'file_imported';
    private $tableArtImport = 'art_importdata';
    private $tmptable = 'tmp_import';
    private $fieldsTmpImport = [
            'year',
            'month',
            'retailer',
            'territory',
            'label',
            'title_disk',
            'title_track',
            'artist',
            'type_trans',
            'quantity',
            'receipts',
            'percentbytrack',
            'usd_ar'
    ];

    private $fieldsArtImport = [
        'id_art',
        'year',
        'month',
        'total_period',
        'total_period_arg',
        'total_period_artarg',
        'total_period_views',
        'change_usd',
        'percent_imp',
        'data_import',
        'data_track',
        'data_retailer',
        'data_country',
        'date_firstimport',
        'user_firstimport',
        'date_lastimport',
        'user_lastimport',
        'act_import'
    ];

    public function __construct(){
        register_shutdown_function( array( $this, '__destruct' ) );
    }

    public function __destruct() {
        return true;
    }
    public function __clone(){ }
    public function __wakeup(){ }

    public function getAllCountries()
    {
        $countries = $this->hasItems('countries',['alpha_2','en','es']);

        return ($countries)?$countries:null;
    }

    public function addTmpImport($data)
    {
        return $this->addItem($this->tableTmpImport,$data);
    }

    public function addTmpImportEXP(string $values)
    {
        $fields =   implode(',', $this->fieldsTmpImport);

        $sql = 'INSERT INTO '.$this->tableTmpImport.'('.$fields.') VALUES '.$values.';';

        $response = $this->execSql($sql);

        return $response;
    }

    public function addRefImp($idart,$artref)
    {
        if(!$this->hasItems('art_refimport',['id'],['id_user'=>$idart,'artist_import'=>$artref])){
            return $this->addItem('art_refimport',['id_user'=>$idart,'artist_import'=>$artref]);
        }
        return false;
    }

    public function addFileImport($data)
    {
        return $this->addItem($this->tablefileimport,$data);
    }

    public function addImpDatArt($artref,$idart,$percentArt,$userid)
    {
        try{

            $this->createTmpTable($artref,$idart);

            $periods =$this->getFromTmpPeriods();

            if(!empty($periods) && is_array($periods)){
                foreach($periods as $v){
                    $totals = $this->getFromTmpTotals($v['year'],$v['month']);

                    $fields =[
                        'id_art'=>$idart,
                        'year'=>$v['year'],
                        'month'=>$v['month'],
                        'total_period'=>$totals[0]['receipts'],
                        'total_period_arg'=>$totals[0]['total_ar'],
                        'total_period_artarg'=>$totals[0]['totalart_ar'],
                        'total_period_views'=>$totals[0]['qty'],
                        'change_usd'=>$v['usd_ar'],
                        'percent_imp'=>$percentArt,
                        'data_track'=>$this->dataToJson($this->getFromTmpTracks($v['year'],$v['month'])),
                        'data_retailer'=>$this->dataToJson($this->getFromTmpRetailer($v['year'],$v['month'])),
                        'data_country'=>$this->dataToJson($this->getFromTmpCountries($v['year'],$v['month'])),
                        'date_firstimport'=>date("Y-m-d H:i:s"),
                        'user_firstimport'=>$userid,
                        'act_import'=>0,
                    ];

                    $add = $this->addItem($this->tableArtImport,$fields);

                }
            }
            $this->dropTmpTable();

            if(isset($add) && !empty($add)){
                $this->delArtImport($artref);
                return ['success'=>'OK','id'=>$add];
            }else{
                return false;
            }

        }catch(Exception $e){
            return ['success'=>'NOK','msgerror'=>$e->getMessage()];
        }

        return false;
    }

    public function createTmpTable($prevart,$idart)
    {
        $sql = 'CREATE TEMPORARY TABLE '.$this->tmptable.'
                SELECT
                prev_import.id,
                prev_import.`year`,
                prev_import.`month`,
                prev_import.retailer,
                prev_import.territory,
                prev_import.label,
                prev_import.title_disk,
                prev_import.title_track,
                prev_import.artist,
                prev_import.type_trans,
                prev_import.quantity,
                prev_import.receipts,
                prev_import.percentbytrack,
                prev_import.usd_ar,
                ROUND((receipts * usd_ar),6) as total_ar,
                ROUND(((receipts * usd_ar) * ((CASE WHEN percentbytrack = 0 THEN (SELECT percent_usr FROM user_sec WHERE id_usr = '.$idart.') ELSE percentbytrack END)/100)),6) as totalart_ar
                FROM
                prev_import
                WHERE
                prev_import.artist = "'.$prevart.'"
                ORDER BY
                prev_import.`year` ASC,
                prev_import.`month` ASC;';
        return $this->execSql($sql);
    }

    public function dropTmpTable()
    {
        $sql = 'DROP TEMPORARY TABLE '.$this->tmptable.';';
        return $this->execSql($sql);
    }

    public function getFromTmpPeriods()
    {
        $sql = 'SELECT `year`,`month`, usd_ar FROM tmp_import GROUP BY `year`,`month`;';
        $response = $this->execSql($sql);
        return (!empty($response && is_array($response)))?$response:null;
    }

    public function getFromTmpTracks($year,$month)
    {
        $sql = 'SELECT
                title_disk,
                title_track,
                type_trans,
                percentbytrack,
                Sum(quantity) as qty,
                Sum(receipts) as receipts,
                Sum(total_ar) as total_ar,
                Sum(totalart_ar) as totalart_ar
                FROM '.$this->tmptable.' 
                WHERE `year`="'.$year.'" AND `month`="'.$month.'"
                GROUP BY
                title_disk,
                title_track,
                type_trans,
                percentbytrack;';

        return $this->execSql($sql);
    }

    public function getFromTmpTotals($year,$month)
    {
        $sql = 'SELECT
                Sum(quantity) as qty,
                Sum(receipts) as receipts,
                Sum(total_ar) as total_ar,
                Sum(totalart_ar) as totalart_ar
                FROM '.$this->tmptable.' 
                WHERE `year`="'.$year.'" AND `month`="'.$month.'";';
        return $this->execSql($sql);
    }

    public function getFromTmpRetailer($year,$month)
    {
        $sql = 'SELECT
                retailer,
                Sum(quantity) as qty,
                Sum(receipts) as receipts,
                Sum(total_ar) as total_ar,
                Sum(totalart_ar) as totalart_ar
                FROM '.$this->tmptable.'
                WHERE `year`="'.$year.'" AND `month`="'.$month.'"
                GROUP BY
                retailer;';

        return $this->execSql($sql);
    }

    public function getFromTmpCountries($year,$month)
    {
        $sql = 'SELECT
                territory,
                countries.es as country,
                Sum(quantity) as qty,
                Sum(receipts) as receipts,
                Sum(total_ar) as total_ar,
                Sum(totalart_ar) as totalart_ar
                FROM '.$this->tmptable.' 
                INNER JOIN countries ON countries.alpha_2 = territory
                WHERE `year`="'.$year.'" AND `month`="'.$month.'"
                GROUP BY
                territory;';

        return $this->execSql($sql);
    }

    public function getFileImport($namefile)
    {
        return $this->hasItems($this->tablefileimport,['name_file','date_imp','usd_ar'],['name_file'=>$namefile]);
    }
    
    public function getprevArt()
    {
        $sql = 'SELECT DISTINCT 
        prev_import.artist, 
        Count(DISTINCT(prev_import.title_track)) AS tracks, 
        Sum(prev_import.quantity) AS qty, 
        Sum(prev_import.receipts) AS receipts,
        case when artist_import is null then 0 else 1 end as flag
        FROM prev_import 
        LEFT JOIN art_refimport ON artist = artist_import
        GROUP BY prev_import.artist 
        ORDER BY flag DESC, prev_import.artist ASC;';
        $data = $this->execSql($sql);
        return $data;
    }

    public function getPrevArtTracks($art)
    {
        $sql = 'SELECT
            prev_import.title_disk,
            prev_import.title_track,
            prev_import.type_trans,
            prev_import.percentbytrack,
            Sum(prev_import.quantity) as qty,
            Sum(prev_import.receipts) as receipts
        FROM
            prev_import
        WHERE
            prev_import.artist = "'.$art.'" 
        GROUP BY
            prev_import.title_disk,
            prev_import.title_track,
            prev_import.type_trans,
            prev_import.percentbytrack';

        $data = $this->execSql($sql);
        return $data;
    }

    public function getPrevArtRetailer($art)
    {
        $sql = 'SELECT
        prev_import.retailer,
        Sum(prev_import.quantity) as qty,
        Sum(prev_import.receipts) as receipts
        FROM
        prev_import
        WHERE
        prev_import.artist = "'.$art.'"
        GROUP BY
        prev_import.retailer';

        $data = $this->execSql($sql);
        return $data;
    }

    public function getPrevArtCountries($art)
    {
        $sql = 'SELECT
        prev_import.territory,
        countries.es as country,
        Sum( prev_import.quantity ) AS qty,
        Sum( prev_import.receipts ) AS receipts
        FROM
        prev_import
        INNER JOIN countries ON countries.alpha_2 = prev_import.territory 
        WHERE
        prev_import.artist = "'.$art.'" 
        GROUP BY
        prev_import.territory';

        $data = $this->execSql($sql);
        return $data;
    }
    
    public function getAllPrevbyArt($art)
    {
        return $this->hasItems($this->tableTmpImport,$this->fieldsTmpImport,['artist'=>$art]);
    }

    public function getIdAsocArt($art)
    {
        $ids = $this->hasItems('art_refimport',['id', 'id_user'],['artist_import'=>$art]);
        return ($ids)?$ids[0]:false;
    }

    public function delArtImport($art)
    {
        return $this->delItem($this->tableTmpImport,['artist'=>$art]);
    }

    public function delAllArtImport()
    {
        $sql = 'TRUNCATE TABLE '.$this->tableTmpImport;
        $truncate = $this->execSql($sql);
        if($truncate){
            $delfile = $this->delItem($this->tablefileimport,['imported'=>0]);
        }
        return $truncate;
    }

}

?>