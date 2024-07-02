<?php
include_once '_header.php';

$dataProvider = new DataProvider($con);
$barrels = $dataProvider->getBarrels();
?>

    <br>
    <!-- Nav tabs -->
    <ul class="nav nav-tabs nav-justified" role="tablist">
        <li role="presentation" class="active">
            <a href="#balanceContainer" aria-controls="balance" role="tab" data-toggle="tab">მიმდინარე მდგომარობა</a>
        </li>
        <li role="presentation">
            <a href="#monthlyInputContainer" aria-controls="debt" role="tab" data-toggle="tab">თვის ჭრილში</a>
        </li>
    </ul>

    <!-- Tab panes -->
    <div class="tab-content">
        <div role="tabpanel" class="tab-pane active" id="balanceContainer">

            <h4 class="storeHouseTitle">სავსე კასრები</h4>
            <table id="tbFullBarrels" class="table table-section">
                <thead>
                <tr>
                    <th>ლუდი</th>
                    <th class="textToEnd">10-იანი</th>
                    <th class="textToEnd">20-იანი</th>
                    <th class="textToEnd">30-იანი</th>
                    <th class="textToEnd">50-იანი</th>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>

            <h4 class="storeHouseTitle">ცარიელი კასრები</h4>
            <table id="tbEmptyBarrels" class="table table-section">
                <thead>
                <tr>
                    <th></th>
                    <th class="textToEnd">10-იანი</th>
                    <th class="textToEnd">20-იანი</th>
                    <th class="textToEnd">30-იანი</th>
                    <th class="textToEnd">50-იანი</th>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>

            <h4 class="storeHouseTitle">ბოთლები</h4>
            <table id="tbBottles" class="table table-section">
                <thead>
                <tr>
                    <th class="textToEnd">დასახელება</th>
                    <th class="textToEnd">რაოდენობა</th>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
        <div role="tabpanel" class="tab-pane" id="monthlyInputContainer">

            <label for="selectYear">აირჩიეთ წელი</label>
            <select id="selectYear" class="form-control"></select>
            <br>
            <div class="mainContainer"></div>
        </div>
    </div>


    <div id="cloneContainerDiv" class="hidden">
        <div class="panel panel-primary barrel-input-month">
            <div class="panel-heading">
                Tve
            </div>
            <div class="panel-body">
                <table class="table table-section">
                    <thead>
                    <th>დასახელება</th>
                    <th>ლიტრაჟი</th>
                    <?php foreach ($barrels as $key => $br) : ?>
                        <th><?= $br['dasaxeleba'] ?></th>
                    <?php endforeach; ?>
                    </thead>
                    <tbody class="barrel-input-items">

                    </tbody>
                </table>
            </div>
        </div>
        <table>
            <tr class="barrels-row">
                <td class="beer-name">a</td>
                <td class="liter">a</td>
                <?php foreach ($barrels as $key => $br) : ?>
                    <td class="<?= $br['dasaxeleba'] ?>">-</td>
                <?php endforeach; ?>
            </tr>
        </table>
    </div>

<?php include_once '_footer.php'; ?>