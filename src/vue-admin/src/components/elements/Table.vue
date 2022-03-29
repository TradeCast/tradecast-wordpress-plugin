<template>
  <table class="wp-list-table widefat fixed striped table-view-list">
    <thead v-if="this.columns?.length">
      <tr>
        <th v-for="column in this.columns" :key="column.name" scope="col" :class="column?.className">
          <slot name="header-column" :column="column">
            {{ column?.title }}
          </slot>
        </th>
      </tr>
    </thead>
    <tbody>
      <template v-if="this.rows?.length">
        <tr v-for="row in this.rows" :key="row" :class="row?.className">
          <slot name="row" :row="row">
            <td v-for="column in this.columns" :key="column.name" :class="column?.className">
              <slot name="row-column" :column="column" :row="row">
                {{ this.resolve(column.property, row) }}
              </slot>
            </td>
          </slot>
        </tr>
      </template>
      <template v-if="!this.rows?.length && this.columns?.length">
        <td :colspan="this.columns.length" class="column-not-found">
          {{ t('notices.items.notFound') }}
        </td>
      </template>
    </tbody>
    <tfoot v-if="this.columns?.length">
      <tr>
        <th v-for="column in this.columns" :key="column.name" scope="col" :class="column?.className">
          <slot name="footer-column" :column="column">
            {{ column?.title }}
          </slot>
        </th>
      </tr>
    </tfoot>
  </table>
</template>

<script lang="ts">
import { get } from 'lodash';
import { defineComponent, PropType } from 'vue';
import { TableColumnType, TableRowType } from '@/types';
import { useI18n } from 'vue-i18n';

const props = {
  columns: {
    type: Object as PropType<TableColumnType[]>,
  },
  rows: {
    type: Object as PropType<TableRowType<Record<string, unknown>>[]>,
  },
};

export default defineComponent({
  name: 'tradecast-table',
  props: props,
  setup() {
    const { t } = useI18n();

    // returns the methods and data, so that it can be used in the template
    return {
      t,
      resolve: (path: string, obj: Record<string, unknown>) => {
        return get(obj, path);
      },
    };
  },
});
</script>
