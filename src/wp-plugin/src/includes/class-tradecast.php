<?php

/**
 * The core plugin class which defines hooks and loads dependencies.
 *
 * @link https://www.kiener.nl
 * @since 1.0.0
 * @package Tradecast
 * @subpackage Tradecast/includes
 * @author Kiener Digital Commerce <support@kiener.nl>
 */
class Tradecast
{
    /**
     * @var Tradecast_Loader $loader
     *
     * @since 1.0.0
     * @access protected
     */
    protected $loader;

    /**
     * Creates a new instance of this WordPress plugin.
     *
     * @since 1.0.0
     * @access public
     */
    public function __construct()
    {
        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_admin_filters();
        $this->define_public_hooks();
    }

    /**
     * Executes all hooks for this WordPress plugin.
     *
     * @since 1.0.0
     * @access public
     */
    public function run()
    {
        $this->loader->run();
    }

    /**
     * Loads all required dependency classes and creates a new instance of the loader class.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies()
    {
        require_once plugin_dir_path(__FILE__) . 'class-tradecast-loader.php';
        require_once plugin_dir_path(__FILE__) . 'class-tradecast-i18n.php';
        require_once plugin_dir_path(__DIR__) . 'admin' . DIRECTORY_SEPARATOR . 'class-tradecast-admin.php';
        require_once plugin_dir_path(__DIR__) . 'public' . DIRECTORY_SEPARATOR . 'class-tradecast-public.php';

        $this->loader = new Tradecast_Loader();
    }

    /**
     * Sets the locale for this WordPress plugin.
     *
     * @since 1.0.0
     * @access private
     */
    private function set_locale()
    {
        $this->loader->add_action('plugins_loaded', new Tradecast_i18n(), 'load_plugin_textdomain');
    }

    /**
     * Defines the action hooks.
     *
     * @since 1.0.0
     * @access private
     */
    private function define_admin_hooks()
    {
        // registers admin styles and scripts
        $this->loader->add_action('admin_enqueue_scripts', new Tradecast_Admin(), [
            'enqueue_styles',
            'enqueue_scripts',
        ]);

        // registers the admin menu
        $this->loader->add_action('admin_menu', new Tradecast_Admin_Settings(), 'add_menu_item_admin');

        // registers the gutenberg blocks
        $this->loader->add_action('init', new Tradecast_Admin_Gutenberg_Blocks(), [
            'register_video_block',
            'register_gallery_block',
        ]);

        // registers the video api route
        $this->loader->add_action(
            'rest_api_init',
            new Tradecast_Admin_Videos_Api_Extensions(),
            'register_video_rest_route'
        );

        // registers the gallery api routes
        $this->loader->add_action(
            'rest_api_init',
            new Tradecast_Admin_Galleries_Api_Extensions(),
            'register_gallery_rest_route'
        );

        // registers the settings api routes
        $this->loader->add_action(
            'rest_api_init',
            new Tradecast_Admin_Settings_Api_Extensions(),
            'register_settings_rest_route'
        );
    }

    /**
     * Defines admin filter hooks.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_filters()
    {
        // adds a filter hook for setting the correct active menu item
        $this->loader->add_filter('parent_file', new Tradecast_Admin_Settings(), 'set_correct_active_menu_item');

        // adds filter hooks for checking videos and galleries on cron
        $this->loader->add_filter(Tradecast_Admin_Cron::CRON_HOOK, new Tradecast_Admin_Cron(), [
            'check_videos',
            'check_galleries',
        ]);
    }

    /**
     * Defines the public action hooks.
     *
     * @since 1.0.0
     * @access private
     */
    private function define_public_hooks()
    {
        // registers front-end styles and scripts
        $this->loader->add_action('wp_enqueue_scripts', new Tradecast_Public(), ['enqueue_styles', 'enqueue_scripts']);

        // registers the gallery post type and its meta fields
        $this->loader->add_action('init', new Tradecast_PostType_Gallery(), [
            'register_post_type',
            'register_post_type_meta_fields',
        ]);

        // registers the video post type and its meta fields
        $this->loader->add_action('init', new Tradecast_PostType_Video(), [
            'register_post_type',
            'register_post_type_meta_fields',
        ]);

        // registers the shortcodes
        $this->loader->add_action('init', new Tradecast_Shortcode_Gallery(), 'register_shortcode');
        $this->loader->add_action('init', new Tradecast_Shortcode_Video(), 'register_shortcode');
    }
}
