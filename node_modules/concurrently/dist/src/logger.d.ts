import * as Rx from 'rxjs';
import { Command, CommandIdentifier } from './command';
export declare class Logger {
    private readonly hide;
    private readonly raw;
    private readonly prefixFormat?;
    private readonly commandLength;
    private readonly dateFormatter;
    private chalk;
    /**
     * How many characters should a prefix have.
     * Prefixes shorter than this will be padded with spaces to the right.
     */
    private prefixLength;
    /**
     * Last character emitted, and from which command.
     * If `undefined`, then nothing has been logged yet.
     */
    private lastWrite?;
    /**
     * Observable that emits when there's been output logged.
     * If `command` is is `undefined`, then the log is for a global event.
     */
    readonly output: Rx.Subject<{
        command: Command | undefined;
        text: string;
    }>;
    constructor({ hide, prefixFormat, commandLength, raw, timestampFormat, }: {
        /**
         * Which commands should have their output hidden.
         */
        hide?: CommandIdentifier[];
        /**
         * Whether output should be formatted to include prefixes and whether "event" logs will be
         * logged.
         */
        raw?: boolean;
        /**
         * The prefix format to use when logging a command's output.
         * Defaults to the command's index.
         */
        prefixFormat?: string;
        /**
         * How many characters should a prefix have at most when the format is `command`.
         */
        commandLength?: number;
        /**
         * Date format used when logging date/time.
         * @see https://www.unicode.org/reports/tr35/tr35-dates.html#Date_Field_Symbol_Table
         */
        timestampFormat?: string;
    });
    /**
     * Toggles colors on/off globally.
     */
    toggleColors(on: boolean): void;
    private shortenText;
    private getPrefixesFor;
    getPrefixContent(command: Command): {
        type: 'default' | 'template';
        value: string;
    } | undefined;
    getPrefix(command: Command): string;
    setPrefixLength(length: number): void;
    colorText(command: Command, text: string): string;
    /**
     * Logs an event for a command (e.g. start, stop).
     *
     * If raw mode is on, then nothing is logged.
     */
    logCommandEvent(text: string, command: Command): void;
    logCommandText(text: string, command: Command): void;
    /**
     * Logs a global event (e.g. sending signals to processes).
     *
     * If raw mode is on, then nothing is logged.
     */
    logGlobalEvent(text: string): void;
    /**
     * Logs a table from an input object array, like `console.table`.
     *
     * Each row is a single input item, and they are presented in the input order.
     */
    logTable(tableContents: Record<string, unknown>[]): void;
    log(prefix: string, text: string, command?: Command): void;
    emit(command: Command | undefined, text: string): void;
}
