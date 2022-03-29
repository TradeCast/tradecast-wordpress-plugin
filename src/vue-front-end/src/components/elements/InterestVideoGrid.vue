<template>
  <div class="tradecast-interests-video-grid-container">
    <tradecast-video-grid :is-loading="this.videosLoading" :videos="this.videos" @video-clicked="this.onVideoClicked" />
    <tradecast-pagination :page="this.page" :total-pages="this.pageCount" @page-changed="this.onPageChanged" />
  </div>
</template>

<script lang="ts">
import { defineComponent, onMounted, PropType, reactive, toRefs, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import {
  DataResponse,
  TradecastApiService,
  TradecastInterestResponse,
  TradecastInterestType,
  TradecastMediaType,
  TradecastRequest,
} from '@tradecast/library';
import TradecastVideoGrid from './VideoGrid.vue';
import TradecastPagination from './Pagination.vue';
import settings from '@/data/settings';

const emits = ['video-clicked'];

const props = {
  interest: {
    type: Object as PropType<TradecastInterestType>,
    default: [],
  },
};

export default defineComponent({
  name: 'tradecast-interest-video-grid',
  components: { TradecastPagination, TradecastVideoGrid },
  props,
  emits,
  setup(props, { emit }) {
    const { t } = useI18n();
    const tradecastApi = new TradecastApiService(settings);

    /**
     * Object that contains methods for performing actions on the API.
     */
    const actions = {
      getInterest: (request: TradecastRequest) => tradecastApi.getInterest(request),
    };

    /**
     * Object that contains reactive data that is used in the template.
     */
    const data = reactive({
      interest: null as TradecastInterestType | null,
      page: 1,
      pageCount: null as number | null,
      limit: 9,
      videos: [] as TradecastMediaType[],
      videosLoading: false,
    });

    /**
     * Initializes the element, updating the interest and loading videos for it.
     */
    const init = () => {
      data.interest = props.interest;

      if (data.interest) {
        loadVideos(data.interest.id);
      }
    };

    /**
     * Loads videos for a given interest id.
     *
     * @param id
     */
    const loadVideos = (id: number): void => {
      data.videosLoading = true;

      actions
        .getInterest({ id: id, page: data.page, limit: data.limit })
        .then((result: DataResponse<TradecastInterestResponse>) => {
          data.videos = result.data?.interest?.mediaList.results ?? [];
          data.videosLoading = false;
          data.pageCount = result.data?.interest?.mediaList.pageCount ?? null;
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
     * Handles video clicks. Emits video clicked event to the parent, providing the video object as data.
     *
     * @param video
     */
    const onVideoClicked = (video: TradecastMediaType): void => {
      emit('video-clicked', video);
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
      () => props.interest,
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
      onVideoClicked,
      t,
    };
  },
});
</script>

<style lang="scss">
@import '../assets/styles/main';
</style>
