declare const JSONC: {
    parse: (text: string) => any;
    stringify: {
        (value: any, replacer?: (this: any, key: string, value: any) => any, space?: string | number): string;
        (value: any, replacer?: (number | string)[] | null, space?: string | number): string;
    };
};
export default JSONC;
