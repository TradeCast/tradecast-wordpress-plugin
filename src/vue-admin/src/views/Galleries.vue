<template>
  <router-view @close="this.onAddGalleryDialogClosed" @submit="this.onAddGalleryDialogSubmitted" />

  <tradecast-confirm-dialog ref="confirmDialog" />

  <div class="wrap">
    <div class="tradecast-galleries-overview-container">
      <h1 class="wp-heading-inline">{{ t('views.galleries.title') }}</h1>

      <tradecast-splash-screen v-if="this.channelId === ''" />

      <template v-if="this.channelId !== ''">
        <button
          type="button"
          class="button button-primary add-gallery"
          :disabled="this.isAddGalleryButtonDisabled"
          :title="t('buttons.galleries.addGallery.label')"
          @click="this.onAddGalleryButtonClicked"
        >
          {{ t('buttons.galleries.addGallery.label') }}
        </button>

        <tradecast-flash-message />

        <tradecast-table :columns="this.tableColumns" :rows="this.tableRows" class="galleries">
          <template #row-column="{ column, row }">
            <template v-if="column.name === 'icon'">
              <span
                v-if="row?.tradecast?.gallery_is_inaccessible"
                class="dashicons dashicons-warning"
                :title="t('notices.galleries.inaccessibleGallery')"
              ></span>
            </template>
            <template v-if="column.name === 'author'">
              <a :href="'profile.php?user_id=' + row.author.id" :title="row.author.name">{{ row.author.name }}</a>
            </template>
            <template v-if="row.id && column.name === 'shortcode'">
              <input type="text" :value="this.getShortcode(row)" @click="this.onSelectShortcode" /><span
                class="dashicons dashicons-clipboard"
                :title="t('buttons.generic.copyShortcode.label')"
                @click="this.onCopyShortcode(row)"
              />
            </template>
            <template v-if="row.id && column.name === 'actions'">
              <span
                class="dashicons dashicons-trash delete-gallery"
                :title="t('buttons.galleries.deleteGallery.label')"
                @click="this.deleteGallery(row.id, row.title)"
              ></span>
            </template>
          </template>
        </tradecast-table>
      </template>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, onMounted, reactive, ref, toRefs } from 'vue';
import { useI18n } from 'vue-i18n';
import { useRouter } from 'vue-router';
import { useStore } from 'vuex';
import { DataResponse, GalleryContentType, WordPressGalleryType, WordPressApiService } from '@tradecast/library';
import { MessageType, TableRowType } from '@/types';
import { TradecastConfirmDialog, TradecastFlashMessage, TradecastSplashScreen, TradecastTable } from '@/components';
import settings from '@/data/settings';
import copy from 'copy-to-clipboard';

export default defineComponent({
  name: 'tradecast-galleries-overview',
  components: {
    TradecastConfirmDialog,
    TradecastFlashMessage,
    TradecastTable,
    TradecastSplashScreen,
  },
  setup() {
    const confirmDialog = ref<InstanceType<typeof TradecastConfirmDialog>>();
    const router = useRouter();
    const store = useStore();
    const wordpressApi = new WordPressApiService(settings);
    const { t } = useI18n();

    /**
     * Object that contains methods for performing actions on the API.
     */
    const actions = {
      addFlashMessage: (message: MessageType) => store.dispatch('messages/addFlashMessage', message),
      listGalleries: () => wordpressApi.listGalleries(),
      addGallery: (media: GalleryContentType) => wordpressApi.addGallery(media),
      deleteGallery: (id: number) => wordpressApi.deleteGallery(id),
    };

    /**
     * Object that contains reactive data that is used in the template.
     */
    const data = reactive({
      channelId: settings.tradecast.channelId,
      isAddGalleryButtonDisabled: false,
      isAddGalleryDialogVisible: false,
      tableColumns: [
        { name: 'icon', className: ['column-icon'] },
        {
          name: 'gallery',
          title: t('views.galleries.table.columns.gallery'),
          property: 'title',
          className: ['column-title'],
        },
        {
          name: 'type',
          title: t('views.galleries.table.columns.type'),
          property: 'tradecast.gallery_type',
          className: ['column-type'],
        },
        { name: 'shortcode', title: t('views.galleries.table.columns.shortcode'), className: ['column-shortcode'] },
        {
          name: 'author',
          title: t('views.galleries.table.columns.author'),
          property: 'author.name',
          className: ['column-author'],
        },
        { name: 'actions', className: ['column-actions', 'text-right'] },
      ],
      tableRows: [] as TableRowType<WordPressGalleryType>[],
    });

    /**
     * Initializes this element. Fetches a list of galleries from the API.
     */
    const init = () => {
      onAddGalleryDialogClosed();

      // fetch a list of galleries from the API
      actions.listGalleries().then((response: DataResponse<WordPressGalleryType[]>) => {
        data.tableRows = response.data as TableRowType<WordPressGalleryType>[];
        data.tableRows.forEach((tableRow) => {
          if (tableRow.tradecast.gallery_is_inaccessible) {
            tableRow.className = 'is-inaccessible';
          }
        });
      });
    };

    /**
     * Deletes a gallery in the backend by its id.
     *
     * @param id
     * @param title
     */
    const deleteGallery = (id: number, title: string) => {
      confirmDialog.value
        ?.show(t('dialogs.galleries.deleteGallery.title'), t('dialogs.galleries.deleteGallery.message', [title]))
        .then((value) => {
          if (!value) {
            return;
          }

          actions
            .deleteGallery(id)
            .then(() => {
              init();

              actions.addFlashMessage({
                className: 'success',
                message: t('notices.galleries.deleteGallery.success', [title]),
              });
            })
            .catch(() => {
              actions.addFlashMessage({
                className: 'failure',
                message: t('notices.galleries.deleteGallery.failure', [title]),
              });
            });
        });
    };

    /**
     * Returns the shortcode for the gallery.
     *
     * @param gallery
     */
    const getShortcode = (gallery: WordPressGalleryType): string => {
      return '[tradecast-gallery id="' + gallery.id + '" columns="3"]';
    };

    /**
     * Handles add gallery button clicks. On click, routes user to the add modal.
     */
    const onAddGalleryButtonClicked = () => {
      data.isAddGalleryButtonDisabled = true;
      router.push('add');
    };

    /**
     * Handles gallery dialog closes. On close, re-enables the add gallery button.
     */
    const onAddGalleryDialogClosed = () => {
      data.isAddGalleryButtonDisabled = false;
    };

    /**
     * Handles gallery dialog submits. On submit, runs the init method.
     */
    const onAddGalleryDialogSubmitted = () => {
      init();
    };

    /**
     * Handles shortcode copies. On click, copies the shortcode to the clipboard.
     *
     * @param gallery
     */
    const onCopyShortcode = (gallery: WordPressGalleryType): void => {
      copy(getShortcode(gallery));
    };

    /**
     * Handles shortcode selects. On select, selects the full text of the shortcode in the input field.
     *
     * @param event
     */
    const onSelectShortcode = (event: PointerEvent): void => {
      event.stopPropagation();

      const target = event.target as HTMLInputElement;
      target.select();
    };

    /**
     * When this element is mounted, runs the init method.
     */
    onMounted(() => {
      init();
    });

    // returns the methods and data, so that it can be used in the template
    return {
      ...toRefs(data),
      confirmDialog,
      deleteGallery,
      init,
      getShortcode,
      onAddGalleryButtonClicked,
      onAddGalleryDialogClosed,
      onAddGalleryDialogSubmitted,
      onCopyShortcode,
      onSelectShortcode,
      t,
    };
  },
});
</script>

<style lang="scss">
@import '../assets/styles/main';
</style>
