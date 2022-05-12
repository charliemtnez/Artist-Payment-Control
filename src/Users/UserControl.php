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

        protected function saveLastAccess()
        {
            return $this->updItem($this->table,['lastaccess'=>date("Y-m-d H:i:s")],['id_usr'=>$this->user['id']]);
        }


    }

?>