let currDate = new Date();
let strDate1 = dateformat(currDate);
currDate.setDate(currDate.getDate() + 1);
let strDate2 = dateformat(currDate);

let dateInput1 = $('#date1');
let dateInput2 = $('#date2');
let summaryContainer = $('#summary');
let clientSelector = $('#selectClient');

$('#btnDone').on('click', function (e) {
    clientID = clientSelector.val();
    window.location.href = "../commonWeb/php/clientDataToExcel.php?clientID=" + clientID
        + "&startDate=" + dateInput1.val() + "&endDate=" + dateInput2.val()
        + "&regionID=" + currentRegionID;
});

clientSelector.on('change', function(e) {
    getData(dateInput1.val(), dateInput2.val(), clientSelector.val())
});

$(document).ready(function () {
    console.log("ready!");
    getRegions();

    dateInput1.attr('max', strDate1);

    dateInput2.val(strDate2).attr('max', strDate2);
    currDate.setDate(1);
    strDate1 = dateformat(currDate);
    dateInput1.val(strDate1);

    getData(dateInput1.val(), dateInput2.val())
});

$('#btnUpdateChart').on('click', function (b) {
    getData(dateInput1.val(), dateInput2.val(), clientSelector.val())
})

let obieqtebi = [];
let beerIds = [];
let beerObjects = [];

function getData(date1, date2, customerID = 0) {
    $.ajax({
        url: 'webApi/getClients.php?date1=' + date1 + '&date2=' + date2 + '&customerID=' + customerID ,
        dataType: 'json',
        headers: getHeaders(),
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
                                'name': item.beerName,
                                'id': item.beerID,
                                'data': [],
                                'summary': 0,
                                'color': item.color
                            }
                        );
                    }
                });

                beerObjects.sort(function (a, b) {
                    if (a.id < b.id)
                        return 1
                    else
                        return -1
                });

                let grByClient = groupBy(sData, x => parseInt(x.clientID));

                let sorted = new Map([...grByClient.entries()].sort(function (a, b) {
                    let reducer = (acum, currVal) => acum + parseInt(currVal.liter);
                    let sum_a = a[1].reduce(reducer, 0);
                    let sum_b = b[1].reduce(reducer, 0);
                    if (sum_a < sum_b)
                        return 1
                    else
                        return -1
                }));

                sorted.forEach(function (client) {
                    // printout(client)
                    obieqtebi.push(client[0].clientName);

                    beerObjects.forEach(function (beer) {
                        let m = client.filter(it => it.beerID == beer.id)
                        if (m.length == 1) {
                            beer.data.push(parseInt(m[0].liter));
                            beer.summary += parseInt(m[0].liter);
                        } else
                            beer.data.push(0);
                    });
                });

                showSummaryAmount();
                drawChart();
            } else {
                showError(resp.errorCode, resp.errorText);
            }
        }
    });
}

function drawChart() {

    let optionO = {
        chart: {
            type: 'bar'
        },
        tooltip: {
            formatter: function() {
                return '<b>' + this.x + '</b></br>'
                    + this.series.name + ': <b>' + this.y
                    + '</b></br>სულ: <b>' + this.total + '</b>';
            }
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

    optionO.chart.height = obieqtebi.length * 30 + 160 + 'px';
    Highcharts.chart('container1', optionO);
}

function showSummaryAmount() {
    summaryContainer.empty();
    beerObjects.forEach(function (beerObj) {
        let itm = $('<li />').text(beerObj.name + ": " + beerObj.summary);
        summaryContainer.prepend(itm);
    })
    summaryContainer.prepend($('<span />').text("ჯამური ლიტრაჟი"));
}