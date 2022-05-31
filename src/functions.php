<?php
    use App\Env\DotEnv;
    use App\UserAuth\UserAuth;


    (new DotEnv(__DIR__ . '/../.env'))->load();

    ini_set('memory_limit','4000M');
    
    if(!empty($_ENV['APP_DEBUG']) && $_ENV['APP_DEBUG'] === true){
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
    }

    if(!empty($_ENV['APP_TIMEZONE'])){
        date_default_timezone_set($_ENV['APP_TIMEZONE']);
    }

    /**
     * This function returns the different URI and paths variable that I'll need. 
     * 
     * @param string $arg The arg should be a string with some of these options URI, URI_VAR, PATH_RESOURCES
     * @return string|array  
     */
    function getPathUriVar($arg)
    {

        switch($arg){
            case 'URI':
                $response = isset($_SERVER['HTTPS'])?'https://'.$_SERVER['SERVER_NAME']:'http://'.$_SERVER['SERVER_NAME'];
            break;
            case 'URI_VAR':
                $response = (strlen($_SERVER['REQUEST_URI']) > 1)?explode('/',@trim(ltrim(substr($_SERVER['REQUEST_URI'],0,(strpos($_SERVER['REQUEST_URI'],'?'))?strpos($_SERVER['REQUEST_URI'],'?'):strlen($_SERVER['REQUEST_URI'])),'/'))):[];
            break;
            case 'PATH_RESOURCES':
                $response = __DIR__.'/../resources/';
            break;
            default:
                $response = null;
        }

        return $response;
    }

    /**
     * Return the layout that will require on the $arg variable
     * 
     * @param string $arg The name of the page (without the ext) that was created on the layouts file
     * @return void
     */
    function getLayouts($arg, $class=null, $add=[], $UserAuth = null) :void
    {
        
        $needle = ['header', 'footer', 'sidenav', 'topnav'];
        if(in_array($arg,$needle,true)){
            include getPathUriVar('PATH_RESOURCES').'layouts/'.$arg.'.php';
        }
    }

    /**
     * 
     */
    function requireContent() :void
    {

        $UserAuth = UserAuth::getInstance('art_magenta');

        $urivar = getPathUriVar('URI_VAR');

        $path = '';

        if(!empty($urivar) && in_array('logout',$urivar)){
            $UserAuth->destroyAuth();
            header('Location: ' . filter_var(getPathUriVar('URI').'/login', FILTER_SANITIZE_URL));
        }

        if(!$UserAuth->userAuth()){
            if(!empty(array_intersect(['login','repass'], $urivar))){
                $path = getPathUriVar('PATH_RESOURCES').'auth/'.$urivar[0].'.php';
            }

            if(!empty(array_intersect(['auth','act'], $urivar))){
                $path = getPathUriVar('PATH_RESOURCES').implode('/',$urivar);
            }

            if(!empty($path) && is_file($path)){
                require_once($path);
            }else{
                if(empty($path)){
                    require_once(getPathUriVar('PATH_RESOURCES').'auth/login.php');
                }else{
                    die(json_encode(['ERROR'=>'No Path success']));  //insertar a futuro funtion getErrorMsg
                }
            }
        }else{

            $path = ($UserAuth->getTypeUser() === 'mag')?getPathUriVar('PATH_RESOURCES').'panel/':getPathUriVar('PATH_RESOURCES').'art/';

            if(empty($urivar)){
                $path = $path.'dashboard/index.php';
            }else{
                // if (in_array('act', $urivar)) {
                //     $path = $path.implode('/', $urivar);
                // }

                if(is_file($path.implode('/',$urivar).'.php')){
                    $path = $path.implode('/',$urivar).'.php';
                }elseif(is_file($path.implode('/',$urivar).'/index.php')){
                    $path = $path.implode('/',$urivar).'/index.php';
                }else{
                    $path = $path.'dashboard/index.php';
                }
                
            }

                include $path;

            
            

        }
        

    }

?>