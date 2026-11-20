<template>
  <div class="manageMyReports">
    <MyReportsList
      v-if="!isEditing"
      :reports="reports"
      :dimensions="dimensions"
      :metrics="metrics"
      :archive-status="archiveStatus"
      :is-loading="isLoading"
      @create="edit(null)"
      @edit="edit($event)"
      @deleted="onChanged()"
    />
    <MyMetricsManager
      v-if="!isEditing && !isLoading"
      :metrics="metrics"
      @changed="fetchMetrics()"
    />
    <MyReportEditor
      v-if="isEditing"
      :report="editedReport"
      :dimensions="dimensions"
      :metrics="metrics"
      :categories="categories"
      :rearchive-months="rearchiveMonths"
      :max-dimensions="maxDimensions"
      @saved="onChanged()"
      @cancel="isEditing = false"
    />
  </div>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import { AjaxHelper, Matomo, ReportingMenuStore } from 'CoreHome';
import MyReportsList from '../MyReportsList/MyReportsList.vue';
import MyReportEditor from '../MyReportEditor/MyReportEditor.vue';
import MyMetricsManager from '../MyMetricsManager/MyMetricsManager.vue';
import {
  ArchiveStatus,
  CatalogueItem,
  Category,
  Report,
} from '../types';

interface ManageMyReportsState {
  isLoading: boolean;
  isEditing: boolean;
  editedReport: Report|null;
  reports: Report[];
  archiveStatus: ArchiveStatus[];
  dimensions: CatalogueItem[];
  metrics: CatalogueItem[];
  categories: Category[];
}

export default defineComponent({
  props: {
    rearchiveMonths: {
      type: Number,
      default: 0,
    },
    maxDimensions: {
      type: Number,
      default: 3,
    },
  },
  components: {
    MyReportsList,
    MyReportEditor,
    MyMetricsManager,
  },
  data(): ManageMyReportsState {
    return {
      isLoading: true,
      isEditing: false,
      editedReport: null,
      reports: [],
      archiveStatus: [],
      dimensions: [],
      metrics: [],
      categories: [],
    };
  },
  created() {
    Promise.all([
      this.fetchReports(),
      AjaxHelper.fetch<CatalogueItem[]>({
        method: 'RebelMyReports.getAvailableDimensions',
        filter_limit: '-1',
      }),
      this.fetchMetrics(),
      AjaxHelper.fetch<Category[]>({
        method: 'RebelMyReports.getAvailableCategories',
        filter_limit: '-1',
      }),
    ]).then(([, dimensions, , categories]) => {
      this.dimensions = dimensions;
      this.categories = categories;
    }).finally(() => {
      this.isLoading = false;
    });
  },
  methods: {
    fetchReports() {
      // Not waited for: it reads the archive tables, which can take longer than the list.
      AjaxHelper.fetch<ArchiveStatus[]>({
        method: 'RebelMyReports.getArchiveStatus',
        filter_limit: '-1',
      }).then((archiveStatus) => {
        this.archiveStatus = archiveStatus;
      });

      return AjaxHelper.fetch<Report[]>({
        method: 'RebelMyReports.getReports',
        filter_limit: '-1',
      }).then((reports) => {
        this.reports = reports;
      });
    },
    fetchMetrics() {
      return AjaxHelper.fetch<CatalogueItem[]>({
        method: 'RebelMyReports.getAvailableMetrics',
        filter_limit: '-1',
      }).then((metrics) => {
        this.metrics = metrics;
      });
    },
    edit(report: Report|null) {
      this.editedReport = report;
      this.isEditing = true;
      Matomo.helper.lazyScrollToContent();
    },
    onChanged() {
      this.isEditing = false;
      this.fetchReports();
      // The reports are pages of the reporting menu.
      ReportingMenuStore.reloadMenuItems();
    },
  },
});
</script>
