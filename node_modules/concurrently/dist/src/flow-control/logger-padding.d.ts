import { Command } from '../command';
import { Logger } from '../logger';
import { FlowController } from './flow-controller';
export declare class LoggerPadding implements FlowController {
    private readonly logger;
    constructor({ logger }: {
        logger: Logger;
    });
    handle(commands: Command[]): {
        commands: Command[];
        onFinish: () => void;
    };
}
