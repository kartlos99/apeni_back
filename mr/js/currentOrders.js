let orderUnitToClone;
let orderRowToClone;
let ordersList = $('div.order-list');
let dateField = $("#orderDate");
let doneBtn = $("#btnLoadOrders");
let beerSumTable = $('#beerSumTable');
let tarigi = "";

let beerMap = new Map();

class BeerRow {
    constructor(beer, k10, k20, k30, k50) {
        this.beer = beer;
        this.k10 = k10;
        this.k20 = k20;
        this.k30 = k30;
        this.k50 = k50;
    }

    getLiterSum() {
        return this.k10 * 10 + this.k20 * 20 + this.k30 * 30 + this.k50 * 50;
    }
}

$(document).ready(function () {
    console.log("ready!");
    getRegions();

    let cloneItemsContainer = $('#cloneContainerDiv');
    orderUnitToClone = cloneItemsContainer.find('div.order-unit');
    orderRowToClone = cloneItemsContainer.find('tr.order-row');

    tarigi = getFormatedDate();
    dateField.val(tarigi)

    getOrders();
    // techPriceForm.find('i.fa-times').trigger('click');
});

doneBtn.on("click", function () {
    tarigi = dateField.val()
    getOrders();
});

function onlyUnique(value, index, self) {
    return self.indexOf(value) === index;
}

function getOrders() {

    $.ajax({
        url: 'webApi/getOrders.php?date=' + tarigi,
        dataType: 'json',
        headers: getHeaders(),
        success: function (resp) {
            ordersList.empty();

            if (resp.success) {
                let oData = resp.data
                beerMap = new Map();

                let sortedData = oData.sort(function (a, b) {
                    if (a.sortValue < b.sortValue)
                        return 1;
                    else
                        return -1
                }).sort(function (a, b) {
                    if (a.orderStatus === 'order_active')
                        return -1;
                    else
                        return 1
                }).sort(function (a, b) {
                    if (a.distr < b.distr)
                        return -1
                    else
                        return 1
                })

                sortedData.forEach(function (order) {
                    let newOrder = orderUnitToClone.clone();

                    let isChek = false
                    if (order.items.find(it => it.chek == "1") != undefined)
                        isChek = true

                    newOrder.find('td.client').text("ობიექტი: " + order.client);
                    newOrder.find('td.distributor').text("დისტრ: " + order.distr);
                    newOrder.find('td.order-status').text("სტატუსი: " + order.statusName);

                    if (order.sales.length > 0) {
                        newOrder.find('table.table-mitana').removeClass("hidden");
                        newOrder.find('td.delivery').text("დისტრ: " + order.sales[0].distributor);
                    }
                    if (order.amount.length > 0) {
                        newOrder.find('table.table-mitana').removeClass("hidden");
                        let moneyCell = newOrder.find('td.money')
                        moneyCell.append($('<span />').text("აღებული: ").addClass(""));
                        order.amount.forEach(function (mItem) {
                            if (mItem.paymentType === "1")
                                moneyCell.append($('<span />').text(mItem.money + "₾ ხელზე").addClass("cash-money"));
                            else
                                moneyCell.append($('<span />').text(mItem.money + "₾ ბანკი").addClass("bank-money"));
                        });
                    }

                    if (isChek) {
                        let iconChk = $('<i />').addClass("fas fa-circle fa-2x");
                        newOrder.find('td.order-chek').append(iconChk);
                    }
                    if (order.comment != null) {
                        newOrder.find('div.order-comment').text(order.comment)
                    }
                    if (order.orderStatus !== "order_active")
                        newOrder.addClass('order-completed');
                    else {
                        order.items.forEach(function (oItem) {
                            switch (oItem.canTypeID) {
                                case "1":
                                    proceedOrderSum(new BeerRow(oItem.dasaxeleba, 0, 0, 0, oItem.count));
                                    break;
                                case "2":
                                    proceedOrderSum(new BeerRow(oItem.dasaxeleba, 0, 0, oItem.count, 0));
                                    break;
                                case "3":
                                    proceedOrderSum(new BeerRow(oItem.dasaxeleba, 0, oItem.count, 0, 0));
                                    break;
                                case "4":
                                    proceedOrderSum(new BeerRow(oItem.dasaxeleba, oItem.count, 0, 0, 0));
                                    break;
                                default:
                                    alert("unknown can type!");
                            }
                        })
                    }

                    let rowContainer = newOrder.find('tbody.order-rows-container');

                    let beerIDs = order.items.map(it => it.beerID).filter(onlyUnique);

                    order.sales.forEach(function (saleItem) {
                        if ($.inArray(saleItem.beerID, beerIDs) === -1)
                            beerIDs.push(saleItem.beerID)
                    })

                    for (let bID of beerIDs) {
                        let newOrderRow = orderRowToClone.clone();

                        let oneBeerItems = order.items.filter(x => x.beerID === bID);
                        let oneBeerSales = order.sales.filter(x => x.beerID === bID);

                        if (oneBeerItems.length > 0 || oneBeerSales.length > 0) {
                            newOrderRow.find('td.beer-name').text(getBeerName(oneBeerItems, oneBeerSales))

                            oneBeerItems.forEach(function (bItem) {
                                let saleCount = oneBeerSales.filter(x => x.canTypeID === bItem.canTypeID).reduce((s, a) => s + parseInt(a.count), 0);
                                let unitData = getOrderWithSaleView(bItem.count, saleCount);
                                newOrderRow.find('td.' + bItem.canTypeID).append(unitData);

                                oneBeerSales = oneBeerSales.filter(function (saleItem) {
                                    return saleItem.canTypeID !== bItem.canTypeID
                                });
                            });
                            oneBeerSales.forEach(function (sItem) {
                                let saleCount = oneBeerSales.filter(x => x.canTypeID === sItem.canTypeID).reduce((s, a) => s + parseInt(a.count), 0);
                                let unitData = getOrderWithSaleView(0, saleCount);
                                newOrderRow.find('td.' + sItem.canTypeID).append(unitData);
                            });

                            rowContainer.append(newOrderRow);
                        }
                    }

                    if (order.emptyBarrels.length > 0) {
                        let newOrderRow = orderRowToClone.clone();
                        newOrderRow.addClass("empty-barrels");
                        newOrderRow.find('td.beer-name').text('წამოღბული კასრები')

                        order.emptyBarrels.forEach(function (emptyItem) {
                            newOrderRow.find('td.' + emptyItem.canTypeID).text(emptyItem.count);
                        });
                        rowContainer.append(newOrderRow);
                    }

                    ordersList.append(newOrder);
                })

            } else {
                console.log(resp);
                showError(resp.errorCode, resp.errorText);
            }
            displayActiveOrderSum();
        }
    });
}

function proceedOrderSum(beerRow) {
    if (beerMap.has(beerRow.beer)) {
        let existedOne = beerMap.get(beerRow.beer)
        let newOne = new BeerRow(
            beerRow.beer,
            parseInt(beerRow.k10) + parseInt(existedOne.k10),
            parseInt(beerRow.k20) + parseInt(existedOne.k20),
            parseInt(beerRow.k30) + parseInt(existedOne.k30),
            parseInt(beerRow.k50) + parseInt(existedOne.k50)
        )
        beerMap.set(beerRow.beer, newOne);
    } else {
        beerMap.set(beerRow.beer, beerRow);
    }
}

function displayActiveOrderSum() {
    beerSumTable.empty();
    beerSumTable.append(getBeerSumHeadRow());
    beerMap.forEach(function (value, key, map) {
        let tdBeer = $('<td />').text(key).addClass("sumTd");
        let td10 = $('<td />').text(value.k10).addClass("sumTd");
        let td20 = $('<td />').text(value.k20).addClass("sumTd");
        let td30 = $('<td />').text(value.k30).addClass("sumTd");
        let td50 = $('<td />').text(value.k50).addClass("sumTd");
        let tdLiter = $('<td />').text(value.getLiterSum()).addClass("sumTd");
        let tr = $('<tr />').append(tdBeer, td10, td20, td30, td50, tdLiter);
        beerSumTable.append(tr);
    })
}

function getBeerSumHeadRow() {
    let tdBeer = $('<td />').text("ლუდი").addClass("sumTd sumTh");
    let td10 = $('<td />').text("კ10").addClass("sumTd sumTh");
    let td20 = $('<td />').text("კ20").addClass("sumTd sumTh");
    let td30 = $('<td />').text("კ30").addClass("sumTd sumTh");
    let td50 = $('<td />').text("კ50").addClass("sumTd sumTh");
    let tdLiter = $('<td />').text("ლიტრი").addClass("sumTd sumTh");
    return $('<tr />').append(tdBeer, td10, td20, td30, td50, tdLiter);
}

function getOrderWithSaleView(orderCount, saleCount) {
    let holeField = $('<span />').text(orderCount).addClass("order-unit");
    if (saleCount > 0)
        $('<span />').text("/" + saleCount).addClass("sale-count").appendTo(holeField);
    return holeField;
}

function getBeerName(orderItems, saleItems) {
    if (orderItems.length > 0)
        return orderItems[0].dasaxeleba;
    if (saleItems.length > 0)
        return saleItems[0].dasaxeleba;
    return "_";
}