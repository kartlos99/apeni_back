
$('#btnDone').on('click', function (e) {
    clientID = $('#selectClient').val();
    window.location.href = "../commonWeb/php/clientDataToExcel.php?clientID=" + clientID;
});

$(document).ready(function () {
    console.log("ready!");
});