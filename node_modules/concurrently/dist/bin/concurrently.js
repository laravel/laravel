#!/usr/bin/env node
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
const yargs_1 = __importDefault(require("yargs"));
const helpers_1 = require("yargs/helpers");
const assert_1 = require("../src/assert");
const defaults = __importStar(require("../src/defaults"));
const index_1 = require("../src/index");
const utils_1 = require("../src/utils");
const read_package_1 = require("./read-package");
const version = String((0, read_package_1.readPackage)().version);
const epilogue = `For documentation and more examples, visit:\nhttps://github.com/open-cli-tools/concurrently/tree/v${version}/docs`;
// Clean-up arguments (yargs expects only the arguments after the program name)
const program = (0, yargs_1.default)((0, helpers_1.hideBin)(process.argv))
    .parserConfiguration({
    // Avoids options that can be specified multiple times from requiring a `--` to pass commands
    'greedy-arrays': false,
    // Makes sure that --passthrough-arguments works correctly
    'populate--': true,
})
    .usage('$0 [options] <command ...>')
    .help('h')
    .alias('h', 'help')
    .version(version)
    .alias('version', 'v')
    .alias('version', 'V')
    // TODO: Add some tests for this.
    .env('CONCURRENTLY')
    .options({
    // General
    'max-processes': {
        alias: 'm',
        describe: 'How many processes should run at once.\n' +
            'New processes only spawn after all restart tries of a process.\n' +
            'Exact number or a percent of CPUs available (for example "50%")',
        type: 'string',
    },
    names: {
        alias: 'n',
        describe: 'List of custom names to be used in prefix template.\n' +
            'Example names: "main,browser,server"',
        type: 'string',
    },
    'name-separator': {
        describe: 'The character to split <names> on. Example usage:\n' +
            '-n "styles|scripts|server" --name-separator "|"',
        default: defaults.nameSeparator,
    },
    success: {
        alias: 's',
        describe: 'Which command(s) must exit with code 0 in order for concurrently exit with ' +
            'code 0 too. Options are:\n' +
            '- "first" for the first command to exit;\n' +
            '- "last" for the last command to exit;\n' +
            '- "all" for all commands;\n' +
            // Note: not a typo. Multiple commands can have the same name.
            '- "command-{name}"/"command-{index}" for the commands with that name or index;\n' +
            '- "!command-{name}"/"!command-{index}" for all commands but the ones with that ' +
            'name or index.\n',
        default: defaults.success,
    },
    raw: {
        alias: 'r',
        describe: 'Output only raw output of processes, disables prettifying ' +
            'and concurrently coloring.',
        type: 'boolean',
    },
    // This one is provided for free. Chalk reads this itself and removes colors.
    // https://www.npmjs.com/package/chalk#chalksupportscolor
    'no-color': {
        describe: 'Disables colors from logging',
        type: 'boolean',
    },
    hide: {
        describe: 'Comma-separated list of processes to hide the output.\n' +
            'The processes can be identified by their name or index.',
        default: defaults.hide,
        type: 'string',
    },
    group: {
        alias: 'g',
        describe: 'Order the output as if the commands were run sequentially.',
        type: 'boolean',
    },
    timings: {
        describe: 'Show timing information for all processes.',
        type: 'boolean',
        default: defaults.timings,
    },
    'passthrough-arguments': {
        alias: 'P',
        describe: 'Passthrough additional arguments to commands (accessible via placeholders) ' +
            'instead of treating them as commands.',
        type: 'boolean',
        default: defaults.passthroughArguments,
    },
    teardown: {
        describe: 'Clean up command(s) to execute before exiting concurrently. Might be specified multiple times.\n' +
            "These aren't prefixed and they don't affect concurrently's exit code.",
        type: 'string',
        array: true,
    },
    // Kill others
    'kill-others': {
        alias: 'k',
        describe: 'Kill other processes once the first exits.',
        type: 'boolean',
    },
    'kill-others-on-fail': {
        describe: 'Kill other processes if one exits with non zero status code.',
        type: 'boolean',
    },
    'kill-signal': {
        alias: 'ks',
        describe: 'Signal to send to other processes if one exits or dies. (SIGTERM/SIGKILL, defaults to SIGTERM)',
        type: 'string',
        default: defaults.killSignal,
    },
    'kill-timeout': {
        describe: 'How many milliseconds to wait before forcing process terminating.',
        type: 'number',
    },
    // Prefix
    prefix: {
        alias: 'p',
        describe: 'Prefix used in logging for each process.\n' +
            'Possible values: index, pid, time, command, name, none, or a template. ' +
            'Example template: "{time}-{pid}"',
        defaultDescription: 'index or name (when --names is set)',
        type: 'string',
    },
    'prefix-colors': {
        alias: 'c',
        describe: 'Comma-separated list of chalk colors to use on prefixes. ' +
            'If there are more commands than colors, the last color will be repeated.\n' +
            '- Available modifiers: reset, bold, dim, italic, underline, inverse, hidden, strikethrough\n' +
            '- Available colors: black, red, green, yellow, blue, magenta, cyan, white, gray, \n' +
            'any hex values for colors (e.g. #23de43) or auto for an automatically picked color\n' +
            '- Available background colors: bgBlack, bgRed, bgGreen, bgYellow, bgBlue, bgMagenta, bgCyan, bgWhite\n' +
            'See https://www.npmjs.com/package/chalk for more information.',
        default: defaults.prefixColors,
        type: 'string',
    },
    'prefix-length': {
        alias: 'l',
        describe: 'Limit how many characters of the command is displayed in prefix. ' +
            'The option can be used to shorten the prefix when it is set to "command"',
        default: defaults.prefixLength,
        type: 'number',
    },
    'pad-prefix': {
        describe: 'Pads short prefixes with spaces so that the length of all prefixes match',
        type: 'boolean',
    },
    'timestamp-format': {
        alias: 't',
        describe: 'Specify the timestamp in Unicode format:\n' +
            'https://www.unicode.org/reports/tr35/tr35-dates.html#Date_Field_Symbol_Table',
        default: defaults.timestampFormat,
        type: 'string',
    },
    // Restarting
    'restart-tries': {
        describe: 'How many times a process that died should restart.\n' +
            'Negative numbers will make the process restart forever.',
        default: defaults.restartTries,
        type: 'number',
    },
    'restart-after': {
        describe: 'Delay before restarting the process, in milliseconds, or "exponential".',
        default: defaults.restartDelay,
        type: 'string',
    },
    // Input
    'handle-input': {
        alias: 'i',
        describe: 'Whether input should be forwarded to the child processes. ' +
            'See examples for more information.',
        type: 'boolean',
    },
    'default-input-target': {
        default: defaults.defaultInputTarget,
        describe: 'Identifier for child process to which input on stdin ' +
            'should be sent if not specified at start of input.\n' +
            'Can be either the index or the name of the process.',
    },
})
    .group(['m', 'n', 'name-separator', 's', 'r', 'no-color', 'hide', 'g', 'timings', 'P', 'teardown'], 'General')
    .group(['p', 'c', 'l', 't', 'pad-prefix'], 'Prefix styling')
    .group(['i', 'default-input-target'], 'Input handling')
    .group(['k', 'kill-others-on-fail', 'kill-signal', 'kill-timeout'], 'Killing other processes')
    .group(['restart-tries', 'restart-after'], 'Restarting')
    .epilogue(epilogue);
const args = program.parseSync();
(0, assert_1.assertDeprecated)(args.nameSeparator === defaults.nameSeparator, 'name-separator', 'Use commas as name separators instead.');
// Get names of commands by the specified separator
const names = (args.names || '').split(args.nameSeparator);
const additionalArguments = (0, utils_1.castArray)(args['--'] ?? []).map(String);
const commands = args.passthroughArguments ? args._ : args._.concat(additionalArguments);
if (!commands.length) {
    program.showHelp();
    process.exit();
}
(0, index_1.concurrently)(commands.map((command, index) => ({
    command: String(command),
    name: names[index],
})), {
    handleInput: args.handleInput,
    defaultInputTarget: args.defaultInputTarget,
    killOthersOn: args.killOthers
        ? ['success', 'failure']
        : args.killOthersOnFail
            ? ['failure']
            : [],
    killSignal: args.killSignal,
    killTimeout: args.killTimeout,
    maxProcesses: args.maxProcesses,
    raw: args.raw,
    hide: args.hide.split(','),
    group: args.group,
    prefix: args.prefix,
    prefixColors: args.prefixColors.split(','),
    prefixLength: args.prefixLength,
    padPrefix: args.padPrefix,
    restartDelay: args.restartAfter === 'exponential' ? 'exponential' : Number(args.restartAfter),
    restartTries: args.restartTries,
    successCondition: args.success,
    timestampFormat: args.timestampFormat,
    timings: args.timings,
    teardown: args.teardown,
    additionalArguments: args.passthroughArguments ? additionalArguments : undefined,
}).result.then(() => process.exit(0), () => process.exit(1));
