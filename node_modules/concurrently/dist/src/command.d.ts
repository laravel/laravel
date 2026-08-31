import { ChildProcess as BaseChildProcess, MessageOptions, SendHandle, SpawnOptions } from 'child_process';
import * as Rx from 'rxjs';
import { EventEmitter, Writable } from 'stream';
/**
 * Identifier for a command; if string, it's the command's name, if number, it's the index.
 */
export type CommandIdentifier = string | number;
export interface CommandInfo {
    /**
     * Command's name.
     */
    name: string;
    /**
     * Which command line the command has.
     */
    command: string;
    /**
     * Which environment variables should the spawned process have.
     */
    env?: Record<string, unknown>;
    /**
     * The current working directory of the process when spawned.
     */
    cwd?: string;
    /**
     * Color to use on prefix of the command.
     */
    prefixColor?: string;
    /**
     * Whether sending of messages to/from this command (also known as "inter-process communication")
     * should be enabled, and using which file descriptor number.
     *
     * If set, must be > 2.
     */
    ipc?: number;
    /**
     * Output command in raw format.
     */
    raw?: boolean;
}
export interface CloseEvent {
    command: CommandInfo;
    /**
     * The command's index among all commands ran.
     */
    index: number;
    /**
     * Whether the command exited because it was killed.
     */
    killed: boolean;
    /**
     * The exit code or signal for the command.
     */
    exitCode: string | number;
    timings: {
        startDate: Date;
        endDate: Date;
        durationSeconds: number;
    };
}
export interface TimerEvent {
    startDate: Date;
    endDate?: Date;
}
export interface MessageEvent {
    message: object;
    handle?: SendHandle;
}
interface OutgoingMessageEvent extends MessageEvent {
    options?: MessageOptions;
    onSent(error?: unknown): void;
}
/**
 * Subtype of NodeJS's child_process including only what's actually needed for a command to work.
 */
export type ChildProcess = EventEmitter & Pick<BaseChildProcess, 'pid' | 'stdin' | 'stdout' | 'stderr' | 'send'>;
/**
 * Interface for a function that must kill the process with `pid`, optionally sending `signal` to it.
 */
export type KillProcess = (pid: number, signal?: string) => void;
/**
 * Interface for a function that spawns a command and returns its child process instance.
 */
export type SpawnCommand = (command: string, options: SpawnOptions) => ChildProcess;
/**
 * The state of a command.
 *
 * - `stopped`: command was never started
 * - `started`: command is currently running
 * - `errored`: command failed spawning
 * - `exited`: command is not running anymore, e.g. it received a close event
 */
type CommandState = 'stopped' | 'started' | 'errored' | 'exited';
export declare class Command implements CommandInfo {
    private readonly killProcess;
    private readonly spawn;
    private readonly spawnOpts;
    readonly index: number;
    /** @inheritdoc */
    readonly name: string;
    /** @inheritdoc */
    readonly command: string;
    /** @inheritdoc */
    readonly prefixColor?: string;
    /** @inheritdoc */
    readonly env: Record<string, unknown>;
    /** @inheritdoc */
    readonly cwd?: string;
    /** @inheritdoc */
    readonly ipc?: number;
    readonly close: Rx.Subject<CloseEvent>;
    readonly error: Rx.Subject<unknown>;
    readonly stdout: Rx.Subject<Buffer<ArrayBufferLike>>;
    readonly stderr: Rx.Subject<Buffer<ArrayBufferLike>>;
    readonly timer: Rx.Subject<TimerEvent>;
    /**
     * A stream of changes to the `#state` property.
     *
     * Note that the command never goes back to the `stopped` state, therefore it's not a value
     * that's emitted by this stream.
     */
    readonly stateChange: Rx.Subject<"started" | "errored" | "exited">;
    readonly messages: {
        incoming: Rx.Subject<MessageEvent>;
        outgoing: Rx.ReplaySubject<OutgoingMessageEvent>;
    };
    process?: ChildProcess;
    private subscriptions;
    stdin?: Writable;
    pid?: number;
    killed: boolean;
    exited: boolean;
    state: CommandState;
    constructor({ index, name, command, prefixColor, env, cwd, ipc }: CommandInfo & {
        index: number;
    }, spawnOpts: SpawnOptions, spawn: SpawnCommand, killProcess: KillProcess);
    /**
     * Starts this command, piping output, error and close events onto the corresponding observables.
     */
    start(): void;
    private changeState;
    private maybeSetupIPC;
    /**
     * Sends a message to the underlying process once it starts.
     *
     * @throws  If the command doesn't have an IPC channel enabled
     * @returns Promise that resolves when the message is sent,
     *          or rejects if it fails to deliver the message.
     */
    send(message: object, handle?: SendHandle, options?: MessageOptions): Promise<void>;
    /**
     * Kills this command, optionally specifying a signal to send to it.
     */
    kill(code?: string): void;
    private cleanUp;
    /**
     * Detects whether a command can be killed.
     *
     * Also works as a type guard on the input `command`.
     */
    static canKill(command: Command): command is Command & {
        pid: number;
        process: ChildProcess;
    };
}
export {};
