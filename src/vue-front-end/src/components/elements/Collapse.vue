<template>
  <div class="tradecast-collapse">
    <div class="container">
      <div class="title" @click="this.onClick">
        <input
          v-if="this.showCheckbox"
          v-model="this.checked"
          class="no-toggle"
          type="checkbox"
          @change="this.onChecked"
        />
        <span>{{ this.title }}</span>
        <span class="dashicons" :class="this.collapsed ? 'dashicons-arrow-down' : 'dashicons-arrow-up'"></span>
      </div>
      <div class="content" :class="this.collapsed ? 'collapsed' : 'expanded'">
        <slot name="content" :collapsed="this.collapsed"> </slot>
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
  checked: {
    type: Boolean as PropType<boolean>,
    default: false,
  },
  collapsed: {
    type: Boolean as PropType<boolean>,
    default: true,
  },
  showCheckbox: {
    type: Boolean as PropType<boolean>,
    default: false,
  },
};

const emits = ['checked', 'beforeExpand', 'expanded', 'beforeCollapse', 'collapsed'];

export default defineComponent({
  name: 'tradecast-collapse',
  props,
  emits,
  setup(props, { emit }) {
    /**
     * Object that contains reactive data that is used in the template.
     */
    const data = reactive({
      title: props.title as string,
      checked: props.checked as boolean,
      collapsed: (props.collapsed as boolean) ?? true,
      showCheckbox: props.showCheckbox as boolean,
    });

    /**
     * Handles the checked event. Emits a checked event to the parent, providing the checked value as data.
     */
    const onChecked = (): void => {
      emit('checked', data.checked);
    };

    /**
     * Handles the click event. Toggles the collapse container. Emits events to the parent.
     *
     * @param event
     */
    const onClick = (event: PointerEvent) => {
      event.stopPropagation();

      const target = event.target as HTMLElement;

      if (target.classList.contains('no-toggle')) {
        return;
      }

      emit(data.collapsed ? 'beforeExpand' : 'beforeCollapse');
      data.collapsed = !data.collapsed;
      emit(data.collapsed ? 'collapsed' : 'expanded');
    };

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
     * Watches the checked property. On update, sets checked in the data object.
     */
    watch(
      () => props.checked,
      (value, oldValue) => {
        if (value !== oldValue) {
          data.checked = value;
        }
      }
    );

    /**
     * Watches the collapsed property. On update, sets collapsed in the data object.
     */
    watch(
      () => props.collapsed,
      (value, oldValue) => {
        if (value !== oldValue) {
          data.collapsed = value;
        }
      }
    );

    /**
     * Watches the showCheckbox property. On update, sets showCheckbox in the data object.
     */
    watch(
      () => props.showCheckbox,
      (value, oldValue) => {
        if (value !== oldValue) {
          data.showCheckbox = value;
        }
      }
    );

    // returns the methods and data, so that it can be used in the template
    return {
      ...toRefs(data),
      onChecked,
      onClick,
    };
  },
});
</script>

<style lang="scss">
@import '../assets/styles/main';
</style>
