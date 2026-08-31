"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
exports.KillOnSignal = void 0;
const operators_1 = require("rxjs/operators");
const SIGNALS = ['SIGINT', 'SIGTERM', 'SIGHUP'];
/**
 * Watches the main concurrently process for signals and sends the same signal down to each spawned
 * command.
 */
class KillOnSignal {
    process;
    abortController;
    constructor({ process, abortController, }) {
        this.process = process;
        this.abortController = abortController;
    }
    handle(commands) {
        let caughtSignal;
        const signalListener = (signal) => {
            caughtSignal = signal;
            this.abortController?.abort();
            commands.forEach((command) => command.kill(signal));
        };
        SIGNALS.forEach((signal) => this.process.on(signal, signalListener));
        return {
            commands: commands.map((command) => {
                const closeStream = command.close.pipe((0, operators_1.map)((exitInfo) => {
                    const exitCode = caughtSignal === 'SIGINT' ? 0 : exitInfo.exitCode;
                    return { ...exitInfo, exitCode };
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
            onFinish: () => {
                // Avoids MaxListenersExceededWarning when running programmatically
                SIGNALS.forEach((signal) => this.process.off(signal, signalListener));
            },
        };
    }
}
exports.KillOnSignal = KillOnSignal;
