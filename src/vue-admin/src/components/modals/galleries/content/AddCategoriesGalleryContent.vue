<template>
  <div class="tradecast-add-categories-gallery-content-container">
    <div class="header">
      <h2>{{ t('modals.galleries.categories.title') }}</h2>

      <div class="search">
        <input v-model="this.searchTerms" type="text" name="search" />
      </div>
    </div>

    <div class="categories">
      <tradecast-loader class="loader-overlay" v-if="this.categoriesLoading" />

      <span v-if="this.resultCount !== null">{{
        t(
          this.resultCount !== 1
            ? 'modals.galleries.categories.search.results'
            : 'modals.galleries.categories.search.result',
          [this.resultCount]
        )
      }}</span>

      <template v-for="category in this.categories" :key="category.id">
        <tradecast-collapse
          :title="category.title"
          :checked="this.gallery.ids?.includes(category.id)"
          :show-checkbox="true"
          class="category-collapse"
          @checked="this.onCategoryChecked(category, $event)"
        >
          <template #content="{ collapsed }">
            <hr />
            <span class="description" v-if="category.description">{{ category.description }}</span>
            <tradecast-category-video-grid v-if="!collapsed" :category="category" />
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
import { TradecastApiService } from '@tradecast/library';
import {
  TradecastCategoryListResponse,
  DataResponse,
  GalleryContentType,
  GalleryTypeEnum,
  TradecastCategoryType,
  TradecastRequest,
} from '@tradecast/library';
import { TradecastCollapse, TradecastLoader, TradecastPagination, TradecastCategoryVideoGrid } from '@/components';
import settings from '@/data/settings';

const emits = ['update'];

export default defineComponent({
  name: 'tradecast-add-categories-gallery-content',
  emits,
  components: { TradecastPagination, TradecastLoader, TradecastCategoryVideoGrid, TradecastCollapse },
  setup(props, { emit }) {
    const { t } = useI18n();
    const tradecastApi = new TradecastApiService(settings);

    /**
     * Object that contains methods for performing actions on the API.
     */
    const actions = {
      getCategoryList: (request: TradecastRequest) => tradecastApi.getCategoryList(request),
    };

    /**
     * Object that contains reactive data that is used in the template.
     */
    const data = reactive({
      gallery: { title: t('title'), ids: [], titles: [], type: GalleryTypeEnum.CATEGORIES } as GalleryContentType,
      categories: [] as TradecastCategoryType[],
      categoriesLoading: false,
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
      data.categoriesLoading = true;

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

      // request category list from the API
      actions.getCategoryList(request).then((result: DataResponse<TradecastCategoryListResponse>) => {
        data.categories = result.data?.categoryList.results ?? [];
        data.categoriesLoading = false;
        data.pageCount = result.data?.categoryList.pageCount ?? null;
        data.resultCount = result.data?.categoryList.resultCount ?? null;
      });
    };

    /**
     * Handles category checks. Pushes category IDs to the gallery object.
     *
     * @param category
     * @param checked
     */
    const onCategoryChecked = (category: TradecastCategoryType, checked: boolean) => {
      if (checked && !data.gallery.ids?.includes(category.id)) {
        data.gallery.ids?.push(category.id);
      }

      if (checked && !data.gallery.titles?.includes(category.title)) {
        data.gallery.titles?.push(category.title);
      }

      if (!checked && data.gallery.ids?.includes(category.id)) {
        data.gallery.ids = data.gallery.ids?.filter((id: number) => id !== category.id) ?? [];
      }

      if (!checked && data.gallery.titles?.includes(category.title)) {
        data.gallery.titles = data.gallery.titles?.filter((title: string) => title !== category.title) ?? [];
      }

      data.gallery.title = data.gallery.titles?.join(', ') || '';

      update();
    };

    /**
     * Handles page changes. On change, runs the init method.
     *
     * @param page
     */
    const onPageChanged = (page: number): void => {
      data.page = page;
      init();
    };

    /**
     * Emits update event to the parent, providing the gallery object as data.
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
      onCategoryChecked,
      onPageChanged,
      t,
    };
  },
});
</script>

<style lang="scss">
@import '../assets/styles/main';
</style>
