let beerList;
let view = {
    fullBarrelTable: $("#tbFullBarrels").find('tbody'),
    emptyBarrelTable: $("#tbEmptyBarrels").find('tbody'),
}

getBeerList();

function getBeerList() {
    $.ajax({
        url: 'mobile/get_ludi_list.php',
        dataType: 'json',
        headers: getHeaders(),
        success: function (resp) {
            if (resp.success) {
                beerList = resp.data;
                getStoreHouseData();
            } else {
                showError(resp.errorCode, resp.errorText);
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
            } else {
                showError(resp.errorCode, resp.errorText);
            }
        }
    });
}

function showStoreHouseInfo(data) {
    view.fullBarrelTable.empty();
    view.emptyBarrelTable.empty();
    proceedFullBarrels(data.full);
    proceedEmptyBarrels(data.empty)
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
        simpleRow.name = beerList.find(item => item.id === fItem[0].beerID).dasaxeleba;
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