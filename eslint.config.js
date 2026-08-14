// For more info, see https://github.com/storybookjs/eslint-plugin-storybook#configuration-flat-config-format
import storybook from "eslint-plugin-storybook";

export default [{
    ignores: ['vendor/**', 'node_modules/**', 'storage/**', 'bootstrap/cache/**'],
}, {
    files: ['resources/js/**/*.{js,vue}', 'app/Modules/AgriVerse/Resources/js/**/*.{js,vue}'],
    rules: {
        'no-unused-vars': 'warn',
        'no-console': ['warn', { allow: ['warn', 'error'] }],
        'vue/no-unused-components': 'warn',
        'vue/multi-word-component-names': 'off',
        'vue/require-default-prop': 'off',
        'vue/require-v-for-key': 'error',
    },
}, ...storybook.configs["flat/recommended"]];
