module.exports = {
  env: {
    browser: true,
    es2021: true,
    node: true,
  },
  extends: ['eslint:recommended', 'plugin:vue/vue3-recommended'],
  parserOptions: {
    ecmaVersion: 'latest',
    sourceType: 'module',
  },
  plugins: ['vue'],
  rules: {
    // Vue specific rules
    'vue/multi-word-component-names': 'off',
    'vue/no-v-html': 'warn',
    'vue/require-default-prop': 'off',
    'vue/require-prop-types': 'warn',
    'vue/valid-v-slot': 'off', // Disable v-slot modifier check
    'vue/max-attributes-per-line': 'off', // Allow multiple attributes on same line
    'vue/html-indent': 'off', // Disable HTML indentation rules
    'vue/singleline-html-element-content-newline': 'off', // Allow single line elements

    // General JavaScript rules
    'no-console': 'off', // Allow console for development
    'no-debugger': 'warn',
    'no-unused-vars': 'warn',
    'no-undef': 'error',
    'prefer-const': 'warn',
    'no-var': 'error',

    // Code style
    indent: ['error', 2],
    quotes: ['error', 'single'],
    semi: ['error', 'always'],
    'comma-dangle': ['error', 'always-multiline'],
    'object-curly-spacing': ['error', 'always'],
    'array-bracket-spacing': ['error', 'never'],
  },
  ignorePatterns: ['dist/', 'node_modules/', '*.min.js'],
};
