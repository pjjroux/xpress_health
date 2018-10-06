/*
|--------------------------------------------------------------------------
| MIS Reports for admin page
|--------------------------------------------------------------------------
|
| Author:         Jaco Roux
| Studentnumber:  5376-553-2
| Date:           2018-10-05
|
*/


// Low stock levels bar chart
if (report_type == 'low_stock') {
    report_data = JSON.parse(report_data);

    var chart_data = [];
    for (var i=0; i < report_data.length; i++) {
        if (i < 10) {
            chart_data.push({
                y : report_data[i].supplement_id,
                a : report_data[i].stock_levels,
                b : report_data[i].min_levels,
            });
        } else {
            break;
        }   
    }

    Morris.Bar({
        element: 'chart',
        data: chart_data,
        xkey: 'y',
        ykeys: [ 'a', 'b'],
        labels: ['In Stock', 'Min Level'],
        barColors: ['#668330', '#B2C39B', '#C9D408'],
        resize: true
    });
}

// Top 10 sold supplements
if (report_type == 'top_10_sold') {
    report_data = JSON.parse(report_data);

    var chart_data = [];
    for (var i=0; i < report_data.length; i++) {
        if (i < 5) {
            chart_data.push({
                label: report_data[i].supplement_id, value: report_data[i].total_quantity_sold,
            });
        } else {
            break;
        } 
    }

    Morris.Donut({
        element: 'chart',
        data: chart_data,
        colors: ['#668330', '#B2C39B'],
        resize: true
    });
}

// Top suppliers
if (report_type == 'top_suppliers') {
    console.log(report_data);
    report_data = JSON.parse(report_data);

    console.log(report_data);

    var chart_data = [];
    for (var i=0; i < report_data.length; i++) {
        chart_data.push({
            y : report_data[i].supplier_name,
            a : report_data[i].number_supplements_supplied,
        });
    }

    Morris.Bar({
        element: 'chart',
        data: chart_data,
        xkey: 'y',
        ykeys: ['a'],
        labels: ['Supplied'],
        barColors: ['#668330', '#B2C39B', '#D5DDC8'],
        resize: true
    });
}

if (report_type == 'top_10_profit') {
    report_data = JSON.parse(report_data);

    var chart_data = [];
    for (var i=0; i < report_data.length; i++) {
        if (i < 10) {
            chart_data.push({
                y : report_data[i].supplement_id,
                a : report_data[i].cost_client,
                b : report_data[i].cost_incl,
                c : report_data[i].profit,
            });
        } else {
            break;
        }   
    }

    Morris.Bar({
        element: 'chart',
        data: chart_data,
        xkey: 'y',
        ykeys: [ 'a', 'b', 'c'],
        labels: ['Selling', 'Cost', 'Profit'],
        barColors: ['#668330', '#B2C39B', '#C9D408'],
        resize: true
    });
}

function validateFormReport() {
    if ($('#start_date').val() > $('#end_date').val()) {
        new Noty({
            type: 'error',
            layout: 'center',
            theme: 'bootstrap-v4',
            modal: true,
            text: 'End date must be greater than starting date'
        }).show();
        $('#end_date').focus();
        return false;
    }

    return true;
}