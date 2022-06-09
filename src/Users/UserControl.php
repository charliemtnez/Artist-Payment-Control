<?php

    namespace App\Users;

    use App\Users\User;

    class UserControl extends User
    {
        protected static $_instance;
        protected static $key;
        protected $users = [];

        protected $table = 'user_sec';
        protected $usefields = [
            'id_usr',
            'user_usr',
            'email_usr', 
            'nombre_usr', 
            'apellido_usr', 
            'birthday_usr',
            'avatar',
            'role_usr',
            'type',
            'created',
            'modified',
            'lastaccess',
            'act_usr',
            'percent_usr',
            'currencyview',
        ];
        protected $hiddenfields = ['pass_usr','salt'];

        public function __construct(){
            register_shutdown_function( array( $this, '__destruct' ) );
        }
    
        public function __destruct() {
            return true;
        }
        public function __clone(){ }
        public function __wakeup(){ }
        
        public function checkMail(string $email)
        {
            $exist = $this->hasItems($this->table,array('email_usr'),array('email_usr'=>$email));
            if($exist){
                return true;
            }else{
                $this->error='The email '.$email. " don't exist.";
                return false;
            }
        }
    
        public function checkUser(string $user): bool
        {
            $exist = $this->hasItems($this->table,array('user_usr'),array('user_usr'=>$user));
            if($exist){
                return true;
            }else{
                $this->error='The user '.$user. " don't exist.";
                return false;
            }
        }

        public function getUserbyId($id = '')
        {
            $cond =($id !== '')?array('id_usr'=>$id):'';
            $exists = $this->hasItems($this->table, $this->usefields, $cond,'',0 ,0);

            if ($exists){
                foreach($exists as $k => $v){
                    $this->setUser($v,$v['id_usr']);
                    $this->users[] = $this->user;
                }
            }

            return $this->users;
        }

        public function getUser($user,$act = null, $allfields = false)
        {
            $cond = 'user_usr = "'.$user.'" OR email_usr = "'.$user; 

            if($act){
                $cond = $cond.'" AND act_usr = '.$act;
            }
            
            $fields = ($allfields)?array_merge($this->usefields,$this->hiddenfields):$this->usefields;
            $exist = $this->hasItems($this->table,$fields,$cond);
            if($exist){
                $this->setUser($exist[0],$exist[0]['id_usr']);
            }
            return ($exist)?$exist[0]:false;
        }

        public function addUser($data, $avatar = null){
    
            $random_salt = hash('sha512', uniqid(mt_rand(1, mt_getrandmax()), true));
            $newpass = hash('sha512', $data['password'] . $random_salt);
            
            $user_array = array(
                // 'user_usr' => (isset($data['user']))?$data['user']:'',
                'nombre_usr' => $data['nombre'],
                'apellido_usr' => (isset($data['apellido']))?$data['apellido']:'',
                // 'email_usr' => (isset($data['email']))?$data['email']:'',
                'pass_usr' => $newpass,
                'salt' => $random_salt,
                'birthday_usr' => (isset($data['birthday']))?$data['birthday']:'',
                'role_usr' => (isset($data['role']))?$data['role']:'',
                'avatar' => 'avatar-1.jpg',
                'type' => $data['type'],
                'created' => date("Y-m-d H:i:s"),
                'act_usr' => (isset($data['active']))?$data['active']:0,
                'percent_usr' => (isset($data['percent']))?$data['percent']:0,
                'currencyview' => (isset($data['currencyview']))?$data['currencyview']:0,
            );

            if(isset($data['user'])){

                if(!$this->checkUser($data['user'])){
                    $user_array['user_usr'] = $data['user'];
                }else{
                    $this->error = "El Usuario que intenta colocar YA existe. ".$this->get_error();
                    return false;
                }
                
            }

            if(isset($data['email'])){

                if(!$this->checkMail($data['email'])){
                    $user_array['email_usr'] = $data['email'];
                }else{
                    $this->error = "El Correo que intenta colocar YA existe. ".$this->get_error();
                    return false;
                }
                
            }

            $insert = $this->addItem("user_sec",$user_array);

            if(!$insert){
                $this->error = "No se ha podido procesar el registro del nuevo usuario. ".$this->get_error();
                return false;
            }

            return $insert;
            
        }

        public function updUser($data, $avatar = null){

            $exist = $this->hasItems($this->table, array_merge($this->usefields,$this->hiddenfields), ['id_usr'=>$data['id_user']],'',0 ,0);
    
            if ($exist) {

                if(!empty($data['password'])){
                    $random_salt = hash('sha512', uniqid(mt_rand(1, mt_getrandmax()), true));
                    $newpass = hash('sha512', $data['password'] . $random_salt);
                }else{
                    $random_salt = $exist[0]['salt'];
                    $newpass = $exist[0]['pass_usr'];
                }

                $user_array = array(
                    // 'user_usr' => (isset($data['user']))?$data['user']:'',
                    'nombre_usr' => $data['nombre'],
                    'apellido_usr' => (isset($data['apellido']))?$data['apellido']:'',
                    // 'email_usr' => (isset($data['email']))?$data['email']:'',
                    'pass_usr' => $newpass,
                    'salt' => $random_salt,
                    'birthday_usr' => (isset($data['birthday']))?$data['birthday']:'',
                    'role_usr' => (isset($data['role']))?$data['role']:'',
                    'avatar' => 'avatar-1.jpg',
                    'type' => $data['type'],
                    'created' => date("Y-m-d H:i:s"),
                    'act_usr' => (isset($data['active']))?$data['active']:0,
                    'percent_usr' => (isset($data['percent']))?$data['percent']:0,
                    'currencyview' => (isset($data['currencyview']))?$data['currencyview']:0,
                );

                if(isset($data['user'])){
                    if($data['user'] != $exist[0]['user_usr']){
                        if(!$this->checkUser($data['user'])){
                            $user_array['user_usr'] = $data['user'];
                        }else{
                            $this->error = "El Usuario que intenta colocar YA existe. ".$this->get_error();
                            return false;
                        }
                    }
                }

                if(isset($data['email'])){
                    if($data['email'] != $exist[0]['email_usr']){
                        if(!$this->checkMail($data['email'])){
                            $user_array['email_usr'] = $data['email'];
                        }else{
                            $this->error = "El Correo que intenta colocar YA existe. ".$this->get_error();
                            return false;
                        }
                    }
                }

                $insert = $this->updItem("user_sec", $user_array, ['id_usr'=>$data['id_user']]);

                if (!$insert) {
                    $this->error = "No se ha podido procesar el registro del nuevo usuario. ".$this->get_error();
                    return false;
                }

                return $insert;
            }

            return false;
            
        }

        protected function saveLastAccess()
        {
            return $this->updItem($this->table,['lastaccess'=>date("Y-m-d H:i:s")],['id_usr'=>$this->user['id']]);
        }


    }

?>