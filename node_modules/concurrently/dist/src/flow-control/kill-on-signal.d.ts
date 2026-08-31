import EventEmitter from 'events';
import { Command } from '../command';
import { FlowController } from './flow-controller';
/**
 * Watches the main concurrently process for signals and sends the same signal down to each spawned
 * command.
 */
export declare class KillOnSignal implements FlowController {
    private readonly process;
    private readonly abortController?;
    constructor({ process, abortController, }: {
        process: EventEmitter;
        abortController?: AbortController;
    });
    handle(commands: Command[]): {
        commands: Command[];
        onFinish: () => void;
    };
}
