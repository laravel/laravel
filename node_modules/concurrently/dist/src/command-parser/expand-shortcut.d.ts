import { CommandInfo } from '../command';
import { CommandParser } from './command-parser';
/**
 * Expands shortcuts according to the following table:
 *
 * | Syntax          | Expands to            |
 * | --------------- | --------------------- |
 * | `npm:<script>`  | `npm run <script>`    |
 * | `pnpm:<script>` | `pnpm run <script>`   |
 * | `yarn:<script>` | `yarn run <script>`   |
 * | `bun:<script>`  | `bun run <script>`    |
 * | `node:<script>` | `node --run <script>` |
 * | `deno:<script>` | `deno task <script>`  |
 */
export declare class ExpandShortcut implements CommandParser {
    parse(commandInfo: CommandInfo): CommandInfo;
}
