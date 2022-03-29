<?php

/**
 * The settings class. Adds menu items in the admin and registers the settings page.
 *
 * @package    Tradecast
 * @subpackage Tradecast/admin
 * @author     Kiener Digital Commerce <support@kiener.nl>
 */
class Tradecast_Admin_Settings
{
    public const ADMIN_PAGE_GALLERIES_SLUG = 'tradecast-galleries';
    public const ADMIN_PAGE_VIDEOS_SLUG = 'tradecast-videos';
    public const ADMIN_PAGE_SETTINGS_SLUG = 'tradecast-settings';

    /**
     * Adds a menu and submenus to the WordPress admin.
     *
     * @since 1.0.0
     * @access public
     */
    public function add_menu_item_admin()
    {
        add_menu_page(
            'Tradecast',
            'Tradecast',
            'edit_posts',
            self::ADMIN_PAGE_SETTINGS_SLUG,
            [$this, 'render_settings_page_content'],
            'dashicons-video-alt3',
            2
        );

        $this->add_menu_subitem_galleries();
        $this->add_menu_subitem_videos();
        $this->add_menu_subitem_settings();
    }

    /**
     * Adds a submenu item for the galleries to the admin menu.
     *
     * @since 1.0.0
     * @access public
     */
    public function add_menu_subitem_galleries()
    {
        add_submenu_page(
            self::ADMIN_PAGE_SETTINGS_SLUG,
            __('Galleries', TRADECAST_TEXT_DOMAIN),
            __('Galleries', TRADECAST_TEXT_DOMAIN),
            'edit_pages',
            self::ADMIN_PAGE_GALLERIES_SLUG,
            [$this, 'render_galleries_page_content'],
            1
        );
    }

    /**
     * Adds a submenu item for the videos to the admin menu.
     *
     * @since 1.0.0
     * @access public
     */
    public function add_menu_subitem_videos()
    {
        add_submenu_page(
            self::ADMIN_PAGE_SETTINGS_SLUG,
            __('Videos', TRADECAST_TEXT_DOMAIN),
            __('Videos', TRADECAST_TEXT_DOMAIN),
            'edit_pages',
            self::ADMIN_PAGE_VIDEOS_SLUG,
            [$this, 'render_videos_page_content'],
            2
        );
    }

    /**
     * Adds a submenu item for the settings to the admin menu.
     *
     * @since 1.0.0
     * @access public
     */
    public function add_menu_subitem_settings()
    {
        add_submenu_page(
            self::ADMIN_PAGE_SETTINGS_SLUG,
            __('Settings', TRADECAST_TEXT_DOMAIN),
            __('Settings', TRADECAST_TEXT_DOMAIN),
            'edit_posts',
            self::ADMIN_PAGE_SETTINGS_SLUG,
            [$this, 'render_settings_page_content'],
            3
        );
    }

    /**
     * Sets the correct active menu item, needed for making the post type pages part of the plugin.
     *
     * @since 1.0.0
     * @access public
     */
    public function set_correct_active_menu_item($parent_file)
    {
        global $submenu_file, $current_screen;

        if (in_array($current_screen->post_type, ['tradecast-galleries', 'tradecast-videos'])) {
            $submenu_file = 'edit.php?post_type=' . $current_screen->post_type;
            $parent_file = self::ADMIN_PAGE_SETTINGS_SLUG;
        }

        return $parent_file;
    }

    /**
     * Renders the content for the galleries page.
     *
     * @since 1.0.0
     * @access public
     * @param string $active_tab
     */
    public function render_galleries_page_content($active_tab = '')
    {
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'partials' . DIRECTORY_SEPARATOR . 'tradecast-admin-galleries.php';
    }

    /**
     * Renders the content for the videos page.
     *
     * @since 1.0.0
     * @access public
     * @param string $active_tab
     */
    public function render_videos_page_content($active_tab = '')
    {
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'partials' . DIRECTORY_SEPARATOR . 'tradecast-admin-videos.php';
    }

    /**
     * Renders the content for the settings page.
     *
     * @since 1.0.0
     * @access public
     * @param string $active_tab
     */
    public function render_settings_page_content($active_tab = '')
    {
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'partials' . DIRECTORY_SEPARATOR . 'tradecast-admin-settings.php';
    }
}
