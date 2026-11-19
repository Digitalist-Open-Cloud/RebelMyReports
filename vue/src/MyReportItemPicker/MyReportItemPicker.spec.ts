import { mount } from '@vue/test-utils';

jest.mock('CorePluginsAdmin', () => ({
  Field: {
    name: 'Field',
    props: ['options'],
    emits: ['update:modelValue'],
    template: '<div class="fieldStub" />',
  },
}), { virtual: true });

// eslint-disable-next-line @typescript-eslint/no-var-requires
const MyReportItemPicker = require('./MyReportItemPicker.vue').default;

const items = [
  {
    id: 'country', name: 'Country', category: 'Location', siteSpecific: false,
  },
  {
    id: 'browser', name: 'Browser', category: 'Visitors', siteSpecific: false,
  },
  {
    id: 'colour', name: 'Colour', category: 'Behaviour', siteSpecific: true,
  },
];

function mountPicker(props = {}) {
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  return mount(MyReportItemPicker as any, {
    props: {
      name: 'dimensions',
      title: 'Dimensions',
      addTitle: 'Add a dimension',
      items,
      modelValue: ['country', 'browser'],
      ...props,
    },
    global: {
      mocks: { translate: (key: string) => key },
    },
  });
}

function lastUpdate(wrapper: ReturnType<typeof mountPicker>) {
  const updates = wrapper.emitted('update:modelValue') as unknown[][];
  return updates[updates.length - 1][0];
}

describe('MyReportItemPicker', () => {
  it('shows the selected items by name, in order', () => {
    const names = mountPicker().findAll('.myReportItemPicker__name').map((el) => el.text());

    expect(names).toEqual(['Country', 'Browser']);
  });

  it('offers only items that are not selected yet', () => {
    const options = mountPicker().findComponent({ name: 'Field' }).props('options');

    expect(options).toEqual([{ group: 'Behaviour', key: 'colour', value: 'Colour' }]);
  });

  it('leaves out items of one website when asked to', () => {
    const wrapper = mountPicker({ hideSiteSpecific: true });

    expect(wrapper.findComponent({ name: 'Field' }).props('options')).toEqual([]);
  });

  it('adds the picked item at the end', async () => {
    const wrapper = mountPicker();

    await wrapper.findComponent({ name: 'Field' }).vm.$emit('update:modelValue', 'colour');

    expect(lastUpdate(wrapper)).toEqual(['country', 'browser', 'colour']);
  });

  it('moves and removes items', async () => {
    const wrapper = mountPicker();

    await wrapper.findAll('.icon-arrow-right')[0].trigger('click');
    expect(lastUpdate(wrapper)).toEqual(['browser', 'country']);

    await wrapper.findAll('.icon-close')[0].trigger('click');
    expect(lastUpdate(wrapper)).toEqual(['browser']);
  });

  it('stops offering items when the limit is reached', () => {
    const wrapper = mountPicker({ max: 2, limitText: 'At most 2' });

    expect(wrapper.findComponent({ name: 'Field' }).exists()).toBe(false);
    expect(wrapper.find('.myReportItemPicker__limit').text()).toBe('At most 2');
  });
});
