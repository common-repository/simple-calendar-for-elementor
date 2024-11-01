<?php

class MIGA_Simple_Calendar_Legend extends \Elementor\Widget_Base
{
    public function get_script_depends()
    {
        return ["miga_calendar_script"];
    }

    public function get_name()
    {
        return "simple-calendar-for-elementor-legend";
    }

    public function get_title()
    {
        return __("Simple Calendar (Legend)", "simple-calendar-for-elementor");
    }

    public function get_icon()
    {
        return "eicon-calendar";
    }

    public function get_categories()
    {
        return ["general"];
    }

    protected function _register_controls()
    {
        $this->start_controls_section("content_section", [
          "label" => __("Content", "simple-calendar-for-elementor"),
            "tab" => \Elementor\Controls_Manager::TAB_CONTENT,
        ]);

        $this->add_control(
            'show_title',
            [
                'label' => esc_html__('Show Title', 'simple-calendar-for-elementor'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Show', 'simple-calendar-for-elementor'),
                'label_off' => esc_html__('Hide', 'simple-calendar-for-elementor'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control("squared_days", [
            "label" => __("Squared days", "simple-calendar-for-elementor"),
            "type" => \Elementor\Controls_Manager::SWITCHER,
            "label_on" => __("yes", "simple-calendar-for-elementor"),
            "label_off" => __("no", "simple-calendar-for-elementor"),
            "return_value" => __("yes", "simple-calendar-for-elementor"),
            "default" => __("no", "simple-calendar-for-elementor"),
        ]);

        $this->end_controls_section();

        require("includes/legend_styles.php");
    }

    public function get_style_depends()
    {
        wp_register_style(
            "miga_calendar_styles",
            plugins_url("../styles/main.css", __FILE__)
        );
        return ["miga_calendar_styles"];
    }

    protected function render()
    {
        $isEditor = \Elementor\Plugin::$instance->editor->is_edit_mode();
        $settings = $this->get_settings_for_display();
        $round_days = ($settings["squared_days"]=="yes") ? 'miga_calendar_box_squared' : '';



        echo '<div class="miga_calendar">';
        global $wpdb;
        $table_name = TABLE_NAME_MIGA_CAL_STATUS;
        $results =$wpdb->get_results(("SELECT * FROM $table_name"));
        if (!empty($results)) {
            echo '<div class="calendar__legend '.$round_days.'">';
            if ($settings["show_title"]=="yes") {
                echo '<div class="calendar__legend_title">' . esc_html__("Legend", "simple-calendar-for-elementor") . "</div>";
            }
            foreach ($results as $row) {
                if ($row->visible) {
                    echo '<div class="calendar__legend_row"><div class="calendar__legend_item ' .
                        esc_html($row->class) .
                        '"></div><div class="calendar__legend_row_status">' .
                        esc_html($row->status) .
                        "</div></div>";
                }
            }
            echo "</div>";
        }
        echo "</div>";
    }
}
