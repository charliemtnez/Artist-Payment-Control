function manage_art(form){

    var loading = true; 
    var titleloading = 'Espere. Puede tardar...'; 

    if(form){

        $('input.form-control').css("border-color", "#ced4da");
        let reg = /^[a-z A-Z áéíóúÁÉÍÓÚÑñäëïöüÄËÏÖÜ 0-9 \. \- _ \\s]{2,250}$/;
        let num = /^[0-9\.]{1,8}$/;
        let email = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/; 
        let re = /(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{4,}/; 

        let btn_ok,btn_cancel,footer;

        switch(form.name){
            case 'init':
                var formData = new FormData();
                formData.append('action',form.name);
                formData.append('idart',form.idart);

                if (form.hasOwnProperty("info")) {
                    formData.append('idart',form.info.data('art'));
                }

                titleloading = 'Cargando datos. Puede tardar...';
            break;
            case 'view_artperiod':
                var formData = new FormData();
                formData.append('action',form.name);
                formData.append('idart',form.artist);
                formData.append('period',$('select[name=allperiod] option').filter(':selected').val());

                titleloading = 'Cargando datos. Puede tardar...';
            break;

            default:
                return false;
            
        }

    }


    proccess_ajaxfile(formData,window.location.origin+'/dashboard/act/act_artist',loading, titleloading).then((obj)=>{

        loadingSpinner(false);

        if(obj.ERROR){
            
            proccess_modal('<div class="alert alert-danger" role="alert">'+obj.ERROR+'</div>','Error');
            return false;
        }
        
        if (obj.hasOwnProperty("btn_imp")) {
            $('#btn_imp').html(obj.btn_imp);
        }

        if (obj.hasOwnProperty("view_art")) {
            
            $('#artistas').html(obj.view_art);

        }

        if (obj.hasOwnProperty("view_artperiod")) {
            
            $('#viewtotalperiod').html(obj.view_artperiod.view_arttotalperiod);

            create_chart(obj.view_artperiod.totalyear);

            if (obj.view_artperiod.hasOwnProperty("tableArtTracks")) {

            
                $('#tracks').html(obj.view_artperiod.tableArtTracks.table);
    
                $('#trackstable').DataTable({
                    destroy: true,
                    order: [],
                    responsive: true,
                    processing: true,
                    lengthChange: false,
                    pageLength: 25,
                    language:{"url":'js/datatable_spanish.json'},
                    data:Object.values(obj.view_artperiod.tableArtTracks.data),
                    columns: [
                        { title: "Discos",data:"title_disk"},
                        { title: "Tracks",data:"title_track"},
                        { title: "Tipo Trans",data:"type_trans"},
                        { title: "Vistas",data:"qty",render: $.fn.dataTable.render.number( '.', ',', 0, '' )},
                        { title: "Recibe Art (USD)",data:"totalart_usd",render: $.fn.dataTable.render.number( '.', ',', 6, '$ ' )},
                        { title: "Recibe Art (ARG)",data:"totalart_ar",render: $.fn.dataTable.render.number( '.', ',', 6, '$ ' )}
                    ]
                });
    
            }

            if (obj.view_artperiod.hasOwnProperty("tableArtRetail")) {
            
                $('#source').html(obj.view_artperiod.tableArtRetail.table);
    
                $('#retailtable').DataTable({
                    destroy: true,
                    order: [],
                    responsive: true,
                    processing: true,
                    lengthChange: false,
                    pageLength: 25,
                    language:{"url":'js/datatable_spanish.json'},
                    data:Object.values(obj.view_artperiod.tableArtRetail.data),
                    columns: [
                        { title: "Tiendas",data:"retailer"},
                        { title: "Vistas",data:"qty",render: $.fn.dataTable.render.number( '.', ',', 0, '' )},
                        { title: "Recibe Art (USD)",data:"totalart_usd",render: $.fn.dataTable.render.number( '.', ',', 6, '$ ' )},
                        { title: "Recibe Art (ARG)",data:"totalart_ar",render: $.fn.dataTable.render.number( '.', ',', 6, '$ ' )}
                    ]
                });
    
            }

            if (obj.view_artperiod.hasOwnProperty("tableArtCountry")) {
            
                $('#country').html(obj.view_artperiod.tableArtCountry.table);
    
                $('#countrytable').DataTable({
                    destroy: true,
                    order: [],
                    responsive: true,
                    processing: true,
                    lengthChange: false,
                    pageLength: 25,
                    language:{"url":'js/datatable_spanish.json'},
                    data:Object.values(obj.view_artperiod.tableArtCountry.data),
                    columns: [
                        { title: "Paises",data:"country"},
                        { title: "Vistas",data:"qty",render: $.fn.dataTable.render.number( '.', ',', 0, '' )},
                        { title: "Recibe Art (USD)",data:"totalart_usd",render: $.fn.dataTable.render.number( '.', ',', 6, '$ ' )},
                        { title: "Recibe Art (ARG)",data:"totalart_ar",render: $.fn.dataTable.render.number( '.', ',', 6, '$ ' )}
                    ]
                });
    
            }

        }

    });

}

function create_chart(data){
    let month_period = [];
    let period_value = [];
    let period_value_art = [];

    console.log(data);

    let month = {1:'Enero', 2:'Febrero', 3:'Marzo', 4:'Abril', 5:'Mayo', 6:'Junio', 7:'Julio', 8:'Agosto', 9:'Septiembre', 10:'Octubre', 11:'Noviembre', 12:'Diciembre'};

    var elemento = document.getElementById("myAreaChart");
    var ctx = elemento.getContext('2d');

    Chart.defaults.global.defaultFontFamily = '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
    Chart.defaults.global.defaultFontColor = '#292b2c';

    try {

        Object.keys(month).map((key, index) => {
            if(data.hasOwnProperty(key)){
                period_value_art.push(parseFloat(data[key].totalartist)); 
            }else{
                period_value.push(0);
                period_value_art.push(0);
            } 
            month_period.push(month[key]);
        });  

        let Chart_Art = {
            label: "Artista",
            lineTension: 0.3,
            backgroundColor: "rgba(2,117,216,0.2)",
            borderColor: "rgba(2,117,216,1)",
            pointRadius: 5,
            pointBackgroundColor: "rgba(2,117,216,1)",
            pointBorderColor: "rgba(255,255,255,0.8)",
            pointHoverRadius: 5,
            pointHoverBackgroundColor: "rgba(2,117,216,1)",
            pointHitRadius: 30,
            pointBorderWidth: 2,
            data: period_value_art,
            };

        var DataChart = {
            labels: month_period,
            datasets: [Chart_Art]
        };

        new Chart(ctx, {
            data: DataChart,
            type: 'line',
            options: {
                legend: {
                    display: true,
                    position: 'bottom'
                }
            }
        });

    } catch (error) {
        console.log('chart tiene un error: '+ error);
    }
}