<template>
  <router-view @close="this.onAddVideoDialogClosed" @submit="this.onAddVideoDialogSubmitted" />

  <tradecast-confirm-dialog ref="confirmDialog" />

  <div class="tradecast-videos-overview-container">
    <h1 class="wp-heading-inline">{{ t('views.videos.title') }}</h1>

    <tradecast-splash-screen v-if="this.channelId === ''" />

    <template v-if="this.channelId !== ''">
      <button
        type="button"
        class="button button-primary add-video"
        :disabled="this.isAddVideoButtonDisabled"
        :title="t('buttons.videos.addVideo.label')"
        @click="this.onAddVideoButtonClicked"
      >
        {{ t('buttons.videos.addVideo.label') }}
      </button>

      <tradecast-flash-message />

      <tradecast-table :columns="this.tableColumns" :rows="this.tableRows" class="videos">
        <template #row-column="{ column, row }">
          <template v-if="column.name === 'icon'">
            <span
              v-if="row?.tradecast?.video_is_inaccessible"
              class="dashicons dashicons-warning"
              :title="t('notices.videos.inaccessibleVideo')"
            ></span>
          </template>
          <template v-if="column.name === 'author'">
            <a :href="'profile.php?user_id=' + row.author.id" :title="row.author.name">{{ row.author.name }}</a>
          </template>
          <template v-if="row.id && column.name === 'shortcode'">
            <input type="text" :value="this.getShortcode(row.id)" @click="this.onSelectShortcode" /><span
              class="dashicons dashicons-clipboard"
              :title="t('buttons.generic.copyShortcode.label')"
              @click="this.onCopyShortcode(row.id)"
            />
          </template>
          <template v-if="row.id && column.name === 'actions'">
            <span
              class="dashicons dashicons-trash delete-video"
              :title="t('buttons.videos.deleteVideo.label')"
              @click="this.deleteVideo(row.id, row.title)"
            ></span>
          </template>
        </template>
      </tradecast-table>
    </template>
  </div>
</template>

<script lang="ts">
import { defineComponent, onMounted, reactive, ref, toRefs } from 'vue';
import { useI18n } from 'vue-i18n';
import { useRouter } from 'vue-router';
import { Store, useStore } from 'vuex';
import { MessageType, TableRowType, RootState } from '@/types';
import { DataResponse, TradecastMediaType, WordPressApiService, WordPressVideoType } from '@tradecast/library';
import { TradecastConfirmDialog, TradecastFlashMessage, TradecastSplashScreen, TradecastTable } from '@/components';
import settings from '@/data/settings';
import copy from 'copy-to-clipboard';

export default defineComponent({
  name: 'tradecast-videos-overview',
  components: {
    TradecastConfirmDialog,
    TradecastFlashMessage,
    TradecastSplashScreen,
    TradecastTable,
  },
  setup() {
    const confirmDialog = ref<InstanceType<typeof TradecastConfirmDialog>>();
    const router = useRouter();
    const store = useStore() as Store<RootState>;
    const state = store.state as RootState;
    const wordpressApi = new WordPressApiService(settings);
    const { t } = useI18n();

    /**
     * Object that contains methods for performing actions on the API.
     */
    const actions = {
      addFlashMessage: (message: MessageType) => store.dispatch('messages/addFlashMessage', message),
      listVideos: () => wordpressApi.listVideos(),
      addVideo: (media: TradecastMediaType) => wordpressApi.addVideo(media),
      deleteVideo: (id: number) => wordpressApi.deleteVideo(id),
    };

    /**
     * Object that contains reactive data that is used in the template.
     */
    const data = reactive({
      channelId: settings.tradecast.channelId,
      isAddVideoButtonDisabled: false,
      tableColumns: [
        { name: 'icon', className: ['column-icon'] },
        { name: 'video', title: t('views.videos.table.columns.video'), property: 'title', className: ['column-title'] },
        { name: 'shortcode', title: t('views.videos.table.columns.shortcode'), className: ['column-shortcode'] },
        {
          name: 'author',
          title: t('views.videos.table.columns.author'),
          property: 'author.name',
          className: ['column-author'],
        },
        { name: 'actions', className: ['column-actions', 'text-right'] },
      ],
      tableRows: [] as TableRowType<WordPressVideoType>[],
    });

    /**
     * Initializes this element. Fetches a list of videos from the API.
     */
    const init = () => {
      onAddVideoDialogClosed();

      actions.listVideos().then((response: DataResponse<WordPressVideoType[]>) => {
        data.tableRows = response.data as TableRowType<WordPressVideoType>[];
        data.tableRows.forEach((tableRow) => {
          if (tableRow.tradecast.video_is_inaccessible) {
            tableRow.className = 'is-inaccessible';
          }
        });
      });
    };

    /**
     * Deletes a video from the backend by its id.
     *
     * @param id
     * @param title
     */
    const deleteVideo = (id: number, title: string) => {
      confirmDialog.value
        ?.show(t('dialogs.videos.deleteVideo.title'), t('dialogs.videos.deleteVideo.message', [title]))
        .then((value) => {
          if (!value) {
            return;
          }

          actions
            .deleteVideo(id)
            .then(() => {
              init();

              actions.addFlashMessage({
                className: 'success',
                message: t('notices.videos.deleteVideo.success', [title]),
              });
            })
            .catch(() => {
              actions.addFlashMessage({
                className: 'failure',
                message: t('notices.videos.deleteVideo.failure', [title]),
              });
            });
        });
    };

    /**
     * Gets the shortcode for a video.
     *
     * @param id
     */
    const getShortcode = (id: number): string => {
      return '[tradecast-video id="' + id + '" width="960" height="540" autoplay="false"]';
    };

    /**
     * Handles add video button clicks. On click, routes user to the add modal.
     */
    const onAddVideoButtonClicked = (): void => {
      data.isAddVideoButtonDisabled = true;
      router.push('add');
    };

    /**
     * Handles video dialog closes. On close, re-enables the add video button.
     */
    const onAddVideoDialogClosed = (): void => {
      data.isAddVideoButtonDisabled = false;
    };

    /**
     * Handles video dialog submits. On submit, runs the init method.
     */
    const onAddVideoDialogSubmitted = (): void => {
      init();
    };

    /**
     * Handles shortcode copies. On click, copies the shortcode to the clipboard.
     *
     * @param id
     */
    const onCopyShortcode = (id: number): void => {
      copy(getShortcode(id));
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
      ...toRefs(state),
      confirmDialog,
      deleteVideo,
      init,
      getShortcode,
      onAddVideoButtonClicked,
      onAddVideoDialogClosed,
      onAddVideoDialogSubmitted,
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
