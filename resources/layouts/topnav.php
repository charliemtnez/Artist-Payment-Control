<nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
    <?php 
        if($UserAuth->getTypeUser() === 'mag'){
                
            echo '<a class="navbar-brand" href="'.getPathUriVar('URI').'">Panel Magenta</a>';
            echo '<button class="btn btn-link btn-sm order-1 order-lg-0" id="sidebarToggle" href="#"><i class="fas fa-bars"></i></button>';
        }else{
            echo '<a class="navbar-brand" href="'.getPathUriVar('URI').'">'. $UserAuth->getFullName().'</a>';
        }
    ?>

    <ul class="navbar-nav ml-auto">
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" id="userDropdown" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">

                <div class="small dropdown-item"><?php echo $UserAuth->getFullName();?></div>
                
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="<?php echo getPathUriVar('URI') ; ?>/logout">Logout</a>
                <div class="dropdown-divider"></div>
            </div>
        </li>
    </ul>
</nav>