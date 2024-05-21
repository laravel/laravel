import "../resources/css/app.css";

import type { Preview } from "@storybook/react";

const preview: Preview = {
  parameters: {
    controls: {
      matchers: {
        color: /(background|color)$/i,
        date: /Date$/,
      },
    },
    storySort: {
      method: "alphabetical",
      order: ["*"], // You can order in whatever way you want.
    },
  },
};

export default preview;
