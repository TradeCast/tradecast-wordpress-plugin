<?php

/**
 * Defines the internationalization functionality.
 *
 * @since 1.0.0
 * @package Tradecast
 * @subpackage Tradecast/includes
 * @author Kiener Digital Commerce <support@kiener.nl>
 */
class Tradecast_i18n
{
    /**
     * Loads the plugins text domain for translation.
     *
     * @since 1.0.0
     * @access public
     */
    public function load_plugin_textdomain()
    {
        if (!defined('TRADECAST_TEXT_DOMAIN')) {
            define('TRADECAST_TEXT_DOMAIN', 'tradecast');
        }

        load_plugin_textdomain(TRADECAST_TEXT_DOMAIN, false, dirname(plugin_basename(__FILE__), 2) . '/languages/');
    }
}
