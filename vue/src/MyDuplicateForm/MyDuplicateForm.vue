<template>
  <div class="myDuplicateForm">
    <span class="myDuplicateForm__text">
      {{ translate('RebelMyReports_DuplicateTo', itemName) }}
    </span>
    <div class="myDuplicateForm__site">
      <SiteSelector
        v-model="site"
        :show-selected-site="true"
        :show-all-sites-item="false"
        :switch-site-on-select="false"
        :only-sites-with-at-least-write-access="true"
      />
    </div>
    <button
      type="button"
      class="btn myDuplicateForm__confirm"
      :disabled="isBusy"
      @click="$emit('confirm', site)"
    >{{ translate('RebelMyReports_Duplicate') }}</button>
    <button type="button" class="myDuplicateForm__cancel" @click="$emit('cancel')">
      {{ translate('General_Cancel') }}
    </button>
  </div>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import { Matomo, SiteSelector } from 'CoreHome';

export interface DestinationSite {
  id: number|string;
  name: string;
}

export default defineComponent({
  props: {
    itemName: {
      type: String,
      required: true,
    },
    isBusy: Boolean,
  },
  emits: ['confirm', 'cancel'],
  components: {
    SiteSelector,
  },
  data() {
    return {
      site: {
        id: Matomo.idSite,
        name: Matomo.helper.htmlDecode(Matomo.siteName),
      } as DestinationSite,
    };
  },
});
</script>
