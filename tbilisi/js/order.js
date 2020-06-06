let orderTable = $("#tbOrderList").find('tbody');
let doneBtn = $("#btnLoadOrders");
let dateField = $("#orderDate");
let tarigi = "";

class OrderItem {
    constructor(chk, obieqti, dasaxeleba, wont_30, wont_50, in_30, in_50, comment, distributor, isDone, money) {
        this.chk = chk;
        this.obieqti = obieqti;
        this.dasaxeleba = dasaxeleba;
        this.wont_30 = wont_30;
        this.wont_50 = wont_50;
        this.in_30 = in_30;
        this.in_50 = in_50;
        this.comment = comment;
        this.distributor = distributor;
        this.isDone = isDone;
        this.money = money;
    }
}

$(function () {
    tarigi = getFormatedDate();
    dateField.val(tarigi)
    getOrders();
});

doneBtn.on("click", function () {
    tarigi = dateField.val()
    getOrders();
})

function getFormatedDate() {
    let date = new Date();
    let dt = date.getFullYear() + "-";
    let month = date.getMonth() + 1;
    if (month < 10) month = "0" + month;
    dt += month + "-";
    let day = date.getDate();
    if (day < 10) day = "0" + day;
    dt += day;
    return dt;
}

function getOrders() {
    // let tarigi = getFormatedDate();
    // tarigi = "2020-04-14";
    console.log(tarigi);
    $.ajax({
        url: '/tbilisi/andr_app_links/get_shekvetebi_web.php?tarigi=' + tarigi,
        method: 'get',
        dataType: 'json',
        success: function (response) {
            // console.log(response);
            orderTable.empty();

            let groped = groupBy(response, it => it.obieqti + it.dasaxeleba)
            // console.log(groped);

            let unSortedOrders = [];

            groped.forEach(function (mList) {
                let summed = sumOrders(mList);
                unSortedOrders.push(summed);
            });

            let os1 = unSortedOrders
                .sort(function (a, b) {
                    if (a.obieqti < b.obieqti)
                        return 1;
                    else
                        return -1;
                });

            let os2 = os1
                .sort(function (a, b) {
                    if (a.distributor > b.distributor)
                        return 1;
                    else
                        return -1;
                });
            let os3 = os2
                .sort(function (a, b) {
                    return a.isDone - b.isDone;
                });

            os3.forEach(function (itm) {
                orderTable.append(orderToRow(itm));
            })
        }
    });
}

function sumOrders(list) {
    console.log("element1 ", list[0]);
    let firstElement = list[0];

    let chk = "0"
    let obieqti = firstElement.obieqti;
    let dasaxeleba = firstElement.dasaxeleba
    let wont_30 = 0.0;
    let wont_50 = 0.0;
    let in_30 = 0.0;
    let in_50 = 0.0;
    let comment = "";
    let distributor = firstElement.name;
    let money = 0.0;

    list.forEach(function (ord) {
        wont_30 += parseFloat(ord.wont_30);
        wont_50 += parseFloat(ord.wont_50);
        in_30 += parseFloat(ord.in_30);
        in_50 += parseFloat(ord.in_50);
        if (ord.chk == "1")
            chk = "1";
        comment += ord.comment + "   ";
        money += parseFloat(ord.money);
    })

    let isDone = 1;
    if (in_30 < wont_30 || in_50 < wont_50)
        isDone = 0;

    return new OrderItem(chk, obieqti, dasaxeleba, wont_30, wont_50, in_30, in_50, comment, distributor, isDone, money);
}

function orderToRow(item) {
    // console.log("gr ", item);
    // recives OrderItem object
    var tdObj = $('<td />').text(item.obieqti).addClass("cl-bold");
    var tdBeer = $('<td />').text(item.dasaxeleba);
    var tdOrder30 = $('<td />').text(item.wont_30);
    var tdOrder50 = $('<td />').text(item.wont_50);
    var tdIn30 = $('<td />').text(item.in_30);
    var tdIn50 = $('<td />').text(item.in_50);
    var tdComment = $('<td />').text(item.comment);
    var tdDistributor = $('<td />').text(item.distributor);
    var tdmoney = $('<td />').text(item.money + " ₾").addClass("cl-money");

    var iconEdit = $('<i />').addClass("fas fa-circle fa-2x");
    var tdChk = $('<td />');
    if (item.chk == "1")
        tdChk.append(iconEdit);

    if (item.wont_30 == 0) tdOrder30.addClass("zeroColor"); else tdOrder30.addClass("cl-bold");
    if (item.wont_50 == 0) tdOrder50.addClass("zeroColor"); else tdOrder50.addClass("cl-bold");
    if (item.in_30 == 0) tdIn30.addClass("zeroColor"); else tdIn30.addClass("cl-bold");
    if (item.in_50 == 0) tdIn50.addClass("zeroColor"); else tdIn50.addClass("cl-bold");
    if (item.money == 0) tdmoney.addClass("zeroColor");

    let row = $('<tr></tr>').append(tdChk, tdObj, tdBeer, tdOrder30, tdOrder50, tdIn30, tdIn50, tdComment, tdDistributor, tdmoney);
    if (item.isDone == 0)
        row.addClass("active-order")

    return row;
}

function groupBy(list, keyGetter) {
    const map = new Map();
    list.forEach((item) => {
        const key = keyGetter(item);
        const collection = map.get(key);
        if (!collection) {
            map.set(key, [item]);
        } else {
            collection.push(item);
        }
    });
    return map;
}