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
const EDIT_TEXT = "რედაქტ.";
const SAVE_TEXT = "შენახვა";

const ProductType = Object.freeze({
    BEER: "beer",
    BOTTLE: "bottle"
});

const DataRowState = Object.freeze({
    NORMAL: "normal",
    EDIT: "edit"
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
            if (activeProduct === ProductType.BEER)
                switchToBeer();
            else
                switchToBottle();
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

function switchToEditMode(dataRow) {
    dataRow.attr("data-state", DataRowState.EDIT);
    dataRow.find('input').show();
    dataRow.find('span').hide();
    dataRow.find('button').text(SAVE_TEXT);
}

function switchToNormalMode(dataRow) {
    dataRow.attr("data-state", DataRowState.NORMAL);
    dataRow.find('input').hide();
    dataRow.find('span').show();
    dataRow.find('button').text(EDIT_TEXT);
}

function onOptionClick(dataRow) {
    if (dataRow.attr('data-state') == DataRowState.NORMAL) {
        pricesTableBody.find("tr[data-state='edit']").each(function () {
            switchToNormalMode($(this));
        });
        switchToEditMode(dataRow);
    } else {
        // switchToNormalMode(dataRow);
        readRowData(dataRow);
    }
}

function createPriceRow(customer, prices) {
    let editBtn = $('<button />').text(EDIT_TEXT).addClass('edit-button');
    editBtn.on('click', function (b) {
        onOptionClick($(this).closest('tr'));
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
        let spanView = $('<span />').text("-");
        if (priceItem !== undefined) {
            priceValue = Number(priceItem.price).toFixed(2)
            spanView = wrapNumberSpan(priceValue);
        }

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

function wrapNumberSpan(numberStr) {
    let parts = numberStr.split('.');
    let decimal = "00"
    if (parts[1] !== undefined) {
        if (parts[1].length === 1)
            decimal = parts[1] + '0';
        else
            decimal = parts[1];
    }
    let frictionSpan = $('<span />').text('.' + decimal).addClass("friction");
    return $('<span />').addClass('int-number')
        .append(parts[0])
        .append(frictionSpan)
}

function readRowData(currentRow) {
    let isAllPricesValid = true;
    let prices = [];
    currentRow.find('input').each(function (index) {
        if (Number($(this).val()) <= 0) {
            isAllPricesValid = false
        }
        prices.push({
            "itemID": $(this).attr('itemID'),
            "price": $(this).val()
        })
    });

    if (isAllPricesValid) {
        savePrices({
            'customerID': currentRow.attr('customerID'),
            'prices': prices,
            'product': activeProduct
        });
    } else {
        showError(499, "არასწორი ფასის შეყვანა! ჩაწერეთ დადებითი რიცხვი!");
    }
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