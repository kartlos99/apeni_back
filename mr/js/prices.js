let mainTable = $("#pricesTable");
let pricesTableHead = mainTable.find('thead');
let pricesTableBody = mainTable.find('tbody');

let beerList;
let customers;
let activeBeerIDs = [];


/**
 * dasahendlia SemTxveva roda obieqtze ludis fasi araa gawerili
 * web & mobile, orivegan
 */

$(document).ready(function () {
    getRegions();
    getBeers();
});

function getBeers() {
    $.ajax({
        url: 'mobileApi/listing/beers.php',
        dataType: 'json',
        headers: getHeaders(),
        success: function (resp) {
            beerList = resp;
            getCustomers();
        },
        error: function (errorResponse) {
            if (errorResponse.status === ownErrorCode) {
                showError(errorResponse.status, "შეცდომა: " + errorResponse.responseJSON.errorMessage);
            } else {
                showError(errorResponse.status, "მოხდა შეცდომა: " + errorResponse.statusText);
            }
        }
    });
}

function getCustomers() {
    $.ajax({
        url: 'mobileApi/listing/customers.php',
        dataType: 'json',
        headers: getHeaders(),
        success: function (resp) {
            customers = resp;
            proceedData();
        },
        error: function (errorResponse) {
            if (errorResponse.status === ownErrorCode) {
                showError(errorResponse.status, "შეცდომა: " + errorResponse.responseJSON.errorMessage);
            } else {
                showError(errorResponse.status, "მოხდა შეცდომა: " + errorResponse.statusText);
            }
        }
    });
}

function proceedData() {
    pricesTableBody.empty()
    pricesTableHead.empty()
    pricesTableHead.append(createPriceHeader())

    customers.forEach(function (customer) {
        pricesTableBody.append(createPriceRow(customer, customer.beerPrices));
    })
}

function createPriceHeader() {
    let tdCustomer = $('<th />').text("ობიექტის დასახელება");
    let headRow = $('<tr />').append(tdCustomer);
    beerList
        .filter(item => item.status === BeerStatus.ACTIVE)
        .forEach(function (beer) {
            activeBeerIDs.push(beer.id);
            headRow.append($('<th />').text(beer.name))
        })
    return headRow;
}

function createPriceRow(customer, prices) {
    let editBtn = $('<button />').text("Edit").addClass('edit-button');
    editBtn.on('click', function (b) {
        let thisRow = $(this).closest('tr'); //.find('.customer').text("midiii");
        if (thisRow.attr('data-state') == "normal") {
            thisRow.attr("data-state", "edit");
            thisRow.find('input').show();
            thisRow.find('span').hide();
            thisRow.find('button').text("Save");
        } else {
            thisRow.attr("data-state", "normal");
            thisRow.find('input').hide();
            thisRow.find('span').show();
            thisRow.find('button').text("Edit");
        }
    })
    let tdCustomer = $('<td />').text(customer.name).addClass('customer');
    let tdOptions = $('<td />').append(editBtn)
    let dataRow = $('<tr />').attr("data-state", "normal").append(tdCustomer);

    activeBeerIDs.forEach(function (beerID) {
        let priceItem = prices.find(function (price) {
            return price.beerID === beerID
        });
        let priceValue = "-";
        if (priceItem !== undefined)
            priceValue = priceItem.price

        let spanView = $('<span />').text(priceValue);
        let inputView = $('<input />').val(priceValue).attr('type', 'number');
        inputView.addClass('price-input');
        inputView.hide();
        let dataCell = $('<td />').append(spanView, inputView).addClass('ricxvi');
        dataRow.append(dataCell);
    });
    dataRow.append(tdOptions);
    return dataRow;
}

