<template>
  <div class="tradecast-gallery-header-container">
    <div class="tradecast-gallery-header">
      <div class="logo" />
      <div class="content">
        <h2>{{ this.title }}</h2>
        <p v-html="this.content" />
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, PropType, reactive, toRefs, watch } from 'vue';

const props = {
  title: {
    type: String as PropType<string>,
    default: '',
  },
  content: {
    type: String as PropType<string>,
    default: '',
  },
};

export default defineComponent({
  name: 'tradecast-gallery-header',
  props,
  setup(props) {
    /**
     * Object that contains reactive data that is used in the template.
     */
    const data = reactive({
      title: props.title as string,
      content: props.content as string,
    });

    /**
     * Watches the title property. On update, sets title in the data object.
     */
    watch(
      () => props.title,
      (value, oldValue) => {
        if (value !== oldValue) {
          data.title = value;
        }
      }
    );

    /**
     * Watches the content property. On update, sets content in the data object.
     */
    watch(
      () => props.content,
      (value, oldValue) => {
        if (value !== oldValue) {
          data.content = value;
        }
      }
    );

    // returns the methods and data, so that it can be used in the template
    return {
      ...toRefs(data),
    };
  },
});
</script>

<style lang="scss">
@import '../assets/styles/main';
</style>
