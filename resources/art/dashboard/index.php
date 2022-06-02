<?php

    getLayouts('header','sb-nav-fixed',['css'=>['style']], $UserAuth);
    getLayouts('topnav',null,[], $UserAuth);

?>

<div id="layoutSidenav">

    <div id="layoutSidenav_content">
        <main>
            <div class="container">
                <!-- Content -->

                <div class="row">
                    
                    <div class="card card-body mb-4 mt-4 col-12">
                        <div id="artistas"></div>
                    </div>
                    
                </div>
                
                <!-- End Content -->
            </div>
        </main>
    </div>

</div>

<?php

    getLayouts('footer',null,['js'=>['sha512','script_artist']]);

?>
<script>manage_art({'name':'init','idart':<?php echo $UserAuth->getId(); ?>});</script>