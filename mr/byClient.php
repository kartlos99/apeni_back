<?php
include_once '_header.php';

$dataProvider = new DataProvider($con);
$clients = $dataProvider->getClients();
?>
    <br>
    <!-- Nav tabs -->
    <ul class="nav nav-tabs nav-justified" role="tablist">
        <li role="presentation" class="active">
            <a href="#salesContainer" aria-controls="sales" role="tab" data-toggle="tab">რეალიზაცია</a>
        </li>
        <li role="presentation">
            <a href="#debtContainer" aria-controls="debt" role="tab" data-toggle="tab">დავალიანება</a>
        </li>
    </ul>

    <!-- Tab panes -->
    <div class="tab-content">
        <div role="tabpanel" class="tab-pane active" id="salesContainer">
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
        </div>
        <div role="tabpanel" class="tab-pane" id="debtContainer">
            <br>
            <button id="btnExportDebt" class="btn">ექსპორტი</button>
            <br>
            <table id="tbDebt" class="table table-section debt-table">
                <thead>
                <tr>
                    <th id="debtTitleName">ობიექტი</th>
                    <th id="debtTitleAmount" class="textToEnd">თანხა</th>
                    <th id="debtTitleBarrel10" class="textToEnd">10-იანი</th>
                    <th id="debtTitleBarrel20" class="textToEnd">20-იანი</th>
                    <th id="debtTitleBarrel30" class="textToEnd">30-იანი</th>
                    <th id="debtTitleBarrel50" class="textToEnd">50-იანი</th>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>

        </div>
    </div>



<?php include_once '_footer.php'; ?>