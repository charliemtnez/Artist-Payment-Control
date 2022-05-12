<?php
    namespace App\UserAuth;

    use App\Users\UserControl;
    use Firebase\JWT\JWT;
    use Firebase\JWT\Key;

    class UserAuth extends UserControl
    {
        protected static $_instance;
        protected static $key;
        private $session_name;
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


        public function destroyAuth()
        {
            $_SESSION = array(); //Se desconfigura los valores de la session;
            $parametros = session_get_cookie_params(); //obtiene los parametros de la session
            //borra las cookies actuales
            setcookie(session_name(), '', time() - 42000, $parametros['path'], $parametros['domain'],$parametros['secure'], $parametros['httponly']);
            setcookie($this->session_name. '_auth', '', time() - 42000, $parametros['path'], $parametros['domain'],$parametros['secure'], $parametros['httponly']);
            return session_destroy();
        }

        /**
         * For user authentication 
         * 
         * @param array $data 
         * @return array $response 
         */
        public function login_user(array $data)
        {
            $date = date("Y-m-d H:i:s");
            $timer = time();

            if(!$this->isUserBlock($data['user'])){
                $user = $this->getUser($data['user'],1,true);
                if($user){
                    $password = hash('sha512', $data['pass'] . $user['salt']);
                    if($password === $user['pass_usr']){
                        $token = array(
                            'iat'=>$timer,
                            'exp'=>strtotime( '+30 days' ),
                            'data'=>$this->user
                        );
                        $_SESSION[$this->session_name . '_auth'] = $this->user;

                        $updLastAccess = $this->saveLastAccess();

                        $delLogBlocj = $this->delItem($this->table_log,['email_usr'=>$this->user['email']]);

                        if($data['remenberme'] == 'true'){
                            $secure = (isset($_SERVER['HTTPS']))?true:false;
                            $httponly = true; 
                            $parametros = session_get_cookie_params();
                            setcookie($this->session_name . '_auth', JWT::encode($token, self::$key, 'HS256'), strtotime( '+30 days' ), $parametros['path'], $parametros['domain'],$secure, $httponly);
                        }

                        return true;

                    }else{
                        $response=array(
                            'user_usr'=> $user['user_usr'],
                            'email_usr'=> $user['email_usr'],
                            'time_user' => time()
                        );
                        $this->addItem($this->table_log,$response);
                        $this->error = 'Existe un error en la contraseña. Intentelo nuevamente.';
                        return false;
                    }
                }else{
                    $this->error = 'No existe este usuario o correo. Por favor revise nuevamente.';
                    return false;
                }
            }else{
                $this->error = 'Este usuario o correo se encuentra bloqueado por intentos fallidos. Por favor debe esperar al menos 2 horas para volver a intentarlo o contacte con un administrativo.';
                return false;
            }

            $this->error = 'No se ha podido realizar el proceso de login';
            return false;
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
                
                $this->user_auth = true;
                $this->user = $_SESSION[$session_name_auth];
    
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

        private function isUserBlock($user){

            $now = time();
            // Todos los intentos de inicio de sesión se cuentan desde las 2 horas anteriores.
            $valid_attempts = $now - (2 * 60 * 60);
    
            $sql = "SELECT COUNT(time_user) as cant_login FROM log_attempt WHERE (user_usr = '".$user."' OR email_usr = '".$user."') AND time_user > ". $valid_attempts;
            
            $resultados = $this->execSql($sql);

            // verifico si hay mas de 5 intentos en las 2 horas anteriores.
            return (!empty($resultados) && $resultados[0]["cant_login"] > 5 )?true:false;
    
        }

    }

?>