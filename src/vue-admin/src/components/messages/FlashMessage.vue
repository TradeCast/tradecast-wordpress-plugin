<template>
  <div class="tradecast-flash-message-container" v-if="this.messages.length">
    <div class="tradecast-flash-message" v-for="message of this.messages" :key="message" :class="message.className">
      {{ message.message }}
      <span class="dashicons dashicons-no-alt icon-close-flash-message" @click="this.remove(message.uuid)" />
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, reactive, toRefs, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import { useStore } from 'vuex';
import { MessageState, MessageType } from '@/types';
import { v4 } from 'uuid';

export default defineComponent({
  name: 'tradecast-flash-message',
  setup() {
    const { t } = useI18n();
    const store = useStore();
    const messageState = store.state.messages as MessageState;

    /**
     * Object that contains reactive data that is used in the template.
     */
    const data = reactive({
      messages: [] as MessageType[],
    });

    /**
     * Object that contains methods for performing actions on the API.
     */
    const actions = {
      removeFlashMessage: (message: MessageType) => store.dispatch('messages/removeFlashMessage', message),
    };

    /**
     * Adds the flash message to the template.
     *
     * @param message
     */
    const add = (message: MessageType) => {
      if (!message.uuid) {
        message.uuid = v4();
      }

      data.messages.push(message);

      setAutoRemove(message.uuid);
    };

    /**
     * Removes the flash message from the template.
     *
     * @param uuid
     */
    const remove = (uuid: string) => {
      data.messages = data.messages.filter((message) => message.uuid !== uuid);
    };

    /**
     * Automatically removes the flash message from the template after a period of time.
     *
     * @param uuid
     */
    const setAutoRemove = (uuid: string) => {
      setTimeout(() => {
        remove(uuid);
      }, 3500);
    };

    /**
     * Watches the messageState property. On update, adds the message to the template and removes the
     * message from the back-end through the API.
     */
    watch(
      () => messageState,
      () => {
        if (messageState.flashMessages?.length) {
          messageState.flashMessages.forEach((flashMessage) => {
            add(flashMessage);
            actions.removeFlashMessage(flashMessage);
          });
        }
      },
      {
        deep: true,
      }
    );

    // returns the methods and data, so that it can be used in the template
    return {
      ...toRefs(data),
      add,
      remove,
      t,
    };
  },
});
</script>

<style lang="scss">
@import '../assets/styles/main';
</style>
