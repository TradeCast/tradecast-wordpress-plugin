import { createApp } from 'vue';
import Store from '@/store';
import Main from '@/views/Main.vue';
import i18n from '@/i18n';
import router from '@/router/videos';

export default class createVideosApp {
  constructor() {
    /**
     * Gets the videos container from the DOM, which is used to mount the videos application upon.
     */
    const videosContainer = document.getElementById('tradecast_video_overview');

    /**
     * Creates the Vue videos application and mounts it to the videos container.
     */
    if (videosContainer) {
      createApp(Main).use(i18n).use(router).use(Store).mount(videosContainer);
    }
  }
}
