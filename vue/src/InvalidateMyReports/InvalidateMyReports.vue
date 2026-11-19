<template>
  <div class="invalidateMyReports">
    <ContentBlock :content-title="translate('RebelMyReports_InvalidateReports')">
      <p>{{ translate('RebelMyReports_InvalidateIntro') }}</p>

      <Field
        uicontrol="checkbox"
        name="allReports"
        v-model="allReports"
        :title="translate('RebelMyReports_InvalidateAllReports')"
      />
      <div v-show="!allReports">
        <Field
          uicontrol="checkbox"
          var-type="array"
          name="idMyReports"
          v-model="idMyReports"
          :title="translate('General_Reports')"
          :options="reportOptions"
        />
      </div>

      <Field
        uicontrol="select"
        name="period"
        v-model="period"
        :title="translate('General_Period')"
        :options="periodOptions"
        :inline-help="translate('RebelMyReports_InvalidatePeriodHelp')"
      />
      <div class="invalidateMyReports__dates">
        <label>
          {{ translate(period === 'range' ? 'General_DateRangeFrom' : 'General_Date') }}
          <input type="date" class="browser-default" name="date" v-model="date" />
        </label>
        <label v-if="period === 'range'">
          {{ translate('General_DateRangeTo') }}
          <input type="date" class="browser-default" name="endDate" v-model="endDate" />
        </label>
      </div>

      <Field
        uicontrol="select"
        name="segment"
        v-model="segment"
        :title="translate('General_Segment')"
        :options="segmentOptions"
      />

      <SaveButton
        :value="translate('RebelMyReports_InvalidateButton')"
        :saving="isSaving"
        :disabled="!canInvalidate"
        @confirm="invalidate()"
      />
    </ContentBlock>
  </div>
</template>

<script lang="ts">
import { defineComponent, PropType } from 'vue';
import {
  AjaxHelper,
  ContentBlock,
  Matomo,
  NotificationsStore,
  translate,
} from 'CoreHome';
import { Field, SaveButton } from 'CorePluginsAdmin';

interface InvalidatedReport {
  idreport: number;
  name: string;
  site: string;
  paused: boolean;
}

function today(): string {
  const now = new Date();
  const pad = (part: number) => String(part).padStart(2, '0');
  return `${now.getFullYear()}-${pad(now.getMonth() + 1)}-${pad(now.getDate())}`;
}

export default defineComponent({
  props: {
    reports: {
      type: Array as PropType<InvalidatedReport[]>,
      required: true,
    },
    segments: {
      type: Array as PropType<{ name: string, definition: string }[]>,
      default: () => [],
    },
  },
  components: {
    ContentBlock,
    Field,
    SaveButton,
  },
  data() {
    return {
      allReports: true,
      idMyReports: [] as number[],
      period: 'day',
      date: today(),
      endDate: today(),
      segment: '',
      isSaving: false,
    };
  },
  computed: {
    reportOptions(): { key: number, value: string }[] {
      return this.reports.filter((report) => !report.paused).map((report) => {
        const site = report.site || translate('RebelMyReports_AllWebsites');
        return {
          key: report.idreport,
          value: Matomo.helper.htmlDecode(`${report.name} (${site})`),
        };
      });
    },
    segmentOptions(): { key: string, value: string }[] {
      return [
        { key: '', value: translate('RebelMyReports_InvalidateAllSegments') },
        ...this.segments.map((segment) => ({
          key: segment.definition,
          value: Matomo.helper.htmlDecode(segment.name),
        })),
      ];
    },
    periodOptions(): Record<string, string> {
      return {
        day: translate('Intl_PeriodDay'),
        week: translate('Intl_PeriodWeek'),
        month: translate('Intl_PeriodMonth'),
        year: translate('Intl_PeriodYear'),
        range: translate('General_DateRangeInPeriodList'),
      };
    },
    canInvalidate(): boolean {
      const hasDates = !!this.date && (this.period !== 'range' || this.endDate >= this.date);
      return hasDates && (this.allReports || this.idMyReports.length > 0);
    },
  },
  methods: {
    invalidate() {
      this.isSaving = true;

      AjaxHelper.post<{ value: number }>({
        method: 'RebelMyReports.invalidateReports',
      }, {
        period: this.period,
        date: this.period === 'range' ? `${this.date},${this.endDate}` : this.date,
        idMyReports: this.allReports ? [] : this.idMyReports,
        segmentFilter: this.segment,
      }).then((response) => {
        NotificationsStore.show({
          id: 'rebelMyReportsInvalidated',
          message: translate('RebelMyReports_InvalidateDone', response.value),
          context: 'success',
          type: 'transient',
        });
      }).finally(() => {
        this.isSaving = false;
      });
    },
  },
});
</script>
