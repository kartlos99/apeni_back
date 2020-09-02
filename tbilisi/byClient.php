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

    <button id="btnDone" class="btn">ჩამოტვირთვა</button>


<?php include_once '_footer.php'; ?>