<template>
  <div class="myReportExamples">
    <ContentBlock :content-title="translate('RebelMyReports_ExamplesTitle')">
      <p>{{ translate('RebelMyReports_ExamplesIntro', siteName) }}</p>

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
          :disabled="busy === example.id || created.includes(example.id)"
          @click="create(example)"
        >
          {{ translate(created.includes(example.id)
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
  },
  data() {
    return {
      busy: '',
      created: [] as string[],
    };
  },
  computed: {
    siteName(): string {
      return Matomo.helper.htmlDecode(Matomo.siteName);
    },
  },
  methods: {
    create(example: Example) {
      this.busy = example.id;

      AjaxHelper.post({
        method: 'RebelMyReports.addExampleReport',
      }, {
        example: example.id,
      }).then(() => {
        this.created.push(example.id);
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
