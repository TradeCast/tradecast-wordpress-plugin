import { createStore } from 'vuex';
import { RootState } from '@/types';
import MessageModule from '@/store/modules/message';

export default createStore({
  state: {} as RootState,
  modules: {
    messages: MessageModule,
  },
});
