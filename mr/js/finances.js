let currDate = new Date();
let strDate = dateformat(currDate);
let lastShownCustomerID = 0;

let dateInput1 = $('#date1');
let dateInput2 = $('#date2');
let clientSelector = $('#selectClient');
let btnRefresh = $('#btnRefresh');
let iconUp = $('i.fa-level-up-alt');
let moneyTable = $("#tbMoney").find('tbody');

clientSelector.on('change', function () {
    lastShownCustomerID = parseInt(clientSelector.val());
    if (lastShownCustomerID > 0)
        getDetailedData(dateInput1.val(), dateInput2.val(), lastShownCustomerID);
    else
        getData(dateInput1.val(), dateInput2.val());
    updateFirstColumnTitle();

    if (lastShownCustomerID === 0)
        iconUp.hide();
    else
        iconUp.show();
});

$(document).ready(function () {
    console.log("ready!");
    getRegions();

    dateInput1.val(strDate).attr('max', strDate);
    dateInput2.val(strDate).attr('max', strDate);

    getData(dateInput1.val(), dateInput2.val());
    updateCustomerList(dateInput1.val(), dateInput2.val());
    iconUp.hide();
});

btnRefresh.on('click', function (e) {
    if (lastShownCustomerID > 0)
        getDetailedData(dateInput1.val(), dateInput2.val(), lastShownCustomerID);
    else
        getData(dateInput1.val(), dateInput2.val());
    updateCustomerList(dateInput1.val(), dateInput2.val());
    updateFirstColumnTitle();
})

function updateFirstColumnTitle() {
    let viewTitle = $("#tbMoney").find('thead').find('th:first');
    let viewTitle2 = $("#dTitle");
    if (lastShownCustomerID === 0) {
        viewTitle.text("ობიექტი")
        viewTitle2.text("");
    } else {
        viewTitle.text("თარიღი")
        viewTitle2.text("დისტრიბუტორი")
    }
}

function getDetailedData(date1, date2, customerID) {
    $.ajax({
        url: 'webApi/getDetailedFinances.php?date1=' + date1 + '&date2=' + date2 + '&customerID=' + customerID,
        dataType: 'json',
        headers: getHeaders(),
        success: function (resp) {
            if (resp.success) {
                moneyTable.empty()
                resp.data.forEach(function (financeDataItem) {
                    moneyTable.append(detailRow(financeDataItem))
                })
                moneyTable.append(totalRow(resp.data))
            } else {
                showError(resp.errorCode, resp.errorText);
            }
        }
    });
}

function getData(date1, date2) {
    $.ajax({
        url: 'webApi/getGroupedFinances.php?date1=' + date1 + '&date2=' + date2,
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

function detailRow(item) {
    let tdDate = $('<td />').text(item.tarigi).addClass("asDate");
    return makeFinanceRow(tdDate, item);
}

function dataToRow(item) {
    let icon = $('<i />')
        .addClass("fas")
        .addClass("btn")
        .addClass("fa-external-link-alt")
        .attr('onclick', "showDetail(" + item.id + ")");

    let tdCustomer = $('<td />').text(item.dasaxeleba);
    tdCustomer.append(icon);
    return makeFinanceRow(tdCustomer, item);
}

function makeFinanceRow(firstCell, item) {
    let tdCash = $('<td />').addClass("ricxvi");
    let tdBank = $('<td />').addClass("ricxvi");
    let tdDistributor = $('<td />').text(item.distributor);
    let rowClass = "";
    if (parseInt(item.paymentType) === 1) {
        tdCash.text(item.amount);
        rowClass = "cash-row";
    }
    if (parseInt(item.paymentType) === 2) {
        tdBank.text(item.amount);
        rowClass = "bank-row";
    }
    return $('<tr></tr>')
        .addClass(rowClass)
        .append(firstCell, tdDistributor, tdCash, tdBank);
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
    let tdCustomer = $('<th />').text("ჯამი ₾");
    let tdCash = $('<th />').text(cash.toFixed(2)).addClass("ricxvi");
    let tdBank = $('<th />').text(bank.toFixed(2)).addClass("ricxvi");
    return $('<tr></tr>')
        .addClass("total-row")
        .append(tdCustomer, $('<td />'), tdCash, tdBank);
}

function updateCustomerList(date1, date2) {
    $.ajax({
        url: 'webApi/getFinanciallyActiveCustomers.php?date1=' + date1 + '&date2=' + date2,
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

function showDetail(customerID) {
    clientSelector.val(customerID);
    clientSelector.trigger('change');
}

iconUp.on('click', function () {
    clientSelector.val(0);
    clientSelector.trigger('change');
})