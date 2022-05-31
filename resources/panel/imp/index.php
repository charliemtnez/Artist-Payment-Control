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
                    <h2 class="mt-4">Importar Datos</h2>

                    <div id="btn_imp"></div>
                    
                    <hr />

                    <div id="panel" style="display: none;">
                        <div class="card card-body shadow mb-4 col-12"></div>
                    </div>

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
    
    getLayouts('footer',null,['js'=>['sha512','script_imp']]);

?>
<script>manage_imp({'name':'init'});</script>