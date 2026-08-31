"use strict";
var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : { "default": mod };
};
Object.defineProperty(exports, "__esModule", { value: true });
exports.ExpandWildcard = void 0;
const fs_1 = __importDefault(require("fs"));
const jsonc_1 = __importDefault(require("../jsonc"));
const utils_1 = require("../utils");
// Matches a negative filter surrounded by '(!' and ')'.
const OMISSION = /\(!([^)]+)\)/;
/**
 * Finds wildcards in 'npm/yarn/pnpm/bun run', 'node --run' and 'deno task'
 * commands and replaces them with all matching scripts in the NodeJS and Deno
 * configuration files of the current directory.
 */
class ExpandWildcard {
    readDeno;
    readPackage;
    static readDeno() {
        try {
            let json = '{}';
            if (fs_1.default.existsSync('deno.json')) {
                json = fs_1.default.readFileSync('deno.json', { encoding: 'utf-8' });
            }
            else if (fs_1.default.existsSync('deno.jsonc')) {
                json = fs_1.default.readFileSync('deno.jsonc', { encoding: 'utf-8' });
            }
            return jsonc_1.default.parse(json);
        }
        catch {
            return {};
        }
    }
    static readPackage() {
        try {
            const json = fs_1.default.readFileSync('package.json', { encoding: 'utf-8' });
            return JSON.parse(json);
        }
        catch {
            return {};
        }
    }
    packageScripts;
    denoTasks;
    constructor(readDeno = ExpandWildcard.readDeno, readPackage = ExpandWildcard.readPackage) {
        this.readDeno = readDeno;
        this.readPackage = readPackage;
    }
    relevantScripts(command) {
        if (!this.packageScripts) {
            this.packageScripts = Object.keys(this.readPackage().scripts || {});
        }
        if (command === 'deno task') {
            if (!this.denoTasks) {
                // If Deno tries to run a task that doesn't exist,
                // it can fall back to running a script with the same name.
                // Therefore, the actual list of tasks is the union of the tasks and scripts.
                this.denoTasks = [
                    ...Object.keys(this.readDeno().tasks || {}),
                    ...this.packageScripts,
                ];
            }
            return this.denoTasks;
        }
        return this.packageScripts;
    }
    parse(commandInfo) {
        // We expect one of the following patterns:
        // - <npm|yarn|pnpm|bun> run <script> [args]
        // - node --run <script> [args]
        // - deno task <script> [args]
        const [, command, scriptGlob, args] = /((?:npm|yarn|pnpm|bun) (?:run)|node --run|deno task) (\S+)([^&]*)/.exec(commandInfo.command) || [];
        const wildcardPosition = (scriptGlob || '').indexOf('*');
        // If the regex didn't match an npm script, or it has no wildcard,
        // then we have nothing to do here
        if (wildcardPosition === -1) {
            return commandInfo;
        }
        const [, omission] = OMISSION.exec(scriptGlob) || [];
        const scriptGlobSansOmission = scriptGlob.replace(OMISSION, '');
        const preWildcard = (0, utils_1.escapeRegExp)(scriptGlobSansOmission.slice(0, wildcardPosition));
        const postWildcard = (0, utils_1.escapeRegExp)(scriptGlobSansOmission.slice(wildcardPosition + 1));
        const wildcardRegex = new RegExp(`^${preWildcard}(.*?)${postWildcard}$`);
        // If 'commandInfo.name' doesn't match 'scriptGlob', this means a custom name
        // has been specified and thus becomes the prefix (as described in the README).
        const prefix = commandInfo.name !== scriptGlob ? commandInfo.name : '';
        return this.relevantScripts(command)
            .map((script) => {
            if (omission && RegExp(omission).test(script)) {
                return;
            }
            const result = wildcardRegex.exec(script);
            const match = result?.[1];
            if (match !== undefined) {
                return {
                    ...commandInfo,
                    command: `${command} ${script}${args}`,
                    // Will use an empty command name if no prefix has been specified and
                    // the wildcard match is empty, e.g. if `npm:watch-*` matches `npm run watch-`.
                    name: prefix + match,
                };
            }
        })
            .filter((commandInfo) => !!commandInfo);
    }
}
exports.ExpandWildcard = ExpandWildcard;
