<?php

$today = date("Y-m-d", time());

function makeHeadRow($columns)
{
    $newRow = "<tr>";
    foreach ($columns as $item) {
        $newRow .= "<th>" . $item . "</th>";
    }
    return $newRow . "</tr>";
}

function makeDataRow($columns)
{
    $newRow = "<tr>";
    foreach ($columns as $key => $val) {
        $newRow .= "<td>" . $val . "</td>";
    }
    return $newRow . "</tr>";
}

function getDataAsTable($conn, $sqlScript, $columns) {

    $result = mysqli_query($conn, $sqlScript);
    $arr = [];
    if ($result) {
        foreach ($result as $row) {
            $arr[] = $row;
        }
    } else {
        echo mysqli_error($conn);
    }

    $output = '<table bordered="3">';
    $output .= makeHeadRow($columns);

    foreach ($arr as $row) {
        $output .= makeDataRow($row);
    }
    $output .= '</table>';
    return $output;
}

function exportToExcel($conn, $sqlScript, $columns, $fileName) {
    header("Content-type: application/octet-stream");
    header("Content-Disposition: attachment; filename=$fileName.xls");
    header("Pragma: no-cache");
    header("Expires: 0");
    echo getDataAsTable($conn, $sqlScript, $columns);
    mysqli_close($conn);
}

function exportToFile($conn, $sqlScript, $columns, $fileName) {
    $myfile = fopen($fileName . ".txt", "w") or die("Unable to open file!");
    fwrite($myfile, getDataAsTable($conn, $sqlScript, $columns));
    fclose($myfile);
    mysqli_close($conn);
}