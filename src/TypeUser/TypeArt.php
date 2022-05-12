<?php
    namespace App\TypeUser;

    use App\Users\UserControl;

    class TypeArt extends UserControl
    {
        public function __construct(){
            register_shutdown_function( array( $this, '__destruct' ) );
        }
    
        public function __destruct() {
            return true;
        }
        public function __clone(){ }
        public function __wakeup(){ }

        
    }
?>