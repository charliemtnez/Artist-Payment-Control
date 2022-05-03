<?php
    require __DIR__.'/../vendor/autoload.php';

    // getLayouts('header');

    echo '<pre>';
    // print_r($db->check_database());
    // print_r($db->get_response());
    
    // print_r($_ENV);
    var_dump(getPathUriVar('URI'));
    var_dump(getPathUriVar('URI_VAR'));
    var_dump(getPathUriVar('PATH_RESOURCES'));
    echo '</pre>';

    requireContent();

    // getLayouts('footer');
?>