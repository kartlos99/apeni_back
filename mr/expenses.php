<?php
include_once '_header.php';
?>

    <br>
    <table class="table">
        <tr>
            <td>
                <label>აირჩიეთ პერიოდი</label>
            </td>
            <td>
                <input id="date1" type="date">-დან <input id="date2" type="date">-მდე
                <button id='btnRefresh'>განახლება</button>
            </td>
        </tr>
    </table>

    <table id="tbExpenses" class="table table-section">
        <thead>
        <tr>
            <th>თარიღი</th>
            <th>ოპერატორი</th>
            <th>კომენტარი</th>
            <th class="textToEnd">თანხა ₾</th>
        </tr>
        </thead>
        <tbody>
        </tbody>
    </table>

<?php include_once '_footer.php'; ?>