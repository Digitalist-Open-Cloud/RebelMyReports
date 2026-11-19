<template>
  <div class="myReportKeyFigure">
    <div class="myReportKeyFigure__name">{{ metricName }}</div>
    <div class="myReportKeyFigure__headline">
      <span class="myReportKeyFigure__value">{{ value }}</span>
      <span class="myReportKeyFigure__badge" v-if="evolution">
        <EvolutionBadge
          :percent="evolution.percent"
          :trend="evolution.trend"
          :is-lower-value-better="evolution.isLowerValueBetter"
          :tooltip="evolution.tooltip"
        />
      </span>
    </div>

    <div
      class="myReportKeyFigure__comparison"
      v-for="(comparison, index) in comparisons"
      :key="index"
    >
      {{ comparison.label }}: <strong>{{ comparison.formatted }}</strong>
      <EvolutionBadge
        :percent="comparison.evolution.percent"
        :trend="comparison.evolution.trend"
        :is-lower-value-better="comparison.evolution.isLowerValueBetter"
        :tooltip="comparison.evolution.tooltip"
      />
    </div>

    <div class="myReportKeyFigure__chart" ref="chart" v-if="points.length > 1">
      <svg
        class="myReportKeyFigure__line"
        viewBox="0 0 100 100"
        preserveAspectRatio="none"
        aria-hidden="true"
      >
        <polyline :points="linePoints" />
      </svg>
      <span
        class="myReportKeyFigure__point"
        v-for="(point, index) in positions"
        :key="index"
        :style="{ left: `${point.x}%`, bottom: `${point.y}%` }"
        :title="`${point.label}: ${point.formatted}`"
      >
        <span
          class="myReportKeyFigure__dot"
          :class="{ 'myReportKeyFigure__dot--last': index === positions.length - 1 }"
        />
        <span class="myReportKeyFigure__label" v-if="point.showLabel">{{ point.formatted }}</span>
      </span>
    </div>

    <div class="myReportKeyFigure__export">
      <MyReportExport :id-my-report="idMyReport" :report-title="reportTitle" />
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, PropType } from 'vue';
import { EvolutionBadge } from 'CoreVisualizations';
import MyReportExport from '../MyReportExport/MyReportExport.vue';

interface Point {
  label: string;
  value: number;
  formatted: string;
}

interface Evolution {
  percent: string|number;
  trend: number;
  isLowerValueBetter: boolean;
  tooltip: string;
}

interface Comparison extends Point {
  evolution: Evolution;
}

interface Position extends Point {
  x: number;
  y: number;
  showLabel: boolean;
}

// Room one value label needs, in pixels.
const LABEL_WIDTH = 44;

export default defineComponent({
  props: {
    idMyReport: {
      type: Number,
      required: true,
    },
    reportTitle: {
      type: String,
      default: '',
    },
    metricName: {
      type: String,
      required: true,
    },
    value: {
      type: [String, Number],
      required: true,
    },
    points: {
      type: Array as PropType<Point[]>,
      default: () => [],
    },
    evolution: {
      type: Object as PropType<Evolution|null>,
      default: null,
    },
    comparisons: {
      type: Array as PropType<Comparison[]>,
      default: () => [],
    },
  },
  components: {
    EvolutionBadge,
    MyReportExport,
  },
  data() {
    return {
      chartWidth: 0,
      resizeObserver: null as ResizeObserver|null,
    };
  },
  mounted() {
    const chart = this.$refs.chart as HTMLElement|undefined;
    if (!chart) {
      return;
    }

    this.chartWidth = chart.clientWidth;
    if (typeof ResizeObserver !== 'undefined') {
      this.resizeObserver = new ResizeObserver(() => {
        this.chartWidth = chart.clientWidth;
      });
      this.resizeObserver.observe(chart);
    }
  },
  beforeUnmount() {
    if (this.resizeObserver) {
      this.resizeObserver.disconnect();
    }
  },
  computed: {
    // Every point has a label when there is room. Otherwise every nth one, counted from the
    // last point so that the current value is always labelled.
    labelStep(): number {
      if (!this.chartWidth) {
        return 1;
      }
      return Math.max(1, Math.ceil((this.points.length * LABEL_WIDTH) / this.chartWidth));
    },
    positions(): Position[] {
      const values = this.points.map((point) => point.value);
      const max = Math.max(...values, 0);
      const min = Math.min(...values, 0);
      const range = max - min || 1;
      const last = this.points.length - 1;

      return this.points.map((point, index) => ({
        ...point,
        x: last > 0 ? (index / last) * 100 : 50,
        y: ((point.value - min) / range) * 100,
        showLabel: (last - index) % this.labelStep === 0,
      }));
    },
    linePoints(): string {
      return this.positions.map((point) => `${point.x},${100 - point.y}`).join(' ');
    },
  },
});
</script>
