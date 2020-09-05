
var currDate = new Date();
var strDate1 = dateformat(currDate);
currDate.setDate(currDate.getDate() + 1);
var strDate2 = dateformat(currDate);

let dateInput1 = $('#date1');
let dateInput2 = $('#date2');

$('#btnDone').on('click', function (e) {
    clientID = $('#selectClient').val();
    window.location.href = "../commonWeb/php/clientDataToExcel.php?clientID=" + clientID
    + "&startDate=" + dateInput1.val() + "&endDate=" + dateInput2.val();
});

$(document).ready(function () {
    console.log("ready!");

    dateInput1.attr('max', strDate1);

    dateInput2.val(strDate2).attr('max', strDate2);
    currDate.setDate(1);
    strDate1 = dateformat(currDate);
    dateInput1.val(strDate1);

    getData(dateInput1.val(), dateInput2.val())
});

$('#btnUpdateChart').on('click', function (b) {
    getData(dateInput1.val(), dateInput2.val())
})

let obieqtebi = [];
let beerIds = [];
let beerObjects = [];

function getData(date1, date2) {
    $.ajax({
        url: 'webApi/getClients.php?date1=' + date1 + '&date2=' + date2,
        dataType: 'json',
        headers: {
            'Authorization': tkn
        },
        success: function (resp) {

            if (resp.success) {
                let sData = resp.data
                obieqtebi = [];
                beerIds = [];
                beerObjects = [];

                sData.forEach(function (item) {
                    if (!beerIds.includes(item.beerID)) {
                        beerIds.push(item.beerID);
                        beerObjects.push(
                            {
                                'name' : item.beerName,
                                'id' : item.beerID,
                                'data': [],
                                'color': item.color
                            }
                        );
                    }
                })

                let grByClient = groupBy(sData, x => parseInt(x.clientID));

                grByClient.forEach(function (client) {
                    // printout(client)
                    obieqtebi.push(client[0].clientName);

                    beerObjects.forEach(function (beer) {
                        let m = client.filter( it => it.beerID == beer.id)
                        if (m.length == 1)
                            beer.data.push(parseInt(m[0].liter));
                        else
                            beer.data.push(0);
                    });
                });

                drawChart()

            } else {
                console.log(resp);
                showError(resp.errorCode, resp.errorText);
            }
        }
    });
}

function drawChart() {

    var optionO = {
        chart: {
            type: 'bar'
        },
        tooltip: {
        },
        title: {
            text: "რეალიზაცია ობიექტების მიხედვით"
        },

        xAxis: {
            categories: obieqtebi
        },
        yAxis: {
            min: 0,
            title: {
                text: 'ლიტრი'
            },
            opposite: true
        },
        legend: {
            reversed: true
        },
        plotOptions: {
            series: {
                stacking: 'normal'
            }
        },
        series: beerObjects
    };

    optionO.chart.height = obieqtebi.length * 30 +160 + 'px';
    Highcharts.chart('container1', optionO);
}