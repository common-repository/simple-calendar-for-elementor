<?php
/**
 * Plugin Name
 *
 * @package           PluginPackage
 * @author            Michael Gangolf
 * @copyright         2022 Michael Gangolf
 * @license           GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       Simple calendar for Elementor
 * Description:       Simple calendar plugin for Elementor to show e.g. availability on different days.
 * Version:           1.6.2
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Michael Gangolf
 * Author URI:        https://www.migaweb.de/
 * License:           GPL v2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       simple-calendar-for-elementor
 * Domain Path:       /languages
 */

global $wpdb;
defined("TABLE_NAME_MIGA_CAL_EVENTS") or
  define("TABLE_NAME_MIGA_CAL_EVENTS", $wpdb->prefix . "miga_calendar_events");

defined("TABLE_NAME_MIGA_CAL_CALENDAR") or
  define("TABLE_NAME_MIGA_CAL_CALENDAR", $wpdb->prefix . "miga_calendar_calendar");

defined("TABLE_NAME_MIGA_CAL_STATUS") or
  define("TABLE_NAME_MIGA_CAL_STATUS", $wpdb->prefix . "miga_calendar_status");

use Elementor\Plugin;

add_action("init", static function () {
    if (!did_action("elementor/loaded")) {
        return false;
    }
    require_once __DIR__ . "/widget/includes/month.php";
    require_once __DIR__ . "/widget/includes/backend_functions.php";
    require_once __DIR__ . "/widget/elementor_calendar.php";
    require_once __DIR__ . "/widget/elementor_calendar_legend.php";
    \Elementor\Plugin::instance()->widgets_manager->register_widget_type(
        new \MIGA_Simple_Calendar()
    );
    \Elementor\Plugin::instance()->widgets_manager->register_widget_type(
        new \MIGA_Simple_Calendar_Legend()
    );

    load_plugin_textdomain('simple-calendar-for-elementor', false, dirname(plugin_basename(__FILE__)) . '/languages');
});

function miga_calendar_events()
{
    global $wpdb;
    $table_name = TABLE_NAME_MIGA_CAL_EVENTS;
    if ($wpdb->get_var("show tables like '$table_name'") != $table_name) {
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE $table_name (id INT NOT NULL AUTO_INCREMENT , date DATE NOT NULL , title VARCHAR(255) NULL DEFAULT NULL, status INT NULL DEFAULT NULL, calendar INT NULL DEFAULT NULL , PRIMARY KEY (`id`)) $charset_collate;";
        require_once ABSPATH . "wp-admin/includes/upgrade.php";
        dbDelta($sql);
    }
}

function miga_calendar_calendar()
{
    global $wpdb;
    $table_name = TABLE_NAME_MIGA_CAL_CALENDAR;
    if ($wpdb->get_var("show tables like '$table_name'") != $table_name) {
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE $table_name (id INT NOT NULL AUTO_INCREMENT ,  title VARCHAR(255) NULL DEFAULT NULL, PRIMARY KEY (`id`)) $charset_collate;";
        require_once ABSPATH . "wp-admin/includes/upgrade.php";
        dbDelta($sql);

        $wpdb->insert(
            $table_name,
            array(
              "title" => "default",
            )
        );
    }
}

function miga_calendar_status()
{
    global $wpdb;
    $table_name = TABLE_NAME_MIGA_CAL_STATUS;
    if ($wpdb->get_var("show tables like '$table_name'") != $table_name) {
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE $table_name (id INT NOT NULL AUTO_INCREMENT , status VARCHAR(255) NULL DEFAULT NULL, class VARCHAR(255) NULL DEFAULT NULL, visible BOOLEAN NOT NULL DEFAULT TRUE, fixed BOOLEAN NOT NULL DEFAULT FALSE, PRIMARY KEY (`id`)) $charset_collate;";
        require_once ABSPATH . "wp-admin/includes/upgrade.php";
        dbDelta($sql);

        $wpdb->insert($table_name, [
            "status" => "blocked",
            "class" => "blocked",
            "fixed" => 1
        ]);
        $wpdb->insert($table_name, [
            "status" => "half blocked",
            "class" => "half_blocked",
            "fixed" => 1
        ]);
    }
}

register_activation_hook(__FILE__, "miga_calendar_events");
register_activation_hook(__FILE__, "miga_calendar_calendar");
register_activation_hook(__FILE__, "miga_calendar_status");

add_action("wp_ajax_miga_custom_post_filter_cal", "miga_ajax_functions_cal");
add_action("wp_ajax_nopriv_miga_custom_post_filter_cal", "miga_ajax_functions_cal");
add_action("wp_ajax_miga_editor_cal", "miga_ajax_editor_cal");
add_action("wp_ajax_nopriv_miga_editor_cal", "miga_ajax_editor_cal");
add_action("wp_ajax_miga_editor_cal_delete", "miga_ajax_editor_cal_delete");
add_action(
    "wp_ajax_nopriv_miga_editor_cal_delete",
    "miga_ajax_editor_cal_delete"
);
add_action("wp_ajax_miga_editor_cal_update", "miga_ajax_editor_cal_update");
add_action(
    "wp_ajax_nopriv_miga_editor_cal_update",
    "miga_ajax_editor_cal_update"
);
add_shortcode('simple-calendar-for-elementor', 'simple_calendar_for_elementor_shortcode');


function simple_calendar_for_elementor_shortcode($atts, $content, $tag)
{
    $calendar = "";
    if (isset($atts["calendar"])) {
        $calendar = (int)$atts["calendar"];
    }
    $showButton = false;
    wp_register_style("miga_calendar_styles", plugins_url("/styles/main.css", __FILE__));
    wp_enqueue_style("miga_calendar_styles");

    echo '<div class="miga_calendar" data-calendar="'.$calendar.'" data-year="'.date("Y").'" data-month="'.date("m").'">';
    echo '<div class="miga_calendar_box">';
    miga_ajax_functions_cal($calendar, [
      "showButton" => $showButton,
      "fixedMonth" => false,
      "selectedMonth" => -1,
      "selectedYear" => -1
    ]);
    echo '<div class="loading_spinner"></div>';
    echo '</div>';
    echo '</div>';
}

add_action('wp_enqueue_scripts', 'miga_calendar_enqueue_scripts');
function miga_calendar_enqueue_scripts()
{
    wp_register_script("miga_calendar_script", plugin_dir_url(__FILE__) . "./scripts/main.js", [], "1.0.1", true);

    wp_localize_script("miga_calendar_script", "miga_calendar", [
        "miga_nonce" => wp_create_nonce("miga_nonce"),
        "wp_url" => admin_url("admin-ajax.php"),
    ]);
    wp_enqueue_script('miga_calendar_script');
}

function miga_calendar_add_page()
{
    add_submenu_page(
        "options-general.php",
        __("Simple Calendar", "simple-calendar-for-elementor"),
        __("Simple Calendar", "simple-calendar-for-elementor"),
        "manage_options",
        "miga_calendar-page",
        "miga_calendar_page",
        100
    );
}

function miga_calendar_register_settings()
{
    add_option("miga_calendar_days", "");
    add_option("eMonth", "");
    add_option("eYear", "");
    register_setting("miga_calendar_days_option_group", "miga_calendar_days");
    register_setting("miga_calendar_days_option_group", "eMonth");
    register_setting("miga_calendar_days_option_group", "eYear");
}

function miga_calendar_page()
{
    $default_tab = null;
    $tab = isset($_GET["tab"]) ? sanitize_key($_GET["tab"]) : sanitize_key($default_tab);
    ?>
<h1>Simple calendar</h1>

  <nav class="nav-tab-wrapper">
      <a href="?page=miga_calendar-page" class="nav-tab <?php if (
          $tab === null || $tab == ""
      ): ?>nav-tab-active<?php endif; ?>"><?php echo __("Calendars", "simple-calendar-for-elementor"); ?></a>
      <a href="?page=miga_calendar-page&tab=events" class="nav-tab <?php if (
          $tab === "events"
      ): ?>nav-tab-active<?php endif; ?>"><?php echo __("Events", "simple-calendar-for-elementor"); ?></a>
      <a href="?page=miga_calendar-page&tab=classes" class="nav-tab <?php if (
          $tab === "classes"
      ): ?>nav-tab-active<?php endif; ?>"><?php echo __("Status/Classes", "simple-calendar-for-elementor"); ?></a>
    </nav>
  <div id="miga_calendar_backend">
    <?php switch ($tab):
        case "events":
            require "widget/includes/backend_events.php";
            break;
        case "classes":
            require "widget/includes/backend_status.php";
            break;
        default:
            require "widget/includes/backend_calendars.php";
            break;
    endswitch; ?>
  </div>
<?php
}

function miga_calendar_enqueue()
{
    wp_register_style("miga_calendar_styles2", plugins_url("styles/main.css", __FILE__));
    wp_enqueue_style("miga_calendar_styles2");
    wp_register_style("miga_calendar_styles", plugins_url("styles/editor.css", __FILE__));
    wp_enqueue_style("miga_calendar_styles");
    wp_register_script("miga_calendar_editor_script", plugins_url("scripts/editor.js", __FILE__), ["wp-i18n"], "", true);
    wp_enqueue_script("miga_calendar_editor_script");

    wp_localize_script("miga_calendar_editor_script", "miga_calendar", [
        "miga_nonce" => wp_create_nonce("miga_nonce"),
        "wp_url" => admin_url("admin-ajax.php"),
    ]);
}

add_action("admin_enqueue_scripts", "miga_calendar_enqueue");
add_action("admin_menu", "miga_calendar_add_page", 999);
add_action("admin_init", "miga_calendar_register_settings");
