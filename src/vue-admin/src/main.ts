import configurationService from '@/service/config';
import createGalleriesApp from '@/apps/galleries';
import createSettingsApp from '@/apps/settings';
import createVideosApp from '@/apps/videos';

/**
 * Load the configuration.
 */
configurationService.init();

/**
 * Create the admin applications.
 */
new createGalleriesApp();
new createVideosApp();
new createSettingsApp();
