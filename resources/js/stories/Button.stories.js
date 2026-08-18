import Button from 'primevue/button';

export default {
  title: 'PrimeVue/Button',
  component: Button,
  argTypes: {
    label: { control: 'text' },
    severity: {
      control: 'select',
      options: ['primary', 'secondary', 'success', 'info', 'warning', 'danger', 'help', 'contrast'],
    },
    size: {
      control: 'select',
      options: ['small', 'large'],
    },
    outlined: { control: 'boolean' },
    disabled: { control: 'boolean' },
    loading: { control: 'boolean' },
  },
};

const Template = (args) => ({
  components: { Button },
  setup() {
    return { args };
  },
  template: '<Button v-bind="args" />',
});

export const Primary = Template.bind({});
Primary.args = {
  label: 'Primary',
  severity: 'primary',
};

export const Success = Template.bind({});
Success.args = {
  label: 'Success',
  severity: 'success',
};

export const Danger = Template.bind({});
Danger.args = {
  label: 'Danger',
  severity: 'danger',
};

export const Outlined = Template.bind({});
Outlined.args = {
  label: 'Outlined',
  outlined: true,
};

export const Loading = Template.bind({});
Loading.args = {
  label: 'Loading',
  loading: true,
};

export const Disabled = Template.bind({});
Disabled.args = {
  label: 'Disabled',
  disabled: true,
};

export const Small = Template.bind({});
Small.args = {
  label: 'Small',
  size: 'small',
};

export const Large = Template.bind({});
Large.args = {
  label: 'Large',
  size: 'large',
};
