export type FormatterOptions = {
    locale?: string;
    calendar?: string;
};
/**
 * Unicode-compliant date/time formatter.
 *
 * @see https://unicode.org/reports/tr35/tr35-dates.html#Date_Format_Patterns
 */
export declare class DateFormatter {
    private readonly options;
    private static tokenRegex;
    private readonly parts;
    constructor(pattern: string, options?: FormatterOptions);
    private compileLiteral;
    private compileOther;
    private compileToken;
    format(date: Date): string;
}
