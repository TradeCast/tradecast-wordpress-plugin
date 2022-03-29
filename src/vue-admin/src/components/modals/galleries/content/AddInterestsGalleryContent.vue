<template>
  <div class="tradecast-add-interests-gallery-content-container">
    <div class="header">
      <h2>{{ t('modals.galleries.interests.title') }}</h2>

      <div class="search">
        <input v-model="this.searchTerms" type="text" name="search" />
      </div>
    </div>

    <div class="interests">
      <tradecast-loader class="loader-overlay" v-if="this.interestsLoading" />

      <span v-if="this.resultCount !== null">{{
        t(
          this.resultCount !== 1
            ? 'modals.galleries.interests.search.results'
            : 'modals.galleries.interests.search.result',
          [this.resultCount]
        )
      }}</span>

      <template v-for="interest in this.interests" :key="interest.id">
        <tradecast-collapse
          :title="interest.name"
          :checked="this.gallery.ids?.includes(interest.id)"
          :show-checkbox="true"
          class="interest-collapse"
          @checked="this.onInterestChecked(interest, $event)"
        >
          <template #content="{ collapsed }">
            <hr />
            <tradecast-interest-video-grid v-if="!collapsed" :interest="interest" />
          </template>
        </tradecast-collapse>
      </template>
      <tradecast-pagination :page="this.page" :total-pages="this.pageCount" @page-changed="this.onPageChanged" />
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, onMounted, reactive, toRefs, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import {
  DataResponse,
  GalleryContentType,
  GalleryTypeEnum,
  TradecastInterestType,
  TradecastInterestListResponse,
} from '@tradecast/library';
import { TradecastApiService } from '@tradecast/library';
import { TradecastRequest } from '@tradecast/library';
import { TradecastCollapse, TradecastLoader, TradecastPagination, TradecastInterestVideoGrid } from '@/components';
import settings from '@/data/settings';

const emits = ['update'];

export default defineComponent({
  name: 'tradecast-add-interests-gallery-content',
  emits,
  components: {
    TradecastInterestVideoGrid,
    TradecastPagination,
    TradecastLoader,
    TradecastCollapse,
  },
  setup(props, { emit }) {
    const { t } = useI18n();
    const tradecastApi = new TradecastApiService(settings);

    /**
     * Object that contains methods for performing actions on the API.
     */
    const actions = {
      getInterestList: (request: TradecastRequest) => tradecastApi.getInterestList(request),
    };

    /**
     * Object that contains reactive data that is used in the template.
     */
    const data = reactive({
      gallery: {
        title: t('modals.galleries.interests.title'),
        ids: [],
        titles: [],
        type: GalleryTypeEnum.INTERESTS,
      } as GalleryContentType,
      interests: [] as TradecastInterestType[],
      interestsLoading: false,
      limit: 5,
      page: 1,
      pageCount: null as number | null,
      resultCount: null as number | null,
      searchTerms: '',
    });

    /**
     * Initializes this element.
     */
    const init = (): void => {
      data.interestsLoading = true;

      // create initial request
      const request = { page: data.page, limit: data.limit };

      // add search terms to request, if present
      if (data.searchTerms !== '') {
        Object.assign(request, {
          filter: {
            search: data.searchTerms,
          },
        });
      }

      // request interest list from the API
      actions.getInterestList(request).then((result: DataResponse<TradecastInterestListResponse>) => {
        data.interests = result.data?.interestList.results ?? [];
        data.interestsLoading = false;
        data.pageCount = result.data?.interestList.pageCount ?? null;
        data.resultCount = result.data?.interestList.resultCount ?? null;
      });
    };

    /**
     * Handles interest checks. Pushes interest IDs to the gallery object.
     *
     * @param interest
     * @param checked
     */
    const onInterestChecked = (interest: TradecastInterestType, checked: boolean) => {
      if (checked && !data.gallery.ids?.includes(interest.id)) {
        data.gallery.ids?.push(interest.id);
      }

      if (checked && !data.gallery.titles?.includes(interest.name)) {
        data.gallery.titles?.push(interest.name);
      }

      if (!checked && data.gallery.ids?.includes(interest.id)) {
        data.gallery.ids = data.gallery.ids?.filter((id: number) => id !== interest.id) ?? [];
      }

      if (!checked && data.gallery.titles?.includes(interest.name)) {
        data.gallery.titles = data.gallery.titles?.filter((title: string) => title !== interest.name) ?? [];
      }

      data.gallery.title = data.gallery.titles?.join(', ') || '';

      update();
    };

    /**
     * Handles page changes. Loads videos for the given page if the page is changed.
     *
     * @param page
     */
    const onPageChanged = (page: number): void => {
      data.page = page;
      init();
    };

    /**
     * Emits an update event to the parent, providing the gallery object as data.
     */
    const update = (): void => {
      emit('update', data.gallery);
    };

    /**
     * When this element is mounted, runs the init and update methods.
     */
    onMounted(() => {
      init();
      update();
    });

    /**
     * Watches the searchTerms property. On update, runs the init method.
     */
    watch(
      () => data.searchTerms,
      (value, oldValue) => {
        if ((value !== oldValue && value.length >= 3) || value === '') {
          if (value !== '') {
            data.page = 1;
          }

          init();
        }
      }
    );

    // returns the methods and data, so that it can be used in the template
    return {
      ...toRefs(data),
      onInterestChecked,
      onPageChanged,
      t,
    };
  },
});
</script>

<style lang="scss">
@import '../assets/styles/main';
</style>
