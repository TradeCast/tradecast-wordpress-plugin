<?php

/**
 * The core class for the admin-specific functionality of this plugin.
 *
 * @link https://www.kiener.nl
 * @since 1.0.0
 *
 * @package Tradecast
 * @subpackage Tradecast/admin
 * @author Kiener Digital Commerce <support@kiener.nl>
 */
class Tradecast_Admin
{
    /**
     * @var string $plugin_name
     *
     * @since 1.0.0
     * @access private
     */
    private $plugin_name;

    /**
     * @var string $version
     *
     * @since 1.0.0
     * @access private
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since 1.0.0
     * @access public
     */
    public function __construct()
    {
        $this->plugin_name = 'tradecast';
        $this->version = '1.0.0';

        if (defined('TRADECAST_PLUGIN_NAME')) {
            $this->plugin_name = TRADECAST_PLUGIN_NAME . '-admin';
        }

        if (defined('TRADECAST_PLUGIN_VERSION')) {
            $this->version = TRADECAST_PLUGIN_VERSION;
        }

        $this->load_dependencies();
    }

    /**
     * Load the required dependencies for the admin area.
     *
     * @since 1.0.0
     * @access public
     */
    public function load_dependencies()
    {
        require_once plugin_dir_path(__FILE__) . 'class-tradecast-cron.php';
        require_once plugin_dir_path(__FILE__) . 'class-tradecast-settings.php';
        require_once plugin_dir_path(__FILE__) . 'class-tradecast-settings-api-extensions.php';
        require_once plugin_dir_path(__FILE__) . 'class-tradecast-videos-api-extensions.php';
        require_once plugin_dir_path(__FILE__) . 'class-tradecast-galleries-api-extensions.php';
        require_once plugin_dir_path(__FILE__) . 'class-tradecast-gutenberg-blocks.php';
    }

    /**
     * Register the stylesheet for the admin area.
     *
     * @since 1.0.0
     * @access public
     */
    public function enqueue_styles()
    {
        wp_enqueue_style(
            $this->plugin_name,
            plugin_dir_url(__FILE__) . 'assets/tradecast-admin.css',
            [],
            $this->version,
            'all'
        );
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since 1.0.0
     * @access public
     */
    public function enqueue_scripts()
    {
        // enqueue the admin script
        wp_enqueue_script(
            $this->plugin_name,
            plugin_dir_url(__FILE__) . 'assets/tradecast-admin.umd.min.js',
            [],
            $this->version,
            true
        );

        // localize the script to provide a nonce which is used for authentication in the WordPress admin API.
        wp_localize_script($this->plugin_name, 'tradecastWpAdminSettings', [
            'root' => esc_url_raw(rest_url()),
            'nonce' => wp_create_nonce('wp_rest'),
        ]);
    }
}
