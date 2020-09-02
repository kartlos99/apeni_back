
var currDate = new Date();
var strDate1 = dateformat(currDate);
currDate.setDate(currDate.getDate() + 1);
var strDate2 = dateformat(currDate);

let dateInput1 = $('#date1');
let dateInput2 = $('#date2');

$('#btnDone').on('click', function (e) {
    clientID = $('#selectClient').val();
    window.location.href = "../commonWeb/php/clientDataToExcel.php?clientID=" + clientID
    + "&startDate=" + dateInput1.val() + "&endDate=" + dateInput2.val();
});

$(document).ready(function () {
    console.log("ready!");

    dateInput1.attr('max', strDate1);

    dateInput2.val(strDate2).attr('max', strDate2);
    currDate.setDate(1);
    strDate1 = dateformat(currDate);
    dateInput1.val(strDate1);
});