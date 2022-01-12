<?php

/**
 * Plugin Name: FortunePlug
 * Plugin URI: https://github.com/shubshinde
 * Description: Add fortune telling crystal to your website.
 * Author: Shubham Shinde, The Ken
 * Author URI: https://github.com/shubshinde
 * Version: 1.0.0
 * License: GPL2+
 * License URI: https://www.gnu.org/licenses/gpl-2.0.txt
 *
 * @package FortunePlug
 */

if (!defined('ABSPATH')) : exit();
endif;

final class FortunePlug_Class
{

    const VERSION = '1.0.0';

    /**
     * Construct Function.
     */
    private function __construct()
    {
        $this->plugin_constants();
        $this->fortune_plug_create_database_tables();

        // Include Admin Area Assets.
        add_action('init', [$this, 'enqueue_admin_area_assets']);

        // Init Plugin.
        add_action('plugins_loaded', [$this, 'init_plugin']);
    }

    /**
     * Define plugin constants.
     */
    public function plugin_constants()
    {
        define('FORTUNE_PLUG_VERSION', self::VERSION);
        define('FORTUNE_PLUG_PLUGIN_PATH', trailingslashit(plugin_dir_path(__FILE__)));
        define('FORTUNE_PLUG_PLUGIN_URL', trailingslashit(plugins_url('/', __FILE__)));

        define('FORTUNE_PLUG_DB_TABLE', 'fortune_plug_fortunes');
    }

    /**
     * Singletone Instance.
     */
    public static function init()
    {
        static $instance = false;
        if (!$instance) {
            $instance = new self();
        }
        return $instance;
    }

    /**
     * Plugin Init.
     */
    public function init_plugin()
    {
        $this->fortune_plug_admin_page();
        $this->fortune_plug_shortcode();
    }

    /**
     * Shortcode for frontend.
     */
    public function fortune_plug_shortcode()
    {
        add_shortcode( 'fortune_plug_crystal_ball', function(){
            ob_start();
            get_template_part( 'template-parts/wpdocs-the-shortcode-template', null, $attributes );
            return ob_get_clean();
        } );
    }

    /**
     * Add Plugin Option Page.
     */
    public function fortune_plug_admin_page()
    {
        include FORTUNE_PLUG_PLUGIN_PATH . '/inc/fortune-plug-admin-page-functions.php';

        add_action('admin_menu', function () {

            // Dashboard Page
            add_menu_page(
                __('Fortune Plug', 'fortune-plug'),
                __('Fortune Plug', 'fortune-plug'),
                'manage_options',
                'fortune-plug',
                'fortune_plug_admin_dashboard_page_markup',
                'dashicons-schedule',
                99
            );


            // Add Fortune Page
            add_submenu_page(
                'fortune-plug',
                __('Add Fortune', 'fortune-plug'),
                __('Add Fortune', 'fortune-plug'),
                'manage_options',
                'fortune-plug-add-fortune',
                'fortune_plug_add_new_fortune_page_markup',
            );

            // All Fortunes Page
            add_submenu_page(
                'fortune-plug',
                __('All Fortunes', 'fortune-plug'),
                __('All Fortunes', 'fortune-plug'),
                'manage_options',
                'fortune-plug-fortune-list',
                'fortune_plug_fortune_list_page_markup',
            );

        });
    }

    /**
     * Enqueue Admin Area Assets.
     */
    public function enqueue_admin_area_assets()
    {

        // Include Dynamite Namespaced Bootstrap css. 
        wp_enqueue_style(
            'fortune-plug-bootstrap-style',
            FORTUNE_PLUG_PLUGIN_URL . '/assets/css/fortune-plug-bootstrap.css',
            [],
            false,
            'all'
        );
        // Include Admin Area Style.
        wp_enqueue_style(
            'fortune-plug-admin-backend-style',
            FORTUNE_PLUG_PLUGIN_URL . '/assets/css/fortune-plug-backend.css',
            [],
            false,
            'all'
        );
        // Include Admin Area Script.
        wp_enqueue_script(
            'fortune-plug-admin-backend-script',
            FORTUNE_PLUG_PLUGIN_URL . '/assets/js/fortune-plug-backend.js',
            filemtime(FORTUNE_PLUG_PLUGIN_PATH . '/assets/js/fortune-plug-backend.js'),
            true
        );
    }


    /**
     * Create Table if do not exist.
     */
    public function fortune_plug_create_database_tables()
    {
        global $wpdb;

        $table_name      = $wpdb->prefix . FORTUNE_PLUG_DB_TABLE;
        $charset_collate = $wpdb->get_charset_collate();

        //create table only if doesnot exist
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
            $sql = "CREATE TABLE $table_name (
                id mediumint(11) NOT NULL AUTO_INCREMENT,
                fortune_message TEXT NOT NULL,
                date_created TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY  (id)
            ) $charset_collate;";

            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
        } else {
            // table already exist on database
        }
    }
}

/**
 * Init FortunePlug.
 */
function fortune_plug_run_plugin()
{
    return FortunePlug_Class::init();
}

// Execute FortunePlug.
fortune_plug_run_plugin();
