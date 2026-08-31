"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
exports.ExpandShortcut = void 0;
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
class ExpandShortcut {
    parse(commandInfo) {
        const [, prefix, script, args] = /^(npm|yarn|pnpm|bun|node|deno):(\S+)(.*)/.exec(commandInfo.command) || [];
        if (!script) {
            return commandInfo;
        }
        let command;
        if (prefix === 'node') {
            command = 'node --run';
        }
        else if (prefix === 'deno') {
            command = 'deno task';
        }
        else {
            command = `${prefix} run`;
        }
        return {
            ...commandInfo,
            name: commandInfo.name || script,
            command: `${command} ${script}${args}`,
        };
    }
}
exports.ExpandShortcut = ExpandShortcut;
