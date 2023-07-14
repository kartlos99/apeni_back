<?php
include_once '_header.php';
?>

    <br>
<!--    <table class="table">
        <tr>
            <td>
                <label>თარიღი</label>
            </td>
            <td>
                <input id="date1" type="date">
                <button id='btnRefresh'>განახლება</button>
            </td>
        </tr>
    </table>-->

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

<?php include_once '_footer.php'; ?>