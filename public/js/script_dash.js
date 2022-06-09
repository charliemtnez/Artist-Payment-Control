function manage_dash(form){

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
                var formData = {'action':form.name};
                loading = true;
                titleloading = 'Cargando datos. Puede tardar...';
            break;

            default:
                return false;
            
        }

    }

    proccess_ajax(formData,window.location.origin+'/dashboard/act/act_dash',loading, titleloading).then((obj)=>{

        loadingSpinner(false);

        if(obj.ERROR){
            
            proccess_modal('<div class="alert alert-danger" role="alert">'+obj.ERROR+'</div>','Error');
            return false;
        }

        if (obj.hasOwnProperty("chart_years")) {

            create_chart(obj.chart_years.yearmonth, 'lineChart');

        }

    });

}

function create_chart(data,elementid)
{
    let month_period = [];
    let period_value = [];
    let period_value_art = [];
    let dataSet = [];

    let month = {1:'Enero', 2:'Febrero', 3:'Marzo', 4:'Abril', 5:'Mayo', 6:'Junio', 7:'Julio', 8:'Agosto', 9:'Septiembre', 10:'Octubre', 11:'Noviembre', 12:'Diciembre'};

    var dynamicColors = function(transp) {
        var r = Math.floor(Math.random() * 255);
        var g = Math.floor(Math.random() * 255);
        var b = Math.floor(Math.random() * 255);
        return "rgba(" + r + "," + g + "," + b + ","+transp+")";
    };

    try {

        Object.keys(data).map((key, index) => {
            
            Object.keys(month).map((kmonth, index) => {

                if(data[key].hasOwnProperty(kmonth)){

                    period_value.push(parseFloat(data[key][kmonth].totalusd));
                    // period_value_art.push(parseFloat(data[key][kmonth].totalartist)); 
                }else{
                    period_value.push(0);
                    // period_value_art.push(0);
                } 
                month_period.push(month[kmonth]);
            }); 

            let r = Math.floor(Math.random() * 255);
            let g = Math.floor(Math.random() * 255);
            let b = Math.floor(Math.random() * 255);

            let chartData = {
                label: key,
                data: period_value,
                lineTension: 0.3,
                backgroundColor: "rgba("+r+","+g+","+b+",0.2)",
                borderColor: "rgba("+r+","+g+","+b+",1)",
                pointRadius: 5,
                pointBackgroundColor: "rgba("+r+","+g+","+b+",1)",
                pointBorderColor: "rgba("+r+","+g+","+b+",0.8)",
                pointHoverRadius: 5,
                pointHoverBackgroundColor: "rgba("+r+","+g+","+b+",1)",
                pointHitRadius: 30,
                pointBorderWidth: 2,
            };

            dataSet.push(chartData);

        });

        var DataChart = {
            labels: Object.values(month),
            datasets: dataSet
        };
    
        var elemento = document.getElementById(elementid);
        var ctx = elemento.getContext('2d');

        Chart.defaults.global.defaultFontFamily = '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
        Chart.defaults.global.defaultFontColor = '#292b2c';

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

function create_table(data,idtable)
{
    
    $('#'+idtable).html(data.table);

    let columnstodo = Object.values(data.columns).map(function(value,index){
        let obj = {title:value['title'],data:value['data']};
        
        if(value.hasOwnProperty("render")){
            if(value['render'] == 'number'){
                obj['render'] = $.fn.dataTable.render.number( '.', ',', 0, '' )
            }
            if(value['render'] == 'currency'){
                obj['render'] = $.fn.dataTable.render.number( '.', ',', 6, '$ ' )
            }
        }

        return obj
    });

    let columnstotals =  Object.values(data.columnstotals);

    $('#'+data.tablaid).DataTable({
        destroy: true,
        order: [],
        responsive: true,
        processing: true,
        lengthChange: false,
        pageLength: 25,
        language:{"url":'js/datatable_spanish.json'},
        data:Object.values(data.data),
        columns:columnstodo,
        showFooter:false,
        footerCallback:function(row, data, start, end, display) {

            var api = this.api(),data;

            var intVal = function(i) {
                return typeof i === 'string' ?
                i.replace(/[\$,]/g, '') * 1 :
                typeof i === 'number' ?
                i : 0;
            };

            if(columnstotals){
                let totals  = columnstotals.map(function(value,index){
                    return api.column(value).data().reduce(function(a,b){
                            return intVal(a) + intVal(b);
                        },0);
                    });

                let pagetotals  = columnstotals.map(function(value,index){
                    return api.column(value,{
                                page: 'current'
                            }).data().reduce(function(a,b){
                                    return intVal(a) + intVal(b);
                                },0);
                            });

                if (end == display.length) {
                    columnstotals.map(function(value,index){
                        let n = Math.round((totals[index]+ Number.EPSILON) * 10000) / 10000;
                        $( api.column( value ).footer() ).html(n.toLocaleString("es-AR",{maximumFractionDigits:4}));
                    });
                }else{
                    columnstotals.map(function(value,index){
                        $( api.column( value ).footer() ).html('');
                    });
                }
            }

        }

    });

}