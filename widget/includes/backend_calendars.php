<div class="flex">
  <div class="miga_calendar_half">
<?php
require "custom_table.php";

global $wpdb;
$table = new Miga_calendar_events_List_Table();
$table_name = TABLE_NAME_MIGA_CAL_CALENDAR;

if (isset($_POST["submit"])) {
    if (!empty($_POST["title"])) {
        $title = sanitize_text_field($_POST["title"]);
        $wpdb->insert($table_name, [
            "title" => esc_attr($title),
        ]);
    }
}

$table->prepare_items();
$table->display();
?>
<br/>

<form method="post" class="miga_calendar_calendar" action="?page=miga_calendar-page">
<input type="text" value="" name="title" id="title" placeholder="<?php echo esc_html__("Add calendar","simple-calendar-for-elementor"); ?>"/>
<?php echo submit_button(esc_html__("add", "simple-calendar-for-elementor")); ?>
</form>

</div>
<div class="miga_calendar_half">
  <h2><?php echo esc_html__("Calendar", "simple-calendar-for-elementor"); ?></h2>
  <p><?php echo esc_html__("Create different calendars you can select in the Elementor Calendar widget. Each calendar can have it's own events.", "simple-calendar-for-elementor"); ?></p>
</div>
</div>
