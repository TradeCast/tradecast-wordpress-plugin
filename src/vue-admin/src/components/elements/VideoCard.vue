<template>
  <div class="tradecast-video-card admin">
    <div
      class="header"
      :style="'background: url(' + (this.video.thumb ?? '') + ') no-repeat center center; background-size: cover;'"
    />
    <div class="content">
      <h3>{{ this.video.title }}</h3>
      <span>{{ this.formatDuration(this.video.duration) }} | {{ this.formatDate(this.video.createdAt) }}</span>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, PropType } from 'vue';
import { useI18n } from 'vue-i18n';
import { TradecastMediaType } from '@tradecast/library';
import { format } from 'date-fns';

const props = {
  video: {
    type: Object as PropType<TradecastMediaType>,
  },
};

export default defineComponent({
  name: 'tradecast-video-card',
  props: props,
  setup() {
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

    // returns the methods and data, so that it can be used in the template
    return {
      formatDate,
      formatDuration,
      t,
    };
  },
});
</script>

<style lang="scss">
@import '../assets/styles/main';
</style>
