import { Command, SpawnCommand } from '../command';
import { Logger } from '../logger';
import { FlowController } from './flow-controller';
export declare class Teardown implements FlowController {
    private readonly logger;
    private readonly spawn;
    private readonly teardown;
    constructor({ logger, spawn, commands, }: {
        logger: Logger;
        /**
         * Which function to use to spawn commands.
         * Defaults to the same used by the rest of concurrently.
         */
        spawn?: SpawnCommand;
        commands: readonly string[];
    });
    handle(commands: Command[]): {
        commands: Command[];
        onFinish: () => Promise<void>;
    };
}
