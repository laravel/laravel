import { CommandInfo } from '../command';
import { CommandParser } from './command-parser';
/**
 * Replace placeholders with additional arguments.
 */
export declare class ExpandArguments implements CommandParser {
    private readonly additionalArguments;
    constructor(additionalArguments: string[]);
    parse(commandInfo: CommandInfo): {
        command: string;
        name: string;
        env?: Record<string, unknown>;
        cwd?: string;
        prefixColor?: string;
        ipc?: number;
        raw?: boolean;
    };
}
