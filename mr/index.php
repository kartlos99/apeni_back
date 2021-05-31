<?php

include_once '_header.php';

$phesh = hash('sha256', 'as');

$dataProvider = new DataProvider($con);
$barrels = $dataProvider->getBarrels();
//echo $phesh;
?>

    <select id="selectYear" class="form-control">
        <!--        <option value="0"></option>-->
        <!--        --><?php //foreach ($clients as $client) : ?>
        <!--            <option value="--><? //= $client['id'] ?><!--">-->
        <? //= $client['dasaxeleba'] ?><!--</option>-->
        <!--        --><?php //endforeach; ?>
    </select>

    <div>
        <div id=container1></div>
        <div id=container2></div>
    </div>

    <div class="mainContainer">

    </div>

    <div id="cloneContainerDiv" class="hidden">
        <div class="panel panel-primary sale-month">
            <div class="panel-heading">
                Tve
            </div>
            <div class="panel-body">
                <table class="table table-section">
                    <thead>
                    <th>დასახელება</th>
                    <th>ღირებულება</th>
                    <th>ლიტრაჟი</th>
                    <?php foreach ($barrels as $key => $br) : ?>
                        <th><?= $br['dasaxeleba'] ?></th>
                    <?php endforeach; ?>
                    </thead>
                    <tbody class="beer-sale-items">

                    </tbody>
                </table>
            </div>
        </div>
        <table>
            <tr class="sale-row">
                <td class="beer-name">a</td>
                <td class="price">a</td>
                <td class="litraji">a</td>
                <?php foreach ($barrels as $key => $br) : ?>
                    <td class="<?= $br['dasaxeleba'] ?>">-</td>
                <?php endforeach; ?>
            </tr>
        </table>
    </div>


<?php include_once '_footer.php'; ?>