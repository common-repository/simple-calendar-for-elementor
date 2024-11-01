<?php

global $wpdb;
$table_name = TABLE_NAME_MIGA_CAL_STATUS;

if (isset($_POST["submit"])) {
    $wpdb->insert($table_name, [
        "status" => sanitize_text_field($_POST["status"]),
        "class" => sanitize_text_field($_POST["class"]),
    ]);
}

$results = $wpdb->get_results("SELECT * FROM $table_name ");
?>
<div class="flex">
<form class="miga_calendar_half" method="post" action="?page=miga_calendar-page&tab=classes">
<table class="miga_calendar_status">
<thead>
  <tr>
    <th class="th_small"><?php echo esc_html__("Visible","simple-calendar-for-elementor"); ?></th>
    <th><?php echo esc_html__("Status/Name","simple-calendar-for-elementor"); ?></th>
    <th><?php echo esc_html__("CSS class", "simple-calendar-for-elementor"); ?></th>
  </tr>
</thead>
<tbody>
<?php if (!empty($results)) {
    foreach ($results as $row) {
        echo "<tr>";
        echo '<td class="th_small"><input type="checkbox" class="status_visible" ' .
            ($row->visible ? "checked" : "") .
            ' value="1"/></td>';
        echo '<td><input type="text" class="status_status" value="' .
            esc_html($row->status) .
            '"/></td>';
        echo "<td>";
        if ($row->fixed == 0) {
            echo '<input type="text" class="status_class" value="' .
                esc_html($row->class) .
                '"/>';
        } else {
            echo '<div class="status_class">' . esc_html($row->class) . "</div>";
        }
        "</td>";
        echo "<td>";
        echo '<button data-value="' .
            (int) $row->id .
            '" onclick="return miga_calendar_updateItem(this);">' .
            esc_html__("update", "simple-calendar-for-elementor") .
            "</button>";

        if ($row->fixed == 0) {
            echo '<button data-value="' .
                (int) $row->id .
                '" onclick="return miga_calendar_deleteItem(this);">' .
                esc_html__("delete", "simple-calendar-for-elementor") .
                "</button>";
        }
        echo "</td>";
        echo "</tr>";
    }
} ?>
<tr><td colspan="4"><hr/></td></tr>
<tr><td colspan="4"><b><?php echo esc_html__("Add new status","simple-calendar-for-elementor"); ?>:</b></td></tr>
<tr><td></td><td><input type="text" name="status" id="status" value="" placeholder="<?php echo esc_html__(
    "Add status",
    "simple-calendar-for-elementor"
); ?>"/></td>
<td><input type="text" value="" name="class" id="class" placeholder="<?php echo esc_html__(
    "Add class",
    "simple-calendar-for-elementor"
); ?>"/></td>
<td class="submit_button"><?php echo submit_button(esc_html__("add", "simple-calendar-for-elementor")); ?></td></tr>
</tbody>
</table>
</form>
  <div class="miga_calendar_half">
    <h2><?php echo esc_html__("Status and classes", "simple-calendar-for-elementor"); ?></h2>
    <p><?php echo wp_kses_post(__("Here you define the status options you can pick for each day. The status name will be visible in the legend and the class will be added to the day.<br/><b>half_blocked</b> and <b>blocked</b> are deafult options.", "simple-calendar-for-elementor")); ?></p>
    <h3><?php echo esc_html__("Add new status/classes", "simple-calendar-for-elementor"); ?></h3>
    <p><?php echo wp_kses_post(__("Below the table you can add a new status name and a class. Go to Appearance - Customizer you can set the styles for that class.<br/>Use <code>.miga_calendar .calendar__day.your_class {};</code> and <code>.miga_calendar .calendar__legend_item.your_class {};</code> to set your styles.", "simple-calendar-for-elementor")); ?></p>
  </div>
</div>
