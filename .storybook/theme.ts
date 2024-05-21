import { create } from "@storybook/theming/create";

const DefaultTheme = create({
  base: "light",
  appBg: "white",
  colorPrimary: "#7445FC",
  colorSecondary: "#7445FC",
  brandImage: "./logo.png",
  brandTitle: "Default Component Library",
  brandTarget: "_self",
});

export default DefaultTheme;
