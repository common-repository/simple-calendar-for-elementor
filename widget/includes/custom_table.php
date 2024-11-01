<?php
if (!class_exists("WP_List_Table")) {
    require_once ABSPATH . "wp-admin/includes/class-wp-list-table.php";
}

class Miga_calendar_events_List_Table extends WP_List_Table
{
    function __construct()
    {
        global $status, $page;

        parent::__construct([
            "singular" => "calendar",
            "plural" => "calendars",
            "ajax" => false,
        ]);
    }

    function column_default($item, $column_name)
    {
        switch ($column_name) {
            case "start_date":
            case "end_date":
            case "status":
                return ucfirst($item[$column_name]);
            default:
                return print_r($item, true);
        }
    }

    function column_title($item)
    {

        $page = sanitize_text_field($_REQUEST["page"]);
        $actions = [
            "delete" => sprintf(
                '<a href="?page=%s&action=%s&calendar=%d">' .
                    __("Delete", "simple-calendar-for-elementor") .
                    "</a>",
                esc_html($page),
                "delete",
                (int) $item["id"]
            ),
        ];

        return sprintf(
            '%1$s %3$s',
            $item["title"],
            $item["id"],
            $this->row_actions($actions)
        );
    }

    // function column_cb($item)
    // {
    //     return sprintf(
    //         '<input type="checkbox" name="%1$s[]" value="%2$s" />',
    //         $this->_args["singular"],
    //         $item["id"]
    //     );
    // }

    function get_columns()
    {
        $columns = [
            // "cb" => '<input type="checkbox">',
            "title" => __("Title", "simple-calendar-for-elementor"),
        ];

        return $columns;
    }

    function get_sortable_columns()
    {
        $sortable_columns = [
            "title" => ["title", false],
        ];

        return $sortable_columns;
    }

    // function get_bulk_actions()
    // {
    //     $actions = [
    //         "delete" => __("Delete","simple-calendar-for-elementor"),
    //     ];
    //
    //     return $actions;
    // }

    function process_bulk_action()
    {
        global $wpdb;

        if ("delete" === $this->current_action()) {
            $table_name = TABLE_NAME_MIGA_CAL_CALENDAR;
            if (is_array($_GET["calendar"])) {
              $calendars = array_map('sanitize_text_field', $_GET["calendar"]);
                foreach ($calendars as $event) {
                    $wpdb->delete($table_name, ["id" => (int) $event]);
                }
            } else {
                $wpdb->delete($table_name, ["id" => (int) $_GET["calendar"]]);
            }
        }
    }

    function prepare_items()
    {
        global $wpdb;

        $table_name = TABLE_NAME_MIGA_CAL_CALENDAR;
        $columns = $this->get_columns();
        $hidden = [];
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = [$columns, $hidden, $sortable];

        $this->process_bulk_action();

        $total_items = $wpdb->get_var("SELECT COUNT(id) FROM $table_name");
        $per_page = -1;
        $orderby = "title";
        $request = isset($request)?array_map('sanitize_text_field', $_REQUEST["order"]):'asc';
        $order = isset($request) &&
            in_array($request, ["asc", "desc"])
                ? esc_attr($request)
                : "asc";

        $this->items = $wpdb->get_results(
            "SELECT * FROM $table_name ORDER BY $orderby $order",
            ARRAY_A
        );
    }
}
