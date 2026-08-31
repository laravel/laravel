"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
exports.OutputErrorHandler = void 0;
const observables_1 = require("../observables");
/**
 * Kills processes and aborts further command spawning on output stream error (namely, SIGPIPE).
 */
class OutputErrorHandler {
    outputStream;
    abortController;
    constructor({ abortController, outputStream, }) {
        this.abortController = abortController;
        this.outputStream = outputStream;
    }
    handle(commands) {
        const subscription = (0, observables_1.fromSharedEvent)(this.outputStream, 'error').subscribe(() => {
            commands.forEach((command) => command.kill());
            // Avoid further commands from spawning, e.g. if `RestartProcess` is used.
            this.abortController.abort();
        });
        return {
            commands,
            onFinish: () => subscription.unsubscribe(),
        };
    }
}
exports.OutputErrorHandler = OutputErrorHandler;
