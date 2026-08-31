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
exports.Command = void 0;
const Rx = __importStar(require("rxjs"));
class Command {
    killProcess;
    spawn;
    spawnOpts;
    index;
    /** @inheritdoc */
    name;
    /** @inheritdoc */
    command;
    /** @inheritdoc */
    prefixColor;
    /** @inheritdoc */
    env;
    /** @inheritdoc */
    cwd;
    /** @inheritdoc */
    ipc;
    close = new Rx.Subject();
    error = new Rx.Subject();
    stdout = new Rx.Subject();
    stderr = new Rx.Subject();
    timer = new Rx.Subject();
    /**
     * A stream of changes to the `#state` property.
     *
     * Note that the command never goes back to the `stopped` state, therefore it's not a value
     * that's emitted by this stream.
     */
    stateChange = new Rx.Subject();
    messages = {
        incoming: new Rx.Subject(),
        outgoing: new Rx.ReplaySubject(),
    };
    process;
    // TODO: Should exit/error/stdio subscriptions be added here?
    subscriptions = [];
    stdin;
    pid;
    killed = false;
    exited = false;
    state = 'stopped';
    constructor({ index, name, command, prefixColor, env, cwd, ipc }, spawnOpts, spawn, killProcess) {
        this.index = index;
        this.name = name;
        this.command = command;
        this.prefixColor = prefixColor;
        this.env = env || {};
        this.cwd = cwd;
        this.ipc = ipc;
        this.killProcess = killProcess;
        this.spawn = spawn;
        this.spawnOpts = spawnOpts;
    }
    /**
     * Starts this command, piping output, error and close events onto the corresponding observables.
     */
    start() {
        const child = this.spawn(this.command, this.spawnOpts);
        this.changeState('started');
        this.process = child;
        this.pid = child.pid;
        const startDate = new Date(Date.now());
        const highResStartTime = process.hrtime();
        this.timer.next({ startDate });
        this.subscriptions = [...this.maybeSetupIPC(child)];
        Rx.fromEvent(child, 'error').subscribe((event) => {
            this.cleanUp();
            const endDate = new Date(Date.now());
            this.timer.next({ startDate, endDate });
            this.error.next(event);
            this.changeState('errored');
        });
        Rx.fromEvent(child, 'close')
            .pipe(Rx.map((event) => event))
            .subscribe(([exitCode, signal]) => {
            this.cleanUp();
            // Don't override error event
            if (this.state !== 'errored') {
                this.changeState('exited');
            }
            const endDate = new Date(Date.now());
            this.timer.next({ startDate, endDate });
            const [durationSeconds, durationNanoSeconds] = process.hrtime(highResStartTime);
            this.close.next({
                command: this,
                index: this.index,
                exitCode: exitCode ?? String(signal),
                killed: this.killed,
                timings: {
                    startDate,
                    endDate,
                    durationSeconds: durationSeconds + durationNanoSeconds / 1e9,
                },
            });
        });
        if (child.stdout) {
            pipeTo(Rx.fromEvent(child.stdout, 'data').pipe(Rx.map((event) => event)), this.stdout);
        }
        if (child.stderr) {
            pipeTo(Rx.fromEvent(child.stderr, 'data').pipe(Rx.map((event) => event)), this.stderr);
        }
        this.stdin = child.stdin || undefined;
    }
    changeState(state) {
        this.state = state;
        this.stateChange.next(state);
    }
    maybeSetupIPC(child) {
        if (!this.ipc) {
            return [];
        }
        return [
            pipeTo(Rx.fromEvent(child, 'message').pipe(Rx.map((event) => {
                const [message, handle] = event;
                return { message, handle };
            })), this.messages.incoming),
            this.messages.outgoing.subscribe((message) => {
                if (!child.send) {
                    return message.onSent(new Error('Command does not have an IPC channel'));
                }
                child.send(message.message, message.handle, message.options, (error) => {
                    message.onSent(error);
                });
            }),
        ];
    }
    /**
     * Sends a message to the underlying process once it starts.
     *
     * @throws  If the command doesn't have an IPC channel enabled
     * @returns Promise that resolves when the message is sent,
     *          or rejects if it fails to deliver the message.
     */
    send(message, handle, options) {
        if (this.ipc == null) {
            throw new Error('Command IPC is disabled');
        }
        return new Promise((resolve, reject) => {
            this.messages.outgoing.next({
                message,
                handle,
                options,
                onSent(error) {
                    if (error) {
                        reject(error);
                    }
                    else {
                        resolve();
                    }
                },
            });
        });
    }
    /**
     * Kills this command, optionally specifying a signal to send to it.
     */
    kill(code) {
        if (Command.canKill(this)) {
            this.killed = true;
            this.killProcess(this.pid, code);
        }
    }
    cleanUp() {
        this.subscriptions?.forEach((sub) => sub.unsubscribe());
        this.messages.outgoing = new Rx.ReplaySubject();
        this.process = undefined;
    }
    /**
     * Detects whether a command can be killed.
     *
     * Also works as a type guard on the input `command`.
     */
    static canKill(command) {
        return !!command.pid && !!command.process;
    }
}
exports.Command = Command;
/**
 * Pipes all events emitted by `stream` into `subject`.
 */
function pipeTo(stream, subject) {
    return stream.subscribe((event) => subject.next(event));
}
