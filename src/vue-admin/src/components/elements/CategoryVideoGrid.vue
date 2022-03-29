<template>
  <div class="tradecast-category-video-grid-container">
    <tradecast-video-grid :is-loading="this.videosLoading" :videos="this.videos" />
    <tradecast-pagination :page="this.page" :total-pages="this.pageCount" @page-changed="this.onPageChanged" />
  </div>
</template>

<script lang="ts">
import { defineComponent, onMounted, PropType, reactive, toRefs, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import {
  DataResponse,
  TradecastCategoryResponse,
  TradecastCategoryType,
  TradecastMediaType,
  TradecastRequest,
} from '@tradecast/library';
import { TradecastApiService } from '@tradecast/library';
import TradecastVideoGrid from './VideoGrid.vue';
import TradecastPagination from './Pagination.vue';
import settings from '@/data/settings';

const props = {
  category: {
    type: Object as PropType<TradecastCategoryType>,
    default: [],
  },
};

export default defineComponent({
  name: 'tradecast-category-video-grid',
  components: { TradecastPagination, TradecastVideoGrid },
  props: props,
  setup(props) {
    const { t } = useI18n();
    const tradecastApi = new TradecastApiService(settings);

    /**
     * Object that contains methods for performing actions on the API.
     */
    const actions = {
      getCategory: (request: TradecastRequest) => tradecastApi.getCategory(request),
    };

    /**
     * Object that contains reactive data that is used in the template.
     */
    const data = reactive({
      category: null as TradecastCategoryType | null,
      page: 1,
      pageCount: null as number | null,
      limit: 4,
      videos: [] as TradecastMediaType[],
      videosLoading: false,
    });

    /**
     * Initializes the element, updating the category and loading videos for it.
     */
    const init = () => {
      data.category = props.category;

      if (data.category) {
        loadVideos(data.category.id);
      }
    };

    /**
     * Loads videos for a given category id.
     *
     * @param id
     */
    const loadVideos = (id: number): void => {
      data.videosLoading = true;

      actions
        .getCategory({ id: id, page: data.page, limit: data.limit })
        .then((result: DataResponse<TradecastCategoryResponse>) => {
          data.videos = result.data?.category?.mediaList.results ?? [];
          data.videosLoading = false;
          data.pageCount = result.data?.category?.mediaList.pageCount ?? null;
        });
    };

    /**
     * Handles page changes. Reloads videos for the given page.
     *
     * @param page
     */
    const onPageChanged = (page: number): void => {
      data.page = page;
      init();
    };

    /**
     * Runs init method when this element is mounted.
     */
    onMounted(() => {
      init();
    });

    /**
     * Watches the category property. On changes, runs init method.
     */
    watch(
      () => props.category,
      (value, oldValue) => {
        if (value != oldValue) {
          init();
        }
      }
    );

    // returns the methods and data, so that it can be used in the template
    return {
      ...toRefs(data),
      onPageChanged,
      t,
    };
  },
});
</script>

<style lang="scss">
@import '../assets/styles/main';
</style>
