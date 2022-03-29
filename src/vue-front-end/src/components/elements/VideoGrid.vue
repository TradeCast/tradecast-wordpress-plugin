<template>
  <div class="tradecast-video-grid">
    <tradecast-loader class="loader-overlay" v-if="this.isLoading" />
    <template v-if="this.videos.length">
      <div class="videos" :class="{ loading: this.isLoading }">
        <tradecast-video-card
          v-for="video of this.videos"
          :video="video"
          :key="video.id"
          @click="this.onVideoClicked"
        />
      </div>
    </template>
  </div>
</template>

<script lang="ts">
import { defineComponent, PropType, reactive, toRefs, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import { TradecastMediaType } from '@tradecast/library';
import TradecastVideoCard from './VideoCard.vue';
import TradecastLoader from './Loader.vue';

const emits = ['videoClicked'];

const props = {
  isLoading: {
    type: Boolean as PropType<boolean>,
    default: false,
  },
  videos: {
    type: Array as PropType<TradecastMediaType[]>,
    default: [],
  },
};

export default defineComponent({
  name: 'tradecast-video-grid',
  components: { TradecastLoader, TradecastVideoCard },
  props,
  emits,
  setup(props, { emit }) {
    const { t } = useI18n();

    /**
     * Object that contains reactive data that is used in the template.
     */
    const data = reactive({
      isLoading: (props.isLoading as boolean) ?? false,
      videos: props.videos ?? ([] as TradecastMediaType[]),
    });

    /**
     * Initializes this element. Setting isLoading and videos on the data object.
     */
    const init = () => {
      data.isLoading = (props.isLoading as boolean) ?? false;
      data.videos = props.videos as TradecastMediaType[];
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
     * Watches the isLoading property. On update, runs the init method.
     */
    watch(
      () => props.isLoading,
      () => {
        init();
      }
    );

    /**
     * Watches the videos property. On update, runs the init method.
     */
    watch(
      () => props.videos,
      () => {
        init();
      }
    );

    // returns the methods and data, so that it can be used in the template
    return {
      ...toRefs(data),
      onVideoClicked,
      t,
    };
  },
});
</script>

<style lang="scss">
@import '../assets/styles/main';
</style>
