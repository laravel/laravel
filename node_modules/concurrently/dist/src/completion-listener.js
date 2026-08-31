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
exports.CompletionListener = void 0;
const Rx = __importStar(require("rxjs"));
const operators_1 = require("rxjs/operators");
/**
 * Provides logic to determine whether lists of commands ran successfully.
 */
class CompletionListener {
    successCondition;
    scheduler;
    constructor({ successCondition = 'all', scheduler, }) {
        this.successCondition = successCondition;
        this.scheduler = scheduler;
    }
    isSuccess(events) {
        if (!events.length) {
            // When every command was aborted, consider a success.
            return true;
        }
        if (this.successCondition === 'first') {
            return events[0].exitCode === 0;
        }
        else if (this.successCondition === 'last') {
            return events[events.length - 1].exitCode === 0;
        }
        const commandSyntaxMatch = this.successCondition.match(/^!?command-(.+)$/);
        if (commandSyntaxMatch == null) {
            // If not a `command-` syntax, then it's an 'all' condition or it's treated as such.
            return events.every(({ exitCode }) => exitCode === 0);
        }
        // Check `command-` syntax condition.
        // Note that a command's `name` is not necessarily unique,
        // in which case all of them must meet the success condition.
        const nameOrIndex = commandSyntaxMatch[1];
        const targetCommandsEvents = events.filter(({ command, index }) => command.name === nameOrIndex || index === Number(nameOrIndex));
        if (this.successCondition.startsWith('!')) {
            // All commands except the specified ones must exit successfully
            return events.every((event) => targetCommandsEvents.includes(event) || event.exitCode === 0);
        }
        // Only the specified commands must exit successfully
        return (targetCommandsEvents.length > 0 &&
            targetCommandsEvents.every((event) => event.exitCode === 0));
    }
    /**
     * Given a list of commands, wait for all of them to exit and then evaluate their exit codes.
     *
     * @returns A Promise that resolves if the success condition is met, or rejects otherwise.
     *          In either case, the value is a list of close events for commands that spawned.
     *          Commands that didn't spawn are filtered out.
     */
    listen(commands, abortSignal) {
        if (!commands.length) {
            return Promise.resolve([]);
        }
        const abort = abortSignal &&
            Rx.fromEvent(abortSignal, 'abort', { once: true }).pipe(
            // The abort signal must happen before commands are killed, otherwise new commands
            // might spawn. Because of this, it's not be possible to capture the close events
            // without an immediate delay
            (0, operators_1.delay)(0, this.scheduler), (0, operators_1.map)(() => undefined), 
            // #502 - node might warn of too many active listeners on this object if it isn't shared,
            // as each command subscribes to abort event over and over
            (0, operators_1.share)());
        const closeStreams = commands.map((command) => abort
            ? // Commands that have been started must close.
                Rx.race(command.close, abort.pipe((0, operators_1.filter)(() => command.state === 'stopped')))
            : command.close);
        return Rx.lastValueFrom(Rx.combineLatest(closeStreams).pipe((0, operators_1.filter)(() => commands.every((command) => command.state !== 'started')), (0, operators_1.map)((events) => events
            // Filter out aborts, since they cannot be sorted and are considered success condition anyways
            .filter((event) => event != null)
            // Sort according to exit time
            .sort((first, second) => first.timings.endDate.getTime() - second.timings.endDate.getTime())), (0, operators_1.switchMap)((events) => this.isSuccess(events)
            ? this.emitWithScheduler(Rx.of(events))
            : this.emitWithScheduler(Rx.throwError(() => events))), (0, operators_1.take)(1)));
    }
    emitWithScheduler(input) {
        return this.scheduler ? input.pipe(Rx.observeOn(this.scheduler)) : input;
    }
}
exports.CompletionListener = CompletionListener;
