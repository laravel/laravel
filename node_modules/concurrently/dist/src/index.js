"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
exports.RestartProcess = exports.LogTimings = exports.LogOutput = exports.LogExit = exports.LogError = exports.KillOthers = exports.KillOnSignal = exports.InputHandler = exports.Command = exports.Logger = exports.createConcurrently = void 0;
exports.concurrently = concurrently;
const assert_1 = require("./assert");
const command_1 = require("./command");
Object.defineProperty(exports, "Command", { enumerable: true, get: function () { return command_1.Command; } });
const concurrently_1 = require("./concurrently");
Object.defineProperty(exports, "createConcurrently", { enumerable: true, get: function () { return concurrently_1.concurrently; } });
const input_handler_1 = require("./flow-control/input-handler");
Object.defineProperty(exports, "InputHandler", { enumerable: true, get: function () { return input_handler_1.InputHandler; } });
const kill_on_signal_1 = require("./flow-control/kill-on-signal");
Object.defineProperty(exports, "KillOnSignal", { enumerable: true, get: function () { return kill_on_signal_1.KillOnSignal; } });
const kill_others_1 = require("./flow-control/kill-others");
Object.defineProperty(exports, "KillOthers", { enumerable: true, get: function () { return kill_others_1.KillOthers; } });
const log_error_1 = require("./flow-control/log-error");
Object.defineProperty(exports, "LogError", { enumerable: true, get: function () { return log_error_1.LogError; } });
const log_exit_1 = require("./flow-control/log-exit");
Object.defineProperty(exports, "LogExit", { enumerable: true, get: function () { return log_exit_1.LogExit; } });
const log_output_1 = require("./flow-control/log-output");
Object.defineProperty(exports, "LogOutput", { enumerable: true, get: function () { return log_output_1.LogOutput; } });
const log_timings_1 = require("./flow-control/log-timings");
Object.defineProperty(exports, "LogTimings", { enumerable: true, get: function () { return log_timings_1.LogTimings; } });
const logger_padding_1 = require("./flow-control/logger-padding");
const output_error_handler_1 = require("./flow-control/output-error-handler");
const restart_process_1 = require("./flow-control/restart-process");
Object.defineProperty(exports, "RestartProcess", { enumerable: true, get: function () { return restart_process_1.RestartProcess; } });
const teardown_1 = require("./flow-control/teardown");
const logger_1 = require("./logger");
Object.defineProperty(exports, "Logger", { enumerable: true, get: function () { return logger_1.Logger; } });
const utils_1 = require("./utils");
function concurrently(commands, options = {}) {
    (0, assert_1.assertDeprecated)(options.killOthers === undefined, 'killOthers', 'Use killOthersOn instead.');
    // To avoid empty strings from hiding the output of commands that don't have a name,
    // keep in the list of commands to hide only strings with some length.
    // This might happen through the CLI when no `--hide` argument is specified, for example.
    const hide = (0, utils_1.castArray)(options.hide).filter((id) => id || id === 0);
    const logger = options.logger ||
        new logger_1.Logger({
            hide,
            prefixFormat: options.prefix,
            commandLength: options.prefixLength,
            raw: options.raw,
            timestampFormat: options.timestampFormat,
        });
    if (options.prefixColors === false) {
        logger.toggleColors(false);
    }
    const abortController = new AbortController();
    const outputStream = options.outputStream || process.stdout;
    return (0, concurrently_1.concurrently)(commands, {
        maxProcesses: options.maxProcesses,
        raw: options.raw,
        successCondition: options.successCondition,
        cwd: options.cwd,
        hide,
        logger,
        outputStream,
        group: options.group,
        abortSignal: abortController.signal,
        controllers: [
            // LoggerPadding needs to run before any other controllers that might output something
            ...(options.padPrefix ? [new logger_padding_1.LoggerPadding({ logger })] : []),
            new log_error_1.LogError({ logger }),
            new log_output_1.LogOutput({ logger }),
            new log_exit_1.LogExit({ logger }),
            new input_handler_1.InputHandler({
                logger,
                defaultInputTarget: options.defaultInputTarget,
                inputStream: options.inputStream || (options.handleInput ? process.stdin : undefined),
                pauseInputStreamOnFinish: options.pauseInputStreamOnFinish,
            }),
            new kill_on_signal_1.KillOnSignal({ process, abortController }),
            new restart_process_1.RestartProcess({
                logger,
                delay: options.restartDelay,
                tries: options.restartTries,
            }),
            new kill_others_1.KillOthers({
                logger,
                conditions: options.killOthersOn || options.killOthers || [],
                timeoutMs: options.killTimeout,
                killSignal: options.killSignal,
                abortController,
            }),
            new output_error_handler_1.OutputErrorHandler({ abortController, outputStream }),
            new log_timings_1.LogTimings({
                logger: options.timings ? logger : undefined,
                timestampFormat: options.timestampFormat,
            }),
            new teardown_1.Teardown({ logger, spawn: options.spawn, commands: options.teardown || [] }),
        ],
        prefixColors: options.prefixColors || [],
        additionalArguments: options.additionalArguments,
    });
}
