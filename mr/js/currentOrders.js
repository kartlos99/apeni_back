let orderUnitToClone;
let orderRowToClone;
let ordersList = $('div.order-list');
let dateField = $("#orderDate");
let doneBtn = $("#btnLoadOrders");
let tarigi = "";

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

                let sortedData = oData.sort(function (a, b) {
                    if (a.sortValue < b.sortValue)
                        return 1;
                    else
                        return -1
                }).sort(function (a, b) {
                    if (a.orderStatus == 'order_active')
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
                        newOrder.find('td.money').text("აღებული: " + order.amount[0].money + "₾");
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
        }
    });
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