import { ActionContext } from 'vuex';
import { MessageState, MessageType, RootState } from '@/types';
import { v4 } from 'uuid';

const initialState: MessageState = {
  flashMessages: [],
  toastMessages: [],
};

const MessageModule = {
  namespaced: true,
  state: initialState,
  getters: {},
  mutations: {},
  actions: {
    /**
     * Adds a flash message to the VueX store.
     *
     * @param context
     * @param message
     */
    addFlashMessage(context: ActionContext<MessageState, RootState>, message: MessageType): void {
      message.uuid = v4();

      if (!context.state.flashMessages) {
        context.state.flashMessages = [];
      }

      context.state.flashMessages.push(message);
    },

    /**
     * Removes a flash message from the VueX store.
     *
     * @param context
     * @param message
     */
    removeFlashMessage(context: ActionContext<MessageState, RootState>, message: MessageType): void {
      context.state.flashMessages =
        context.state.flashMessages?.filter((flashMessage) => flashMessage.uuid !== message.uuid) ?? [];
    },

    /**
     * Adds a toast message to the VueX store.
     *
     * @param context
     * @param message
     */
    addToastMessage(context: ActionContext<MessageState, RootState>, message: MessageType): void {
      message.uuid = v4();

      if (!context.state.toastMessages) {
        context.state.toastMessages = [];
      }

      context.state.toastMessages.push(message);
    },

    /**
     * Removes a toast message from the VueX store.
     *
     * @param context
     * @param message
     */
    removeToastMessage(context: ActionContext<MessageState, RootState>, message: MessageType): void {
      context.state.toastMessages =
        context.state.toastMessages?.filter((toastMessage) => toastMessage.uuid !== message.uuid) ?? [];
    },
  },
};

export default MessageModule;
