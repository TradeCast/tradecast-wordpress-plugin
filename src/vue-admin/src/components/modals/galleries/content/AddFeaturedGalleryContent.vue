<template>
  <div class="tradecast-add-featured-gallery-content-container">
    <tradecast-gallery-header
      :title="t('modals.galleries.featured.title')"
      :content="t('modals.galleries.featured.content')"
    />
    <tradecast-collapse class="video-collapse" :title="t('labels.generic.results')" @expanded="this.onExpanded">
      <template #content>
        <hr />
        <tradecast-video-grid :is-loading="this.videosLoading" :videos="this.videos" />
        <tradecast-pagination :page="this.page" :total-pages="this.pageCount" @page-changed="this.onPageChanged" />
      </template>
    </tradecast-collapse>
  </div>
</template>

<script lang="ts">
import { defineComponent, onMounted, reactive, toRefs } from 'vue';
import { useI18n } from 'vue-i18n';
import { TradecastApiService } from '@tradecast/library';
import {
  DataResponse,
  GalleryTypeEnum,
  GalleryContentType,
  TradecastMediaType,
  TradecastMediaListResponse,
  TradecastRequest,
} from '@tradecast/library';
import { TradecastCollapse, TradecastGalleryHeader, TradecastPagination, TradecastVideoGrid } from '@/components';
import settings from '@/data/settings';

const emits = ['update'];

export default defineComponent({
  name: 'tradecast-add-featured-gallery-content',
  components: { TradecastGalleryHeader, TradecastPagination, TradecastVideoGrid, TradecastCollapse },
  emits,
  setup(props, { emit }) {
    const { t } = useI18n();
    const tradecastApi = new TradecastApiService(settings);

    /**
     * Object that contains methods for performing actions on the API.
     */
    const actions = {
      getMediaList: (request: TradecastRequest) => tradecastApi.getMediaList(request),
    };

    /**
     * Object that contains reactive data that is used in the template.
     */
    const data = reactive({
      videos: [] as TradecastMediaType[],
      videosLoading: false,
      gallery: { title: t('modals.galleries.featured.name'), type: GalleryTypeEnum.FEATURED } as GalleryContentType,
      page: 1,
      pageCount: null as number | null,
      limit: 5,
    });

    /**
     * Loads featured videos.
     */
    const loadVideos = (): void => {
      data.videosLoading = true;
      actions
        .getMediaList({ filter: { eq: { featured: true } }, page: data.page, limit: data.limit })
        .then((response: DataResponse<TradecastMediaListResponse>) => {
          data.videos = response.data?.mediaList.results ?? [];
          data.videosLoading = false;
          data.pageCount = response.data?.mediaList.pageCount ?? null;
        });
    };

    /**
     * Handles expanded. If a collapse element is expanded, videos in that collapse element are loaded.
     */
    const onExpanded = (): void => {
      loadVideos();
    };

    /**
     * Handles page changes. Loads videos for the given page if the page is changed.
     *
     * @param page
     */
    const onPageChanged = (page: number): void => {
      data.page = page;
      loadVideos();
    };

    /**
     * Emits an update event to the parent, providing the gallery object as data.
     */
    const update = () => {
      emit('update', data.gallery);
    };

    /**
     * When this element is mounted, runs the update method.
     */
    onMounted(() => {
      update();
    });

    // returns the methods and data, so that it can be used in the template
    return {
      ...toRefs(data),
      onExpanded,
      onPageChanged,
      t,
    };
  },
});
</script>

<style lang="scss">
@import '../assets/styles/main';
</style>
