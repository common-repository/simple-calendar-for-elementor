<?php

class MIGA_Simple_Calendar extends \Elementor\Widget_Base
{
    public function __construct($data = [], $args = null)
    {
        parent::__construct($data, $args);
        wp_register_script(
            "miga_calendar_script",
            plugin_dir_url(__FILE__) . "../scripts/main.js",
            [],
            "1.0.0",
            true
        );

        wp_localize_script("miga_calendar_script", "miga_calendar", [
            "miga_nonce" => wp_create_nonce("miga_nonce"),
            "wp_url" => admin_url("admin-ajax.php"),
        ]);
    }

    public function get_script_depends()
    {
        return ["miga_calendar_script"];
    }

    public function get_name()
    {
        return "simple-calendar-for-elementor";
    }

    public function get_title()
    {
        return __("Simple Calendar", "simple-calendar-for-elementor");
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

        global $wpdb;
        $table_name_cal = TABLE_NAME_MIGA_CAL_CALENDAR;
        $results = $wpdb->get_results(("SELECT * FROM $table_name_cal"));
        $cal = [];
        $default = "";
        if (!empty($results)) {
            foreach ($results as $row) {
                $cal[$row->id] = esc_attr($row->title);
                if (empty($default)) {
                    $default = (int) $row->id;
                }
            }
        }

        $this->add_control("calendar", [
            "label" => __("Calendar", "simple-calendar-for-elementor"),
            "type" => \Elementor\Controls_Manager::SELECT,
            "options" => $cal,
            "default" => $default,
        ]);

        $this->add_control("layout", [
            "label" => __("Layout", "simple-calendar-for-elementor"),
            "type" => \Elementor\Controls_Manager::SELECT,
            "options" => [
                "" => "Default",
                "miga_calendar_full" => __("Full width", "simple-calendar-for-elementor"),
            ],
            "default" => "",
        ]);

        $this->add_control("squared_days", [
            "label" => __("Squared days", "simple-calendar-for-elementor"),
            "type" => \Elementor\Controls_Manager::SWITCHER,
            "label_on" => __("yes", "simple-calendar-for-elementor"),
            "label_off" => __("no", "simple-calendar-for-elementor"),
            "return_value" => "yes",
            "default" => "no",
            'condition' => [
                'layout' => "",
            ],
        ]);

        $this->add_control("show_legend", [
            "label" => __("Show legend", "simple-calendar-for-elementor"),
            "type" => \Elementor\Controls_Manager::SWITCHER,
            "label_on" => __("Show", "simple-calendar-for-elementor"),
            "label_off" => __("Hide", "simple-calendar-for-elementor"),
            "return_value" => "yes",
            "default" => "no",
        ]);

        $this->add_control("show_today", [
            "label" => __("Show today button", "simple-calendar-for-elementor"),
            "type" => \Elementor\Controls_Manager::SWITCHER,
            "label_on" => __("Show", "simple-calendar-for-elementor"),
            "label_off" => __("Hide", "simple-calendar-for-elementor"),
            "return_value" => "yes",
            "default" => "yes",
        ]);

        $this->add_control("legend_position", [
            "label" => __("Legend position", "simple-calendar-for-elementor"),
            "type" => \Elementor\Controls_Manager::SELECT,
            "options" => [
                "" => __("Side", "simple-calendar-for-elementor"),
                "legend_below" => __("Below", "simple-calendar-for-elementor"),
            ],
            'condition' => [
                'show_legend' => "yes",
            ],
            "default" => "",
        ]);


        $this->add_responsive_control(
            'day_height',
            [
                'label' => esc_html__('Day height', 'simple-calendar-for-elementor'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 200,
                        'step' => 1,
                    ]
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 50,
                ],
                'selectors' => [
                    '{{WRAPPER}} .calendar__day' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'day_width',
            [
                'label' => esc_html__('Day width', 'simple-calendar-for-elementor'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 200,
                        'step' => 1,
                    ]
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 50,
                ],
                'selectors' => [
                    '{{WRAPPER}} .calendar__day, {{WRAPPER}} .calendar__day_name' => 'width: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'layout' => "",
                ],
            ]
        );

        $this->add_responsive_control(
            'day_gap',
            [
                'label' => esc_html__('Day gap', 'simple-calendar-for-elementor'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ]
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 2,
                ],
                'selectors' => [
                    '{{WRAPPER}} .calendar__day' => 'margin: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'show_title',
            [
                        'label' => esc_html__('Show event titles when hovering a day', 'simple-calendar-for-elementor'),
                        'type' => \Elementor\Controls_Manager::SWITCHER,
                        'label_on' => esc_html__('Show', 'simple-calendar-for-elementor'),
                        'label_off' => esc_html__('Hide', 'simple-calendar-for-elementor'),
                        'return_value' => 'yes',
                        'default' => 'yes',
                    ]
        );
        $this->add_control(
            'fixed_month',
            [
                        'label' => esc_html__('Show a fixed month', 'simple-calendar-for-elementor'),
                        'type' => \Elementor\Controls_Manager::SWITCHER,
                        'label_on' => esc_html__('Show', 'simple-calendar-for-elementor'),
                        'label_off' => esc_html__('Hide', 'simple-calendar-for-elementor'),
                        'return_value' => 'yes',
                        'default' => 'no',
                    ]
        );

        $this->add_control(
            'selected_month',
            [
                'label' => esc_html__('Month', 'simple-calendar-for-elementor'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'description' =>esc_html__('Changes might only be visible in the frontend', 'simple-calendar-for-elementor'),
                'default' => '1',
                'options' => [
                    '1' => esc_html__('Januar', 'simple-calendar-for-elementor'),
                    '2'  => esc_html__('February', 'simple-calendar-for-elementor'),
                    '3' => esc_html__('March', 'simple-calendar-for-elementor'),
                    '4' => esc_html__('April', 'simple-calendar-for-elementor'),
                    '5' => esc_html__('May', 'simple-calendar-for-elementor'),
                    '6' => esc_html__('June', 'simple-calendar-for-elementor'),
                    '7' => esc_html__('July', 'simple-calendar-for-elementor'),
                    '8' => esc_html__('August', 'simple-calendar-for-elementor'),
                    '9' => esc_html__('September', 'simple-calendar-for-elementor'),
                    '10' => esc_html__('October', 'simple-calendar-for-elementor'),
                    '11' => esc_html__('November', 'simple-calendar-for-elementor'),
                    '12' => esc_html__('December', 'simple-calendar-for-elementor'),
                ],
                'selectors' => [
                    '{{WRAPPER}} .your-class' => 'border-style: {{VALUE}};',
                ],
                'condition' => ['fixed_month' => 'yes'],
            ]
        );
        $this->add_control(
            'selected_year',
            [
                'label' => esc_html__('Year', 'simple-calendar-for-elementor'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => date('Y'),
                'placeholder' => esc_html__('2023', 'simple-calendar-for-elementor'),
                'condition' => ['fixed_month' => 'yes'],
            ]
        );

        $this->end_controls_section();


        $this->start_controls_section(
            'section_style_default',
            [
              'label' => esc_html__('Default styles', 'simple-calendar-for-elementor'),
              "tab" => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'color_lines',
            [
                'label' => esc_html__('Top/bottom line', 'simple-calendar-for-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .calendar__dates' => 'border-top-color: {{VALUE}}; border-bottom-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'color_bg',
            [
                'label' => esc_html__('Background color', 'simple-calendar-for-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .miga_calendar_box' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'color_bg_header',
            [
                'label' => esc_html__('Header background color', 'simple-calendar-for-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .calendar__header' => 'background-color: {{VALUE}}',
                ],
            ]
        );
        $this->add_control(
            'color_header',
            [
                'label' => esc_html__('Header text color', 'simple-calendar-for-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .calendar__header, {{WRAPPER}} .calendar__next, {{WRAPPER}} .calendar__prev' => 'color: {{VALUE}}',
                ],
            ]
        );
        $this->add_control(
            'color_header_hover',
            [
                'label' => esc_html__('Header text color (hover)', 'simple-calendar-for-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    ' {{WRAPPER}} .calendar__next:hover, {{WRAPPER}} .calendar__prev:hover' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'border_cal',
                // 'label' => esc_html__('Border', 'simple-calendar-for-elementor'),
                'selector' => '{{WRAPPER}} .calendar',
            ]
        );


        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'content_typography',
                'selector' => '{{WRAPPER}} .calendar',
            ]
        );

        $this->end_controls_section();
        $this->start_controls_section(
            'section_style_weekday',
            [
              'label' => esc_html__('Weekdays', 'simple-calendar-for-elementor'),
              "tab" => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'content_typography_weekday',
                'selector' => '{{WRAPPER}} .calendar__week .calendar__day_name',
            ]
        );
        $this->add_control(
            'color_bg_header_days',
            [
                'label' => esc_html__('Days background color', 'simple-calendar-for-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .calendar__day_name' => 'background-color: {{VALUE}}',
                ],
            ]
        );
        $this->add_control(
            'color_header_days',
            [
                'label' => esc_html__('Days text color', 'simple-calendar-for-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .calendar__day_name' => 'color: {{VALUE}}',
                ],
            ]
        );
        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_buttons',
            [
              'label' => esc_html__('Prev/Next styles', 'simple-calendar-for-elementor'),
              "tab" => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'color_arrows',
            [
                'label' => esc_html__('Arrow color', 'simple-calendar-for-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .calendar__next svg, {{WRAPPER}} .calendar__prev svg' => 'fill: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'border_next_prev',
                // 'label' => esc_html__('Border prev/next', 'simple-calendar-for-elementor'),
                'selector' => '{{WRAPPER}} .calendar__next, {{WRAPPER}} .calendar__prev',
            ]
        );

        $this->add_control(
            'color_bg_arrow',
            [
                'label' => esc_html__('Background color', 'simple-calendar-for-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .calendar__next, {{WRAPPER}} .calendar__prev' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_today_day',
            [
              'label' => esc_html__('Today day styles', 'simple-calendar-for-elementor'),
              "tab" => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'color_today_day',
            [
                'label' => esc_html__('Text color', 'simple-calendar-for-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .calendar__day--today' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'color_bg_today_day',
            [
                'label' => esc_html__('Background color', 'simple-calendar-for-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .calendar__day--today' => 'background-color: {{VALUE}}',
                ],
            ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'color_border_today_day',
                'selector' => '{{WRAPPER}} .calendar__day--today',
            ]
        );

        $this->end_controls_section();


        $this->start_controls_section(
            'section_style_today',
            [
              'label' => esc_html__('Today button styles', 'simple-calendar-for-elementor'),
              "tab" => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'border_today',
                // 'label' => esc_html__('Border today', 'simple-calendar-for-elementor'),
                'selector' => '{{WRAPPER}} .calendar__today',
            ]
        );

        $this->add_control(
            'color_bg_footer',
            [
                'label' => esc_html__('Today background color', 'simple-calendar-for-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .calendar__today' => 'background-color: {{VALUE}}',
                ],
            ]
        );
        $this->add_control(
            'color_bg_footer_hover',
            [
                'label' => esc_html__('Today background color (hover)', 'simple-calendar-for-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .calendar__today:hover' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'color_today_font',
            [
                'label' => esc_html__('Today color', 'simple-calendar-for-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .calendar__today' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'color_today_font_hover',
            [
                'label' => esc_html__('Today color (hover)', 'simple-calendar-for-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .calendar__today:hover' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_days',
            [
              'label' => esc_html__('Default days', 'simple-calendar-for-elementor'),
              "tab" => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'font_color',
            [
                'label' => esc_html__('Day font color', 'simple-calendar-for-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .calendar__day' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'bg_color_default',
            [
                'label' => esc_html__('Background color day', 'simple-calendar-for-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    'html .miga_calendar' => '--miga-cal-default-day-color: {{VALUE}}',
                ],
            ]
        );
        $this->add_control(
            'bg_color_empty',
            [
                'label' => esc_html__('Background color empty day', 'simple-calendar-for-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    'html .miga_calendar' => '--miga-cal-empty-day-color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_days_selection',
            [
                'label' => esc_html__('Days with status', 'simple-calendar-for-elementor'),
                "tab" => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'bg_color',
            [
                'label' => esc_html__('Background Color', 'simple-calendar-for-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    'html .miga_calendar' => '--miga-cal-color: {{VALUE}}',
                ],
            ]
        );
        $this->add_control(
            'bg_color2',
            [
                'label' => esc_html__('Background Color (second)', 'simple-calendar-for-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    'html .miga_calendar' => '--miga-cal-color-second: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'font_color2',
            [
                'label' => esc_html__('Font Color with status', 'simple-calendar-for-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .calendar__day.calendar__day--hasClass' => 'color: {{VALUE}}',
                ],
            ]
        );
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
        $calendar = $settings["calendar"];
        $layout = $settings["layout"];
        $legend_position = $settings["legend_position"];
        $round_days = ($settings["squared_days"] == "yes") ? 'miga_calendar_box_squared' : '';
        $showButton = ($settings["show_today"] == "yes");
        $fixed_month = ($settings["fixed_month"] == "yes");
        $show_title = ($settings["show_title"] == "yes");
        $selected_month = -1;
        $selected_year = -1;

        if ($fixed_month) {
            $selected_month = (int)$settings["selected_month"];
            $selected_year = (int)$settings["selected_year"];
            $showButton = 0;
        }

        if ($isEditor) {
            echo '<input type="hidden" value="'.esc_html($calendar).'" id="currentCalendar"/>';
        }

        echo '<div class="miga_calendar ' .esc_html($layout).
            ' ' .esc_html($legend_position) .' '. '" data-year="'.date("Y").'" data-month="'.date("m").'" data-showtitle="'.$show_title.'" data-showbutton="'.$showButton.'" data-calendar="' .
            esc_html($calendar) .
            '">';
        echo '<div class="miga_calendar_box '.$round_days.'">';
        miga_ajax_functions_cal($calendar, [
          "showButton" => $showButton,
          "fixedMonth" => $fixed_month,
          "selectedMonth" => $selected_month,
          "selectedYear" => $selected_year,
          "showTitle" => $show_title,
        ]);
        echo '<div class="loading_spinner"></div>';
        echo '</div>';

        if ($settings["show_legend"] == "yes") {
            global $wpdb;
            $table_name = TABLE_NAME_MIGA_CAL_STATUS;

            if (isset($_POST["submit"])) {
                $wpdb->insert($table_name, [
                    "status" => sanitize_text_field($_POST["status"]),
                    "class" => sanitize_text_field($_POST["class"]),
                ]);
            }

            $results = $wpdb->get_results(("SELECT * FROM $table_name"));
            if (!empty($results)) {
                echo '<div class="calendar__legend '.$round_days.'">';
                echo '<div class="calendar__legend_title">' . esc_html__("Legend", "simple-calendar-for-elementor") . "</div>";
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
        }
        echo "</div>";
    }
}
