<template>
  <div class="myMetricsManager">
    <ContentBlock :content-title="translate('RebelMyReports_CalculatedMetrics')">
      <p>{{ translate('RebelMyReports_CalculatedMetricsIntro') }}</p>

      <div v-if="!edited">
        <table v-content-table>
          <thead>
            <tr>
              <th>{{ translate('General_Name') }}</th>
              <th>{{ translate('RebelMyReports_Formula') }}</th>
              <th>{{ translate('RebelMyReports_Format') }}</th>
              <th>{{ translate('General_Actions') }}</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="!calculatedMetrics.length">
              <td colspan="4">{{ translate('RebelMyReports_NoCalculatedMetricsYet') }}</td>
            </tr>
            <tr v-for="metric in calculatedMetrics" :key="metric.idmetric">
              <td>{{ decode(metric.name) }}</td>
              <td><code class="myMetricsManager__formula">{{ metric.formula }}</code></td>
              <td>{{ formats[metric.format] }}</td>
              <td class="myMetricsManager__actions">
                <button
                  type="button"
                  class="table-action icon-edit"
                  :title="translate('General_Edit')"
                  @click="edit(metric)"
                />
                <button
                  type="button"
                  class="table-action icon-content-copy"
                  :title="translate('RebelMyReports_Duplicate')"
                  @click="duplicated = metric"
                />
                <button
                  type="button"
                  class="table-action icon-delete"
                  :title="translate('General_Delete')"
                  @click="confirmDelete(metric)"
                />
              </td>
            </tr>
          </tbody>
        </table>
        <div class="myMetricsManager__duplicate" v-if="duplicated">
          <MyDuplicateForm
            :key="duplicated.idmetric"
            :item-name="decode(duplicated.name)"
            :is-busy="isDuplicating"
            @confirm="duplicate($event)"
            @cancel="duplicated = null"
          />
        </div>
        <div class="tableActionBar">
          <button type="button" class="btn" @click="edit(null)">
            <span class="icon-add" /> {{ translate('RebelMyReports_CreateCalculatedMetric') }}
          </button>
        </div>
      </div>

      <form class="myMetricsManager__form" v-else @submit.prevent="save()">
        <div class="myMetricsManager__field">
          <Field
            uicontrol="text"
            name="metricName"
            v-model="edited.name"
            :maxlength="100"
            :full-width="true"
            :title="translate('General_Name')"
          />
        </div>
        <div class="myMetricsManager__field">
          <Field
            uicontrol="text"
            name="metricFormula"
            v-model="edited.formula"
            :maxlength="1000"
            :full-width="true"
            :title="translate('RebelMyReports_Formula')"
            :inline-help="translate('RebelMyReports_FormulaHelp')"
          />
        </div>
        <div class="myMetricsManager__field">
          <Field
            uicontrol="expandable-select"
            name="insertMetric"
            :full-width="true"
            :model-value="''"
            :title="translate('RebelMyReports_InsertMetric')"
            :options="insertOptions"
            @update:model-value="insert($event)"
          />
        </div>
        <div class="myMetricsManager__field">
          <Field
            uicontrol="select"
            name="metricFormat"
            v-model="edited.format"
            :full-width="true"
            :title="translate('RebelMyReports_Format')"
            :options="formatOptions"
          />
        </div>
        <div class="myMetricsManager__buttons">
          <div class="myMetricsManager__save">
            <SaveButton
              :saving="isSaving"
              :disabled="!edited.name.trim() || !edited.formula.trim()"
              :value="translate('General_Save')"
              @confirm="save()"
            />
          </div>
          <button type="button" class="myMetricsManager__cancel" @click="edited = null">
            {{ translate('General_Cancel') }}
          </button>
        </div>
      </form>
    </ContentBlock>

    <div class="ui-confirm" ref="confirm">
      <h2>{{ translate('RebelMyReports_DeleteMetricConfirm', metricToDeleteName) }}</h2>
      <input role="yes" type="button" :value="translate('General_Yes')" />
      <input role="no" type="button" :value="translate('General_No')" />
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, PropType } from 'vue';
import {
  AjaxHelper,
  ContentBlock,
  ContentTable,
  Matomo,
  NotificationsStore,
  translate,
} from 'CoreHome';
import { Field, SaveButton } from 'CorePluginsAdmin';
import MyDuplicateForm, { DestinationSite } from '../MyDuplicateForm/MyDuplicateForm.vue';
import { CalculatedMetric, CatalogueItem } from '../types';

interface EditedMetric {
  idmetric: number|null;
  name: string;
  formula: string;
  format: string;
}

interface MyMetricsManagerState {
  calculatedMetrics: CalculatedMetric[];
  edited: EditedMetric|null;
  isSaving: boolean;
  metricToDeleteName: string;
  duplicated: CalculatedMetric|null;
  isDuplicating: boolean;
}

export default defineComponent({
  props: {
    metrics: {
      type: Array as PropType<CatalogueItem[]>,
      required: true,
    },
  },
  emits: ['changed'],
  components: {
    ContentBlock,
    Field,
    SaveButton,
    MyDuplicateForm,
  },
  directives: {
    ContentTable,
  },
  data(): MyMetricsManagerState {
    return {
      calculatedMetrics: [],
      edited: null,
      isSaving: false,
      metricToDeleteName: '',
      duplicated: null,
      isDuplicating: false,
    };
  },
  created() {
    this.fetch();
  },
  computed: {
    formats(): Record<string, string> {
      return {
        number: translate('RebelMyReports_FormatNumber'),
        percent: translate('RebelMyReports_FormatPercent'),
        duration: translate('RebelMyReports_FormatDuration'),
        money: translate('RebelMyReports_FormatMoney'),
      };
    },
    formatOptions(): { key: string, value: string }[] {
      return Object.entries(this.formats).map(([key, value]) => ({ key, value }));
    },
    insertOptions(): { group: string, key: string, value: string }[] {
      return this.metrics
        .filter((metric) => metric.usableInFormula)
        .map((metric) => ({
          group: metric.category,
          key: metric.id,
          value: `${metric.name} (${metric.id})`,
        }));
    },
  },
  methods: {
    decode(text: string): string {
      return Matomo.helper.htmlDecode(text);
    },
    fetch() {
      return AjaxHelper.fetch<CalculatedMetric[]>({
        method: 'RebelMyReports.getCalculatedMetrics',
        filter_limit: '-1',
      }).then((metrics) => {
        this.calculatedMetrics = metrics;
      });
    },
    edit(metric: CalculatedMetric|null) {
      this.edited = metric
        ? {
          idmetric: metric.idmetric,
          name: this.decode(metric.name),
          formula: metric.formula,
          format: metric.format,
        }
        : {
          idmetric: null,
          name: '',
          formula: '',
          format: 'number',
        };
    },
    insert(id: string) {
      if (id && this.edited) {
        this.edited.formula = `${this.edited.formula} ${id}`.trim();
      }
    },
    onChanged() {
      this.edited = null;
      this.fetch();
      this.$emit('changed');
    },
    save() {
      const { edited } = this;
      if (!edited || this.isSaving) {
        return;
      }

      this.isSaving = true;

      const params: Record<string, unknown> = {
        method: edited.idmetric
          ? 'RebelMyReports.updateCalculatedMetric'
          : 'RebelMyReports.addCalculatedMetric',
      };
      if (edited.idmetric) {
        params.idMetric = edited.idmetric;
      }

      AjaxHelper.post(params, {
        name: edited.name,
        formula: edited.formula,
        metricFormat: edited.format,
      }).then(() => {
        this.onChanged();
      }).finally(() => {
        this.isSaving = false;
      });
    },
    duplicate(site: DestinationSite) {
      const metric = this.duplicated;
      if (!metric) {
        return;
      }

      this.isDuplicating = true;
      AjaxHelper.post({
        method: 'RebelMyReports.duplicateCalculatedMetric',
        idMetric: metric.idmetric,
      }, {
        idDestinationSite: site.id,
      }).then(() => {
        NotificationsStore.show({
          id: 'rebelMyReportsDuplicated',
          message: translate('RebelMyReports_DuplicatedTo', this.decode(metric.name), site.name),
          context: 'success',
          type: 'transient',
        });
        this.duplicated = null;
        this.onChanged();
      }).finally(() => {
        this.isDuplicating = false;
      });
    },
    confirmDelete(metric: CalculatedMetric) {
      this.metricToDeleteName = this.decode(metric.name);

      Matomo.helper.modalConfirm(this.$refs.confirm as HTMLElement, {
        yes: () => {
          AjaxHelper.post({
            method: 'RebelMyReports.deleteCalculatedMetric',
            idMetric: metric.idmetric,
          }).then(() => {
            this.onChanged();
          });
        },
      });
    },
  },
});
</script>
