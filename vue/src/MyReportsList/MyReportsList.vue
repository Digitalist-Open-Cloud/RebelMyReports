<template>
  <div class="myReportsList">
    <ContentBlock>
      <p>
        {{ translate('RebelMyReports_ManageReportsIntro') }}
        <a :href="documentationUrl">{{ translate('RebelMyReports_Documentation') }}</a>
      </p>

      <p v-show="isLoading">
        <span class="loadingPiwik">
          <MatomoLoader />
          {{ translate('General_LoadingData') }}
        </span>
      </p>

      <input
        type="search"
        class="myReportsList__search browser-default"
        v-model="search"
        v-show="!isLoading && reports.length > 1"
        :placeholder="translate('General_Search')"
        :aria-label="translate('General_Search')"
      />

      <table v-content-table v-show="!isLoading">
        <thead>
          <tr>
            <th></th>
            <th>{{ translate('General_Name') }}</th>
            <th>{{ translate('RebelMyReports_Dimensions') }}</th>
            <th>{{ translate('General_Metrics') }}</th>
            <th>{{ translate('RebelMyReports_AvailableFor') }}</th>
            <th>{{ translate('RebelMyReports_ArchivedData') }}</th>
            <th>{{ translate('RebelMyReports_Created') }}</th>
            <th>{{ translate('RebelMyReports_Updated') }}</th>
            <th>{{ translate('General_Actions') }}</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="!visibleReports.length">
            <td colspan="9">
              {{ translate(reports.length
                ? 'General_SearchNoResults'
                : 'RebelMyReports_NoReportsYet') }}
            </td>
          </tr>
          <tr
            v-for="report in visibleReports"
            :key="report.idreport"
            :draggable="canManage(report)"
            @dragstart="dragged = report"
            @dragover.prevent
            @drop.prevent="move(dragged, report)"
          >
            <td class="myReportsList__handleCell">
              <button
                type="button"
                class="table-action icon-menu-hamburger myReportsList__handle"
                :title="translate('RebelMyReports_Reorder')"
                :disabled="!canManage(report)"
                @keydown.up.prevent="moveBy(report, -1)"
                @keydown.down.prevent="moveBy(report, 1)"
              />
            </td>
            <td class="myReportsList__nameCell">
              <a
                class="myReportsList__name"
                :href="reportUrl(report)"
                :title="translate('RebelMyReports_ViewReport')"
              >{{ decode(report.name) }}</a>
              <span class="myReportsList__chip" v-if="report.paused">
                {{ translate('RebelMyReports_Paused') }}
              </span>
              <span class="myReportsList__description" v-if="report.description">
                {{ decode(report.description) }}
              </span>
            </td>
            <td>
              <span
                class="myReportsList__chip"
                v-for="id in report.dimensions"
                :key="id"
              >{{ dimensionNames[id] || id }}</span>
              <span class="myReportsList__none" v-if="!report.dimensions.length">
                {{ report.report_type === 'keyfigure'
                  ? translate('RebelMyReports_TypeKeyFigure')
                  : translate('RebelMyReports_TypeEvolution') }}
              </span>
            </td>
            <td>
              <span
                class="myReportsList__chip myReportsList__chip--metric"
                v-for="id in report.metrics"
                :key="id"
              >{{ metricNames[id] || id }}</span>
            </td>
            <td>
              {{ report.idsite === 0
                ? translate('RebelMyReports_AllWebsites')
                : translate('RebelMyReports_ThisWebsite') }}
            </td>
            <td>
              <template v-if="statusById[report.idreport]">
                <span class="myReportsList__date" v-if="statusById[report.idreport].first_date">
                  {{ statusById[report.idreport].first_date }}
                  – {{ statusById[report.idreport].last_date }}
                </span>
                <span class="myReportsList__none" v-else>
                  {{ translate('RebelMyReports_NoArchivedData') }}
                </span>
                <span
                  class="myReportsList__description"
                  v-if="statusById[report.idreport].is_queued"
                >{{ translate('RebelMyReports_WaitingToBeArchived') }}</span>
                <span
                  class="myReportsList__problem"
                  v-if="statusById[report.idreport].problem"
                >{{ problemText(statusById[report.idreport]) }}</span>
              </template>
            </td>
            <td>
              <span class="myReportsList__date">{{ report.created_date.slice(0, 10) }}</span>
              <span class="myReportsList__description">{{ report.created_by }}</span>
            </td>
            <td>
              <template v-if="report.updated_date !== report.created_date">
                <span class="myReportsList__date">{{ report.updated_date.slice(0, 10) }}</span>
                <span class="myReportsList__description">{{ report.updated_by }}</span>
              </template>
            </td>
            <td class="myReportsList__actions">
              <button
                type="button"
                class="table-action"
                :class="report.paused ? 'icon-play' : 'icon-pause'"
                :title="translate(report.paused ? 'RebelMyReports_Resume' : 'RebelMyReports_Pause')"
                :disabled="!canManage(report)"
                @click="setPaused(report, !report.paused)"
              />
              <button
                type="button"
                class="table-action icon-edit"
                :title="translate('General_Edit')"
                :disabled="!canManage(report)"
                @click="$emit('edit', report)"
              />
              <button
                type="button"
                class="table-action icon-content-copy"
                :title="translate('RebelMyReports_Duplicate')"
                @click="duplicated = report"
              />
              <button
                type="button"
                class="table-action icon-delete"
                :title="translate('General_Delete')"
                :disabled="!canManage(report)"
                @click="confirmDelete(report)"
              />
            </td>
          </tr>
        </tbody>
      </table>

      <div class="myReportsList__duplicate" v-if="duplicated">
        <MyDuplicateForm
          :key="duplicated.idreport"
          :item-name="decode(duplicated.name)"
          :is-busy="isDuplicating"
          @confirm="duplicate($event)"
          @cancel="duplicated = null"
        />
      </div>

      <div class="tableActionBar">
        <button type="button" class="btn" :disabled="isLoading" @click="$emit('create')">
          <span class="icon-add" /> {{ translate('RebelMyReports_CreateNewReport') }}
        </button>
      </div>
    </ContentBlock>

    <div class="ui-confirm" ref="confirm">
      <h2>{{ translate('RebelMyReports_DeleteReportConfirm', reportToDeleteName) }}</h2>
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
  MatomoLoader,
  MatomoUrl,
  NotificationsStore,
  translate,
} from 'CoreHome';
import MyDuplicateForm, { DestinationSite } from '../MyDuplicateForm/MyDuplicateForm.vue';
import { ArchiveStatus, CatalogueItem, Report } from '../types';

function namesById(items: CatalogueItem[]): Record<string, string> {
  return Object.fromEntries(items.map((item) => [item.id, item.name]));
}

export default defineComponent({
  props: {
    reports: {
      type: Array as PropType<Report[]>,
      required: true,
    },
    dimensions: {
      type: Array as PropType<CatalogueItem[]>,
      required: true,
    },
    metrics: {
      type: Array as PropType<CatalogueItem[]>,
      required: true,
    },
    archiveStatus: {
      type: Array as PropType<ArchiveStatus[]>,
      required: true,
    },
    isLoading: Boolean,
  },
  emits: ['create', 'edit', 'deleted'],
  components: {
    ContentBlock,
    MatomoLoader,
    MyDuplicateForm,
  },
  directives: {
    ContentTable,
  },
  data() {
    return {
      reportToDeleteName: '',
      duplicated: null as Report|null,
      dragged: null as Report|null,
      isDuplicating: false,
      search: '',
    };
  },
  computed: {
    documentationUrl(): string {
      return `?${MatomoUrl.stringify({ module: 'RebelMyReports', action: 'documentation' })}`;
    },
    dimensionNames(): Record<string, string> {
      return namesById(this.dimensions);
    },
    metricNames(): Record<string, string> {
      return namesById(this.metrics);
    },
    visibleReports(): Report[] {
      const words = this.search.toLowerCase().split(/\s+/).filter((word) => word);
      return this.reports.filter((report) => {
        const text = [
          this.decode(report.name),
          this.decode(report.description),
          ...report.dimensions.map((id) => this.dimensionNames[id] || id),
          ...report.metrics.map((id) => this.metricNames[id] || id),
        ].join(' ').toLowerCase();
        return words.every((word) => text.includes(word));
      });
    },
    statusById(): Record<number, ArchiveStatus> {
      return Object.fromEntries(this.archiveStatus.map((status) => [status.idreport, status]));
    },
  },
  methods: {
    decode(text: string): string {
      return Matomo.helper.htmlDecode(text);
    },
    canManage(report: Report): boolean {
      return report.idsite !== 0 || Matomo.hasSuperUserAccess;
    },
    reportUrl(report: Report): string {
      const query = MatomoUrl.stringify({
        ...MatomoUrl.hashParsed.value,
        category: report.category_id,
        subcategory: report.subcategory_id,
      });
      return `#?${query}`;
    },
    problemText(status: ArchiveStatus): string {
      if (!status.problem) {
        return '';
      }

      const [start, end] = status.problem.period.split(',');
      const type = status.problem.type.replace(/^./, (letter) => letter.toUpperCase());
      return translate(`RebelMyReports_Problem${type}`, start === end ? start : `${start} – ${end}`);
    },
    moveBy(report: Report, step: number) {
      const movable = this.reports.filter((other) => this.canManage(other));
      this.move(report, movable[movable.indexOf(report) + step] || null);
    },
    move(report: Report|null, target: Report|null) {
      if (!report || !target || report === target || !this.canManage(target)) {
        return;
      }

      const movable = this.reports.filter((other) => this.canManage(other));
      movable.splice(movable.indexOf(target), 0, movable.splice(movable.indexOf(report), 1)[0]);
      // Reports the user cannot manage keep their place.
      const ordered = this.reports.map((other) => (
        this.canManage(other) ? movable.shift() as Report : other
      ));

      AjaxHelper.post({
        method: 'RebelMyReports.setReportOrder',
      }, {
        idMyReports: ordered.map((other) => other.idreport),
      }).then(() => {
        this.$emit('deleted');
      });
    },
    setPaused(report: Report, paused: boolean) {
      AjaxHelper.post({
        method: paused ? 'RebelMyReports.pauseReport' : 'RebelMyReports.resumeReport',
        idMyReport: report.idreport,
      }).then(() => {
        this.$emit('deleted');
      });
    },
    duplicate(site: DestinationSite) {
      const report = this.duplicated;
      if (!report) {
        return;
      }

      this.isDuplicating = true;
      AjaxHelper.post({
        method: 'RebelMyReports.duplicateReport',
        idMyReport: report.idreport,
      }, {
        idDestinationSite: site.id,
      }).then(() => {
        NotificationsStore.show({
          id: 'rebelMyReportsDuplicated',
          message: translate('RebelMyReports_DuplicatedTo', this.decode(report.name), site.name),
          context: 'success',
          type: 'transient',
        });
        this.duplicated = null;
        this.$emit('deleted');
      }).finally(() => {
        this.isDuplicating = false;
      });
    },
    confirmDelete(report: Report) {
      this.reportToDeleteName = this.decode(report.name);

      Matomo.helper.modalConfirm(this.$refs.confirm as HTMLElement, {
        yes: () => {
          AjaxHelper.post({
            method: 'RebelMyReports.deleteReport',
            idMyReport: report.idreport,
          }).then(() => {
            this.$emit('deleted');
          });
        },
      });
    },
  },
});
</script>
