<template>
  <div class="tradecast-video-overlay-container" v-if="this.visible" @click="this.onClose">
    <div class="tradecast-video-overlay" v-if="this.video?.embedURL">
      <iframe :src="this.video.embedURL" frameborder="0" allowfullscreen />
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, PropType, reactive, toRefs, watch } from 'vue';
import { TradecastMediaType } from '@tradecast/library';

const emits = ['close'];

const props = {
  video: {
    type: Object as PropType<TradecastMediaType>,
  },
  visible: {
    type: Boolean as PropType<boolean>,
    default: true,
  },
};

export default defineComponent({
  name: 'tradecast-video-overlay',
  props,
  emits,
  setup(props, { emit }) {
    const data = reactive({
      video: props.video as TradecastMediaType,
      visible: (props.visible as boolean) ?? true,
    });

    /**
     * Handles closes. On close, emits the close event to the parent.
     *
     * @param event
     */
    const onClose = (event: MouseEvent): void => {
      event.stopPropagation();
      emit('close');
    };

    /**
     * Watches the video property. On update, sets the video property to the data object.
     */
    watch(
      () => props.video,
      () => {
        data.video = props.video as TradecastMediaType;
      }
    );

    /**
     * Watches the visible property. On update, sets the visible property to the data object.
     */
    watch(
      () => props.visible,
      () => {
        data.visible = (props.visible as boolean) ?? true;
      }
    );

    // returns the methods and data, so that it can be used in the template
    return {
      ...toRefs(data),
      onClose,
    };
  },
});
</script>

<style lang="scss">
@import '../assets/styles/main';
</style>
