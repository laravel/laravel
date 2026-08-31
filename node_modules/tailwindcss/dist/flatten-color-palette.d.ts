type Colors = {
    [key: string | number]: string | Colors;
};
declare function flattenColorPalette(colors: Colors): Record<string, string>;

export { flattenColorPalette as default };
