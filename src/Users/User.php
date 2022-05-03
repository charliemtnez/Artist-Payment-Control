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
            return $this->id;
        }

        public function getMail(){
            return $this->user['email_usr'];
        }

    }
?>