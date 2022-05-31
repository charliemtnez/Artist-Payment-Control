<!doctype html>
<html lang="es-ES">
    <head>
    <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta content="Sistema para artistas" name="description" />
        <meta content="IT Moob" name="author" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />

        <title>Sistema - Magenta</title>

        <link href="<?php echo getPathUriVar('URI'); ?>/css/styles.css" rel="stylesheet" />
        <?php 
            if(!empty($add) && is_array($add) && isset($add['css'])){
                if(is_array($add['css']) && !empty($add['css'])){
                    foreach($add['css'] as $css){
                        echo '<link href="'.getPathUriVar('URI').'/css/'.$css.'.css" rel="stylesheet" />';
                    }
                }
                
            }
        ?>

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

        
        <link href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css" rel="stylesheet" crossorigin="anonymous" />
        <link href="https://cdn.datatables.net/responsive/2.2.6/css/responsive.bootstrap4.min.css" rel="stylesheet" crossorigin="anonymous" />

        <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" rel="stylesheet" crossorigin="anonymous" />
        
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" rel="stylesheet" crossorigin="anonymous" />
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/regular.min.css" rel="stylesheet" crossorigin="anonymous" />

        <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/js/all.min.js" crossorigin="anonymous"></script>
        <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/js/all.min.js" crossorigin="anonymous"></script> -->

        <!-- App favicon -->
        <link rel="shortcut icon" href="<?php echo getPathUriVar('URI'); ?>/img/favicon.png" />

    </head>
    <body id=<?=$class ?>>

