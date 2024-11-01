<?php

function miga_ajax_editor_cal_delete()
{
    global $wpdb;
    $table_name = TABLE_NAME_MIGA_CAL_STATUS;

    $wpdb->delete($table_name, [
        "id" => (int) $_POST["s"],
    ]);
}

function miga_ajax_editor_cal_update()
{
    global $wpdb;
    $table_name = TABLE_NAME_MIGA_CAL_STATUS;

    $wpdb->update(
        $table_name,
        [
            "status" => sanitize_text_field($_POST["n"]),
            "class" => sanitize_text_field($_POST["c"]),
            "visible" => (int) $_POST["v"],
        ],
        ["id" => (int) $_POST["s"]]
    );
}

function miga_ajax_editor_cal()
{
    require_once ABSPATH . "wp-admin/includes/admin.php";
    global $wpdb;
    $month = date("m");
    $year = date("Y");

    if (isset($_POST["m"])) {
        $month = (int) $_POST["m"];
    }
    if (isset($_POST["y"])) {
        $year = (int) $_POST["y"];
    }
    $currentCal = 0;

    if (isset($_GET["cal"])) {
        $currentCal = (int) $_GET["cal"];
    }

    if (isset($_POST["c"])) {
        $currentCal = (int) $_POST["c"];
    }

    if (isset($_POST["calendar"])) {
        $currentCal = (int) $_POST["calendar"];
    }

    $table_name_cal = TABLE_NAME_MIGA_CAL_CALENDAR;
    $results = $wpdb->get_results(("SELECT * FROM $table_name_cal"));
    echo '<div class="flex align-middle editor_header">';
    echo "<b>" . esc_html__("Current calendar", "simple-calendar-for-elementor") . '</b>';

    $cal = [];
    $hasCalendar = !empty($results);
    if ($hasCalendar) {
      echo '<select id="miga_calendar_select" onchange="miga_calendar_onchangeCal();">';
        if ($currentCal == 0) {
            $currentCal = $results[0]->id;
        }
        foreach ($results as $row) {
            $sel = "";
            if ($row->id == $currentCal) {
                $sel = " selected ";
            }
            echo '<option value="' . esc_html($row->id) . '" ' . esc_html($sel) .">" . esc_html($row->title) ."</option>";
        }
        echo "</select>";
    } else {
      echo '<span id="miga_calendar_select"> - </span>';
    }

    $days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    $status = [];
    $table_name = TABLE_NAME_MIGA_CAL_EVENTS;
    $table_name_status = TABLE_NAME_MIGA_CAL_STATUS;

    if (isset($_POST["submit"])) {
        $data = array_map('sanitize_text_field', $_POST["miga_calendar_days"]);
        $eYear = (int) sanitize_key($_POST["eYear"]);
        $eMonth = (int) sanitize_key($_POST["eMonth"]);
        $cal = (int) sanitize_key($_POST["calendar"]);

        foreach ($data as $key => $item) {

          $results =$wpdb->get_results( $wpdb->prepare("SELECT * FROM $table_name WHERE calendar=%d AND DAY(date)=%d AND MONTH(date)=%d AND YEAR(date)=%d",
            $cal,
            $key,
            $eMonth,
            $eYear
          ));

            if (!empty($results)) {
                $wpdb->update(
                    $table_name,
                    [
                        "date" => date(
                            "Y-m-d",
                            strtotime($eYear . "-" . $eMonth . "-" . $key)
                        ),
                        "status" => esc_html($item),
                        // "title" => $texts[$key],
                        "calendar" => (int) $cal,
                    ],
                    ["id" => $results[0]->id]
                );
            } else {
                if (!empty($item)) {
                    $wpdb->insert($table_name, [
                        "date" => date(
                            "Y-m-d",
                            strtotime($eYear . "-" . $eMonth . "-" . $key)
                        ),
                        "status" => esc_html($item),
                        "calendar" => (int) $cal,
                    ]);
                }
            }
        }
    }

    $results =$wpdb->get_results( $wpdb->prepare("SELECT a.title, a.date, b.id AS statusId,b.visible, b.status, b.class FROM $table_name AS a LEFT JOIN $table_name_status AS b ON a.status = b.id  WHERE calendar=%d AND MONTH(date)=%d AND YEAR(date)=%d",
    $currentCal,
    $month,
    $year
    ));

    if (!empty($results)) {
        foreach ($results as $row) {
            $replace = [
                (int) date("d", strtotime($row->date)) => [
                    "statusId" => (int) $row->statusId,
                    "class" => esc_html($row->class),
                    "title" => esc_html($row->title),
                    "visible" => (int) $row->visible,
                ],
            ];
            $status = array_replace($status, $replace);
        }
    }

    echo '<input type="hidden" value="' .
        esc_html($currentCal) .
        '" id="currentCalendar"/>';
    echo "";
    echo "</div>";

    echo '<div class="flex miga_calendar" data-year="'.date("Y").'" data-month="'.date("m").'">';
    echo '<div class="static_cal">';
    require "Calendar.class.php";
    $m = new Miga_calendar_Month($month, $year);
    if ($wpdb != null) {
        $table_name = TABLE_NAME_MIGA_CAL_EVENTS;
        $table_name_status = TABLE_NAME_MIGA_CAL_STATUS;

        $results =$wpdb->get_results( $wpdb->prepare("SELECT a.date, b.status, b.class,b.visible FROM $table_name AS a LEFT JOIN $table_name_status AS b ON a.status = b.id WHERE calendar=%d AND MONTH(date)=%d AND YEAR(date)=%d",
        $currentCal,
        $month,
        $year
        ));

        if (!empty($results)) {
            foreach ($results as $row) {
                if ($row->visible) {
                    // only show visible events
                    $m->setStatus(
                        date("d", strtotime($row->date)),
                        $row->class,
                        $row->status
                    );
                }
            }
        }
    }

    $options = [];

    if ($wpdb != null) {
        $table_name = TABLE_NAME_MIGA_CAL_STATUS;
        $results = $wpdb->get_results("SELECT * FROM $table_name");
        if (!empty($results)) {
            foreach ($results as $row) {
                if ($row->visible) {
                    // only add visible options
                    $options[] = [
                        "id" => $row->id,
                        "status" => $row->status,
                        "class" => $row->class,
                        "visible" => $row->visible,
                    ];
                }
            }
        }
    }

    $m->render([
        "editorMenu" => true,
    ]);
    echo "</div>";
    if ($hasCalendar) {

        echo '<form class="miga_calendar_events" method="post" action="?page=miga_calendar-page&tab=events">';
        settings_fields("miga_calendar_days_option_group");
        ?>
    <table>
      <thead>
        <tr>
          <th><?php echo esc_html__("Day", "simple-calendar-for-elementor"); ?></th>
          <th><?php echo esc_html__("Status", "simple-calendar-for-elementor"); ?></th>
          <!-- <th><?php /*echo esc_html__("Description", "simple-calendar-for-elementor");*/ ?></th> -->
        </tr>
      </thead>
      <tbody>

      <?php
      for ($i = 0; $i < $days; ++$i) {
          $sel1 = "";
          $sel2 = "";
          $title = "";
          if (isset($status[$i + 1])) {
              $title = $status[$i + 1]["title"];
          }

          $class = "";
          if (isset($status[$i + 1])) {
              if ($status[$i + 1]["visible"]) {
                  $class = $status[$i + 1]["class"]. " hasClass";
              }
          }

          echo '<tr><td><label class="calendar__editor__day '. esc_html($class) . '">' . esc_html($i + 1) . "</label></td><td>";
          echo '<select id="miga_calendar_id_' .
              (int)($i + 1) .
              '" name="miga_calendar_days[' .
              (int)($i + 1) .
              ']">';
          echo '<option value="">-</option>';
          foreach ($options as $option) {
              $sel = "";
              if (
                  isset($status[$i + 1]) &&
                  $status[$i + 1]["statusId"] == $option["id"]
              ) {
                  $sel = " selected ";
              }
              echo '<option value="' .
                  esc_html($option["id"]) .
                  '"' .
                  esc_html($sel) .
                  ">" .
                  esc_html($option["status"]) .
                  "</option>";
          }
          echo "</select></td>";
          echo "</tr>";
      }
      echo "</tbody></table>";
      submit_button();
      echo '<input type="hidden"  name="eMonth" id="eMonth" value="' . esc_html($month) .'"/>';
      echo '<input type="hidden"  name="eYear" id="eYear" value="' . esc_html($year) .'"/>';
      echo '<input type="hidden"  name="calendar" id="calendar" value="' . esc_html($currentCal) .'"/>';
      echo "</form>";
    } else {
        echo '<p style="flex-shrink:0; margin-left: 10px">' . esc_html__("No calendar found. Please add a calendar first.", "simple-calendar-for-elementor") ."</p>";
    }
    echo "</div>";

    if (isset($_POST["m"])) {
        die();
    }
}
