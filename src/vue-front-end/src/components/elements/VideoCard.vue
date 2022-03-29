<template>
  <div class="tradecast-video-card front-end" @click="this.onClicked">
    <div class="header">
      <img v-if="this.video.thumb" :src="this.video.thumb" :alt="this.video.title" />
    </div>
    <div class="content">
      <h3>{{ this.video.title }}</h3>
      <span>{{ this.formatDuration(this.video.duration) }} | {{ this.title(this.video) }}</span>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, PropType } from 'vue';
import { useI18n } from 'vue-i18n';
import { TradecastCategoryType, TradecastMediaType } from '@tradecast/library';
import { format } from 'date-fns';

const emits = ['click'];

const props = {
  video: {
    type: Object as PropType<TradecastMediaType>,
  },
};

export default defineComponent({
  name: 'tradecast-video-card',
  props,
  emits,
  setup(props, { emit }) {
    const { t } = useI18n();

    /**
     * Formats the date to a locale date string.
     *
     * @param date
     */
    const formatDate = (date: string): string => {
      const d = new Date(date);
      return d.toLocaleDateString();
    };

    /**
     * Formats the duration in minutes and seconds.
     *
     * @param duration
     */
    const formatDuration = (duration: number): string => {
      return format(duration * 1000, 'mm:ss');
    };

    /**
     * Generates the title for the video.
     *
     * @param video
     */
    const title = (video: TradecastMediaType): string => {
      if (!video.categories?.length) {
        return formatDate(video.createdAt.toString());
      }

      let titles: string[] = [];

      video.categories.forEach((category: TradecastCategoryType) => {
        titles.push(category.title);
      });

      return titles.join(',');
    };

    /**
     * Handles clicks. On click, emits a click event to the parent, providing the video object as data.
     * @param event
     */
    const onClicked = (event: MouseEvent): void => {
      event.stopPropagation();
      emit('click', props.video);
    };

    // returns the methods and data, so that it can be used in the template
    return {
      formatDate,
      formatDuration,
      onClicked,
      t,
      title,
    };
  },
});
</script>

<style lang="scss">
@import '../assets/styles/main';
</style>
