<?php

/**
 * The public-facing functionality of the wp-plugin.
 *
 * @link       https://www.kiener.nl
 * @since      1.0.0
 *
 * @package    Tradecast
 * @subpackage Tradecast/public
 */

/**
 * The public-facing functionality of the wp-plugin.
 *
 * Defines the wp-plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Tradecast
 * @subpackage Tradecast/public
 * @author     Kiener Digital Commerce <support@kiener.nl>
 */
class Tradecast_Public
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
     * Load the required dependencies for the public area.
     *
     * @since    1.0.0
     * @access   public
     */
    public function load_dependencies()
    {
        require_once plugin_dir_path(__FILE__) . 'class-tradecast-posttype-video.php';
        require_once plugin_dir_path(__FILE__) . 'class-tradecast-posttype-gallery.php';
        require_once plugin_dir_path(__FILE__) . 'class-tradecast-shortcode-gallery.php';
        require_once plugin_dir_path(__FILE__) . 'class-tradecast-shortcode-video.php';
    }

    /**
     * Register the stylesheet for the public area.
     *
     * @since 1.0.0
     * @access public
     */
    public function enqueue_styles()
    {
        wp_enqueue_style('dashicons');
        wp_enqueue_style(
            $this->plugin_name,
            plugin_dir_url(__FILE__) . 'assets/tradecast-front-end.css',
            [],
            $this->version,
            'all'
        );
    }

    /**
     * Register the JavaScript for the public area.
     *
     * @since 1.0.0
     * @access public
     */
    public function enqueue_scripts()
    {
        wp_enqueue_script(
            $this->plugin_name,
            plugin_dir_url(__FILE__) . 'assets/tradecast-front-end.umd.min.js',
            [],
            $this->version,
            true
        );
    }
}
