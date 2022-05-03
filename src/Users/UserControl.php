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

        public function getUsers($id = '')
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

        private function setUser(array $dbuser, $id = null): void
        {

            if(!empty($id)){ $this->user['id'] = $id; }

            $this->user['name'] = $dbuser['nombre_usr'];
            $this->user['lastname'] = $dbuser['apellido_usr'];
            $this->user['bod'] = $dbuser['birthday_usr'];
            $this->user['username'] = $dbuser['user_usr'];
            $this->user['email'] = $dbuser['email_usr'];
            $this->user['role'] = $dbuser['role_usr'];
            $this->user['avatar'] = $dbuser['avatar'];
            $this->user['type'] = $dbuser['type'];
            $this->user['lastaccess'] = $dbuser['lastaccess'];
            $this->user['act'] = $dbuser['act_usr'];

        }

    }

?>