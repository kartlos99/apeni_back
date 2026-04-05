
let currDate = new Date();
let strDate = dateformat(currDate);

let dateInput1 = $('#date1');
let dateInput2 = $('#date2');

let expensesTable = $("#tbExpenses").find('tbody');
let btnRefresh = $('#btnRefresh');

let view = {
    expensesDayToClone: $('div.expenses-day'),
    mainContainer: $('div.mainContainer')
}

$(document).ready(function () {
    console.log("expenses: ready!");
    getRegions();

    dateInput1.val(strDate).attr('max', strDate);
    dateInput2.val(strDate).attr('max', strDate);

    getData(dateInput1.val(), dateInput2.val());
});

function getData(date1, date2) {
    $.ajax({
        url: 'webApi/getExpenses.php?date1=' + date1 + '&date2=' + date2,
        dataType: 'json',
        headers: getHeaders(),
        success: function (resp) {
            if (resp.success) {
                proceedData(Object.values(resp.data));
                // expensesTable.append(totalRow(resp.data))
            } else {
                showError(resp.errorCode, resp.errorText);
            }
        }
    });
}

btnRefresh.on('click', function (e) {
    getData(dateInput1.val(), dateInput2.val());
});

function proceedData(data) {
    view.mainContainer.empty();

    data.forEach(function (expenseItem) {
        view.mainContainer.append(constructDay(expenseItem))

    })
}

function constructDay(dayData) {
    let dayView = view.expensesDayToClone.clone();

    let dayTB = dayView.find('tbody.day-item');
    let sum = 0;
    dayTB.empty()
    dayData.expenses.forEach(function (exp) {
        dayTB.append(dataToRow(exp))
        sum += parseFloat(exp.tanxa);
    });

    dayTB.append(daySumRow(dayData.cash, sum))

    let dayTitle = dayData.expenses[0].expenseDate;
    dayView.find('div.panel-heading').text(dayTitle);

    return dayView;
}

function daySumRow(cash, expSum) {
    let atHand = parseFloat(cash) - parseFloat(expSum);
    let tdDate = $('<th />').text("შეჯამება:");
    let tdOperator = $('<td />').text("ქეში: " + cash).addClass("ricxvi");
    let tdComment = $('<td />').text("ხარჯბი: " + expSum).addClass("ricxvi");
    let tdAmount = $('<td />').text("ხელზე: " + atHand).addClass("ricxvi");
    let tdEmpty = $('<td />');
    return $('<tr />').addClass("cash-row").append(tdDate, tdOperator, tdComment, tdAmount, tdEmpty);
}

function dataToRow(item) {
    let tdDate = $('<td />').text(item.expenseDate);
    let tdOperator = $('<td />').text(item.operator);
    let tdCategory = $('<td />').text(item.category);
    let tdComment = $('<td />').text(item.comment);
    let tdAmount = $('<td />').text(item.tanxa).addClass("ricxvi");
    return $('<tr />').append(tdDate, tdOperator, tdCategory, tdComment, tdAmount);
}

$('#exportExpensesBtn').on('click', function () {
    window.location.href = "../mr/webApi/getExpenses.php?date1=" + dateInput1.val()
        + '&date2=' + dateInput2.val()
        + '&regionID=' + currentRegionID
        + '&token=' + token
        + '&forExport=true';
});