<template>
  <div class="tradecast-pagination">
    <template v-if="this.totalPages > 1">
      <span class="page prev" v-if="this.page > 1" @click="this.previousPage">&lsaquo;</span>
      <span class="page first" :class="{ current: this.page === 1 }" @click="this.setPage(1)">1</span>
      <span class="page divider" v-if="this.page > 3">..</span>
      <span
        class="page"
        :class="{ current: this.page === page }"
        v-for="page in this.pages"
        :key="page"
        @click="this.setPage(page)"
        >{{ page }}</span
      >
      <span class="page divider" v-if="this.page < this.totalPages - 2">..</span>
      <span
        class="page last"
        :class="{ current: this.page === this.totalPages }"
        v-if="this.totalPages > 1"
        @click="this.setPage(this.totalPages)"
        >{{ this.totalPages }}</span
      >
      <span class="page next" v-if="this.page < this.totalPages" @click="this.nextPage">&rsaquo;</span>
    </template>
  </div>
</template>

<script lang="ts">
import { defineComponent, PropType, reactive, toRefs, watch } from 'vue';
import { range } from 'lodash';

const props = {
  page: {
    type: Number as PropType<number>,
    default: 1,
  },
  totalPages: {
    type: Number as PropType<number>,
    default: 1,
  },
};

const emits = ['pageChanged'];

export default defineComponent({
  name: 'tradecast-pagination',
  props,
  emits,
  setup(props, { emit }) {
    /**
     * Object that contains reactive data that is used in the template.
     */
    const data = reactive({
      page: 1,
      pages: [] as number[],
    });

    /**
     * Emits page changed event to the parent.
     */
    const emitPageChanged = (): void => {
      emit('pageChanged', data.page);
    };

    /**
     * Initializes the element.
     */
    const init = () => {
      data.page = props.page as number;
      data.pages = range(2, 1 + (props.totalPages - 1), 1);

      if (props.totalPages <= 5) {
        return;
      }

      // change the start and end, so that not all pages are shown as buttons
      // but just a select number of pages around the current page
      let start = data.page - 1;
      let end = data.page + 2;

      if (data.page < 3) {
        start = 2;
        end = data.page + (4 - data.page);
      }

      if (data.page > props.totalPages - 2) {
        start = data.page - 2 - (props.totalPages - data.page);
        end = props.totalPages;
      }

      data.pages = range(start, end, 1);
    };

    /**
     * Handles changing to the previous page. Sets the page in the data object. Emits page changed event to the parent.
     */
    const previousPage = (): void => {
      data.page = data.page - 1 > 1 ? data.page - 1 : 1;
      emitPageChanged();
    };

    /**
     * Handles changing to the next page. Sets the page in the data object. Emits page changed event to the parent.
     */
    const nextPage = (): void => {
      data.page = data.page + 1 < props.totalPages ? data.page + 1 : props.totalPages;
      emitPageChanged();
    };

    /**
     * Sets the page to a given page number. Emits page changed event to the parent.
     *
     * @param page
     */
    const setPage = (page: number): void => {
      if (page < 1 || page > props.totalPages) {
        return;
      }

      data.page = page;
      emitPageChanged();
    };

    /**
     * Watches the page property. On update, runs the init method.
     */
    watch(
      () => props.page,
      () => {
        init();
      }
    );

    /**
     * Watches the totalPages property. On update, runs the init method.
     */
    watch(
      () => props.totalPages,
      () => {
        init();
      }
    );

    // returns the methods and data, so that it can be used in the template
    return {
      ...toRefs(data),
      previousPage,
      nextPage,
      setPage,
    };
  },
});
</script>

<style lang="scss">
@import '../assets/styles/main';
</style>
