import { createApp } from 'vue';
import Store from '@/store';
import Main from '@/views/Main.vue';
import i18n from '@/i18n';
import router from '@/router/galleries';

export default class createGalleriesApp {
  constructor() {
    /**
     * Gets the galleries container from the DOM which is used to mount the galleries application upon.
     */
    const galleriesContainer = document.getElementById('tradecast_gallery_overview');

    /**
     * Creates the Vue galleries application and mount it to the galleries container.
     */
    if (galleriesContainer) {
      createApp(Main).use(i18n).use(router).use(Store).mount(galleriesContainer);
    }
  }
}
