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
    
    getLayouts('footer',null,['js'=>['sha512','script_art']]);

?>
<script>manage_art({'name':'init'});</script>