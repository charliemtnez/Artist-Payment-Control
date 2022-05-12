<?php 
    
    if(isset($_POST['act'])){
        switch($_POST['act']){
            case 'login':
                if($UserAuth->login_user($_POST)){
                    $response = array(
                        'status'=>'OK'
                    );
                }else{
                    $response = array(
                        'status'=>'NOOK',
                        'ERROR'=>$UserAuth->get_error()
                    );
                }
            break;
            case 'repass':

            break;
            default:
            $response = ['ERROR'=>'No existen acciones declaradas'];
        }

        die(json_encode($response,JSON_UNESCAPED_UNICODE | JSON_FORCE_OBJECT));
    }else{
        header('Location: ' . filter_var(getPathUriVar('URI').'/login', FILTER_SANITIZE_URL));
    }

    
?>