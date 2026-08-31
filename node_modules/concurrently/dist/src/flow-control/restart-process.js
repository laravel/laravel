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
exports.RestartProcess = void 0;
const Rx = __importStar(require("rxjs"));
const operators_1 = require("rxjs/operators");
const defaults = __importStar(require("../defaults"));
/**
 * Restarts commands that fail up to a defined number of times.
 */
class RestartProcess {
    logger;
    scheduler;
    delay;
    tries;
    constructor({ delay, tries, logger, scheduler, }) {
        this.logger = logger;
        this.delay = delay ?? 0;
        this.tries = tries != null ? +tries : defaults.restartTries;
        this.tries = this.tries < 0 ? Infinity : this.tries;
        this.scheduler = scheduler;
    }
    handle(commands) {
        if (this.tries === 0) {
            return { commands };
        }
        const delayOperator = (0, operators_1.delayWhen)((_, index) => {
            const { delay } = this;
            const value = delay === 'exponential' ? Math.pow(2, index) * 1000 : delay;
            return Rx.timer(value, this.scheduler);
        });
        commands
            .map((command) => command.close.pipe((0, operators_1.take)(this.tries), (0, operators_1.takeWhile)(({ exitCode }) => exitCode !== 0)))
            .forEach((failure, index) => Rx.merge(
        // Delay the emission (so that the restarts happen on time),
        // explicitly telling the subscriber that a restart is needed
        failure.pipe(delayOperator, (0, operators_1.map)(() => true)), 
        // Skip the first N emissions (as these would be duplicates of the above),
        // meaning it will be empty because of success, or failed all N times,
        // and no more restarts should be attempted.
        failure.pipe((0, operators_1.skip)(this.tries), (0, operators_1.map)(() => false), (0, operators_1.defaultIfEmpty)(false))).subscribe((restart) => {
            const command = commands[index];
            if (restart) {
                this.logger.logCommandEvent(`${command.command} restarted`, command);
                command.start();
            }
        }));
        return {
            commands: commands.map((command) => {
                const closeStream = command.close.pipe((0, operators_1.filter)(({ exitCode }, emission) => {
                    // We let all success codes pass, and failures only after restarting won't happen again
                    return exitCode === 0 || emission >= this.tries;
                }));
                // Return a proxy so that mutations happen on the original Command object.
                // If either `Object.assign()` or `Object.create()` were used, it'd be hard to
                // reflect the mutations on Command objects referenced by previous flow controllers.
                return new Proxy(command, {
                    get(target, prop) {
                        return prop === 'close' ? closeStream : target[prop];
                    },
                });
            }),
        };
    }
}
exports.RestartProcess = RestartProcess;
