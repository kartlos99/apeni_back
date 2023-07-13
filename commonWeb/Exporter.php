<?php

class Exporter
{

    const TAG_TH = "th";
    const TAG_TD = "td";

    private function makeRow($columns, $teg = self::TAG_TD): string
    {
        $hRow = "<tr>";
        foreach ($columns as $item) {
            $hRow .= "<" . $teg . ">" . $item . "</" . $teg . ">";
        }
        return $hRow . "</tr>";
    }

    function exportData($columns, $rows, $filename) {
        $output = '<table class="table" bordered="1">';
        $output .= $this->makeRow($columns, self::TAG_TH);
        foreach ($rows as $row) {
            $output .= $this->makeRow($row);
        }
        $output .= '</table>';

        header("Content-Type: application/xls");
        header("Content-Disposition: attachment; filename=$filename.xls");
        echo $output;
    }
}