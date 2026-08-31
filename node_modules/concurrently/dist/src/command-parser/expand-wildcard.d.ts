import { CommandInfo } from '../command';
import { CommandParser } from './command-parser';
/**
 * Finds wildcards in 'npm/yarn/pnpm/bun run', 'node --run' and 'deno task'
 * commands and replaces them with all matching scripts in the NodeJS and Deno
 * configuration files of the current directory.
 */
export declare class ExpandWildcard implements CommandParser {
    private readonly readDeno;
    private readonly readPackage;
    static readDeno(): any;
    static readPackage(): any;
    private packageScripts?;
    private denoTasks?;
    constructor(readDeno?: typeof ExpandWildcard.readDeno, readPackage?: typeof ExpandWildcard.readPackage);
    private relevantScripts;
    parse(commandInfo: CommandInfo): CommandInfo | CommandInfo[];
}
