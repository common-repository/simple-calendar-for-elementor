<?php if (!did_action("elementor/loaded")) {
  echo "Please install and enable Elementor first";
    return false;
} ?>

<div class="flex">
  <div id="miga_calendar_half_cal" class="miga_calendar_half">
    <?php miga_ajax_editor_cal(); ?>
</div>
<div class="miga_calendar_half">
  <h2><?php echo esc_html__("Events", "simple-calendar-for-elementor"); ?></h2>
  <p><?php echo wp_kses_post(__("Select a calendar and add an event to a day. To add or change a status go to the <b>Status/Classes</b> tab.", "simple-calendar-for-elementor")); ?></p>
  <h2><?php echo esc_html__("Custom colors", "simple-calendar-for-elementor"); ?></h2>
  <p><?php echo wp_kses_post(__("Colors for the custom classes are only visible in the frontend.", "simple-calendar-for-elementor")); ?></p>
</div>
</div>
