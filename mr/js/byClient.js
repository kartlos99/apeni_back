let currDate = new Date();
let strDate = dateformat(currDate);
let lastShownCustomerID = 0;

let dateInput1 = $('#date1');
let dateInput2 = $('#date2');
let summaryContainer = $('#summary');
let clientSelector = $('#selectClient');
let view = {
    debtTable: $("#tbDebt").find('tbody'),
    debtSearchInput: $("#debtSearchInput"),
    debtSearchClearBtn: $("#debtSearchClearBtn"),
    debtSearchBtn: $("#debtSearchBtn")
}

$('#btnDone').on('click', function (e) {
    clientID = clientSelector.val();
    window.location.href = "../commonWeb/php/clientDataToExcel.php?clientID=" + clientID
        + "&startDate=" + dateInput1.val() + "&endDate=" + dateInput2.val()
        + "&regionID=" + currentRegionID;
});

$('#btnExportDebt').on('click', function () {
    window.location.href = "../mr/webApi/client/getDebtList.php?forExport=true";
});

clientSelector.on('change', function (e) {
    lastShownCustomerID = clientSelector.val();
    getData(dateInput1.val(), dateInput2.val(), lastShownCustomerID);
});

$(document).ready(function () {
    console.log("ready!");
    getRegions();

    dateInput1.val(strDate).attr('max', strDate);
    dateInput2.val(strDate).attr('max', strDate);

    getData(dateInput1.val(), dateInput2.val());
    updateCustomerList(dateInput1.val(), dateInput2.val());

    getDebtList();
});

$('#btnUpdateChart').on('click', function (b) {
    getData(dateInput1.val(), dateInput2.val(), lastShownCustomerID);
    updateCustomerList(dateInput1.val(), dateInput2.val());
});

let obieqtebi = [];
let beerIds = [];
let beerObjects = [];

function getData(date1, date2, customerID = 0) {
    $.ajax({
        url: 'webApi/getClients.php?date1=' + date1 + '&date2=' + date2 + '&customerID=' + customerID,
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

function updateCustomerList(date1, date2) {
    $.ajax({
        url: 'webApi/getActiveCustomers.php?date1=' + date1 + '&date2=' + date2,
        dataType: 'json',
        headers: getHeaders(),
        success: function (resp) {

            if (resp.success) {
                clientSelector.empty();
                clientSelector.append($("<option/>").text("ყველა ობიექტი").val(0))
                resp.data.forEach(function (customer) {
                    clientSelector.append($("<option/>").text(customer.dasaxeleba).val(customer.id));
                })
            }
            clientSelector.val(lastShownCustomerID);

            if (clientSelector.val() == null) {
                clientSelector.val(0);
                lastShownCustomerID = 0;
                getData(dateInput1.val(), dateInput2.val(), lastShownCustomerID);
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
            formatter: function () {
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
    let grandTotal = 0;
    beerObjects.forEach(function (beerObj) {
        grandTotal += beerObj.summary;
        let itm = $('<li />').text(beerObj.name + ": " + beerObj.summary);
        summaryContainer.prepend(itm);
    })
    // let totalSpan = $('<span />').text("ჯამი: " + grandTotal).addClass("totalSpan");
    summaryContainer.prepend($('<span />').text("ჯამური ლიტრაჟი: " + grandTotal));
    // summaryContainer.find('span').te;
}

let debtList = [];
let debtQuery = "";

function getDebtList() {
    $.ajax({
        url: 'webApi/client/getDebtList.php',
        dataType: 'json',
        headers: getHeaders(),
        success: function (resp) {
            console.log(resp)
            if (resp.success) {
                debtList = resp.data;
                showDebtInfo(debtList);
            } else {
                showError(resp.errorCode, resp.errorText);
            }
        }
    });

}

function showDebtInfo(fData) {
    let nList = fData.filter(ob => ob.clientName.includes(debtQuery) || ob.moneyBalance.includes(debtQuery));
    view.debtTable.empty();
    nList.forEach(function (item) {
        view.debtTable.append(dataToRow(item))
    });
    view.debtTable.append(totalRow(calculateSum(nList)));
}

function totalRow(total) {
    return makeDebtRow("ჯამი", total.amount.toFixed(2), total.barrel_10, total.barrel_20, total.barrel_30, total.barrel_50, true);
}

function dataToRow(item) {
    return makeDebtRow(item.clientName, item.moneyBalance, item['10იანი'], item['20იანი'], item['30იანი'], item['50იანი'], false);
}

function makeDebtRow (name, amount, b1, b2, b3, b5, isTotal ) {
    let tdCustomer = $('<td />').text(name);
    let tdMoney = formatMoney(amount);
    let td10 = $('<td />').text(b1).addClass("ricxvi");
    let td20 = $('<td />').text(b2).addClass("ricxvi");
    let td30 = $('<td />').text(b3).addClass("ricxvi");
    let td50 = $('<td />').text(b5).addClass("ricxvi");
    let tr = $('<tr />').append(tdCustomer, tdMoney, td10, td20, td30, td50);
    if (isTotal) {
        tr.find('td').addClass("total-row")
    }
    return tr;
}

function formatMoney(f) {
    // let td = $('<td />').addClass("ricxvi");
    let rr = f.split('.');
    let frictionSpan = $('<span />').text('.' + rr[1]).addClass("friction");
    return $('<td />').addClass("ricxvi")
        .append(rr[0])
        .append(frictionSpan)
        .append(' ₾')
}

const KEY_BARREL_10 = '10იანი';
const KEY_BARREL_20 = '20იანი';
const KEY_BARREL_30 = '30იანი';
const KEY_BARREL_50 = '50იანი';

function calculateSum(array) {
    let debtSum = {
        amount: 0.0,
        barrel_10: 0,
        barrel_20: 0,
        barrel_30: 0,
        barrel_50: 0
    }

    array.forEach(element => {
        debtSum.amount += parseFloat(element.moneyBalance);
        debtSum.barrel_10 += parseInt(element[KEY_BARREL_10]);
        debtSum.barrel_20 += parseInt(element[KEY_BARREL_20]);
        debtSum.barrel_30 += parseInt(element[KEY_BARREL_30]);
        debtSum.barrel_50 += parseInt(element[KEY_BARREL_50]);
    });
    return debtSum;
}

// sort functions
function compareByName(a, b) {
    return a.clientName.localeCompare(b.clientName);
}
function compareByAmount(a, b) {
    return parseFloat(b.moneyBalance) - parseFloat(a.moneyBalance);
}
function compareByB10(a, b) {
    return parseInt(b[KEY_BARREL_10]) - parseFloat(a[KEY_BARREL_10]);
}
function compareByB20(a, b) {
    return parseInt(b[KEY_BARREL_20]) - parseFloat(a[KEY_BARREL_20]);
}
function compareByB30(a, b) {
    return parseInt(b[KEY_BARREL_30]) - parseFloat(a[KEY_BARREL_30]);
}
function compareByB50(a, b) {
    return parseInt(b[KEY_BARREL_50]) - parseFloat(a[KEY_BARREL_50]);
}

$('#debtTitleName').on('click', function () {
    debtList.sort(compareByName);
    showDebtInfo(debtList);
    selectThis($(this));
});
$('#debtTitleAmount').on('click', function () {
    debtList.sort(compareByAmount);
    showDebtInfo(debtList);
    selectThis($(this));
});
$('#debtTitleBarrel10').on('click', function () {
    debtList.sort(compareByB10);
    showDebtInfo(debtList);
    selectThis($(this));
});
$('#debtTitleBarrel20').on('click', function () {
    debtList.sort(compareByB20);
    showDebtInfo(debtList);
    selectThis($(this));
});
$('#debtTitleBarrel30').on('click', function () {
    debtList.sort(compareByB30);
    showDebtInfo(debtList);
    selectThis($(this));
});
$('#debtTitleBarrel50').on('click', function () {
    debtList.sort(compareByB50);
    showDebtInfo(debtList);
    selectThis($(this));
});

function selectThis(titleEl) {
    $('#debt-table,th').removeClass("selected-title");
    titleEl.addClass("selected-title");
}

function filterDebtList(query) {
    console.log(query);
    debtQuery = query;
    showDebtInfo(debtList);
}

view.debtSearchInput.on('keyup', function () {
    filterDebtList(view.debtSearchInput.val())
});

view.debtSearchBtn.on('click', function () {
    filterDebtList(view.debtSearchInput.val())
})

view.debtSearchClearBtn.on('click', function () {
    view.debtSearchInput.val("");
    filterDebtList("");
})