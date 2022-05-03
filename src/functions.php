<?php
    use App\Env\DotEnv;
    use App\UserAuth\UserAuth;

    (new DotEnv(__DIR__ . '/../.env'))->load();
    
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
                $response = (strlen($_SERVER['REQUEST_URI']) > 1)?explode('/',@trim(ltrim(substr($_SERVER['REQUEST_URI'],0,(strpos($_SERVER['REQUEST_URI'],'?'))?strpos($_SERVER['REQUEST_URI'],'?'):strlen($_SERVER['REQUEST_URI'])),'/'))):null;
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
    function getLayouts($arg) :void
    {
        $needle = ['header', 'footer'];
        if(in_array($arg,$needle,true)){
            require_once getPathUriVar('PATH_RESOURCES').'layouts/'.$arg.'.php';
        }
    }

    function requireContent() :void
    {
        $UserAuth = UserAuth::getInstance('art_magenta');

        $urivar = getPathUriVar('URI_VAR');

        echo '<pre>';
            var_dump($UserAuth->userAuth());
            var_dump($UserAuth->get_error());
        echo '</pre>';

        if(!$UserAuth->userAuth() && !empty(array_intersect(['login','repass'], $urivar))){
            require_once(getPathUriVar('PATH_RESOURCES').'auth/'.$urivar[0].'.php');
        }else{

        }
    }

?>