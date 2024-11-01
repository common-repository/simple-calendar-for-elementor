<?php
$this->start_controls_section("style_title_section", [
  "label" => __("Legend title style", "simple-calendar-for-elementor"),
    "tab" => \Elementor\Controls_Manager::TAB_STYLE,
]);

$this->add_group_control(
    \Elementor\Group_Control_Typography::get_type(),
    [
        'name' => 'title_typography',
        'selector' => '{{WRAPPER}} .calendar__legend_title',
    ]
);

$this->add_control(
    'title_color',
    [
        'label' => esc_html__('Title Color', 'textdomain'),
        'type' => \Elementor\Controls_Manager::COLOR,
        'selectors' => [
            '{{WRAPPER}} .calendar__legend_title' => 'color: {{VALUE}}',
        ],
    ]
);

$this->end_controls_section();

$this->start_controls_section("style_section", [
  "label" => __("Legend style", "simple-calendar-for-elementor"),
    "tab" => \Elementor\Controls_Manager::TAB_STYLE,
]);

$this->add_group_control(
    \Elementor\Group_Control_Typography::get_type(),
    [
        'name' => 'content_typography_legend',
        'selector' => '{{WRAPPER}} .calendar__legend_row_status',
    ]
);

$this->add_control(
    'text_color',
    [
        'label' => esc_html__('Text Color', 'textdomain'),
        'type' => \Elementor\Controls_Manager::COLOR,
        'selectors' => [
            '{{WRAPPER}} .calendar__legend_row_status' => 'color: {{VALUE}}',
        ],
    ]
);

$this->end_controls_section();
?>
