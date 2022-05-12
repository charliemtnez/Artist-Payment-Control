        <script src="https://code.jquery.com/jquery-3.6.0.min.js" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>


        <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js" crossorigin="anonymous"></script>
        <script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js" crossorigin="anonymous"></script>

        <script src="https://cdn.datatables.net/responsive/2.2.6/js/dataTables.responsive.min.js" crossorigin="anonymous"></script>
        <script src="https://cdn.datatables.net/responsive/2.2.6/js/responsive.bootstrap4.min.js" crossorigin="anonymous"></script>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js" charset="UTF-8"></script>

        <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js" integrity="sha256-t9UJPrESBeG2ojKTIcFLPGF7nHi2vEc7f5A2KpH/UBU=" crossorigin="anonymous"></script>

        <script src="<?php echo getPathUriVar('URI'); ?>/js/scripts.js"></script>

        <?php 

            if(!empty($add) && is_array($add) && isset($add['js'])){
                if(is_array($add['js']) && !empty($add['js'])){
                    foreach($add['js'] as $js){
                        echo '<script src="'.getPathUriVar('URI').'/js/'.$js.'.js"></script>';
                    }
                }
            }
        ?>

    </body>
</html>