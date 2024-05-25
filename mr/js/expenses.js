
let currDate = new Date();
let strDate = dateformat(currDate);

let dateInput1 = $('#date1');
let dateInput2 = $('#date2');

let expensesTable = $("#tbExpenses").find('tbody');
let btnRefresh = $('#btnRefresh');

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
                expensesTable.empty()
                resp.data.forEach(function (expenseItem) {
                    expensesTable.append(dataToRow(expenseItem))
                })
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

function dataToRow(item) {
    let tdDate = $('<td />').text(item.expenseDate);
    let tdOperator = $('<td />').text(item.operator);
    let tdComment = $('<td />').text(item.comment);
    let tdAmount = $('<td />').text(item.tanxa).addClass("ricxvi");
    return $('<tr />').append(tdDate, tdOperator, tdComment, tdAmount);
}