<?php
    namespace App\TypeUser;

    use App\Users\UserControl;

    class ManArtist extends UserControl
    {
        private $tableartimp = 'art_importdata';
        private $fieldsImp = [
            'id_art',
            'year',
            'month',
            'total_period',
            'total_period_arg',
            'total_period_artarg',
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
            'act_import',
        ];

        public function __construct(){
            register_shutdown_function( array( $this, '__destruct' ) );
        }
    
        public function __destruct() {
            return true;
        }
        public function __clone(){ }
        public function __wakeup(){ }

        public function hasArtRef($art)
        {
            $response = false;
            $ref = $this->hasItems('art_refimport',['id','id_user'],['artist_import'=>$art]);
            if(!empty($ref)){
                $tmp = $this->getUserbyId($ref[0]['id_user']);
                $response = ['name'=>$this->getFullName(),'id'=>$this->getId()];
            }
            return $response;
        }

        public function getRefArt($id_art = '')
        {
            $cond = (!empty(trim($id_art)))?['id_user'=>$id_art]:'';
            return $this->hasItems('art_refimport',['id','id_user','artist_import'],$cond);
        }

        public function getAllArt()
        {
            return $this->hasItems($this->table,$this->usefields,['type'=>'art']);
        }

        public function getImpArtbyIdArt($id_art)
        {
            return $this->hasItems($this->tableartimp,array_merge(['id'],$this->fieldsImp),['id_art'=>$id_art]);
        }

        public function addArt($data)
        {
            $art = array(
                'nombre' => $data['nombre'],
                'apellido' => (isset($data['apellido']))?$data['apellido']:null,
                'percent' => (isset($data['percent']))?$data['percent']:0,
                'user' => (isset($data['user']))?$data['user']:null,
                'email' => (isset($data['email']))?$data['email']:null,
                'password' => (isset($data['password']))?$data['password']:null,
                'type' => 'art',
                'active' => (isset($data['canlog']))?1:0,
            );

            return $this->addUser($art);
        }

        public function updArt($data)
        {
            $art = array(
                'id_user'=>$data['idart'],
                'nombre' => $data['nombre'],
                'apellido' => (isset($data['apellido']))?$data['apellido']:null,
                'percent' => (isset($data['percent']))?$data['percent']:0,
                'user' => (isset($data['user']))?$data['user']:null,
                'email' => (isset($data['email']))?$data['email']:null,
                'password' => (isset($data['password']))?$data['password']:null,
                'type' => 'art',
                'active' => (isset($data['canlog']))?1:0,
            );

            return $this->updUser($art);
        }

    }
?>