<template>
  <div class="tradecast-gallery-container">
    <tradecast-video-overlay
      :video="this.video"
      :visible="this.isVideoOverlayVisible"
      @close="this.onVideoOverlayClosed"
    />

    <template v-if="!this.isVideosGallery">
      <tradecast-loader v-if="this.isLoading" />
    </template>

    <template v-if="this.isCategoriesGallery && this.categories.length">
      <div class="tradecast-gallery" v-for="category in this.categories" :key="category">
        <tradecast-collapse :title="category.title" :collapsed="false">
          <template #content="{ collapsed }">
            <tradecast-category-video-grid
              v-if="!collapsed"
              :category="category"
              @video-clicked="this.onVideoClicked"
            />
          </template>
        </tradecast-collapse>
      </div>
    </template>
    <template v-if="this.isInterestsGallery && this.interests.length">
      <div class="tradecast-gallery" v-for="interest in this.interests" :key="interest">
        <tradecast-collapse :title="interest.name" :collapsed="false">
          <template #content="{ collapsed }">
            <tradecast-interest-video-grid
              v-if="!collapsed"
              :interest="interest"
              @video-clicked="this.onVideoClicked"
            />
          </template>
        </tradecast-collapse>
      </div>
    </template>
    <template v-if="this.isVideosGallery">
      <div class="tradecast-gallery">
        <tradecast-video-grid :is-loading="this.isLoading" :videos="this.videos" @video-clicked="this.onVideoClicked" />
      </div>
      <tradecast-pagination :page="this.page" :total-pages="this.totalPages" @page-changed="this.onPageChanged" />
    </template>
  </div>
</template>

<script lang="ts">
import { defineComponent, onMounted, PropType, reactive, toRefs } from 'vue';
import {
  DataResponse,
  GalleryTypeEnum,
  Settings,
  TradecastApiService,
  TradecastCategoryResponse,
  TradecastCategoryType,
  TradecastInterestResponse,
  TradecastInterestType,
  TradecastMediaListResponse,
  TradecastMediaType,
  TradecastRequest,
} from '@tradecast/library';
import {
  TradecastCollapse,
  TradecastCategoryVideoGrid,
  TradecastInterestVideoGrid,
  TradecastLoader,
  TradecastVideoGrid,
} from '@/components';
import TradecastPagination from '@/components/elements/Pagination.vue';
import TradecastVideoOverlay from '@/components/elements/VideoOverlay.vue';

const props = {
  settings: Object as PropType<Settings>,
};

export default defineComponent({
  name: 'Gallery',
  props,
  components: {
    TradecastVideoOverlay,
    TradecastPagination,
    TradecastCollapse,
    TradecastCategoryVideoGrid,
    TradecastInterestVideoGrid,
    TradecastLoader,
    TradecastVideoGrid,
  },
  setup(props) {
    const settings = props.settings as Settings;
    const tradecastApi = new TradecastApiService(settings);

    /**
     * Object that contains methods for performing actions on the API.
     */
    const actions = {
      getCategory: (id: number) => tradecastApi.getCategory({ id, thumb: thumbSize(settings.gallery.columns) }),
      getInterest: (id: number) => tradecastApi.getInterest({ id, thumb: thumbSize(settings.gallery.columns) }),
      getMediaList: (request: TradecastRequest) => tradecastApi.getMediaList(request),
    };

    /**
     * Object that contains reactive data that is used in the template.
     */
    const data = reactive({
      categories: [] as TradecastCategoryType[],
      interests: [] as TradecastInterestType[],
      isLoading: false,
      isVideoOverlayVisible: false,
      page: 1,
      limit: 9,
      totalPages: null as number | null,
      video: null as TradecastMediaType | null,
      videos: [] as TradecastMediaType[],
    });

    /**
     * Initializes the element, updating the limits and loading media based on the gallery type.
     */
    const init = (): void => {
      // limit the number of items fetched, based on the number of colums
      data.limit = 9;

      if (settings.gallery.columns === 1) {
        data.limit = 6;
      }

      if (settings.gallery.columns === 2) {
        data.limit = 8;
      }

      if (settings.gallery.columns === 4) {
        data.limit = 12;
      }

      // if the gallery is of type categories, load the categories
      if (isCategoriesGallery()) {
        loadCategories();
      }

      // if the gallery is of type featured, load the featured videos
      if (isFeaturedGallery()) {
        loadMedia({
          filter: { eq: { featured: true } },
          page: data.page,
          limit: data.limit,
          thumb: thumbSize(settings.gallery.columns),
        });
      }

      // if the gallery is of type interests, load the interests
      if (isInterestsGallery()) {
        loadInterests();
      }

      // if the gallery is of type latest, load the latest videos
      if (isLatestGallery()) {
        loadMedia({
          sort: { publishStart: 'desc' },
          page: data.page,
          limit: data.limit,
          thumb: thumbSize(settings.gallery.columns),
        });
      }

      // if the gallery is of type popular, load the popular videos
      if (isPopularGallery()) {
        loadMedia({
          sort: { amountViewsMonth: 'desc' },
          page: data.page,
          limit: data.limit,
          thumb: thumbSize(settings.gallery.columns),
        });
      }
    };

    /**
     * Returns whether the gallery is of type categories.
     */
    const isCategoriesGallery = (): boolean => {
      return settings.gallery.type === GalleryTypeEnum.CATEGORIES;
    };

    /**
     * Returns whether the gallery is of type featured.
     */
    const isFeaturedGallery = (): boolean => {
      return settings.gallery.type === GalleryTypeEnum.FEATURED;
    };

    /**
     * Returns whether the gallery is of type interests.
     */
    const isInterestsGallery = (): boolean => {
      return settings.gallery.type === GalleryTypeEnum.INTERESTS;
    };

    /**
     * Returns whether the gallery is of type latest.
     */
    const isLatestGallery = (): boolean => {
      return settings.gallery.type === GalleryTypeEnum.LATEST;
    };

    /**
     * Returns whether the gallery is of type popular.
     */
    const isPopularGallery = (): boolean => {
      return settings.gallery.type === GalleryTypeEnum.POPULAR;
    };

    /**
     * Returns whether the gallery contains videos directly, or categories/interests with videos.
     */
    const isVideosGallery = (): boolean => {
      return isFeaturedGallery() || isLatestGallery() || isPopularGallery();
    };

    /**
     * Loads the categories based on the gallery's array of ids.
     */
    const loadCategories = (): void => {
      reset();

      settings.gallery.ids.forEach((id: number) => {
        data.isLoading = true;

        actions
          .getCategory(id)
          .then((response: DataResponse<TradecastCategoryResponse>) => {
            if (!response.data?.category) {
              throw new Error();
            }

            data.categories.push(response.data?.category as TradecastCategoryType);
            data.categories.sort((a, b) => a.title.localeCompare(b.title, undefined, { sensitivity: 'base' }));
            data.isLoading = false;
          })
          .catch(() => {
            data.isLoading = false;
            return;
          });
      });
    };

    /**
     * Loads the categories based on the gallery's array of ids.
     */
    const loadInterests = (): void => {
      reset();

      settings.gallery.ids.forEach((id: number) => {
        data.isLoading = true;

        actions
          .getInterest(id)
          .then((response: DataResponse<TradecastInterestResponse>) => {
            if (!response.data?.interest) {
              throw new Error();
            }

            data.interests.push(response.data?.interest as TradecastInterestType);
            data.interests.sort((a, b) => a?.name?.localeCompare(b?.name ?? '', undefined, { sensitivity: 'base' }));
            data.isLoading = false;
          })
          .catch(() => {
            data.isLoading = false;
            return;
          });
      });
    };

    /**
     * Loads media based on a request object.
     */
    const loadMedia = (request: TradecastRequest): void => {
      reset();

      data.isLoading = true;

      actions
        .getMediaList(request)
        .then((response: DataResponse<TradecastMediaListResponse>) => {
          if (!response.data?.mediaList) {
            throw new Error();
          }

          data.totalPages = response.data.mediaList.pageCount;
          data.videos = response.data.mediaList.results ?? [];
          data.isLoading = false;
        })
        .catch(() => {
          data.isLoading = false;
          return;
        });
    };

    /**
     * Resets the data in the gallery.
     */
    const reset = (): void => {
      data.categories = [];
      data.interests = [];
    };

    /**
     * Returns the thumbnail size for a given number of columns.
     *
     * @param columns
     */
    const thumbSize = (columns: number): number => {
      let size;

      switch (columns) {
        case 1:
          size = 1440;
          break;
        case 2:
          size = 1080;
          break;
        case 3:
          size = 860;
          break;
        default:
          size = 720;
      }

      return size;
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
     * Handles video clicks. Adds the video to the data object and shows the video overlay.
     *
     * @param video
     */
    const onVideoClicked = (video: TradecastMediaType): void => {
      data.video = video;
      data.isVideoOverlayVisible = true;
    };

    /**
     * Handles video overlay closes. On close, hides the video overlay.
     */
    const onVideoOverlayClosed = (): void => {
      data.isVideoOverlayVisible = false;
    };

    /**
     * Runs init method when this element is mounted.
     */
    onMounted(() => {
      init();
    });

    // returns the methods and data, so that it can be used in the template
    return {
      ...toRefs(data),
      isCategoriesGallery,
      isInterestsGallery,
      isVideosGallery,
      onPageChanged,
      onVideoClicked,
      onVideoOverlayClosed,
    };
  },
});
</script>

<style scoped></style>
