<template>
  <div class="tradecast-splash-screen-container">
    <div class="tradecast-splash-screen">
      <div class="content" v-html="this.content" />
    </div>
  </div>
</template>

<script lang="ts">
import { computed, defineComponent } from 'vue';
import { useI18n } from 'vue-i18n';

export default defineComponent({
  name: 'tradecast-splash-screen',
  setup() {
    const { t } = useI18n();

    /**
     * Returns the translated content for the splash screen.
     */
    const content = computed({
      get: () => {
        return '<h2>' + t('screens.splash.title') + '</h2>\n' + t('screens.splash.content');
      },
      set: () => {
        return;
      },
    });

    // returns the methods and data, so that it can be used in the template
    return {
      t,
      content,
    };
  },
});
</script>

<style lang="scss">
@import '../assets/styles/main';
</style>
