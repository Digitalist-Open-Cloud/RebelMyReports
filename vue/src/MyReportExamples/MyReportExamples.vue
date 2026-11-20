<template>
  <div class="myReportExamples">
    <ContentBlock :content-title="translate('RebelMyReports_ExamplesTitle')">
      <p>{{ translate('RebelMyReports_ExamplesIntro') }}</p>
      <div class="myReportExamples__site">
        <SiteSelector
          v-model="site"
          :show-selected-site="true"
          :default-to-first-site="true"
          :show-all-sites-item="false"
          :switch-site-on-select="false"
          :only-sites-with-at-least-write-access="true"
        />
      </div>

      <div class="myReportExamples__example" v-for="example in examples" :key="example.id">
        <div class="myReportExamples__text">
          <h3 class="myReportExamples__name">{{ example.name }}</h3>
          <p>{{ example.description }}</p>
          <p class="myReportExamples__parts">
            <template v-if="example.dimensions.length">
              {{ translate('RebelMyReports_Dimensions') }}: {{ example.dimensions.join(', ') }}.
            </template>
            {{ translate('General_Metrics') }}: {{ example.metrics.join(', ') }}.
          </p>
        </div>
        <button
          type="button"
          class="btn"
          :disabled="!site || busy === example.id || isCreated(example)"
          @click="create(example)"
        >
          {{ translate(isCreated(example)
            ? 'RebelMyReports_ExampleCreated'
            : 'RebelMyReports_ExampleCreate') }}
        </button>
      </div>
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
  SiteSelector,
  translate,
} from 'CoreHome';

interface Example {
  id: string;
  name: string;
  description: string;
  dimensions: string[];
  metrics: string[];
}

export default defineComponent({
  props: {
    examples: {
      type: Array as PropType<Example[]>,
      required: true,
    },
  },
  components: {
    ContentBlock,
    SiteSelector,
  },
  data() {
    return {
      busy: '',
      created: [] as string[],
      // Without a site the selector chooses one.
      site: (Matomo.idSite ? {
        id: Matomo.idSite,
        name: Matomo.helper.htmlDecode(Matomo.siteName),
      } : undefined) as { id: number|string, name: string }|undefined,
    };
  },
  methods: {
    isCreated(example: Example): boolean {
      return this.created.includes(`${this.site?.id}:${example.id}`);
    },
    create(example: Example) {
      const idSite = this.site?.id;
      this.busy = example.id;

      AjaxHelper.post({
        method: 'RebelMyReports.addExampleReport',
        idSite,
      }, {
        example: example.id,
      }).then(() => {
        this.created.push(`${idSite}:${example.id}`);
        NotificationsStore.show({
          id: 'rebelMyReportsExampleCreated',
          message: translate('RebelMyReports_ReportSaved'),
          context: 'success',
          type: 'transient',
        });
      }).finally(() => {
        this.busy = '';
      });
    },
  },
});
</script>
