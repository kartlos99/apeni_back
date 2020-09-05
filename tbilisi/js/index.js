// let mTkn = window.localStorage.getItem('tkn');
let saleMonthToClone;
let saleRowToClone;
let mainDiv = $('div.mainContainer');

function getSales(year) {

    $.ajax({
        url: 'webApi/getSaleByMonth.php?year=' + year,
        dataType: 'json',
        headers: {
            'Authorization': tkn
        },
        success: function (resp) {

            if (resp.success) {
                let sData = resp.data

                let beerIDs = [];
                mainDiv.empty();

                Object.entries(sData).forEach(function (sItem) {

                    let newSaleMonth = saleMonthToClone.clone();

                    let monthSalesContainer = newSaleMonth.find('tbody.beer-sale-items');
                    let monthTotalLiter = 0;

                    Object.values(sItem[1].sales).forEach(function (sRow) {
                        monthTotalLiter += parseInt(sRow.liter);
                        let newSaleRow = saleRowToClone.clone();
                        newSaleRow.find('td.beer-name').text(sRow.beerName);
                        newSaleRow.find('td.price').text(sRow.price);
                        newSaleRow.find('td.litraji').text(sRow.liter);

                        sRow.barrels.forEach(function (barrel) {
                            let bType = barrel.canType;
                            newSaleRow.find('td.' + bType).text(barrel.canCount);
                        })

                        monthSalesContainer.append(newSaleRow);

                        if (!beerIDs.includes(parseInt(sRow.beerID)))
                            beerIDs.push(parseInt(sRow.beerID))

                    });

                    let monthID = monthObj[sItem[0]];
                    let unitTitle = monthID + " - ლიტრაჟი: " + monthTotalLiter + " ლტ.";
                    newSaleMonth.find('div.panel-heading').text(unitTitle);

                    mainDiv.append(newSaleMonth);
                })
                showChart1(sData, beerIDs)
            } else {
                console.log(resp);
                showError(resp.errorCode, resp.errorText);
            }
        }
    });

}

function getBeerDataByID(bID, fullData) {
    let oneBeerSaleRow = [];
    tveebi.forEach(function (tve, index) {

        if (fullData[index + 1] != undefined) {
            let mBeer = fullData[index + 1].sales.filter(x => parseInt(x.beerID) == bID)
            if (mBeer.length == 1) {
                oneBeerSaleRow.push(parseInt(mBeer[0].liter))
            } else {
                oneBeerSaleRow.push(0);
            }
        } else
            oneBeerSaleRow.push(0);
    })
    return oneBeerSaleRow;
}

function getBeerNameAndColor(bID, data) {
    let b = undefined
    Object.values(data).forEach(function (rowMain) {
        rowMain.sales.forEach(function (item) {
            if (parseInt(item.beerID) == bID && b == undefined) {
                b = item;
            }
        })
    })
    return b;
}

let tveebi = ['იანვ', 'თებ', 'მარ', 'აპრ', 'მაისი', 'ივნ', 'ივლ', 'აგვ', 'სექტ', 'ოქტ', 'ნოემ', 'დეკ'];

function showChart1(data, beerIDs) {
    var chemiSeriisData = [];
    let mydata = [];

    var years = [];

    beerIDs.sort().forEach(function (bID) {
        let beer = getBeerNameAndColor(bID, data);
        let oneBeer = getBeerDataByID(bID, data)
        mydata.push({
            name: beer.beerName,
            data: oneBeer,
            color: beer.color
        });
    })

    let option = {
        chart: {
            type: 'column'
        },
        title: {
            text: 'რეალიზაცია თვეების მიხედვით'
        },
        xAxis: {
            categories: tveebi
        },
        yAxis: {
            title: {
                text: 'ლიტრაჟი'
            }
        },
        series: mydata
    };

    Highcharts.chart('container1', option);
}

let ready = $(document).ready(function () {
    console.log("ready!");
    // $('#typename_id').attr("data-nn", 0);
    // $('#brandname_id').attr("data-nn", 1);
    // $('#modelname_id').attr("data-nn", 2);
    // $('#typename_id').addClass("chosen").chosen();
    // $('#brandname_id').addClass("chosen").chosen();
    // $('#modelname_id').addClass("chosen").chosen();


    // $('#price_crit_weight_status_id').attr("readonly", true).val(0).find('option').attr('disabled', true);
    // loadTypesList(0, 'typename_id');

    var i;
    for (i = 2018; i <= getYear(); i++) {
        $('<option />').text(i).attr('value', i).appendTo('#selectYear');
    }

    $('#selectYear').val(getYear());

    saleMonthToClone = $('#cloneContainerDiv').find('div.sale-month');
    saleRowToClone = $('#cloneContainerDiv').find('tr.sale-row');
    getSales(getYear());
    // techPriceForm.find('i.fa-times').trigger('click');
});

function getYear() {
    return new Date().getFullYear();
}

$('#selectYear').on('change', function (e) {
    getSales($('#selectYear').val());
})