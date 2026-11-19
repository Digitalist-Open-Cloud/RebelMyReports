<template>
  <div class="myReportEditor">
    <ContentBlock :content-title="title">
      <form class="myReportEditor__layout" @submit.prevent="save()">
        <div class="myReportEditor__form">
          <div class="myReportEditor__field">
            <Field
              uicontrol="text"
              name="name"
              v-model="form.name"
              :maxlength="100"
              :full-width="true"
              :title="translate('General_Name')"
            />
          </div>
          <div class="myReportEditor__field">
            <Field
              uicontrol="textarea"
              name="description"
              v-model="form.description"
              :maxlength="1000"
              :full-width="true"
              :title="translate('General_Description')"
              :ui-control-attributes="{ class: 'compact-textarea' }"
            />
          </div>
          <div class="myReportEditor__field">
            <Field
              uicontrol="radio"
              name="reportType"
              v-model="form.reportType"
              :full-width="true"
              :title="translate('RebelMyReports_ReportType')"
              :options="reportTypes"
            />
          </div>

          <p class="myReportEditor__help" v-show="form.reportType === 'keyfigure'">
            {{ translate('RebelMyReports_KeyFigureHelp') }}
          </p>

          <div class="myReportEditor__picker" v-show="form.reportType === 'table'">
            <MyReportItemPicker
              name="dimensions"
              v-model="form.dimensions"
              :items="dimensions"
              :max="maxDimensions"
              :hide-site-specific="form.allWebsites"
              :title="translate('RebelMyReports_Dimensions')"
              :add-title="translate('RebelMyReports_AddDimension')"
              :limit-text="translate('RebelMyReports_MaxDimensionsReached', maxDimensions)"
            />
          </div>
          <div
            class="myReportEditor__valueGroups"
            v-show="form.reportType === 'table' && form.dimensions.length"
          >
            <h3 class="myReportEditor__sectionTitle">
              {{ translate('RebelMyReports_ValueGroups') }}
            </h3>
            <p class="myReportEditor__help">
              {{ translate('RebelMyReports_ValueGroupsHelp') }}
            </p>
            <div
              class="myReportEditor__valueGroup"
              v-for="(group, index) in form.valueGroups"
              :key="index"
            >
              <div class="myReportEditor__row">
                <Field
                  uicontrol="text"
                  :name="`groupName${index}`"
                  v-model="group.name"
                  :full-width="true"
                  :maxlength="100"
                  :title="translate('RebelMyReports_ValueGroupName')"
                />
                <Field
                  uicontrol="select"
                  :name="`groupDimension${index}`"
                  v-model="group.dimension"
                  :full-width="true"
                  :title="translate('RebelMyReports_ValueGroupDimension')"
                  :options="dimensionOptions"
                />
                <Field
                  uicontrol="select"
                  :name="`groupOperator${index}`"
                  v-model="group.operator"
                  :full-width="true"
                  :title="translate('RebelMyReports_ValueGroupOperator')"
                  :options="groupOperatorOptions"
                />
                <button
                  type="button"
                  class="myReportEditor__removeFilter btn-flat icon-delete"
                  :title="translate('General_Remove')"
                  :aria-label="translate('General_Remove')"
                  @click="form.valueGroups.splice(index, 1)"
                />
              </div>
              <div class="myReportEditor__groupValues">
                <span
                  class="myReportEditor__groupValue"
                  v-for="(value, valueIndex) in group.values"
                  :key="value"
                >
                  {{ valueLabel(group.dimension, value) }}
                  <button
                    type="button"
                    class="icon-close"
                    :title="translate('General_Remove')"
                    :aria-label="translate('General_Remove')"
                    @click="group.values.splice(valueIndex, 1)"
                  />
                </span>
                <input
                  type="text"
                  class="myReportEditor__groupValueInput browser-default"
                  :name="`groupValue${index}`"
                  v-model="group.draft"
                  maxlength="255"
                  :list="`myReportValues_${group.dimension}`"
                  :placeholder="translate('RebelMyReports_ValueGroupAddValue')"
                  :aria-label="translate('RebelMyReports_ValueGroupAddValue')"
                  @keydown.enter.prevent="addGroupValue(group)"
                  @change="addGroupValue(group)"
                  @input="onGroupValueInput(group, $event)"
                />
              </div>
            </div>
            <datalist
              v-for="(values, dimension) in dimensionValues"
              :key="dimension"
              :id="`myReportValues_${dimension}`"
            >
              <option
                v-for="item in values"
                :key="item.value"
                :value="item.value"
              >{{ item.label }}</option>
            </datalist>
            <button
              type="button"
              class="btn-flat myReportEditor__addValueGroup"
              @click="addValueGroup()"
            >
              <span class="icon-add" /> {{ translate('RebelMyReports_AddValueGroup') }}
            </button>
          </div>

          <div class="myReportEditor__picker">
            <MyReportItemPicker
              name="metrics"
              v-model="form.metrics"
              :items="metrics"
              :max="form.reportType === 'keyfigure' ? 1 : 0"
              :hide-site-specific="form.allWebsites"
              :title="translate('General_Metrics')"
              :add-title="translate('RebelMyReports_AddMetric')"
              :limit-text="translate('RebelMyReports_MaxMetricsReached')"
            />
          </div>

          <h3 class="myReportEditor__sectionTitle">
            {{ translate('RebelMyReports_SegmentFilter') }}
          </h3>
          <p class="myReportEditor__help">
            {{ translate('RebelMyReports_SegmentFilterHelp') }}
          </p>
          <div class="myReportEditor__segment">
            <SegmentGenerator v-model="form.segmentFilter" />
          </div>

          <div v-show="form.reportType === 'table'">
            <h3 class="myReportEditor__sectionTitle">
              {{ translate('RebelMyReports_Rows') }}
            </h3>
            <div class="myReportEditor__field">
              <Field
                uicontrol="select"
                name="visualization"
                v-model="form.visualization"
                :full-width="true"
                :title="translate('RebelMyReports_Visualization')"
                :inline-help="translate('RebelMyReports_VisualizationHelp')"
                :options="visualizationOptions"
              />
            </div>
            <div class="myReportEditor__row">
              <Field
                uicontrol="select"
                name="sortMetric"
                v-model="form.sortMetric"
                :full-width="true"
                :title="translate('RebelMyReports_SortBy')"
                :options="sortMetricOptions"
              />
              <Field
                uicontrol="select"
                name="sortOrder"
                v-model="form.sortOrder"
                :full-width="true"
                :title="translate('RebelMyReports_SortOrder')"
                :options="sortOrderOptions"
              />
            </div>
            <div class="myReportEditor__field">
              <Field
                uicontrol="number"
                name="rowLimit"
                v-model="form.rowLimit"
                :min="0"
                :full-width="true"
                :title="translate('RebelMyReports_RowLimit')"
                :inline-help="translate('RebelMyReports_RowLimitHelp')"
              />
            </div>
            <div
              class="myReportEditor__row"
              v-for="(filter, index) in form.metricFilters"
              :key="index"
            >
              <Field
                uicontrol="select"
                :name="`filterMetric${index}`"
                v-model="filter.metric"
                :full-width="true"
                :title="translate('RebelMyReports_OnlyRowsWhere')"
                :options="metricOptions"
              />
              <Field
                uicontrol="select"
                :name="`filterOperator${index}`"
                v-model="filter.operator"
                :full-width="true"
                :title="translate('RebelMyReports_FilterOperator')"
                :options="filterOperatorOptions"
              />
              <Field
                uicontrol="number"
                :name="`filterValue${index}`"
                v-model="filter.value"
                :full-width="true"
                :title="translate('General_Value')"
              />
              <button
                type="button"
                class="myReportEditor__removeFilter btn-flat icon-delete"
                :title="translate('General_Remove')"
                :aria-label="translate('General_Remove')"
                @click="form.metricFilters.splice(index, 1)"
              />
            </div>
            <button
              type="button"
              class="btn-flat myReportEditor__addFilter"
              @click="addFilter()"
            >
              <span class="icon-add" /> {{ translate('RebelMyReports_AddFilter') }}
            </button>
            <p class="myReportEditor__help">
              {{ translate('RebelMyReports_RowsHelp') }}
            </p>
          </div>

          <h3 class="myReportEditor__sectionTitle">
            {{ translate('RebelMyReports_WhereToShow') }}
          </h3>
          <div class="myReportEditor__field">
            <Field
              uicontrol="select"
              name="category"
              v-model="form.category"
              :full-width="true"
              :title="translate('RebelMyReports_ReportingCategory')"
              :options="categoryOptions"
            />
          </div>
          <div class="myReportEditor__field">
            <Field
              uicontrol="select"
              name="subcategory"
              v-model="form.subcategory"
              :full-width="true"
              :title="translate('RebelMyReports_ReportPage')"
              :inline-help="translate('RebelMyReports_ReportPageHelp')"
              :options="pageOptions"
            />
          </div>
          <div class="myReportEditor__field" v-if="isSuperUser && !report">
            <Field
              uicontrol="checkbox"
              name="allWebsites"
              v-model="form.allWebsites"
              :full-width="true"
              :title="translate('RebelMyReports_AvailableForAllWebsites')"
              :inline-help="translate('RebelMyReports_AvailableForAllWebsitesHelp')"
            />
          </div>

          <div class="myReportEditor__warning" v-if="changesData && rearchiveMonths > 0">
            <Notification context="warning" :noclear="true">
              {{ translate('RebelMyReports_UpdateWarning', rearchiveMonths) }}
            </Notification>
          </div>

          <div class="myReportEditor__actions">
            <div class="myReportEditor__save">
              <SaveButton
                :saving="isSaving"
                :disabled="!canSave"
                :value="translate('General_Save')"
                @confirm="save()"
              />
            </div>
            <button type="button" class="myReportEditor__cancel" @click="$emit('cancel')">
              {{ translate('General_Cancel') }}
            </button>
          </div>
        </div>

        <div class="myReportEditor__preview">
          <MyReportPreview
            :dimensions="selectedDimensions"
            :metrics="form.metrics"
            :segment-filter="form.segmentFilter"
            :row-settings="rowSettings"
            :visualization="form.reportType === 'table' ? form.visualization : 'table'"
            :available-dimensions="dimensions"
            :available-metrics="metrics"
          />
        </div>
      </form>
    </ContentBlock>
  </div>
</template>

<script lang="ts">
import { defineComponent, PropType } from 'vue';
import {
  AjaxHelper,
  ContentBlock,
  Matomo,
  Notification,
  NotificationsStore,
  translate,
} from 'CoreHome';
import { Field, SaveButton } from 'CorePluginsAdmin';
import { SegmentGenerator } from 'SegmentEditor';
import MyReportItemPicker from '../MyReportItemPicker/MyReportItemPicker.vue';
import MyReportPreview from '../MyReportPreview/MyReportPreview.vue';
import {
  CatalogueItem,
  Category,
  Report,
  ValueGroup,
} from '../types';

// The draft is a value that is typed but not added to the group yet.
interface ValueGroupForm extends ValueGroup {
  draft: string;
}

// Names and values are stored HTML-encoded, like everything Matomo tracks.
function decodeGroups(groups: ValueGroup[]): ValueGroupForm[] {
  return groups.map((group) => ({
    dimension: group.dimension,
    name: Matomo.helper.htmlDecode(group.name),
    operator: group.operator,
    values: group.values.map((value) => Matomo.helper.htmlDecode(value)),
    draft: '',
  }));
}

interface ReportForm {
  name: string;
  description: string;
  reportType: string;
  dimensions: string[];
  metrics: string[];
  segmentFilter: string;
  category: string;
  subcategory: string;
  allWebsites: boolean;
  sortMetric: string;
  sortOrder: string;
  rowLimit: number|string;
  visualization: string;
  metricFilters: { metric: string, operator: string, value: number|string }[];
  valueGroups: ValueGroupForm[];
}

interface DimensionValue {
  value: string;
  label: string;
}

interface MyReportEditorState {
  isSaving: boolean;
  dimensionValues: Record<string, DimensionValue[]>;
  form: ReportForm;
}

const NOTIFICATION_ID = 'rebelMyReportsSaved';

export default defineComponent({
  props: {
    report: {
      type: Object as PropType<Report|null>,
      default: null,
    },
    dimensions: {
      type: Array as PropType<CatalogueItem[]>,
      required: true,
    },
    metrics: {
      type: Array as PropType<CatalogueItem[]>,
      required: true,
    },
    categories: {
      type: Array as PropType<Category[]>,
      required: true,
    },
    rearchiveMonths: {
      type: Number,
      default: 0,
    },
    maxDimensions: {
      type: Number,
      default: 3,
    },
  },
  emits: ['saved', 'cancel'],
  components: {
    ContentBlock,
    Field,
    Notification,
    SaveButton,
    SegmentGenerator,
    MyReportItemPicker,
    MyReportPreview,
  },
  data(): MyReportEditorState {
    const { report } = this;

    return {
      isSaving: false,
      dimensionValues: {},
      form: {
        name: report ? Matomo.helper.htmlDecode(report.name) : '',
        description: report ? Matomo.helper.htmlDecode(report.description) : '',
        reportType: report ? report.report_type : 'table',
        dimensions: report ? [...report.dimensions] : [],
        metrics: report ? [...report.metrics] : ['nb_visits'],
        segmentFilter: report ? report.segment_filter : '',
        category: report ? report.category : '',
        subcategory: report ? report.subcategory : '',
        allWebsites: report ? report.idsite === 0 : false,
        sortMetric: report ? report.sort_metric : '',
        sortOrder: report ? report.sort_order : 'desc',
        rowLimit: report ? report.row_limit : 0,
        visualization: report ? report.visualization : 'table',
        metricFilters: report ? report.metric_filters.map((filter) => ({ ...filter })) : [],
        valueGroups: report ? decodeGroups(report.value_groups) : [],
      },
    };
  },
  computed: {
    title(): string {
      return this.report
        ? translate('RebelMyReports_EditReport', Matomo.helper.htmlDecode(this.report.name))
        : translate('RebelMyReports_CreateNewReport');
    },
    isSuperUser(): boolean {
      return Matomo.hasSuperUserAccess;
    },
    reportTypes(): Record<string, string> {
      return {
        table: translate('RebelMyReports_TypeTable'),
        evolution: translate('RebelMyReports_TypeEvolution'),
        keyfigure: translate('RebelMyReports_TypeKeyFigure'),
      };
    },
    pageOptions(): { key: string, value: string }[] {
      const categoryId = this.form.category || 'RebelMyReports_MyReports';
      const category = this.categories.find((c) => c.id === categoryId);

      return [
        { key: '', value: translate('RebelMyReports_NewPageForReport') },
        ...(category?.pages || []).map((page) => ({
          key: page.id,
          value: Matomo.helper.htmlDecode(page.name),
        })),
      ];
    },
    categoryOptions(): { key: string, value: string }[] {
      return [
        { key: '', value: translate('RebelMyReports_MyReports') },
        ...this.categories.map((category) => ({ key: category.id, value: category.name })),
      ];
    },
    metricOptions(): { key: string, value: string }[] {
      return this.form.metrics.map((id) => ({
        key: id,
        value: this.metrics.find((metric) => metric.id === id)?.name || id,
      }));
    },
    rowSettings(): Record<string, unknown> {
      if (this.form.reportType !== 'table') {
        return {};
      }

      return {
        sortMetric: this.form.sortMetric,
        sortOrder: this.form.sortOrder,
        rowLimit: Number(this.form.rowLimit) || 0,
        metricFilters: this.form.metricFilters.map((filter) => ({
          ...filter,
          value: Number(filter.value) || 0,
        })),
        valueGroups: this.completeValueGroups,
      };
    },
    // A group without value or name is still being filled in.
    completeValueGroups(): ValueGroup[] {
      return this.form.valueGroups.map((group) => {
        const draft = group.draft.trim();
        return {
          dimension: group.dimension,
          name: group.name.trim(),
          operator: group.operator,
          values: draft && !group.values.includes(draft) ? [...group.values, draft] : group.values,
        };
      }).filter((group) => group.name && group.values.length);
    },
    dimensionOptions(): { key: string, value: string }[] {
      return this.form.dimensions.map((id) => ({
        key: id,
        value: this.dimensions.find((dimension) => dimension.id === id)?.name || id,
      }));
    },
    visualizationOptions(): Record<string, string> {
      return {
        table: translate('General_Table'),
        graphVerticalBar: translate('General_VBarGraph'),
        graphPie: translate('General_Piechart'),
        cloud: translate('General_TagCloud'),
      };
    },
    groupOperatorOptions(): Record<string, string> {
      return {
        equals: translate('RebelMyReports_ValueGroupEquals'),
        contains: translate('RebelMyReports_ValueGroupContains'),
        starts: translate('RebelMyReports_ValueGroupStarts'),
        ends: translate('RebelMyReports_ValueGroupEnds'),
      };
    },
    sortMetricOptions(): { key: string, value: string }[] {
      return [{ key: '', value: translate('RebelMyReports_FirstMetric') }, ...this.metricOptions];
    },
    sortOrderOptions(): Record<string, string> {
      return {
        desc: translate('RebelMyReports_SortDescending'),
        asc: translate('RebelMyReports_SortAscending'),
      };
    },
    filterOperatorOptions(): Record<string, string> {
      return {
        gt: translate('RebelMyReports_OperatorGreaterThan'),
        gte: translate('RebelMyReports_OperatorAtLeast'),
        lt: translate('RebelMyReports_OperatorLessThan'),
        lte: translate('RebelMyReports_OperatorAtMost'),
        eq: translate('RebelMyReports_OperatorEquals'),
      };
    },
    selectedDimensions(): string[] {
      return this.form.reportType === 'table' ? this.form.dimensions : [];
    },
    changesData(): boolean {
      // Name, description, type and page only change how the archived data is shown.
      const { report } = this;
      return !!report && (
        JSON.stringify(this.selectedDimensions) !== JSON.stringify(report.dimensions)
        || JSON.stringify(this.rowSettings.valueGroups || [])
          !== JSON.stringify(decodeGroups(report.value_groups).map(({ draft, ...group }) => group))
        || JSON.stringify(this.form.metrics) !== JSON.stringify(report.metrics)
        || this.form.segmentFilter !== report.segment_filter
      );
    },
    canSave(): boolean {
      const hasDimension = this.form.reportType !== 'table' || this.form.dimensions.length > 0;
      const hasCompleteGroups = this.form.reportType !== 'table'
        || this.completeValueGroups.length === this.form.valueGroups.length;
      return !!this.form.name.trim() && this.form.metrics.length > 0 && hasDimension
        && hasCompleteGroups;
    },
  },
  watch: {
    'form.reportType': function onTypeChange(type: string) {
      if (type === 'keyfigure') {
        this.form.metrics = this.form.metrics.slice(0, 1);
      }
    },
    'form.dimensions': function onDimensionsChange(dimensions: string[]) {
      this.form.valueGroups = this.form.valueGroups.filter(
        (group) => dimensions.includes(group.dimension),
      );
    },
    'form.valueGroups': {
      deep: true,
      immediate: true,
      handler: function onValueGroupsChange(groups: ValueGroupForm[]) {
        groups.forEach((group) => this.fetchDimensionValues(group.dimension));
      },
    },
    'form.metrics': function onMetricsChange(metrics: string[]) {
      if (!metrics.includes(this.form.sortMetric)) {
        this.form.sortMetric = '';
      }
      this.form.metricFilters = this.form.metricFilters.filter(
        (filter) => metrics.includes(filter.metric),
      );
    },
    'form.category': function onCategoryChange() {
      if (!this.pageOptions.some((page) => page.key === this.form.subcategory)) {
        this.form.subcategory = '';
      }
    },
  },
  methods: {
    addValueGroup() {
      this.form.valueGroups.push({
        dimension: this.form.dimensions[0],
        operator: 'equals',
        values: [],
        name: '',
        draft: '',
      });
    },
    addGroupValue(group: ValueGroupForm) {
      const value = group.draft.trim();
      if (value && !group.values.includes(value)) {
        group.values.push(value);
      }
      group.draft = '';
    },
    // Choosing a suggestion adds it right away. Typing does not, also when it equals one.
    onGroupValueInput(group: ValueGroupForm, event: Event) {
      const { inputType } = event as InputEvent;
      if (!inputType || inputType === 'insertReplacementText') {
        this.addGroupValue(group);
      }
    },
    valueLabel(dimension: string, value: string): string {
      const known = (this.dimensionValues[dimension] || []).find((item) => item.value === value);
      return known && known.label !== value ? `${known.label} (${value})` : value;
    },
    // The values to suggest. Without them the value can still be typed.
    fetchDimensionValues(dimension: string) {
      if (this.dimensionValues[dimension]) {
        return;
      }

      this.dimensionValues[dimension] = [];
      AjaxHelper.fetch<DimensionValue[]>({
        method: 'RebelMyReports.getDimensionValues',
        dimension,
        metric: this.form.metrics[0] || 'nb_visits',
        filter_limit: '-1',
      }, { createErrorNotification: false }).then((values) => {
        this.dimensionValues[dimension] = values.map((item) => ({
          value: Matomo.helper.htmlDecode(item.value),
          label: Matomo.helper.htmlDecode(item.label),
        }));
      }).catch(() => {
        // No suggestions.
      });
    },
    addFilter() {
      this.form.metricFilters.push({ metric: this.form.metrics[0], operator: 'gt', value: 0 });
    },
    save() {
      if (!this.canSave || this.isSaving) {
        return;
      }

      this.isSaving = true;

      const params: Record<string, unknown> = {
        method: this.report ? 'RebelMyReports.updateReport' : 'RebelMyReports.addReport',
      };
      if (this.report) {
        params.idMyReport = this.report.idreport;
      }

      AjaxHelper.post(params, {
        name: this.form.name,
        description: this.form.description,
        reportType: this.form.reportType,
        dimensions: this.selectedDimensions,
        metrics: this.form.metrics,
        segmentFilter: this.form.segmentFilter,
        category: this.form.category,
        subcategory: this.form.subcategory,
        allWebsites: this.form.allWebsites ? 1 : 0,
        sortMetric: '',
        sortOrder: 'desc',
        rowLimit: 0,
        metricFilters: [],
        valueGroups: [],
        visualization: this.form.visualization,
        ...this.rowSettings,
      }).then(() => {
        NotificationsStore.show({
          id: NOTIFICATION_ID,
          message: translate('RebelMyReports_ReportSaved'),
          context: 'success',
          type: 'transient',
        });
        this.$emit('saved');
      }).finally(() => {
        this.isSaving = false;
      });
    },
  },
});
</script>
