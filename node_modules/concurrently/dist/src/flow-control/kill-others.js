"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
exports.KillOthers = void 0;
const operators_1 = require("rxjs/operators");
const command_1 = require("../command");
const utils_1 = require("../utils");
/**
 * Sends a SIGTERM signal to all commands when one of the commands exits with a matching condition.
 */
class KillOthers {
    logger;
    abortController;
    conditions;
    killSignal;
    timeoutMs;
    constructor({ logger, abortController, conditions, killSignal, timeoutMs, }) {
        this.logger = logger;
        this.abortController = abortController;
        this.conditions = (0, utils_1.castArray)(conditions);
        this.killSignal = killSignal;
        this.timeoutMs = timeoutMs;
    }
    handle(commands) {
        const conditions = this.conditions.filter((condition) => condition === 'failure' || condition === 'success');
        if (!conditions.length) {
            return { commands };
        }
        const closeStates = commands.map((command) => command.close.pipe((0, operators_1.map)(({ exitCode }) => exitCode === 0 ? 'success' : 'failure'), (0, operators_1.filter)((state) => conditions.includes(state))));
        closeStates.forEach((closeState) => closeState.subscribe(() => {
            this.abortController?.abort();
            const killableCommands = commands.filter((command) => command_1.Command.canKill(command));
            if (killableCommands.length) {
                this.logger.logGlobalEvent(`Sending ${this.killSignal || 'SIGTERM'} to other processes..`);
                killableCommands.forEach((command) => command.kill(this.killSignal));
                this.maybeForceKill(killableCommands);
            }
        }));
        return { commands };
    }
    maybeForceKill(commands) {
        // No need to force kill when the signal already is SIGKILL.
        if (!this.timeoutMs || this.killSignal === 'SIGKILL') {
            return;
        }
        setTimeout(() => {
            const killableCommands = commands.filter((command) => command_1.Command.canKill(command));
            if (killableCommands) {
                this.logger.logGlobalEvent(`Sending SIGKILL to ${killableCommands.length} processes..`);
                killableCommands.forEach((command) => command.kill('SIGKILL'));
            }
        }, this.timeoutMs);
    }
}
exports.KillOthers = KillOthers;
