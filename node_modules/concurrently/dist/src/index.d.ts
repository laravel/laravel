import { Readable } from 'stream';
import { CloseEvent, Command, CommandIdentifier, TimerEvent } from './command';
import { concurrently as createConcurrently, ConcurrentlyCommandInput, ConcurrentlyOptions as BaseConcurrentlyOptions, ConcurrentlyResult } from './concurrently';
import { FlowController } from './flow-control/flow-controller';
import { InputHandler } from './flow-control/input-handler';
import { KillOnSignal } from './flow-control/kill-on-signal';
import { KillOthers, ProcessCloseCondition } from './flow-control/kill-others';
import { LogError } from './flow-control/log-error';
import { LogExit } from './flow-control/log-exit';
import { LogOutput } from './flow-control/log-output';
import { LogTimings } from './flow-control/log-timings';
import { RestartDelay, RestartProcess } from './flow-control/restart-process';
import { Logger } from './logger';
export type ConcurrentlyOptions = Omit<BaseConcurrentlyOptions, 'abortSignal' | 'hide'> & {
    /**
     * Which command(s) should have their output hidden.
     */
    hide?: CommandIdentifier | CommandIdentifier[];
    /**
     * The prefix format to use when logging a command's output.
     * Defaults to the command's index.
     */
    prefix?: string;
    /**
     * How many characters should a prefix have at most, used when the prefix format is `command`.
     */
    prefixLength?: number;
    /**
     * Pads short prefixes with spaces so that all prefixes have the same length.
     */
    padPrefix?: boolean;
    /**
     * Whether output should be formatted to include prefixes and whether "event" logs will be logged.
     */
    raw?: boolean;
    /**
     * Date format used when logging date/time.
     * @see https://www.unicode.org/reports/tr35/tr35-dates.html#Date_Field_Symbol_Table
     */
    timestampFormat?: string;
    defaultInputTarget?: CommandIdentifier;
    inputStream?: Readable;
    handleInput?: boolean;
    pauseInputStreamOnFinish?: boolean;
    /**
     * How much time in milliseconds to wait before restarting a command.
     *
     * @see RestartProcess
     */
    restartDelay?: RestartDelay;
    /**
     * How many times commands should be restarted when they exit with a failure.
     *
     * @see RestartProcess
     */
    restartTries?: number;
    /**
     * @deprecated Use `killOthersOn` instead.
     * @see KillOthers
     */
    killOthers?: ProcessCloseCondition | ProcessCloseCondition[];
    /**
     * Once the first command exits with one of these statuses, kill other commands.
     * @see KillOthers
     */
    killOthersOn?: ProcessCloseCondition | ProcessCloseCondition[];
    /**
     * Signal to send to killed processes.
     */
    killSignal?: string;
    /**
     * How many milliseconds to wait before killing processes.
     */
    killTimeout?: number;
    /**
     * Whether to output timing information for processes.
     *
     * @see LogTimings
     */
    timings?: boolean;
    /**
     * Clean up command(s) to execute before exiting concurrently.
     * These won't be prefixed and don't affect concurrently's exit code.
     */
    teardown?: readonly string[];
    /**
     * List of additional arguments passed that will get replaced in each command.
     * If not defined, no argument replacing will happen.
     */
    additionalArguments?: string[];
};
export declare function concurrently(commands: ConcurrentlyCommandInput[], options?: Partial<ConcurrentlyOptions>): ConcurrentlyResult;
export { ConcurrentlyCommandInput, ConcurrentlyResult, createConcurrently, Logger };
export { CloseEvent, Command, CommandIdentifier, TimerEvent };
export { FlowController, InputHandler, KillOnSignal, KillOthers, LogError, LogExit, LogOutput, LogTimings, RestartProcess, };
