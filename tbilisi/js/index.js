// let mTkn = window.localStorage.getItem('tkn');
let saleMonthToClone;
let saleRowToClone;
let mainDiv = $('div.mainContainer');

function getSales()
{

    $.ajax({
        url: 'webApi/getSaleByMonth.php',
        dataType: 'json',
        headers: {
            'Authorization': tkn
        },
        success: function (resp) {

            if (resp.success) {
                let sData = resp.data

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
                    });

                    let monthID = monthObj[sItem[0]];
                    let unitTitle = monthID + " - ლიტრაჟი: " + monthTotalLiter + " ლტ.";
                    newSaleMonth.find('div.panel-heading').text(unitTitle);

                    mainDiv.append(newSaleMonth);
                })
            } else {
                console.log(resp);
                showError(resp.errorCode, resp.errorText);
            }
        }
    });

}

$(document).ready(function () {
    console.log("ready!");
    // $('#typename_id').attr("data-nn", 0);
    // $('#brandname_id').attr("data-nn", 1);
    // $('#modelname_id').attr("data-nn", 2);
    // $('#typename_id').addClass("chosen").chosen();
    // $('#brandname_id').addClass("chosen").chosen();
    // $('#modelname_id').addClass("chosen").chosen();


    // $('#price_crit_weight_status_id').attr("readonly", true).val(0).find('option').attr('disabled', true);
    // loadTypesList(0, 'typename_id');

    saleMonthToClone = $('#cloneContainerDiv').find('div.sale-month');
    saleRowToClone = $('#cloneContainerDiv').find('tr.sale-row');
    getSales();
    // techPriceForm.find('i.fa-times').trigger('click');
});
