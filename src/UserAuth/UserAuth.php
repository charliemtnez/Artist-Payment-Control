<?php
    namespace App\UserAuth;

    use App\Users\UserControl;
    use Firebase\JWT\JWT;
    use Firebase\JWT\Key;

    class UserAuth extends UserControl
    {
        protected static $_instance;
        protected static $key;
        // private $table = 'user_sec';
        private $table_log = 'log_attempt';
        private $table_passreset = 'password_resets';

        private $user_auth = false;

        private function __construct($session_name){
            register_shutdown_function( array( $this, '__destruct' ) );
            $this->session_name = $session_name;
            $this->sec_session_start();
            $this->check_auth();
            self::$key = isset($_ENV['APP_KEY'])?$_ENV['APP_KEY']:"";
        }
    
        public function __destruct() {
            return true;
        }
        public function __clone(){ }
        public function __wakeup(){ }
     
        public static function getInstance($session_name){
            if (!(self::$_instance instanceof self)){
                self::$_instance=new self($session_name);
            }
            return self::$_instance;
        }

        public function userAuth(){
            return $this->user_auth;
        }

        private function sec_session_start() {

            $secure = false; //define los niveles de seguridad true es para https y false para http
            $httponly = true; //detiene que javascript sea capaz de acceder a la identificación de la sesión
            //obligando a las sessiones a utilizar cookies
            if(ini_set('session.use_only_cookies',1) === false){
                header("Location: ../error.php?err='no se puede iniciar una sesion segura (ini_set)'");
                exit();
            }
            //obtener los parametros de la cookie
            $cookieParame = session_get_cookie_params();
            session_set_cookie_params($cookieParame['lifetime'], $cookieParame['path'], $cookieParame['domain'], $secure, $httponly);
            
            session_name($this->session_name); //nombre de la session
            if (!isset($_SESSION)) { session_start(); } //inicio la session
            // session_regenerate_id(true); //regenera la sesión, borra la previa.
        }

        private function check_auth() :void
        {
            $session_name_auth = $this->session_name . '_auth';
            $session_name_location = $this->session_name . '_location';
    
            if(isset($_SESSION[$session_name_auth])){
                
                $this->user_auth = $_SESSION[$session_name_auth];
    
                if(isset($_SESSION[$session_name_location])){
                    $location = $_SESSION[$session_name_location];
                    $this->group_page = $location['group'];
                    $this->page_mod = $location['page'];
                }
            
            }elseif(isset($_COOKIE[$session_name_auth])) {
    
                $user_auth = json_decode(json_encode(JWT::decode($_COOKIE[$session_name_auth], self::$key, array('HS256'))),true);
                $this->user_auth  = $user_auth['data'];
                $_SESSION[$session_name_auth] = $this->user_auth;
    
                $this->SetCookie($this->user_auth);
    
            }else{
                $this->user_auth = false;
            }
        }

        private function SetCookie($data)
        {
            $timer = time();
            $token = array(
                'iat'=>$timer,
                'exp'=>strtotime( '+30 days' ),
                'data'=>$data
            );
            $secure = (isset($_SERVER['HTTPS']))?true:false;
            $httponly = true; 
            $parametros = session_get_cookie_params();
            setcookie(
                $this->session_name . '_auth', 
                JWT::encode($token, self::$key, 'HS256'), 
                strtotime( '+30 days' ), 
                $parametros['path'], 
                $parametros['domain'],
                $secure, 
                $httponly);
        }

    }

?>