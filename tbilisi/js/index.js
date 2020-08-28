// let mTkn = window.localStorage.getItem('tkn');
printout(tkn);

$.ajax({
    url: 'webApi/getClients.php',
    dataType: 'json',
    headers: {
        'Authorization': tkn
    },
    success: function (resp) {
        console.log(resp);
    }
})