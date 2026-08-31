"use strict";
var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : { "default": mod };
};
Object.defineProperty(exports, "__esModule", { value: true });
exports.getSpawnOpts = void 0;
exports.spawn = spawn;
const assert_1 = __importDefault(require("assert"));
const child_process_1 = require("child_process");
const supports_color_1 = __importDefault(require("supports-color"));
/**
 * Spawns a command using `cmd.exe` on Windows, or `/bin/sh` elsewhere.
 */
// Implementation based off of https://github.com/mmalecki/spawn-command/blob/v0.0.2-1/lib/spawn-command.js
function spawn(command, options, 
// For testing
spawn = child_process_1.spawn, process = global.process) {
    let file = '/bin/sh';
    let args = ['-c', command];
    if (process.platform === 'win32') {
        file = 'cmd.exe';
        args = ['/s', '/c', `"${command}"`];
        options.windowsVerbatimArguments = true;
    }
    return spawn(file, args, options);
}
const getSpawnOpts = ({ colorSupport = supports_color_1.default.stdout, cwd, process = global.process, ipc, stdio = 'normal', env = {}, }) => {
    const stdioValues = stdio === 'normal'
        ? ['pipe', 'pipe', 'pipe']
        : stdio === 'raw'
            ? ['inherit', 'inherit', 'inherit']
            : ['pipe', 'ignore', 'ignore'];
    if (ipc != null) {
        // Avoid overriding the stdout/stderr/stdin
        assert_1.default.ok(ipc > 2, '[concurrently] the IPC channel number should be > 2');
        stdioValues[ipc] = 'ipc';
    }
    return {
        cwd: cwd || process.cwd(),
        stdio: stdioValues,
        ...(process.platform.startsWith('win') && { detached: false }),
        env: {
            ...(colorSupport ? { FORCE_COLOR: colorSupport.level.toString() } : {}),
            ...process.env,
            ...env,
        },
    };
};
exports.getSpawnOpts = getSpawnOpts;
