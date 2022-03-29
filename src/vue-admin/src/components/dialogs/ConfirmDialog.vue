<template>
  <div class="tradecast-confirm-dialog-container">
    <div
      class="tradecast-confirm-dialog-backdrop close-dialog"
      :class="{ 'display-none': !this.isDialogVisible }"
      @click="this.cancel"
    >
      <div class="tradecast-confirm-dialog">
        <div class="header">
          <h2>{{ this.title ?? t('title') }}</h2>
          <span class="dashicons dashicons-no-alt icon-close-dialog close-dialog" @click="this.cancel" />
        </div>
        <div class="content">
          {{ this.message }}
        </div>
        <div class="footer">
          <button
            autofocus
            tabindex="1"
            type="button"
            class="button button-primary yes close-dialog"
            ref="confirmButton"
            @click="this.confirm"
          >
            {{ this.isYesNoDialog ? t('buttons.generic.yes.label') : t('buttons.generic.ok.label') }}
          </button>
          <button tabindex="2" type="button" class="button button-secondary no close-dialog" @click="this.cancel">
            {{ this.isYesNoDialog ? t('buttons.generic.no.label') : t('buttons.generic.cancel.label') }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, reactive, ref, toRefs, watch } from 'vue';
import { useI18n } from 'vue-i18n';

export default defineComponent({
  name: 'tradecast-confirm-dialog',
  setup() {
    const { t } = useI18n();
    const confirmButton = ref<InstanceType<typeof HTMLInputElement>>();

    /**
     * Object that contains reactive data that is used in the template.
     */
    const data = reactive({
      title: '',
      message: '',
      isDialogVisible: false,
      isYesNoDialog: true,
      resolvePromise: undefined as ((value: boolean) => void) | undefined,
      rejectPromise: undefined as ((value: boolean) => void) | undefined,
    });

    /**
     * Cancel event handler. Closes the dialog and resolves the promise with a value of false.
     *
     * @param event
     */
    const cancel = (event: Event) => {
      event.stopPropagation();

      // get the target element
      const target: HTMLElement = event.target as HTMLElement;

      if (!target.classList.contains('close-dialog')) {
        return;
      }

      // make the dialog invisible
      data.isDialogVisible = false;

      if (data.resolvePromise === undefined) {
        return;
      }

      // resolve the promise
      data.resolvePromise(false);
    };

    /**
     * Confirm event handler. Closes the dailog and resolves the promise with a value of true.
     */
    const confirm = () => {
      // make the dialog invisible
      data.isDialogVisible = false;

      if (data.resolvePromise === undefined) {
        return;
      }

      // resolve the promise
      data.resolvePromise(true);
    };

    /**
     * Sets focus to the confirm button when the dialog is visible.
     */
    const setFocus = () => {
      if (!data.isDialogVisible) {
        return;
      }

      setTimeout(() => {
        confirmButton.value?.focus();
      }, 50);
    };

    /**
     * Shows the dialog. Setting its title, message and type (yes/no or okay/cancel).
     *
     * @param title
     * @param message
     * @param isYesNo
     */
    const show = (title: string, message: string, isYesNo = true) => {
      data.title = title;
      data.message = message;
      data.isDialogVisible = true;
      data.isYesNoDialog = isYesNo;

      return new Promise((resolve, reject) => {
        data.resolvePromise = resolve;
        data.rejectPromise = reject;
      });
    };

    /**
     * Watches the isDialogVisible variable. On changes, it calls the setFocus method.
     */
    watch(
      () => data.isDialogVisible,
      () => {
        setFocus();
      }
    );

    // returns the methods and data, so that it can be used in the template
    return {
      ...toRefs(data),
      cancel,
      confirm,
      confirmButton,
      show,
      t,
    };
  },
});
</script>

<style lang="scss">
@import '../assets/styles/main';
</style>
