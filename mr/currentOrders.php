<?php
include_once '_header.php';

$phesh = hash('sha256', 'as');

$dataProvider = new DataProvider($con);
$barrels = $dataProvider->getBarrels();
//echo $phesh;
?>

    <div id="dateDiv">
        <label for="orderDate">აირჩიეთ თარიღი: </label>
        <input id="orderDate" type="date">
        <button id="btnLoadOrders" class="btn">ჩატვირთვა</button>
    </div>

    <div class="order-list"></div>

    <div id="cloneContainerDiv" class="hidden">
        <div class="order-unit ">
            <div class="order-header">
                <table class="table ">
                    <tr>
                        <td class="client title-field"></td>
                        <td class="distributor title-field"></td>
                        <td class="order-status title-field"></td>
                        <td class="order-chek"></td>
                    </tr>
                </table>
            </div>
            <div class="order-body ">
                <table class="table ">
                    <thead>
                    <th>ლუდი</th>
                    <?php foreach ($barrels as $key => $br) : ?>
                        <th><?= $br['dasaxeleba'] ?></th>
                    <?php endforeach; ?>
                    </thead>
                    <tbody class="order-rows-container">

                    </tbody>
                </table>
            </div>
            <div class="order-comment "></div>
        </div>
        <table>
            <tr class="order-row">
                <td class="beer-name">ლაგერი</td>
                <td class="4"></td>
                <td class="3"></td>
                <td class="2"></td>
                <td class="1"></td>
            </tr>
        </table>
    </div>

<?php include_once '_footer.php'; ?>