import { Writable } from 'stream';
import { Command } from '../command';
import { FlowController } from './flow-controller';
/**
 * Kills processes and aborts further command spawning on output stream error (namely, SIGPIPE).
 */
export declare class OutputErrorHandler implements FlowController {
    private readonly outputStream;
    private readonly abortController;
    constructor({ abortController, outputStream, }: {
        abortController: AbortController;
        outputStream: Writable;
    });
    handle(commands: Command[]): {
        commands: Command[];
        onFinish(): void;
    };
}
