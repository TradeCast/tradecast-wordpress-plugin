<?php
/**
 * Provide a video overview page in the admin area view for the plugin.
 *
 * @link https://www.kiener.nl
 * @since 1.0.0
 *
 * @package Tradecast
 * @subpackage Tradecast/admin/partials
 */

// fetch the plugin settings
$settings = get_option(Tradecast_Admin_Settings_Api_Extensions::OPTION_SETTINGS);

// get the channel id from the settings
$channelId = $settings[Tradecast_Admin_Settings_Api_Extensions::ARRAY_KEY_CHANNEL_ID] ?: '';
?>

<div id="tradecast_gallery_overview" data-locale="<?php echo esc_attr(get_user_locale()); ?>" data-channel-id="<?php echo esc_attr($channelId); ?>"></div>
