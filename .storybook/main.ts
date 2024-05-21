import type { StorybookConfig } from "@storybook/react-vite";
import { mergeConfig } from "vite";

const config: StorybookConfig = {

  /**
   * Fix for asset loading due to Laravel-Vite setup
   * https://github.com/storybookjs/storybook/issues/22550#issuecomment-1661920350
   */
  async viteFinal(config) {
    return mergeConfig(config, {
      server: {
        origin: ''
      },
    });
  },

  stories: [
    "../resources/js/**/*.stories.@(js|jsx|mjs|ts|tsx)",
  ],

  staticDirs: ["./images"],

  addons: [
    "@storybook/addon-links",
    "@storybook/addon-essentials",
    "@storybook/addon-onboarding",
    "@storybook/addon-interactions",
    "@storybook/addon-a11y",
    "@chromatic-com/storybook",
  ],

  framework: {
    name: "@storybook/react-vite",
    options: {},
  },

  docs: {},

  typescript: {
    reactDocgen: "react-docgen-typescript",
  },
};
export default config;
