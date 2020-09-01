<?php
include_once '_header.php';

$dataProvider = new DataProvider($con);
$clients = $dataProvider->getClients();
?>

    <label for="selectClient">აირჩიეთ ობიექტი</label>
    <select id="selectClient" class="form-control">
        <option value="0">ყველა ობიექტი</option>
        <?php foreach ($clients as $client) : ?>
            <option value="<?= $client['id'] ?>"><?= $client['dasaxeleba'] ?></option>
        <?php endforeach; ?>
    </select>
    <br>
    <button id="btnDone" class="btn">ჩამოტვირთვა</button>


<?php include_once '_footer.php'; ?>