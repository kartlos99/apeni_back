<?php
include_once '_header.php';

$dataProvider = new DataProvider($con);
$clients = $dataProvider->getClients();
?>
    <br>
    <table class="table">
        <tr>
            <td>
                <label>აირჩიეთ პერიოდი</label>
            </td>
            <td>
                <input id="date1" type="date">-დან <input id="date2" type="date">-მდე
                <!--            <button id='btnRefresh' >განახლება </button>-->
                <!--            <i class="material-icons">refresh</i>-->
            </td>
        </tr>
        <tr>
            <td>
                <label for="selectClient">აირჩიეთ ობიექტი</label>
            </td>
            <td>
                <select id="selectClient" class="form-control">
                    <option value="0">ყველა ობიექტი</option>
                    <?php foreach ($clients as $client) : ?>
                        <option value="<?= $client['id'] ?>"><?= $client['dasaxeleba'] ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
    </table>

<table width="100%">
    <tr>
        <td>
            <button id="btnDone" class="btn">ჩამოტვირთვა</button>
            <button id="btnUpdateChart" class="btn">დიაგრამის განახლება</button>
            <button id="btnExportDebt" class="btn">დავალიანების ექსპ.</button>
        </td>
        <td>
            <div id="summary" style="float: right"></div>
        </td>
    </tr>
</table>





    <div>
        <div id=container1></div>
        <div id=container2></div>
    </div>

<?php include_once '_footer.php'; ?>