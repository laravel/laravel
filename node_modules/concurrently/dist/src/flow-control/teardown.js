"use strict";
var __createBinding = (this && this.__createBinding) || (Object.create ? (function(o, m, k, k2) {
    if (k2 === undefined) k2 = k;
    var desc = Object.getOwnPropertyDescriptor(m, k);
    if (!desc || ("get" in desc ? !m.__esModule : desc.writable || desc.configurable)) {
      desc = { enumerable: true, get: function() { return m[k]; } };
    }
    Object.defineProperty(o, k2, desc);
}) : (function(o, m, k, k2) {
    if (k2 === undefined) k2 = k;
    o[k2] = m[k];
}));
var __setModuleDefault = (this && this.__setModuleDefault) || (Object.create ? (function(o, v) {
    Object.defineProperty(o, "default", { enumerable: true, value: v });
}) : function(o, v) {
    o["default"] = v;
});
var __importStar = (this && this.__importStar) || (function () {
    var ownKeys = function(o) {
        ownKeys = Object.getOwnPropertyNames || function (o) {
            var ar = [];
            for (var k in o) if (Object.prototype.hasOwnProperty.call(o, k)) ar[ar.length] = k;
            return ar;
        };
        return ownKeys(o);
    };
    return function (mod) {
        if (mod && mod.__esModule) return mod;
        var result = {};
        if (mod != null) for (var k = ownKeys(mod), i = 0; i < k.length; i++) if (k[i] !== "default") __createBinding(result, mod, k[i]);
        __setModuleDefault(result, mod);
        return result;
    };
})();
Object.defineProperty(exports, "__esModule", { value: true });
exports.Teardown = void 0;
const Rx = __importStar(require("rxjs"));
const spawn_1 = require("../spawn");
class Teardown {
    logger;
    spawn;
    teardown;
    constructor({ logger, spawn, commands, }) {
        this.logger = logger;
        this.spawn = spawn || spawn_1.spawn;
        this.teardown = commands;
    }
    handle(commands) {
        const { logger, teardown, spawn } = this;
        const onFinish = async () => {
            if (!teardown.length) {
                return;
            }
            for (const command of teardown) {
                logger.logGlobalEvent(`Running teardown command "${command}"`);
                const child = spawn(command, (0, spawn_1.getSpawnOpts)({ stdio: 'raw' }));
                const error = Rx.fromEvent(child, 'error');
                const close = Rx.fromEvent(child, 'close');
                try {
                    const [exitCode, signal] = await Promise.race([
                        Rx.firstValueFrom(error).then((event) => {
                            throw event;
                        }),
                        Rx.firstValueFrom(close).then((event) => event),
                    ]);
                    logger.logGlobalEvent(`Teardown command "${command}" exited with code ${exitCode ?? signal}`);
                    if (signal === 'SIGINT') {
                        break;
                    }
                }
                catch (error) {
                    const errorText = String(error instanceof Error ? error.stack || error : error);
                    logger.logGlobalEvent(`Teardown command "${command}" errored:`);
                    logger.logGlobalEvent(errorText);
                    return Promise.reject();
                }
            }
        };
        return { commands, onFinish };
    }
}
exports.Teardown = Teardown;
