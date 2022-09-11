let currDate = new Date();
let strDate1 = dateformat(currDate);
currDate.setDate(currDate.getDate() + 1);
let strDate2 = dateformat(currDate);
let lastShownCustomerID = 0;

let dateInput1 = $('#date1');
let dateInput2 = $('#date2');
let clientSelector = $('#selectClient');
let btnRefresh = $('#btnRefresh');

let moneyTable = $("#tbMoney").find('tbody');

clientSelector.on('change', function() {
    lastShownCustomerID = clientSelector.val();
    getData(dateInput1.val(), dateInput2.val(), lastShownCustomerID);
});

$(document).ready(function () {
    console.log("ready!");
    getRegions();

    dateInput1.attr('max', strDate1);

    dateInput2.val(strDate2).attr('max', strDate2);
    currDate.setDate(1);
    strDate1 = dateformat(currDate);
    dateInput1.val(strDate1);

    getData(dateInput1.val(), dateInput2.val());
    updateCustomerList(dateInput1.val(), dateInput2.val());
});

btnRefresh.on('click', function (e) {
    getData(dateInput1.val(), dateInput2.val());
    updateCustomerList(dateInput1.val(), dateInput2.val());
})

function getData(date1, date2, customerID = 0) {
    $.ajax({
        url: 'webApi/getGroupedFinances.php?date1=' + date1 + '&date2=' + date2 ,
        dataType: 'json',
        headers: getHeaders(),
        success: function (resp) {

            if (resp.success) {
                moneyTable.empty()
                resp.data.forEach(function (financeDataItem) {
                    moneyTable.append(dataToRow(financeDataItem))
                })
                moneyTable.append(totalRow(resp.data))
            } else {
                showError(resp.errorCode, resp.errorText);
            }
        }
    });
}

function dataToRow(item) {
    let tdCustomer = $('<td />').text(item.dasaxeleba).addClass("cl-bold");
    let tdCash = $('<td />').addClass("ricxvi");
    let tdBank = $('<td />').addClass("ricxvi");
    let tdDistributor = $('<td />').text(item.distributor);
    if (parseInt(item.paymentType) === 1)
        tdCash.text(item.amount);
    if (parseInt(item.paymentType) === 2)
        tdBank.text(item.amount);

    return $('<tr></tr>').append(tdCustomer, tdDistributor, tdCash, tdBank);
}

function totalRow(data) {
    let cash = 0;
    let bank = 0;
    data.forEach(function (item) {
        if (parseInt(item.paymentType) === 1)
            cash += parseFloat(item.amount);
        if (parseInt(item.paymentType) === 2)
            bank += parseFloat(item.amount);
    });
    let tdCustomer = $('<td />').text("ჯამი").addClass("cl-bold");
    let tdCash = $('<td />').text(cash.toFixed(2)).addClass("total-row");
    let tdBank = $('<td />').text(bank.toFixed(2)).addClass("total-row");
    return $('<tr></tr>')
        .addClass("total-row")
        .append(tdCustomer, $('<td />'), tdCash, tdBank);
}

function updateCustomerList(date1, date2) {
    $.ajax({
        url: 'webApi/getActiveCustomers.php?date1=' + date1 + '&date2=' + date2 ,
        dataType: 'json',
        headers: getHeaders(),
        success: function (resp) {

            if (resp.success) {
                clientSelector.empty();
                clientSelector.append($("<option/>").text("ყველა ობიექტი").val(0))
                resp.data.forEach( function(customer) {
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