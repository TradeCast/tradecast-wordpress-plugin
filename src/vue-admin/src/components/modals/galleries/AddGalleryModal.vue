<template>
  <div class="tradecast-add-gallery-modal-container">
    <div class="tradecast-add-gallery-modal-backdrop close-dialog" @click="this.onCloseDialog">
      <div class="tradecast-add-gallery-modal">
        <span class="dashicons dashicons-no-alt icon-close-dialog close-dialog" @click="this.onCloseDialog" />
        <div class="modal-container">
          <div class="navigation">
            <h2>{{ t('modals.galleries.title') }}</h2>

            <ul>
              <li :class="{ active: this.$route.name === 'add-categories-gallery' }">
                <router-link :to="{ name: 'add-categories-gallery' }">
                  {{ t('modals.galleries.navigation.categories.label') }}
                </router-link>
              </li>
              <li :class="{ active: this.$route.name === 'add-featured-gallery' }">
                <router-link :to="{ name: 'add-featured-gallery' }">
                  {{ t('modals.galleries.navigation.featured.label') }}
                </router-link>
              </li>
              <li :class="{ active: this.$route.name === 'add-latest-gallery' }">
                <router-link :to="{ name: 'add-latest-gallery' }">
                  {{ t('modals.galleries.navigation.latest.label') }}</router-link
                >
              </li>
              <li :class="{ active: this.$route.name === 'add-interests-gallery' }">
                <router-link :to="{ name: 'add-interests-gallery' }">
                  {{ t('modals.galleries.navigation.interests.label') }}
                </router-link>
              </li>
              <li :class="{ active: this.$route.name === 'add-popular-gallery' }">
                <router-link :to="{ name: 'add-popular-gallery' }">
                  {{ t('modals.galleries.navigation.popular.label') }}
                </router-link>
              </li>
            </ul>
          </div>
          <div class="content-container">
            <div class="content">
              <router-view @update="this.onUpdate" />
            </div>
            <div class="footer">
              <span class="status">{{ this.status }}</span>

              <button
                type="button"
                class="button button-primary add-gallery"
                :disabled="!this.isGalleryValid"
                @click="this.onSave"
              >
                {{ t('buttons.galleries.addGallery.label') }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, reactive, toRefs } from 'vue';
import { useI18n } from 'vue-i18n';
import { useRouter } from 'vue-router';
import { useStore } from 'vuex';
import { MessageType } from '@/types';
import {
  DataResponse,
  GalleryContentType,
  GalleryTypeEnum,
  WordPressApiService,
  WordPressGalleryType,
} from '@tradecast/library';
import settings from '@/data/settings';

const emits = ['submit', 'close'];

export default defineComponent({
  name: 'tradecast-add-gallery-modal',
  emits,
  setup(props, { emit }) {
    const { t } = useI18n();
    const router = useRouter();
    const store = useStore();
    const wordpressApi = new WordPressApiService(settings);

    /**
     * Object that contains methods for performing actions on the API.
     */
    const actions = {
      addFlashMessage: (message: MessageType) => store.dispatch('messages/addFlashMessage', message),
      addGallery: (gallery: GalleryContentType) => wordpressApi.addGallery(gallery),
    };

    /**
     * Object that contains reactive data that is used in the template.
     */
    const data = reactive({
      gallery: {} as GalleryContentType,
      isGalleryValid: false,
      status: '',
    });

    /**
     * Handles dialog closes. Emits close event to the parent and resets this modal.
     * @param event
     */
    const onCloseDialog = (event: PointerEvent) => {
      event.stopPropagation();

      // get target element
      const target = event.target as HTMLElement;

      if (!target.classList.contains('close-dialog')) {
        return;
      }

      // emit close to the parent
      emit('close');

      // reset this modal
      reset();
    };

    /**
     * Handles saves. Stores the gallery in the backend through the API.
     */
    const onSave = () => {
      actions
        .addGallery(data.gallery)
        .then((response: DataResponse<WordPressGalleryType>) => {
          if (!response?.success) {
            throw Error();
          }

          actions.addFlashMessage({
            className: 'success',
            message: t('notices.galleries.addGallery.success', [data.gallery?.title]),
          });

          emit('submit');
          reset();
        })
        .catch(() => {
          actions.addFlashMessage({
            className: 'failure',
            message: t('notices.galleries.addGallery.failure', [data.gallery?.title]),
          });

          emit('close');
          reset();
        });
    };

    /**
     * Handles updates from the modal. Checks validation and sets status accordingly.
     *
     * @param gallery
     */
    const onUpdate = (gallery: GalleryContentType) => {
      data.gallery = gallery;
      data.isGalleryValid = false;

      if (data.gallery.type !== GalleryTypeEnum.CATEGORIES) {
        data.status = '';
        data.isGalleryValid = true;
      }

      if (data.gallery.type === GalleryTypeEnum.CATEGORIES) {
        data.status = t('labels.galleries.categoriesSelected', [data.gallery.ids?.length ?? 0]);

        if (data.gallery.ids?.length === 1) {
          data.status = t('labels.galleries.categorySelected');
        }

        data.isGalleryValid = (data.gallery.ids?.length ?? 0) > 0;
      }

      if (data.gallery.type === GalleryTypeEnum.INTERESTS) {
        data.status = t('labels.galleries.interestsSelected', [data.gallery.ids?.length ?? 0]);

        if (data.gallery.ids?.length === 1) {
          data.status = t('labels.galleries.interestSelected');
        }

        data.isGalleryValid = (data.gallery.ids?.length ?? 0) > 0;
      }
    };

    /**
     * Resets this modal.
     */
    const reset = () => {
      router.push('/');
    };

    // returns the methods and data, so that it can be used in the template
    return {
      ...toRefs(data),
      onCloseDialog,
      onSave,
      onUpdate,
      t,
    };
  },
});
</script>

<style lang="scss">
@import '../assets/styles/main';
</style>
