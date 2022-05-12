<?php

    getLayouts('header','sb-nav-fixed',['css'=>['style']], $UserAuth);
    getLayouts('topnav',null,[], $UserAuth);

?>

<div id="layoutSidenav">

    <!-- SideNav -->
    <?php
        getLayouts('sidenav',null,[],$UserAuth);
    ?>
    <!-- End SideNav -->

    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid">
                <!-- Content -->

                <section>
                    <h2 class="mt-4">Artistas</h2>

                    <?php 
                        if($UserAuth->isAdmin()){
                            echo '<button type="button" name="crea_rol" id="crea_rol" class="btn btn-info btn-sm" onclick="manage_art({\'name\':\'form_user\'});">Crear artista</button>';
                            echo '<button type="button" name="importxls" id="importxls" class="btn btn-info btn-sm ml-2" onclick="manage_art({\'name\':\'form_import\'});">Importar Información</button>';
                        }
                        echo '<hr />';
                        if($UserAuth->isAdmin()){
                            echo '<div id="panel" style="display: none;">';
                            echo '  <div class="card card-body shadow mb-4 col-12">';
                            echo '  </div>';
                            echo '</div>';
                        }
                    ?>

                    <div class="card card-body mb-4 col-12">
                        <div id="artistas"></div>
                    </div>
                </section>
                
                <!-- End Content -->
            </div>
        </main>
    </div>

</div>

<?php

    getLayouts('footer',null,['js'=>['script_art']]);

?>