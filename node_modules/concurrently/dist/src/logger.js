"use strict";
var __createBinding = (this && this.__createBinding) || (Object.create ? (function(o, m, k, k2) {
    if (k2 === undefined) k2 = k;
    var desc = Object.getOwnPropertyDescriptor(m, k);
    if (!desc || ("get" in desc ? !m.__esModule : desc.writable || desc.configurable)) {
      desc = { enumerable: true, get: function() { return m[k]; } };
    }
    Object.defineProperty(o, k2, desc);
}) : (function(o, m, k, k2) {
    if (k2 === undefined) k2 = k;
    o[k2] = m[k];
}));
var __setModuleDefault = (this && this.__setModuleDefault) || (Object.create ? (function(o, v) {
    Object.defineProperty(o, "default", { enumerable: true, value: v });
}) : function(o, v) {
    o["default"] = v;
});
var __importStar = (this && this.__importStar) || (function () {
    var ownKeys = function(o) {
        ownKeys = Object.getOwnPropertyNames || function (o) {
            var ar = [];
            for (var k in o) if (Object.prototype.hasOwnProperty.call(o, k)) ar[ar.length] = k;
            return ar;
        };
        return ownKeys(o);
    };
    return function (mod) {
        if (mod && mod.__esModule) return mod;
        var result = {};
        if (mod != null) for (var k = ownKeys(mod), i = 0; i < k.length; i++) if (k[i] !== "default") __createBinding(result, mod, k[i]);
        __setModuleDefault(result, mod);
        return result;
    };
})();
var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : { "default": mod };
};
Object.defineProperty(exports, "__esModule", { value: true });
exports.Logger = void 0;
const chalk_1 = __importDefault(require("chalk"));
const Rx = __importStar(require("rxjs"));
const date_format_1 = require("./date-format");
const defaults = __importStar(require("./defaults"));
const utils_1 = require("./utils");
const defaultChalk = chalk_1.default;
const noColorChalk = new chalk_1.default.Instance({ level: 0 });
function getChalkPath(chalk, path) {
    return path
        .split('.')
        .reduce((prev, key) => prev[key], chalk);
}
class Logger {
    hide;
    raw;
    prefixFormat;
    commandLength;
    dateFormatter;
    chalk = defaultChalk;
    /**
     * How many characters should a prefix have.
     * Prefixes shorter than this will be padded with spaces to the right.
     */
    prefixLength = 0;
    /**
     * Last character emitted, and from which command.
     * If `undefined`, then nothing has been logged yet.
     */
    lastWrite;
    /**
     * Observable that emits when there's been output logged.
     * If `command` is is `undefined`, then the log is for a global event.
     */
    output = new Rx.Subject();
    constructor({ hide, prefixFormat, commandLength, raw = false, timestampFormat, }) {
        this.hide = (hide || []).map(String);
        this.raw = raw;
        this.prefixFormat = prefixFormat;
        this.commandLength = commandLength || defaults.prefixLength;
        this.dateFormatter = new date_format_1.DateFormatter(timestampFormat || defaults.timestampFormat);
    }
    /**
     * Toggles colors on/off globally.
     */
    toggleColors(on) {
        this.chalk = on ? defaultChalk : noColorChalk;
    }
    shortenText(text) {
        if (!text || text.length <= this.commandLength) {
            return text;
        }
        const ellipsis = '..';
        const prefixLength = this.commandLength - ellipsis.length;
        const endLength = Math.floor(prefixLength / 2);
        const beginningLength = prefixLength - endLength;
        const beginning = text.slice(0, beginningLength);
        const end = text.slice(text.length - endLength, text.length);
        return beginning + ellipsis + end;
    }
    getPrefixesFor(command) {
        return {
            // When there's limited concurrency, the PID might not be immediately available,
            // so avoid the string 'undefined' from becoming a prefix
            pid: command.pid != null ? String(command.pid) : '',
            index: String(command.index),
            name: command.name,
            command: this.shortenText(command.command),
            time: this.dateFormatter.format(new Date()),
        };
    }
    getPrefixContent(command) {
        const prefix = this.prefixFormat || (command.name ? 'name' : 'index');
        if (prefix === 'none') {
            return;
        }
        const prefixes = this.getPrefixesFor(command);
        if (Object.keys(prefixes).includes(prefix)) {
            return { type: 'default', value: prefixes[prefix] };
        }
        const value = Object.entries(prefixes).reduce((prev, [key, val]) => {
            const keyRegex = new RegExp((0, utils_1.escapeRegExp)(`{${key}}`), 'g');
            return prev.replace(keyRegex, String(val));
        }, prefix);
        return { type: 'template', value };
    }
    getPrefix(command) {
        const content = this.getPrefixContent(command);
        if (!content) {
            return '';
        }
        return content.type === 'template'
            ? content.value.padEnd(this.prefixLength, ' ')
            : `[${content.value.padEnd(this.prefixLength, ' ')}]`;
    }
    setPrefixLength(length) {
        this.prefixLength = length;
    }
    colorText(command, text) {
        let color;
        if (command.prefixColor?.startsWith('#')) {
            color = this.chalk.hex(command.prefixColor);
        }
        else {
            const defaultColor = getChalkPath(this.chalk, defaults.prefixColors);
            color = getChalkPath(this.chalk, command.prefixColor ?? '') ?? defaultColor;
        }
        return color(text);
    }
    /**
     * Logs an event for a command (e.g. start, stop).
     *
     * If raw mode is on, then nothing is logged.
     */
    logCommandEvent(text, command) {
        if (this.raw) {
            return;
        }
        // Last write was from this command, but it didn't end with a line feed.
        // Prepend one, otherwise the event's text will be concatenated to that write.
        // A line feed is otherwise inserted anyway.
        let prefix = '';
        if (this.lastWrite?.command === command && this.lastWrite.char !== '\n') {
            prefix = '\n';
        }
        this.logCommandText(prefix + this.chalk.reset(text) + '\n', command);
    }
    logCommandText(text, command) {
        if (this.hide.includes(String(command.index)) || this.hide.includes(command.name)) {
            return;
        }
        const prefix = this.colorText(command, this.getPrefix(command));
        return this.log(prefix + (prefix ? ' ' : ''), text, command);
    }
    /**
     * Logs a global event (e.g. sending signals to processes).
     *
     * If raw mode is on, then nothing is logged.
     */
    logGlobalEvent(text) {
        if (this.raw) {
            return;
        }
        this.log(this.chalk.reset('-->') + ' ', this.chalk.reset(text) + '\n');
    }
    /**
     * Logs a table from an input object array, like `console.table`.
     *
     * Each row is a single input item, and they are presented in the input order.
     */
    logTable(tableContents) {
        // For now, can only print array tables with some content.
        if (this.raw || !Array.isArray(tableContents) || !tableContents.length) {
            return;
        }
        let nextColIndex = 0;
        const headers = {};
        const contentRows = tableContents.map((row) => {
            const rowContents = [];
            Object.keys(row).forEach((col) => {
                if (!headers[col]) {
                    headers[col] = {
                        index: nextColIndex++,
                        length: col.length,
                    };
                }
                const colIndex = headers[col].index;
                const formattedValue = String(row[col] == null ? '' : row[col]);
                // Update the column length in case this rows value is longer than the previous length for the column.
                headers[col].length = Math.max(formattedValue.length, headers[col].length);
                rowContents[colIndex] = formattedValue;
                return rowContents;
            });
            return rowContents;
        });
        const headersFormatted = Object.keys(headers).map((header) => header.padEnd(headers[header].length, ' '));
        if (!headersFormatted.length) {
            // No columns exist.
            return;
        }
        const borderRowFormatted = headersFormatted.map((header) => '─'.padEnd(header.length, '─'));
        this.logGlobalEvent(`┌─${borderRowFormatted.join('─┬─')}─┐`);
        this.logGlobalEvent(`│ ${headersFormatted.join(' │ ')} │`);
        this.logGlobalEvent(`├─${borderRowFormatted.join('─┼─')}─┤`);
        contentRows.forEach((contentRow) => {
            const contentRowFormatted = headersFormatted.map((header, colIndex) => {
                // If the table was expanded after this row was processed, it won't have this column.
                // Use an empty string in this case.
                const col = contentRow[colIndex] || '';
                return col.padEnd(header.length, ' ');
            });
            this.logGlobalEvent(`│ ${contentRowFormatted.join(' │ ')} │`);
        });
        this.logGlobalEvent(`└─${borderRowFormatted.join('─┴─')}─┘`);
    }
    log(prefix, text, command) {
        if (this.raw) {
            return this.emit(command, text);
        }
        // #70 - replace some ANSI code that would impact clearing lines
        text = text.replace(/\u2026/g, '...');
        // This write's interrupting another command, emit a line feed to start clean.
        if (this.lastWrite && this.lastWrite.command !== command && this.lastWrite.char !== '\n') {
            this.emit(this.lastWrite.command, '\n');
        }
        // Clean lines should emit a prefix
        if (!this.lastWrite || this.lastWrite.char === '\n') {
            this.emit(command, prefix);
        }
        const textToWrite = text.replaceAll('\n', (lf, i) => lf + (text[i + 1] ? prefix : ''));
        this.emit(command, textToWrite);
    }
    emit(command, text) {
        this.lastWrite = { command, char: text[text.length - 1] };
        this.output.next({ command, text });
    }
}
exports.Logger = Logger;
