<template>
  <div class="wrap">
    <div class="tradecast-settings-container">
      <h1 class="wp-heading-inline">{{ t('views.settings.title') }}</h1>
      <p>{{ t('views.settings.description') }}</p>
      <div>
        <span class="label channel-id">{{ t('views.settings.input.channelId.label') }}:</span>
        <span class="validate" :class="this.getValidatorClass"><span class="dashicons icon"></span></span>
        <input type="text" class="input channel-id" v-model="this.channelId" />
        <span class="label status italic" :class="this.isStatusMessageVisible === false ? 'transparent' : ''">{{
          this.statusMessage
        }}</span>
      </div>
      <div>
        <button
          type="button"
          class="button button-primary submit"
          :disabled="this.isSaveButtonDisabled"
          @click="this.onSave"
        >
          {{ t('buttons.generic.save.label') }}
        </button>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { computed, defineComponent, onMounted, reactive, toRefs, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import {
  DataResponse,
  TradecastSettingsResponse,
  TradecastSettingsType,
  TradecastApiService,
  WordPressApiService,
  WordPressSettingsType,
} from '@tradecast/library';
import settings from '@/data/settings';

export default defineComponent({
  name: 'tradecast-settings-form',
  setup() {
    const tradecastApi = new TradecastApiService(settings);
    const wordpressApi = new WordPressApiService(settings);
    const { t } = useI18n();

    /**
     * Object that contains methods for performing actions on the API.
     */
    const actions = {
      tradecast: {
        getChannelInfo: (channelId: string) => tradecastApi.getChannelInfo(channelId),
      },
      wordpress: {
        getSettings: (refresh = true) => wordpressApi.getSettings(refresh),
        setSettings: (channelId: string) => wordpressApi.setSettings(channelId),
      },
    };

    /**
     * Object that contains reactive data that is used in the template.
     */
    const data = reactive({
      channelId: null as string | null,
      lastChannelId: null as string | null,
      isChannelIdValid: null as boolean | null,
      isSaveButtonDisabled: true,
      isStatusMessageVisible: false,
      statusMessage: '',
      validationTimer: null as number | null,
    });

    /**
     * Disable or enables the save button based on the given value.
     *
     * @param disabled
     */
    const disableSaveButton = (disabled: boolean): void => {
      data.isSaveButtonDisabled = disabled;
    };

    /**
     * Gets the class for the validator element in the template.
     */
    const getValidatorClass = computed({
      get: () => {
        if (data.isChannelIdValid === null) {
          return '';
        }

        return data.isChannelIdValid ? 'valid' : 'invalid';
      },
      set: () => {
        return;
      },
    });

    /**
     * Removes the validation from the data object.
     */
    const removeValidation = () => {
      data.isChannelIdValid = null;
    };

    /**
     * Shows a status message in the template. Auto-hides it after a period of time.
     *
     * @param success
     */
    const showStatus = (success: boolean) => {
      data.statusMessage = t('notices.generic.' + (success ? 'stored' : 'couldNotBeStored'));
      data.isStatusMessageVisible = true;

      setTimeout(() => {
        data.isStatusMessageVisible = false;
      }, 5000);
    };

    /**
     * Toggles the channel id validation in the data object.
     *
     * @param valid
     */
    const toggleValidation = (valid: boolean) => {
      data.isChannelIdValid = valid;
    };

    /**
     * Validates the channel id against the API.
     *
     * @param channelId
     */
    const validateChannelId = (channelId: string) => {
      if (channelId.length <= 3) {
        return;
      }

      actions.tradecast
        .getChannelInfo(channelId)
        .then((response: DataResponse<TradecastSettingsResponse>) => {
          const settings = response.data?.settings as TradecastSettingsType;

          toggleValidation(settings.channel.name?.toString() !== '');
        })
        .catch(() => {
          toggleValidation(false);
        });
    };

    /**
     * Handles input changes. On change, validates the input.
     */
    const onInputChannelIdChanged = () => {
      disableSaveButton(false);
      removeValidation();

      if (data.validationTimer) {
        clearTimeout(data.validationTimer);
      }

      if (!data.channelId || data.channelId.length <= 3) {
        return;
      }

      const channelId = data.channelId;

      data.validationTimer = setTimeout(() => {
        validateChannelId(channelId);
      }, 350);
    };

    /**
     * Handles saves. On click, saves the channel id in the backend through the API.
     */
    const onSave = () => {
      disableSaveButton(true);

      actions.wordpress
        .setSettings(data.channelId ?? '')
        .then(() => {
          showStatus(true);
        })
        .catch(() => {
          showStatus(false);
          disableSaveButton(false);
        });
    };

    /**
     * When this element is mounted. Validate the channel id, if set.
     */
    onMounted(() => {
      actions.wordpress.getSettings().then((response: WordPressSettingsType) => {
        data.channelId = response?.channelId ?? null;

        validateChannelId(data.channelId);
      });
    });

    /**
     * Watches the channelId property. On change, runs the input channel id changed handler.
     */
    watch(
      () => data.channelId,
      (value, oldValue) => {
        if (oldValue !== null && value !== oldValue) {
          onInputChannelIdChanged();
        }
      }
    );

    // returns the methods and data, so that it can be used in the template
    return {
      ...toRefs(data),
      getValidatorClass,
      onSave,
      t,
    };
  },
});
</script>

<style lang="scss">
@import '../assets/styles/main';
</style>
