import { ChildProcess, SpawnOptions } from 'child_process';
import supportsColor from 'supports-color';
/**
 * Spawns a command using `cmd.exe` on Windows, or `/bin/sh` elsewhere.
 */
export declare function spawn(command: string, options: SpawnOptions, spawn?: (command: string, args: string[], options: SpawnOptions) => ChildProcess, process?: Pick<NodeJS.Process, 'platform'>): ChildProcess;
export declare const getSpawnOpts: ({ colorSupport, cwd, process, ipc, stdio, env, }: {
    /**
     * What the color support of the spawned processes should be.
     * If set to `false`, then no colors should be output.
     *
     * Defaults to whatever the terminal's stdout support is.
     */
    colorSupport?: Pick<supportsColor.supportsColor.Level, "level"> | false;
    /**
     * The NodeJS process.
     */
    process?: Pick<NodeJS.Process, "cwd" | "platform" | "env">;
    /**
     * A custom working directory to spawn processes in.
     * Defaults to `process.cwd()`.
     */
    cwd?: string;
    /**
     * The file descriptor number at which the channel for inter-process communication
     * should be set up.
     */
    ipc?: number;
    /**
     * Which stdio mode to use. Raw implies inheriting the parent process' stdio.
     *
     * - `normal`: all of stdout, stderr and stdin are piped
     * - `hidden`: stdin is piped, stdout/stderr outputs are ignored
     * - `raw`: all of stdout, stderr and stdin are inherited from the main process
     *
     * Defaults to `normal`.
     */
    stdio?: "normal" | "hidden" | "raw";
    /**
     * Map of custom environment variables to include in the spawn options.
     */
    env?: Record<string, unknown>;
}) => SpawnOptions;
