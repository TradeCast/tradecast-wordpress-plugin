import { createApp } from 'vue';
import Store from '@/store';
import Settings from '@/views/Settings.vue';
import i18n from '@/i18n';

export default class createSettingsApp {
  constructor() {
    /**
     * Gets the settings form container from the DOM, which is used to mount the settings form application upon.
     */
    const settingsFormContainer = document.getElementById('tradecast_settings_form');

    /**
     * Creates the Vue settings form application and mounts it to the settings form container.
     */
    if (settingsFormContainer) {
      createApp(Settings).use(i18n).use(Store).mount(settingsFormContainer);
    }
  }
}
