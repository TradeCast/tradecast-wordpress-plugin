/**
 * Object containing base settings.
 */
import { Settings } from '@tradecast/library';

const settings: Settings = {
  gallery: {
    type: '' as string,
    ids: [] as number[],
    columns: 3,
  },
  tradecast: {
    api: 'https://api.tradecast.eu/v3/graphql',
    channelId: '',
  },
  wordpress: {
    api: '',
    nonce: '',
  },
};

export default settings;
