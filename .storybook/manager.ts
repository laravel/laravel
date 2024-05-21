import { addons } from "@storybook/manager-api";

import DefaultTheme from "./theme";

addons.setConfig({
  theme: DefaultTheme,
  sidebar: {
    showRoots: true,
  },
});
