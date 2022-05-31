<?php
    namespace App\Users;

    use App\DbConn\Opdb;

    class User extends Opdb
    {
        protected $user = [
            'username' => '',
            'name' => '',
            'lastname' => '',
            'email' => '',
            'bod' => '',
            'avatar' => '',
        ];

        public function __construct($id = null){
            register_shutdown_function( array( $this, '__destruct' ) );
            // $this->user['id'] = $id;
        }
    
        public function __destruct() { return true; }
        public function __clone(){}
        public function __wakeup(){}

        public function getFullName(){
            return $this->user['name']. " " . $this->user['lastname'];
        }

        public function getId(){
            return $this->user['id'];
        }

        public function getMail(){
            return $this->user['email'];
        }

        public function getTypeUser(){
            return $this->user['type'];
        }

        public function getAvatar(){
            return $this->user['avatar'];
        }

        public function getRole(){
            return $this->user['role'];
        }

        public function getPercent(){
            return $this->user['percentart'];
        }

        public function isAdmin(){
            return ($this->user['role'] === 'sadmin' || $this->user['role'] === 'admin')?true:false;
        }

        public function isSAdmin(){
            return ($this->user['role'] === 'sadmin')?true:false;
        }

        public function isMag(){
            return ($this->user['type'] === 'mag')?true:false;
        }

        protected function setUser(array $dbuser, $id = null): void
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
            $this->user['percentart'] = $dbuser['percent_usr'];

        }

    }
?>