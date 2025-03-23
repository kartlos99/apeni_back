let beerList;
let barrelInputMonthToClone;
let inputRowToClone;
let bottleInputMonthToClone;
let bottleInputRowToClone;

let view = {
    fullBarrelTable: $("#tbFullBarrels").find('tbody'),
    emptyBarrelTable: $("#tbEmptyBarrels").find('tbody'),
    bottlesTable: $("#tbBottles").find('tbody'),
    yearSelector: $('#selectYear'),
    mainDiv: $('div.mainContainer'),
    mainBottlesDiv: $('div.mainBottlesContainer'),
    cloneContainer: $('#cloneContainerDiv'),
}

getBeerList();

function getBeerList() {
    $.ajax({
        url: 'mobileApi/listing/beers.php',
        dataType: 'json',
        headers: getHeaders(),
        success: function (resp) {
            beerList = resp;
            getStoreHouseData();
        },
        error: function (errorResponse) {
            if (errorResponse.status === 422) {
                showError(errorResponse.status, "შეცდომა: " + errorResponse.responseJSON.errorMessage);
            } else {
                showError(errorResponse.status, "მოხდა შეცდომა: " + errorResponse.statusText);
            }
        }
    });
}

function getStoreHouseData() {
    $.ajax({
        url: 'mobile/storeHouse/getBalance.php',
        dataType: 'json',
        headers: getHeaders(),
        success: function (resp) {
            if (resp.success) {
                showStoreHouseInfo(resp.data);
                getBottles(resp.data.bottles)
            } else {
                showError(resp.errorCode, resp.errorText);
            }
        }
    });
}

function getBottles(bottlesData) {
    $.ajax({
        url: 'mobile/bottle/list.php',
        dataType: 'json',
        headers: getHeaders(),
        success: function (resp) {
            showBottlesBalance(resp, bottlesData);
        }
    });
}

function showStoreHouseInfo(data) {
    view.fullBarrelTable.empty();
    view.emptyBarrelTable.empty();
    proceedFullBarrels(data.full);
    proceedEmptyBarrels(data.empty)
}

function showBottlesBalance(bottles, bottlesData) {
    view.bottlesTable.empty();

    bottlesData.forEach(function (bottleDataItem) {
        let tdName = $('<td />');
        let tdCount = $('<td />').addClass("ricxvi");
        let bottleName = bottles.find(item => item.id === bottleDataItem.bottleID).name;
        let count = parseInt(bottleDataItem.inputToStore) - parseInt(bottleDataItem.saleCount);
        tdName.text(bottleName);
        tdCount.text(count)
        let tr = $('<tr></tr>').append(tdName, tdCount);
        if (count < 0) {
            tr.addClass("warning");
        }
        view.bottlesTable.append(tr);
    })
}

function proceedFullBarrels(fData) {
    let result = [];
    let grouped = fData.reduce(function (r, a) {
        r[a.beerID] = r[a.beerID] || [];
        r[a.beerID].push(a);
        return r;
    }, Object.create(null));
    let groupedArray = Object.values(grouped);
    groupedArray.forEach(function (fItem) {
        let simpleRow = {};
        simpleRow.name = beerList.find(item => item.id === fItem[0].beerID).name;
        fItem.forEach(function (fItemItem) {
            simpleRow[fItemItem.barrelID] = fItemItem.inputToStore - fItemItem.saleCount;
        })
        result.push(Object.assign({}, simpleRow));
    });
    result.forEach(function (readyFullItem) {
        view.fullBarrelTable.append(makeFullRow(readyFullItem))
    })
}

function proceedEmptyBarrels(eData) {
    let simpleRow = {};
    simpleRow.name = "-";
    eData.forEach(function (eItem) {
        simpleRow[eItem.barrelID] = eItem.inputEmptyToStore - eItem.outputEmptyFromStoreCount;
    })
    view.emptyBarrelTable.append(makeFullRow(simpleRow))
}

function proceedBottles(data) {

}

function makeFullRow(item) {
    let tdName = $('<td />');
    let td50 = $('<td />').addClass("ricxvi");
    let td30 = $('<td />').addClass("ricxvi");
    let td20 = $('<td />').addClass("ricxvi");
    let td10 = $('<td />').addClass("ricxvi");
    tdName.text(item.name);
    td50.text(item["1"]);
    td30.text(item["2"]);
    td20.text(item["3"]);
    td10.text(item["4"]);

    return $('<tr></tr>').append(tdName, td10, td20, td30, td50);
}

$(document).ready(function () {
    getRegions();

    let i;
    let currentYear = getYear();

    for (i = 2018; i <= currentYear; i++) {
        $('<option />').text(i).attr('value', i).appendTo('#selectYear');
    }

    view.yearSelector.val(currentYear);
    getStoreHouseInputs(currentYear);
    getStoreHouseBottleInputs(currentYear);
    barrelInputMonthToClone = view.cloneContainer.find('div.barrel-input-month');
    inputRowToClone = view.cloneContainer.find('tr.barrels-row')
    bottleInputMonthToClone = view.cloneContainer.find('div.bottle-input-month');
    bottleInputRowToClone = view.cloneContainer.find('tr.bottle-row')
});

view.yearSelector.on('change', function (e) {
    getStoreHouseInputs(view.yearSelector.val());
    getStoreHouseBottleInputs(view.yearSelector.val());
});

function getStoreHouseInputs(year) {
    if (currentRegionID !== '1') return
    $.ajax({
        url: 'webApi/getStoreHouseInputByMonth.php?year=' + year,
        dataType: 'json',
        headers: getHeaders(),
        success: function (resp) {

            if (resp.success) {
                console.log(resp.success)
                proceedInputData(resp.data)
            } else {
                console.log(resp);
                showError(resp.errorCode, resp.errorText);
            }
        }
    });
}

function getStoreHouseBottleInputs(year) {
    if (currentRegionID !== '1') return
    $.ajax({
        url: 'webApi/getStoreHouseBottleInputByMonth.php?year=' + year,
        dataType: 'json',
        headers: getHeaders(),
        success: function (resp) {

            if (resp.success) {
                console.log(resp.success)
                proceedBottleInputs(resp.data)
            } else {
                console.log(resp);
                showError(resp.errorCode, resp.errorText);
            }
        }
    });
}

function proceedBottleInputs(data) {
    console.log(data);
    view.mainBottlesDiv.empty();

    Object.entries(data).forEach(function (sItem) {

        let monthCloneView = bottleInputMonthToClone.clone();

        let monthInputsContainer = monthCloneView.find('tbody.bottle-input-items');
        let monthTotalLiter = 0;

        Object.values(sItem[1]).forEach(function (sRow) {
            monthTotalLiter += parseInt(sRow.liter);
            let barrelInputRow = bottleInputRowToClone.clone();
            barrelInputRow.find('td.bottle-name').text(sRow.bottle);
            barrelInputRow.find('td.liter').text(sRow.liter);
            barrelInputRow.find('td.amount').text(sRow.amount);
            monthInputsContainer.append(barrelInputRow);
        });

        let monthTitle = monthObj[sItem[0]];
        let unitTitle = monthTitle + " - ლიტრაჟი: " + monthTotalLiter + " ლტ.";
        monthCloneView.find('div.panel-heading').text(unitTitle);

        view.mainBottlesDiv.append(monthCloneView);
    })
}

function proceedInputData(data) {
    view.mainDiv.empty();

    Object.entries(data).forEach(function (sItem) {

        let monthCloneView = barrelInputMonthToClone.clone();

        let monthInputsContainer = monthCloneView.find('tbody.barrel-input-items');
        let monthTotalLiter = 0;

        Object.values(sItem[1]).forEach(function (sRow) {
            monthTotalLiter += parseInt(sRow.liter);
            let barrelInputRow = inputRowToClone.clone();
            barrelInputRow.find('td.beer-name').text(sRow.beer);
            barrelInputRow.find('td.liter').text(sRow.liter);

            sRow.barrels.forEach(function (barrel) {
                let bType = barrel.canType;
                barrelInputRow.find('td.' + bType).text(barrel.amount);
            })

            monthInputsContainer.append(barrelInputRow);
        });

        let monthTitle = monthObj[sItem[0]];
        let unitTitle = monthTitle + " - ლიტრაჟი: " + monthTotalLiter + " ლტ.";
        monthCloneView.find('div.panel-heading').text(unitTitle);

        view.mainDiv.append(monthCloneView);
    })
}