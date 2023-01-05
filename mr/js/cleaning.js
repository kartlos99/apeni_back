let cleaningTable = $("#tbCleaning").find('tbody');

$(document).ready(function () {
    getRegions();
    getCleaningData();
});

function getCleaningData() {
    $.ajax({
        url: 'mobile/other/getCleaningList.php',
        dataType: 'json',
        headers: getHeaders(),
        success: function (response) {

            console.log(response)
            if (response.success) {
                cleaningTable.empty()
                response.data.forEach(function (item) {
                    cleaningTable.append(dataToRow(item))
                })
            } else {
                showError(resp.errorCode, resp.errorText);
            }
        }
    });
}

function dataToRow(item) {
    let tdCustomer = $('<td />').text(item.dasaxeleba);
    let tdDate = $('<td />').text(item.clearDate).addClass("ricxvi");
    let tdDays = $('<td />').text(item.passDays).addClass("ricxvi");
    return $('<tr />').append(tdCustomer, tdDate, tdDays);
}