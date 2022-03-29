import settings from '@/data/settings';
import { Settings } from '@tradecast/library';

export default class configurationService {
  /**
   * Sets the configuration to the config object.
   */
  public static init(galleryContainer: Element): Settings {
    const galleryIds = galleryContainer?.getAttribute('data-gallery-ids')?.split(',') ?? [];
    const tradecastContainer = document.querySelector('[id^="tradecast"][data-channel-id]');

    settings.gallery.type = galleryContainer?.getAttribute('data-gallery-type') ?? '';

    if (galleryIds.length) {
      settings.gallery.ids = [];

      galleryIds.forEach((id: string) => {
        settings.gallery.ids.push(parseInt(id));
      });
    }

    settings.gallery.columns = parseInt(galleryContainer?.getAttribute('data-columns') ?? '3');
    settings.tradecast.channelId = tradecastContainer?.getAttribute('data-channel-id') ?? '';

    return settings;
  }
}
