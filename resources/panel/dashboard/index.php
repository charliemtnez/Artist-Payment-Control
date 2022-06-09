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
                <h2 class="mt-4">Inicio</h2>
                <hr />
                
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-chart-area mr-1"></i>
                        <sapn id="title_chart">Ingresos totales por años</span>
                    </div>

                    <div class="card-body">
                        <div id="chartline" style="width: 100%;">
                            <canvas id="lineChart" width="800" height="200"></canvas>
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>

</div>

<?php

    getLayouts('footer',null,['js'=>['script_dash']], $UserAuth);

?>
<script>manage_dash({'name':'init'});</script>