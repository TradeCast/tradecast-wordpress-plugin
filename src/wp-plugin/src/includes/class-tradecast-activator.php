<?php

/**
 * Fired during plugin activation.
 *
 * @since      1.0.0
 * @package    Tradecast
 * @subpackage Tradecast/includes
 * @author     Kiener Digital Commerce <support@kiener.nl>
 */
class Tradecast_Activator
{
    /**
     * Runs when the plugin is activated.
     *
     * @since 1.0.0
     * @access public
     */
    public static function activate()
    {
        // requires the Tradecast cron class
        require_once __DIR__ .
            DIRECTORY_SEPARATOR .
            '..' .
            DIRECTORY_SEPARATOR .
            'admin' .
            DIRECTORY_SEPARATOR .
            'class-tradecast-cron.php';

        // registers the cron jobs
        (new Tradecast_Admin_Cron())->schedule_event();
    }
}
