let orderUnitToClone;
let orderRowToClone;
let ordersList = $('div.order-list');
let dateField = $("#orderDate");
let doneBtn = $("#btnLoadOrders");
let tarigi = "";

$(document).ready(function () {
    console.log("ready!");

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
        headers: {
            'Authorization': tkn
        },
        success: function (resp) {
            ordersList.empty();

            if (resp.success) {
                let oData = resp.data

                let sordedData = oData.sort(function (a, b) {
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

                sordedData.forEach(function (order) {
                    let newOrder = orderUnitToClone.clone();

                    let isChek = false
                    if (order.items.find(it => it.chek == "1") != undefined)
                        isChek = true

                    newOrder.find('td.client').text(order.client);
                    newOrder.find('td.distributor').text(order.distr);
                    newOrder.find('td.order-status').text(order.statusName);
                    if (isChek) {
                        var iconChk = $('<i />').addClass("fas fa-circle fa-2x");
                        newOrder.find('td.order-chek').append(iconChk);
                    }
                    if (order.comment != null) {
                        newOrder.find('div.order-comment').text(order.comment)
                    }
                    if (order.orderStatus != "order_active")
                        newOrder.addClass('order-completed');

                    let rowContainer = newOrder.find('tbody.order-rows-container');

                    let beerIDs = order.items.map(it => it.beerID).filter(onlyUnique);

                    for (bID of beerIDs) {
                        let newOrderRow = orderRowToClone.clone();

                        var oneBeerItems = order.items.filter(x => x.beerID == bID);
                        if (oneBeerItems.length > 0) {
                            newOrderRow.find('td.beer-name').text(oneBeerItems[0].dasaxeleba)
                            oneBeerItems.forEach(function (bItem) {
                                newOrderRow.find('td.' + bItem.canTypeID).text(bItem.count);
                            });
                            rowContainer.append(newOrderRow);
                        }
                    }

                    if (order.items.length > 0)
                        ordersList.append(newOrder);
                })

            } else {
                console.log(resp);
                showError(resp.errorCode, resp.errorText);
            }
        }
    });
}