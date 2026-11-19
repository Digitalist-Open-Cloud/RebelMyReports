<template>
  <div class="myReportPreview">
    <h3 class="myReportPreview__title">{{ translate('RebelMyReports_Preview') }}</h3>
    <p class="myReportPreview__hint">{{ translate('RebelMyReports_PreviewHint') }}</p>

    <p class="myReportPreview__message" v-if="!metrics.length">
      {{ translate('RebelMyReports_PreviewNeedsMetric') }}
    </p>
    <p class="myReportPreview__message" v-else-if="isLoading">
      <span class="loadingPiwik">
        <MatomoLoader />
        {{ translate('General_LoadingData') }}
      </span>
    </p>
    <p class="myReportPreview__message myReportPreview__message--error" v-else-if="error">
      {{ error }}
    </p>
    <p class="myReportPreview__message" v-else-if="!rows.length">
      {{ translate('CoreHome_ThereIsNoDataForThisReport') }}
    </p>
    <p
      class="myReportPreview__message myReportPreview__message--error"
      v-if="slowSeconds && !isLoading"
    >
      {{ translate('RebelMyReports_PreviewSlow', slowSeconds) }}
    </p>
    <template v-if="showsRows && visualization !== 'table'">
      <p class="myReportPreview__hint">{{ metricNames[metrics[0]] || metrics[0] }}</p>

      <div class="myReportPreview__bars" v-if="visualization === 'graphVerticalBar'">
        <div
          class="myReportPreview__bar"
          v-for="(item, index) in graphItems"
          :key="index"
          :title="`${item.label}: ${item.formatted}`"
        >
          <span class="myReportPreview__barValue">{{ item.formatted }}</span>
          <span class="myReportPreview__barFill" :style="{ height: `${item.share * 100}%` }" />
          <span class="myReportPreview__barLabel">{{ item.label }}</span>
        </div>
      </div>

      <div class="myReportPreview__pie" v-else-if="visualization === 'graphPie'">
        <span class="myReportPreview__pieChart" :style="{ background: pieBackground }" />
        <ul class="myReportPreview__legend">
          <li v-for="(item, index) in graphItems" :key="index">
            <span class="myReportPreview__dot" :style="{ background: item.color }" />
            {{ item.label }}: {{ item.formatted }}
          </li>
        </ul>
      </div>

      <div class="myReportPreview__cloud" v-else>
        <span
          v-for="(item, index) in graphItems"
          :key="index"
          :style="{ fontSize: `${12 + item.share * 18}px` }"
          :title="item.formatted"
        >{{ item.label }}</span>
      </div>
    </template>
    <div class="myReportPreview__scroll" v-else-if="showsRows">
      <table class="myReportPreview__table" v-content-table>
        <thead>
          <tr>
            <th v-if="dimensions.length">{{ labelTitle }}</th>
            <th v-for="id in metrics" :key="id">{{ metricNames[id] || id }}</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(row, index) in rows" :key="index">
            <td v-if="dimensions.length">{{ row.label }}</td>
            <td v-for="id in metrics" :key="id">{{ row[id] ?? 0 }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, PropType } from 'vue';
import {
  AjaxHelper,
  ContentTable,
  MatomoLoader,
} from 'CoreHome';
import { CatalogueItem } from '../types';

type PreviewRow = Record<string, string|number>;

interface GraphItem {
  label: string;
  formatted: string|number;
  value: number;
  // The value as a share of the highest one.
  share: number;
  color: string;
}

const GRAPH_COLORS = [
  '#3450a3', '#43a047', '#f4a261', '#d4291f', '#8e6bbf',
  '#2a9d8f', '#e9c46a', '#6c757d', '#e76f51', '#58a6d6',
];

// A preview of one day that takes this long tells of a report that is heavy to archive.
const SLOW_SECONDS = 5;

interface MyReportPreviewState {
  isLoading: boolean;
  error: string;
  rows: PreviewRow[];
  requestId: number;
  slowSeconds: number;
  timer: number;
}

export default defineComponent({
  props: {
    dimensions: {
      type: Array as PropType<string[]>,
      required: true,
    },
    metrics: {
      type: Array as PropType<string[]>,
      required: true,
    },
    segmentFilter: {
      type: String,
      default: '',
    },
    rowSettings: {
      type: Object as PropType<Record<string, unknown>>,
      default: () => ({}),
    },
    visualization: {
      type: String,
      default: 'table',
    },
    availableDimensions: {
      type: Array as PropType<CatalogueItem[]>,
      required: true,
    },
    availableMetrics: {
      type: Array as PropType<CatalogueItem[]>,
      required: true,
    },
  },
  components: {
    MatomoLoader,
  },
  directives: {
    ContentTable,
  },
  data(): MyReportPreviewState {
    return {
      isLoading: false,
      error: '',
      rows: [],
      requestId: 0,
      slowSeconds: 0,
      timer: 0,
    };
  },
  created() {
    this.$watch(
      () => [this.dimensions, this.metrics, this.segmentFilter, this.rowSettings],
      () => {
        this.isLoading = this.metrics.length > 0;
        window.clearTimeout(this.timer);
        this.timer = window.setTimeout(() => this.fetch(), 600);
      },
      { deep: true, immediate: true },
    );
  },
  computed: {
    showsRows(): boolean {
      return this.metrics.length > 0 && !this.isLoading && !this.error && this.rows.length > 0;
    },
    // Like the graphs of Matomo, the preview of a graph shows the first metric.
    graphItems(): GraphItem[] {
      const values = this.rows.map((row) => Math.max(0, Number(row.graph_value) || 0));
      const highest = Math.max(...values, 0) || 1;

      return this.rows.map((row, index) => ({
        label: `${row.label}`,
        formatted: row[this.metrics[0]] ?? 0,
        value: values[index],
        share: values[index] / highest,
        color: GRAPH_COLORS[index % GRAPH_COLORS.length],
      }));
    },
    pieBackground(): string {
      const total = this.graphItems.reduce((sum, item) => sum + item.value, 0) || 1;
      let start = 0;
      const slices = this.graphItems.map((item) => {
        const end = start + (item.value / total) * 100;
        const slice = `${item.color} ${start}% ${end}%`;
        start = end;
        return slice;
      });

      return `conic-gradient(${slices.join(', ')})`;
    },
    metricNames(): Record<string, string> {
      return Object.fromEntries(this.availableMetrics.map((item) => [item.id, item.name]));
    },
    labelTitle(): string {
      const names = Object.fromEntries(this.availableDimensions.map((d) => [d.id, d.name]));
      return this.dimensions.map((id) => names[id] || id).join(' - ');
    },
  },
  methods: {
    fetch() {
      if (!this.metrics.length) {
        return;
      }

      this.requestId += 1;
      const { requestId } = this;
      const started = Date.now();
      this.slowSeconds = 0;

      AjaxHelper.fetch<PreviewRow[]|PreviewRow>(
        {
          method: 'RebelMyReports.previewReport',
          metrics: this.metrics,
          dimensions: this.dimensions,
          segmentFilter: this.segmentFilter,
          ...this.rowSettings,
          format_metrics: 1,
        },
        { createErrorNotification: false },
      ).then((response) => {
        if (requestId === this.requestId) {
          this.rows = Array.isArray(response) ? response : [response];
          this.error = '';
          const seconds = Math.round((Date.now() - started) / 1000);
          this.slowSeconds = seconds >= SLOW_SECONDS ? seconds : 0;
        }
      }).catch((error) => {
        if (requestId === this.requestId) {
          this.rows = [];
          this.error = error?.message || `${error}`;
        }
      }).finally(() => {
        if (requestId === this.requestId) {
          this.isLoading = false;
        }
      });
    },
  },
});
</script>
