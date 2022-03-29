import { CustomWindowInterface } from '@tradecast/library';
import i18n from '@/i18n';
import settings from '@/data/settings';

declare const window: CustomWindowInterface;

export default class configurationService {
  /**
   * Sets the configuration to the config object.
   */
  public static init(): void {
    const dataLocale = document.querySelector('[id^="tradecast"][data-locale]');
    const channelId = document.querySelector('[id^="tradecast"][data-channel-id]');

    settings.tradecast.channelId = channelId?.getAttribute('data-channel-id') || '';

    settings.wordpress.api =
      window.tradecastWpAdminSettings.root.substr(-1) === '/'
        ? window.tradecastWpAdminSettings.root.substr(0, window.tradecastWpAdminSettings.root.length - 1)
        : window.tradecastWpAdminSettings.root;

    settings.wordpress.nonce = window.tradecastWpAdminSettings.nonce;

    i18n.global.locale.value = dataLocale?.getAttribute('data-locale')?.substr(0, 2) || 'en';
  }
}
