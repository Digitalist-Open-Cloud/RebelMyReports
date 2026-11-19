<template>
  <div class="myReportExport" data-report="RebelMyReports.getReportData" ref="root">
    <a
      class="myReportExport__link"
      href=""
      v-report-export="exportBinding"
      :title="translate('General_ExportThisReport')"
      @click.prevent
    ><span class="icon-export" /> {{ translate('General_Export') }}</a>
  </div>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import { Matomo, MatomoUrl, ReportExport } from 'CoreHome';

const { $ } = window;

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
  },
  directives: {
    ReportExport,
  },
  mounted() {
    // The export dialog reads the site, period and segment from the data table it belongs
    // to. A key figure has no data table, so it gets the same values from the page URL.
    $(this.$refs.root as HTMLElement).data('uiControlObject', {
      param: {
        idSite: Matomo.idSite,
        period: MatomoUrl.parsed.value.period,
        date: MatomoUrl.parsed.value.date,
        segment: MatomoUrl.parsed.value.segment || '',
        filter_limit: -1,
      },
      props: {},
    });
  },
  computed: {
    exportBinding() {
      return {
        reportTitle: Matomo.helper.htmlDecode(this.reportTitle),
        requestParams: { idMyReport: this.idMyReport },
        apiMethod: 'RebelMyReports.getReportData',
        reportFormats: {
          TSV: 'TSV (Excel)',
          HTML: 'HTML',
          JSON: 'JSON',
          XML: 'XML',
          CSV: 'CSV',
          RSS: 'RSS',
        },
        maxFilterLimit: 0,
        canExportFlat: false,
      };
    },
  },
});
</script>
