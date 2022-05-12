<!-- SideNav -->
<div id="layoutSidenav_nav">
    <nav class="sb-sidenav accordion sb-sidenav-light" id="sidenavAccordion">
        <div class="sb-sidenav-menu">
            <div class="nav">
                <div class="sb-sidenav-menu-heading">Core</div>
                <a class="nav-link" href="<?php echo getPathUriVar('URI') ; ?>">
                    <div class="sb-nav-link-icon">
                    <i class="fas fa-tachometer-alt"></i>
                    </div>
                    Inicio
                </a>
                <?php 
                    if($UserAuth->getRole() == 'sadmin' || $UserAuth->getRole() == 'admin'){
                        echo '<a class="nav-link" href="'.getPathUriVar('URI').'/users">';
                        echo '<div class="sb-nav-link-icon"><i class="fas fa-users-cog"></i></div>';
                        echo 'Usuarios';
                        echo '</a>';
                    }
                ?>

                <a class="nav-link" href="<?php echo getPathUriVar('URI') ; ?>/art">
                    <div class="sb-nav-link-icon"><i class="fas fa-users-cog"></i></div>
                    Artistas
                </a>
                
    </nav>

</div>
<!-- End SideNav -->