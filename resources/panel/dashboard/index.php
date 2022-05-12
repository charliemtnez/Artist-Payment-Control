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
            
            

            </div>
        </main>
    </div>

</div>

<?php

    getLayouts('footer',null,[], $UserAuth);

?>