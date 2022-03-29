import { createApp } from 'vue';
import Gallery from '@/views/Gallery.vue';
import i18n from '@/i18n';
import configurationService from '@/service/config';

export default class createGalleryApp {
  constructor() {
    /**
     * Gets the gallery containers from the DOM which are used to mount the gallery applications upon.
     */
    const galleryContainers = document.querySelectorAll('[id="tradecast_gallery"][data-gallery-type]');

    /**
     * Creates the Vue gallery application for each container and mounts to it.
     */
    galleryContainers.forEach((galleryContainer) => {
      createApp(Gallery, {
        settings: configurationService.init(galleryContainer),
      })
        .use(i18n)
        .mount(galleryContainer);
    });
  }
}
