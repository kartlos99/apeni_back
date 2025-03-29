let mainTable = $("#pricesTable");
let pricesTableHead = mainTable.find('thead');
let pricesTableBody = mainTable.find('tbody');
let beerSwitchBtn = $("#beerSwitchBtn");
let bottleSwitchBtn = $("#bottleSwitchBtn");

let beerList;
let bottleList;
let customers;
let activeBeerIDs = [];
let activeBottleIDs = [];
let activeItemIDs = [];

const CLASS_CHECK = "fa-check";

const ProductType = Object.freeze({
    BEER: "beer",
    BOTTLE: "bottle"
});

let activeProduct = ProductType.BEER;

/**
 * dasahendlia SemTxveva roda obieqtze ludis fasi araa gawerili
 * web & mobile, orivegan
 */

$(document).ready(function () {
    getRegions();
    getBeers();
    getBottles();
});

beerSwitchBtn.on('click', function () {
    switchToBeer()
});

bottleSwitchBtn.on('click', function () {
    switchToBottle()
});

function switchToBeer() {
    bottleSwitchBtn.removeClass(CLASS_CHECK);
    beerSwitchBtn.addClass(CLASS_CHECK);
    activeProduct = ProductType.BEER;
    activeItemIDs = activeBeerIDs;
    proceedData();
}

function switchToBottle() {
    beerSwitchBtn.removeClass(CLASS_CHECK);
    bottleSwitchBtn.addClass(CLASS_CHECK);
    activeProduct = ProductType.BOTTLE;
    activeItemIDs = activeBottleIDs;
    proceedData();
}

function getBeers() {
    $.ajax({
        url: 'mobileApi/listing/beers.php',
        dataType: 'json',
        headers: getHeaders(),
        success: function (resp) {
            beerList = resp;
            resp.filter(item => item.status === BeerStatus.ACTIVE)
                .forEach(function (beer) {
                    activeBeerIDs.push(beer.id);
                });
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

function getBottles() {
    $.ajax({
        url: 'mobileApi/listing/bottles.php',
        dataType: 'json',
        headers: getHeaders(),
        success: function (resp) {
            bottleList = resp;
            resp.filter(item => item.status === BeerStatus.ACTIVE)
                .forEach(function (bottle) {
                    activeBottleIDs.push(bottle.id);
                });
            // getCustomers();
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
            // initially show beer prices
            switchToBeer();
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
    if (activeProduct === ProductType.BEER)
        pricesTableHead.append(createPriceHeader());
    else
        pricesTableHead.append(createBottlePriceHeader());

    customers.forEach(function (customer) {
        if (activeProduct === ProductType.BEER)
            pricesTableBody.append(createPriceRow(customer, customer.beerPrices.map((it) => ({
                "itemID": it.beerID,
                "price": it.price
            }))));
        else
            pricesTableBody.append(createPriceRow(customer, customer.bottlePrices.map((it) => ({
                "itemID": it.bottleID,
                "price": it.price
            }))));
    })
}

function createPriceHeader() {
    let tdCustomer = $('<th />').text("ობიექტის დასახელება");
    let headRow = $('<tr />').append(tdCustomer);
    beerList
        .filter(item => item.status === BeerStatus.ACTIVE)
        .forEach(function (beer) {
            headRow.append($('<th />').text(beer.name))
        })
    return headRow;
}

function createBottlePriceHeader() {
    let tdCustomer = $('<th />').text("ობიექტის დასახელება");
    let headRow = $('<tr />').append(tdCustomer);
    bottleList
        .filter(item => item.status === BeerStatus.ACTIVE)
        .forEach(function (item) {
            headRow.append($('<th />').text(item.name))
        })
    return headRow;
}

function createPriceRow(customer, prices) {
    let editBtn = $('<button />').text("Edit").addClass('edit-button');
    editBtn.on('click', function (b) {
        let thisRow = $(this).closest('tr');
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
            readRowData(thisRow);
        }
    })
    let tdCustomer = $('<td />').text(customer.name).addClass('customer');
    let tdOptions = $('<td />').append(editBtn)
    let dataRow = $('<tr />')
        .attr("data-state", "normal")
        .attr("customerID", customer.id)
        .append(tdCustomer);

    activeItemIDs.forEach(function (itemID) {
        let priceItem = prices.find(function (price) {
            return price.itemID === itemID
        });
        let priceValue = "-";
        if (priceItem !== undefined)
            priceValue = priceItem.price

        let spanView = $('<span />').text(priceValue);
        let inputView = $('<input />').val(priceValue).attr('type', 'number');
        inputView.addClass('price-input');
        inputView.attr("itemID", itemID);
        inputView.hide();
        let dataCell = $('<td />').append(spanView, inputView).addClass('ricxvi');
        dataRow.append(dataCell);
    });
    dataRow.append(tdOptions);
    return dataRow;
}

function readRowData(currentRow) {
    let prices = [];
    currentRow.find('input').each(function (index) {
        prices.push({
            "itemID": $(this).attr('itemID'),
            "price": $(this).val()
        })
    });

    savePrices({
        'customerID': currentRow.attr('customerID'),
        'prices': prices,
        'product': activeProduct
    })
}

function savePrices(data) {
    $.ajax({
        url: 'webApi/client/updatePrices.php',
        method: 'post',
        data: data,
        dataType: 'json',
        headers: getHeaders(),
        success: function (resp) {
            getCustomers()
            console.log(resp);
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