import { Settings } from '../types';
import axios from 'axios';

export class AxiosService {
  /**
   * Initializes a new axios service and setting it up using the settings object.
   *
   * @param settings
   */
  public static init(settings: Settings): void {
    new AxiosService().setupAxios(settings);
  }

  /**
   * Sets up axios. Adding interceptors for the request which handle authentication.
   *
   * @param settings
   * @private
   */
  private setupAxios(settings: Settings): void {
    axios.interceptors.request.use((config) => {
      if (
        (!settings?.tradecast?.api || !settings?.wordpress?.api) &&
        config.url?.indexOf(settings.tradecast.api) !== 0 &&
        config.url?.indexOf(settings.wordpress.api) !== 0
      ) {
        return config;
      }

      if (settings.tradecast.api !== '' && config.url?.indexOf(settings.tradecast.api) === 0) {
        config.headers = {
          channelId: settings.tradecast.channelId,
        };
      }

      if (settings.wordpress.api !== '' && config.url?.indexOf(settings.wordpress.api) === 0) {
        config.headers = {
          'X-WP-Nonce': settings.wordpress.nonce,
        };
      }

      return config;
    });
  }
}
