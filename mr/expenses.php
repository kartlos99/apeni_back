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
            <td>მაქს. 500 ჩანაწერი</td>
            <td>
                <div style="float: right">
                    <button id="exportExpensesBtn" class="btn">ექსპორტი</button>
                </div>
            </td>
        </tr>
    </table>

    <div class="mainContainer"></div>

    <div id="cloneContainerDiv" class="hidden">
        <div class="panel panel-primary expenses-day">
            <div class="panel-heading">
                day
            </div>
            <div class="panel-body">
                <table id="tbExpenses" class="table table-section">
                    <thead>
                    <tr>
                        <th>თარიღი</th>
                        <th>ოპერატორი</th>
                        <th>კატეგორია</th>
                        <th>კომენტარი</th>
                        <th class="textToEnd">თანხა ₾</th>
                    </tr>
                    </thead>
                    <tbody class="day-item">
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<?php include_once '_footer.php'; ?>