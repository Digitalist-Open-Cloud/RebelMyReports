<template>
  <div class="myReportItemPicker">
    <div class="myReportItemPicker__title">{{ title }}</div>

    <ol class="myReportItemPicker__chipList" v-if="modelValue.length">
      <li class="myReportItemPicker__chip" v-for="(id, index) in modelValue" :key="id">
        <span class="myReportItemPicker__position">{{ index + 1 }}</span>
        <span class="myReportItemPicker__name">{{ names[id] || id }}</span>
        <button
          type="button"
          class="myReportItemPicker__button icon-arrow-left"
          :title="translate('RebelMyReports_MoveEarlier')"
          :disabled="index === 0"
          @click="move(index, -1)"
        />
        <button
          type="button"
          class="myReportItemPicker__button icon-arrow-right"
          :title="translate('RebelMyReports_MoveLater')"
          :disabled="index === modelValue.length - 1"
          @click="move(index, 1)"
        />
        <button
          type="button"
          class="myReportItemPicker__button icon-close"
          :title="translate('General_Remove')"
          @click="remove(index)"
        />
      </li>
    </ol>

    <div class="myReportItemPicker__add" v-if="canAdd">
      <Field
        uicontrol="expandable-select"
        :name="name"
        :full-width="true"
        :model-value="''"
        :title="addTitle"
        :options="options"
        @update:model-value="add($event)"
      />
    </div>
    <p class="myReportItemPicker__limit" v-else>{{ limitText }}</p>
  </div>
</template>

<script lang="ts">
import { defineComponent, PropType } from 'vue';
import { Field } from 'CorePluginsAdmin';
import { CatalogueItem } from '../types';

export default defineComponent({
  props: {
    name: {
      type: String,
      required: true,
    },
    title: {
      type: String,
      required: true,
    },
    addTitle: {
      type: String,
      required: true,
    },
    limitText: {
      type: String,
      default: '',
    },
    items: {
      type: Array as PropType<CatalogueItem[]>,
      required: true,
    },
    modelValue: {
      type: Array as PropType<string[]>,
      required: true,
    },
    max: {
      type: Number,
      default: 0,
    },
    hideSiteSpecific: Boolean,
  },
  emits: ['update:modelValue'],
  components: {
    Field,
  },
  computed: {
    names(): Record<string, string> {
      return Object.fromEntries(this.items.map((item) => [item.id, item.name]));
    },
    canAdd(): boolean {
      return !this.max || this.modelValue.length < this.max;
    },
    options(): { group: string, key: string, value: string }[] {
      return this.items
        .filter((item) => !this.modelValue.includes(item.id))
        .filter((item) => !this.hideSiteSpecific || !item.siteSpecific)
        .map((item) => ({ group: item.category, key: item.id, value: item.name }));
    },
  },
  methods: {
    add(id: string) {
      if (id && !this.modelValue.includes(id)) {
        this.$emit('update:modelValue', [...this.modelValue, id]);
      }
    },
    remove(index: number) {
      this.$emit('update:modelValue', this.modelValue.filter((id, i) => i !== index));
    },
    move(index: number, offset: number) {
      const ids = [...this.modelValue];
      [ids[index], ids[index + offset]] = [ids[index + offset], ids[index]];
      this.$emit('update:modelValue', ids);
    },
  },
});
</script>
