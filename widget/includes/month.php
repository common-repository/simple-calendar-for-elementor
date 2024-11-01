<?php

function miga_ajax_functions_cal($calendar, $options = [])
{
    global $wpdb;
    $month = date("m");
    $year = date("Y");
    require_once "Calendar.class.php";
    $showButton = false;
    $fixedMonth = false;
    $showTitle = false;
    $selectedMonth = -1;
    $selectedYear = -1;

    if (isset($options["showButton"])) {
      $showButton = $options["showButton"];
    }
    if (isset($options["fixedMonth"])) {
      $fixedMonth = $options["fixedMonth"];
    }
    if (isset($options["selectedMonth"])) {
      $selectedMonth = $options["selectedMonth"];
    }
    if (isset($options["selectedYear"])) {
      $selectedYear = $options["selectedYear"];
    }
    if (isset($options["showTitle"])) {
      $showTitle = (bool)$options["showTitle"];
    }

    if (isset($_POST["m"])) {
        $month = (int) $_POST["m"];
    }
    if (isset($_POST["y"])) {
        $year = (int) $_POST["y"];
    }
    if (isset($_POST["c"])) {
        $calendar = (int) $_POST["c"];
    }
    if (isset($_POST["sb"])) {
        $showButton = (bool) $_POST["sb"];
    }
    if (isset($_POST["st"])) {
        $showTitle = (bool) $_POST["st"];
    }
    if ($fixedMonth) {
        $showButton = false;
        if ($selectedMonth > -1) {
            $month = (int) $selectedMonth;
        }
        if ($selectedYear > -1) {
            $year = (int) $selectedYear;
        }
    }

    $m = new Miga_calendar_Month($month, $year);

    if ($wpdb != null) {
        $table_name = TABLE_NAME_MIGA_CAL_EVENTS;
        $table_name_status = TABLE_NAME_MIGA_CAL_STATUS;

        $results = $wpdb->get_results($wpdb->prepare(
            "SELECT a.date, b.status, b.class FROM $table_name AS a LEFT JOIN $table_name_status AS b ON a.status = b.id WHERE calendar=%d AND MONTH(date)=%d AND YEAR(date)=%d",
            $calendar,
            $month,
            $year
        ));

        if (!empty($results)) {
            foreach ($results as $row) {
                $m->setStatus(date("d", strtotime($row->date)), $row->class, $row->status);
            }
        }
    }

    $m->render([
        "menu" => true,
        "fixedMonth" => $fixedMonth,
        "showButton" => $showButton,
        "showTitle" => $showTitle
    ]);

    if (isset($_POST["m"])) {
        die();
    }
}
