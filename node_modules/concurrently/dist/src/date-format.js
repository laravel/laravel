"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
exports.DateFormatter = void 0;
/**
 * Unicode-compliant date/time formatter.
 *
 * @see https://unicode.org/reports/tr35/tr35-dates.html#Date_Format_Patterns
 */
class DateFormatter {
    options;
    static tokenRegex = /[A-Z]/i;
    parts = [];
    constructor(pattern, options = {}) {
        this.options = options;
        let i = 0;
        while (i < pattern.length) {
            const char = pattern[i];
            const { fn, length } = char === "'"
                ? this.compileLiteral(pattern, i)
                : DateFormatter.tokenRegex.test(char)
                    ? this.compileToken(pattern, i)
                    : this.compileOther(pattern, i);
            this.parts.push(fn);
            i += length;
        }
    }
    compileLiteral(pattern, offset) {
        let length = 1;
        let value = '';
        for (; length < pattern.length; length++) {
            const i = offset + length;
            const char = pattern[i];
            if (char === "'") {
                const nextChar = pattern[i + 1];
                length++;
                // if the next character is another single quote, it's been escaped.
                // if not, then the literal has been closed
                if (nextChar !== "'") {
                    break;
                }
            }
            value += char;
        }
        return { fn: () => value || "'", length };
    }
    compileOther(pattern, offset) {
        let value = '';
        while (!DateFormatter.tokenRegex.test(pattern[offset]) && pattern[offset] !== "'") {
            value += pattern[offset++];
        }
        return { fn: () => value, length: value.length };
    }
    compileToken(pattern, offset) {
        const type = pattern[offset];
        const token = tokens.get(type);
        if (!token) {
            throw new SyntaxError(`Formatting token "${type}" is invalid`);
        }
        let length = 0;
        while (pattern[offset + length] === type) {
            length++;
        }
        const tokenFn = token[length - 1];
        if (!tokenFn) {
            throw new RangeError(`Formatting token "${type.repeat(length)}" is unsupported`);
        }
        return { fn: tokenFn, length };
    }
    format(date) {
        return this.parts.reduce((output, part) => output + String(part(date, this.options)), '');
    }
}
exports.DateFormatter = DateFormatter;
/**
 * A map of token to its implementations by length.
 * If an index is undefined, then that token length is unsupported.
 */
const tokens = new Map()
    // era
    .set('G', [
    makeTokenFn({ era: 'short' }, 'era'),
    makeTokenFn({ era: 'short' }, 'era'),
    makeTokenFn({ era: 'short' }, 'era'),
    makeTokenFn({ era: 'long' }, 'era'),
    makeTokenFn({ era: 'narrow' }, 'era'),
])
    // year
    .set('y', [
    // TODO: does not support BC years.
    // https://stackoverflow.com/a/41345095/2083599
    (date) => date.getFullYear(),
    (date) => pad(2, date.getFullYear()).slice(-2),
    (date) => pad(3, date.getFullYear()),
    (date) => pad(4, date.getFullYear()),
    (date) => pad(5, date.getFullYear()),
])
    .set('Y', [
    getWeekYear,
    (date, options) => pad(2, getWeekYear(date, options)).slice(-2),
    (date, options) => pad(3, getWeekYear(date, options)),
    (date, options) => pad(4, getWeekYear(date, options)),
    (date, options) => pad(5, getWeekYear(date, options)),
])
    .set('u', [])
    .set('U', [
    // Fallback implemented as yearName is not available in gregorian calendars, for instance.
    makeTokenFn({ dateStyle: 'full' }, 'yearName', (date) => String(date.getFullYear())),
])
    .set('r', [
    // Fallback implemented as relatedYear is not available in gregorian calendars, for instance.
    makeTokenFn({ dateStyle: 'full' }, 'relatedYear', (date) => String(date.getFullYear())),
])
    // quarter
    .set('Q', [
    (date) => Math.floor(date.getMonth() / 3) + 1,
    (date) => pad(2, Math.floor(date.getMonth() / 3) + 1),
    // these aren't localized in Intl.DateTimeFormat.
    undefined,
    undefined,
    (date) => Math.floor(date.getMonth() / 3) + 1,
])
    .set('q', [
    (date) => Math.floor(date.getMonth() / 3) + 1,
    (date) => pad(2, Math.floor(date.getMonth() / 3) + 1),
    // these aren't localized in Intl.DateTimeFormat.
    undefined,
    undefined,
    (date) => Math.floor(date.getMonth() / 3) + 1,
])
    // month
    .set('M', [
    (date) => date.getMonth() + 1,
    (date) => pad(2, date.getMonth() + 1),
    // these include the day so that it forces non-stand-alone month part
    makeTokenFn({ day: 'numeric', month: 'short' }, 'month'),
    makeTokenFn({ day: 'numeric', month: 'long' }, 'month'),
    makeTokenFn({ day: 'numeric', month: 'narrow' }, 'month'),
])
    .set('L', [
    (date) => date.getMonth() + 1,
    (date) => pad(2, date.getMonth() + 1),
    makeTokenFn({ month: 'short' }, 'month'),
    makeTokenFn({ month: 'long' }, 'month'),
    makeTokenFn({ month: 'narrow' }, 'month'),
])
    .set('l', [() => ''])
    // week
    .set('w', [getWeek, (date, options) => pad(2, getWeek(date, options))])
    .set('W', [getWeekOfMonth])
    // day
    .set('d', [(date) => date.getDate(), (date) => pad(2, date.getDate())])
    .set('D', [
    getDayOfYear,
    (date) => pad(2, getDayOfYear(date)),
    (date) => pad(3, getDayOfYear(date)),
])
    .set('F', [(date) => Math.ceil(date.getDate() / 7)])
    .set('g', [])
    // week day
    .set('E', [
    makeTokenFn({ weekday: 'short' }, 'weekday'),
    makeTokenFn({ weekday: 'short' }, 'weekday'),
    makeTokenFn({ weekday: 'short' }, 'weekday'),
    makeTokenFn({ weekday: 'long' }, 'weekday'),
])
    .set('e', [
    undefined,
    undefined,
    makeTokenFn({ weekday: 'short' }, 'weekday'),
    makeTokenFn({ weekday: 'long' }, 'weekday'),
])
    .set('c', [])
    // period
    .set('a', [
    makeTokenFn({ hour12: true, timeStyle: 'full' }, 'dayPeriod'),
    makeTokenFn({ hour12: true, timeStyle: 'full' }, 'dayPeriod'),
    makeTokenFn({ hour12: true, timeStyle: 'full' }, 'dayPeriod'),
])
    .set('b', [])
    .set('B', [
    makeTokenFn({ dayPeriod: 'short' }, 'dayPeriod'),
    makeTokenFn({ dayPeriod: 'short' }, 'dayPeriod'),
    makeTokenFn({ dayPeriod: 'short' }, 'dayPeriod'),
    makeTokenFn({ dayPeriod: 'long' }, 'dayPeriod'),
])
    // hour
    .set('h', [(date) => date.getHours() % 12 || 12, (date) => pad(2, date.getHours() % 12 || 12)])
    .set('H', [(date) => date.getHours(), (date) => pad(2, date.getHours())])
    .set('K', [(date) => date.getHours() % 12, (date) => pad(2, date.getHours() % 12)])
    .set('k', [(date) => date.getHours() % 24 || 24, (date) => pad(2, date.getHours() % 24 || 24)])
    .set('j', [])
    .set('J', [])
    .set('C', [])
    // minute
    .set('m', [(date) => date.getMinutes(), (date) => pad(2, date.getMinutes())])
    // second
    .set('s', [(date) => date.getSeconds(), (date) => pad(2, date.getSeconds())])
    .set('S', [
    (date) => Math.trunc(date.getMilliseconds() / 100),
    (date) => pad(2, Math.trunc(date.getMilliseconds() / 10)),
    (date) => pad(3, Math.trunc(date.getMilliseconds())),
])
    .set('A', [])
    // zone
    // none of these have tests
    .set('z', [
    makeTokenFn({ timeZoneName: 'short' }, 'timeZoneName'),
    makeTokenFn({ timeZoneName: 'short' }, 'timeZoneName'),
    makeTokenFn({ timeZoneName: 'short' }, 'timeZoneName'),
    makeTokenFn({ timeZoneName: 'long' }, 'timeZoneName'),
])
    .set('Z', [
    undefined,
    undefined,
    undefined,
    // equivalent to `OOOO`.
    makeTokenFn({ timeZoneName: 'longOffset' }, 'timeZoneName'),
])
    .set('O', [
    makeTokenFn({ timeZoneName: 'shortOffset' }, 'timeZoneName'),
    undefined,
    undefined,
    // equivalent to `ZZZZ`.
    makeTokenFn({ timeZoneName: 'longOffset' }, 'timeZoneName'),
])
    .set('v', [
    makeTokenFn({ timeZoneName: 'shortGeneric' }, 'timeZoneName'),
    undefined,
    undefined,
    makeTokenFn({ timeZoneName: 'longGeneric' }, 'timeZoneName'),
])
    .set('V', [])
    .set('X', [])
    .set('x', []);
let locale;
function getLocale(options) {
    if (!locale || locale.baseName !== options.locale) {
        locale = new Intl.Locale(
        /* v8 ignore next - fallback value only for safety */
        options.locale || new Intl.DateTimeFormat().resolvedOptions().locale);
    }
    return locale;
}
/**
 * Creates a token formatting function that returns the value of the chosen part type,
 * using the current locale's settings.
 *
 * If the date/formatter settings doesn't include the requested part type,
 * the `fallback` function is invoked, if specified. If none has been specified, returns an
 * empty string.
 */
function makeTokenFn(options, type, fallback) {
    let formatter;
    return (date, formatterOptions) => {
        // Allow tests to set a different locale and have that cause the formatter to be recreated
        if (!formatter ||
            formatter.resolvedOptions().locale !== formatterOptions.locale ||
            formatter.resolvedOptions().calendar !== formatterOptions.calendar) {
            formatter = new Intl.DateTimeFormat(formatterOptions.locale, {
                ...options,
                calendar: options.calendar ?? formatterOptions.calendar,
            });
        }
        const parts = formatter.formatToParts(date);
        const part = parts.find((p) => p.type === type);
        /* v8 ignore next - fallback value '' only for safety */
        return part?.value ?? (fallback ? fallback(date, formatterOptions) : '');
    };
}
function startOfWeek(date, options) {
    const locale = getLocale(options);
    const firstDay = locale.weekInfo.firstDay === 7 ? 0 : locale.weekInfo.firstDay;
    const day = date.getDay();
    const diff = (day < firstDay ? 7 : 0) + day - firstDay;
    date.setDate(date.getDate() - diff);
    date.setHours(0, 0, 0, 0);
    return date;
}
function getWeekYear(date, options) {
    const locale = getLocale(options);
    const minimalDays = locale.weekInfo.minimalDays;
    const year = date.getFullYear();
    const thisYear = startOfWeek(new Date(year, 0, minimalDays), options);
    const nextYear = startOfWeek(new Date(year + 1, 0, minimalDays), options);
    if (date.getTime() >= nextYear.getTime()) {
        return year + 1;
    }
    else if (date.getTime() >= thisYear.getTime()) {
        return year;
    }
    else {
        return year - 1;
    }
}
function getWeek(date, options) {
    const locale = getLocale(options);
    const weekMs = 7 * 24 * 3600 * 1000;
    const temp = startOfWeek(new Date(date), options);
    const thisYear = new Date(getWeekYear(date, options), 0, locale.weekInfo.minimalDays);
    startOfWeek(thisYear, options);
    const diff = temp.getTime() - thisYear.getTime();
    return Math.round(diff / weekMs) + 1;
}
function getWeekOfMonth(date, options) {
    const current = new Date(date);
    current.setHours(0, 0, 0, 0);
    const monthWeekStart = startOfWeek(new Date(date.getFullYear(), date.getMonth(), 1), options);
    const weekMs = 7 * 24 * 3600 * 1000;
    return Math.floor((date.getTime() - monthWeekStart.getTime()) / weekMs) + 1;
}
function getDayOfYear(date) {
    let days = 0;
    for (let i = 0; i <= date.getMonth() - 1; i++) {
        const temp = new Date(date.getFullYear(), i + 1, 0, 0, 0, 0);
        days += temp.getDate();
    }
    return days + date.getDate();
}
function pad(length, val) {
    return String(val).padStart(length, '0');
}
