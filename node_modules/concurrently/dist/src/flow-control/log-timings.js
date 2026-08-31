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
Object.defineProperty(exports, "__esModule", { value: true });
exports.LogTimings = void 0;
const assert = __importStar(require("assert"));
const Rx = __importStar(require("rxjs"));
const operators_1 = require("rxjs/operators");
const date_format_1 = require("../date-format");
const defaults = __importStar(require("../defaults"));
/**
 * Logs timing information about commands as they start/stop and then a summary when all commands finish.
 */
class LogTimings {
    static mapCloseEventToTimingInfo({ command, timings, killed, exitCode, }) {
        const readableDurationMs = (timings.endDate.getTime() - timings.startDate.getTime()).toLocaleString();
        return {
            name: command.name,
            duration: readableDurationMs,
            'exit code': exitCode,
            killed,
            command: command.command,
        };
    }
    logger;
    dateFormatter;
    constructor({ logger, timestampFormat = defaults.timestampFormat, }) {
        this.logger = logger;
        this.dateFormatter = new date_format_1.DateFormatter(timestampFormat);
    }
    printExitInfoTimingTable(exitInfos) {
        assert.ok(this.logger);
        const exitInfoTable = exitInfos
            .sort((a, b) => b.timings.durationSeconds - a.timings.durationSeconds)
            .map(LogTimings.mapCloseEventToTimingInfo);
        this.logger.logGlobalEvent('Timings:');
        this.logger.logTable(exitInfoTable);
        return exitInfos;
    }
    handle(commands) {
        const { logger } = this;
        if (!logger) {
            return { commands };
        }
        // individual process timings
        commands.forEach((command) => {
            command.timer.subscribe(({ startDate, endDate }) => {
                if (!endDate) {
                    const formattedStartDate = this.dateFormatter.format(startDate);
                    logger.logCommandEvent(`${command.command} started at ${formattedStartDate}`, command);
                }
                else {
                    const durationMs = endDate.getTime() - startDate.getTime();
                    const formattedEndDate = this.dateFormatter.format(endDate);
                    logger.logCommandEvent(`${command.command} stopped at ${formattedEndDate} after ${durationMs.toLocaleString()}ms`, command);
                }
            });
        });
        // overall summary timings
        const closeStreams = commands.map((command) => command.close);
        const finished = new Rx.Subject();
        const allProcessesClosed = Rx.merge(...closeStreams).pipe((0, operators_1.bufferCount)(closeStreams.length), (0, operators_1.take)(1), (0, operators_1.combineLatestWith)(finished));
        allProcessesClosed.subscribe(([exitInfos]) => this.printExitInfoTimingTable(exitInfos));
        return { commands, onFinish: () => finished.next() };
    }
}
exports.LogTimings = LogTimings;
