<template>
  <div class="tradecast-add-video-modal-container">
    <div class="tradecast-add-video-modal-backdrop close-dialog" @click="this.onCloseDialog">
      <div class="tradecast-add-video-modal">
        <div class="header">
          <h2>{{ t('modals.videos.title') }}</h2>
          <span class="dashicons dashicons-no-alt icon-close-dialog close-dialog" @click="this.onCloseDialog" />
        </div>
        <div class="content">
          <div class="media-field-container">
            <label for="mediaField">{{ t('modals.videos.input.label') }}</label>
            <input
              id="mediaField"
              type="text"
              class="media-field"
              v-model="this.mediaFieldValue"
              ref="mediaField"
              :class="this.getValidatorClass"
              :placeholder="t('modals.videos.input.placeholder')"
              @keyup="this.onInputMediaFieldKeyUp"
            />
            <span class="status">{{ this.statusMessage }}</span>
          </div>
        </div>
        <div class="footer">
          <button
            type="button"
            class="button button-primary add-video"
            :disabled="!this.isMediaIdValid"
            @click="this.onSave"
          >
            {{ t('buttons.videos.addVideo.label') }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { computed, defineComponent, reactive, ref, toRefs, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import { useStore } from 'vuex';
import { useRouter } from 'vue-router';
import { MessageType } from '@/types';
import {
  DataResponse,
  TradecastApiService,
  TradecastMediaResponse,
  TradecastMediaType,
  TradecastRequest,
  WordPressApiService,
  WordPressVideoType,
} from '@tradecast/library';
import settings from '@/data/settings';

const emits = ['submit', 'close'];

export default defineComponent({
  name: 'tradecast-add-video-modal',
  emits,
  setup(props, { emit }) {
    const { t } = useI18n();
    const mediaField = ref<InstanceType<typeof HTMLInputElement>>();
    const router = useRouter();
    const store = useStore();
    const tradecastApi = new TradecastApiService(settings);
    const wordpressApi = new WordPressApiService(settings);

    /**
     * Object that contains reactive data that is used in the template.
     */
    const data = reactive({
      media: null as TradecastMediaType | null,
      mediaFieldValue: '',
      isMediaIdValid: null as boolean | null,
      validationTimer: null as number | null,
      statusMessage: t('modals.videos.status.initial'),
    });

    /**
     * Object that contains methods for performing actions on the API.
     */
    const actions = {
      addFlashMessage: (message: MessageType) => store.dispatch('messages/addFlashMessage', message),
      addVideo: (media: TradecastMediaType) => wordpressApi.addVideo(media),
      getMedia: (request: TradecastRequest) => tradecastApi.getMedia(request),
    };

    /**
     * Gets the media id from a given string input.
     */
    const getMediaId = (): number | null => {
      const id = parseInt(data.mediaFieldValue);

      // if the media field value can be parsed as int, return its value
      if (!isNaN(id)) {
        return id;
      }

      // if the media field value can't be parsed as int, try to find a number in the string through regular expression
      const matches = data.mediaFieldValue.split(new RegExp(/(\/\d+|=\d+)/));
      const matchedId = parseInt(matches.length > 1 && matches[1].length > 0 ? matches[1].substr(1) : '');

      return !isNaN(matchedId) ? matchedId : null;
    };

    /**
     * Gets the class for the validator in the template.
     */
    const getValidatorClass = computed({
      get: () => {
        if (data.isMediaIdValid === null) {
          return '';
        }

        return data.isMediaIdValid ? 'valid' : 'invalid';
      },
      set: () => {
        return;
      },
    });

    /**
     * Handles closes. Closes the dialog, emits a close event to the parent and resets the modal.
     *
     * @param event
     */
    const onCloseDialog = (event: PointerEvent) => {
      event.stopPropagation();

      const target = event.target as HTMLElement;

      if (!target.classList.contains('close-dialog')) {
        return;
      }

      emit('close');
      reset();
    };

    /**
     * Handles input field changes. Starts or removes validation of the media field value.
     */
    const onInputMediaFieldChanged = () => {
      if (data.validationTimer) {
        clearTimeout(data.validationTimer);
      }

      removeValidation();

      if (data.mediaFieldValue.length < 1) {
        return;
      }

      data.validationTimer = setTimeout(() => {
        validateMediaId();
      }, 350);
    };

    /**
     * Handles key presses. If enter is pressed, the media is saved.
     *
     * @param event
     */
    const onInputMediaFieldKeyUp = (event: KeyboardEvent) => {
      event.stopPropagation();

      if (event.key !== 'Enter') {
        return;
      }

      // if the media is already validated, save it
      if (data.isMediaIdValid && data.media !== null) {
        onSave();
        return;
      }

      // if the media is not validated, validate it and then save it
      setDisabled(true);
      validateMediaId()
        .then((response: DataResponse<TradecastMediaResponse> | boolean) => {
          if (response === false) {
            return;
          }

          onSave();
          setDisabled(false);
        })
        .catch(() => {
          setDisabled(false);
        });
    };

    /**
     * Handles saves. Stores the video in the backend through the API.
     */
    const onSave = () => {
      if (data.media === null) {
        return;
      }

      actions
        .addVideo(data.media as TradecastMediaType)
        .then((response: DataResponse<WordPressVideoType>) => {
          if (!response?.success) {
            throw Error();
          }

          actions.addFlashMessage({
            className: 'success',
            message: t('notices.videos.addVideo.success', [data.media?.title]),
          });

          emit('submit');
          reset();
        })
        .catch(() => {
          actions.addFlashMessage({
            className: 'failure',
            message: t('notices.video.addVideo.failure', [data.media?.title]),
          });

          emit('close');
          reset();
        });
    };

    /**
     * Removes the validation from the template.
     */
    const removeValidation = () => {
      data.isMediaIdValid = null;
      data.statusMessage = t('modals.videos.status.initial');
    };

    /**
     * Resets this modal.
     */
    const reset = () => {
      router.push('/');
      removeValidation();
      data.media = null;
      data.mediaFieldValue = '';
    };

    /**
     * Disables the media field.
     *
     * @param disabled
     */
    const setDisabled = (disabled: boolean) => {
      if (!mediaField.value) {
        return;
      }

      mediaField.value.disabled = disabled;
    };

    /**
     * Sets focus on the media field.
     */
    const setFocus = () => {
      setTimeout(() => {
        mediaField.value?.focus();
      }, 50);
    };

    /**
     * Toggles the validation in the template between valid and invalid.
     *
     * @param valid
     */
    const toggleValidation = (valid: boolean) => {
      data.isMediaIdValid = valid;

      if (valid) {
        data.statusMessage = t('modals.videos.status.found');
      }

      if (!valid) {
        data.statusMessage = t('modals.videos.status.invalid');
      }
    };

    /**
     * Validates the media id against the API.
     */
    const validateMediaId = (): Promise<DataResponse<TradecastMediaResponse> | boolean> => {
      const mediaId = getMediaId();

      if (!mediaId) {
        return new Promise<DataResponse<TradecastMediaResponse> | boolean>((reject) => {
          reject(false);
        });
      }

      return actions
        .getMedia({ id: mediaId })
        .then((response: DataResponse<TradecastMediaResponse>) => {
          toggleValidation(response.data?.media !== null && response.data?.media !== undefined);

          if (response.data?.media !== undefined) {
            data.media = response.data.media;
          }
        })
        .then();
    };

    /**
     * Sets focus on the media field when the element is ready.
     */
    router.isReady().then(() => {
      setFocus();
    });

    /**
     * Watches the mediaFieldValue property. On change, runs the media field changed handler.
     */
    watch(
      () => data.mediaFieldValue,
      () => {
        onInputMediaFieldChanged();
      }
    );

    // returns the methods and data, so that it can be used in the template
    return {
      ...toRefs(data),
      mediaField,
      onCloseDialog,
      onInputMediaFieldChanged,
      onInputMediaFieldKeyUp,
      onSave,
      getValidatorClass,
      t,
    };
  },
});
</script>

<style lang="scss">
@import '../assets/styles/main';
</style>
