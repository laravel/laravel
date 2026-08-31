import * as Rx from 'rxjs';
import { Writable } from 'stream';
import { Command } from './command';
/**
 * Class responsible for actually writing output onto a writable stream.
 */
export declare class OutputWriter {
    private readonly outputStream;
    private readonly group;
    readonly buffers: string[][];
    activeCommandIndex: number;
    readonly error: Rx.Observable<unknown>;
    private get errored();
    constructor({ outputStream, group, commands, }: {
        outputStream: Writable;
        group: boolean;
        commands: Command[];
    });
    private ensureWritable;
    write(command: Command | undefined, text: string): void;
    private flushBuffer;
}
