"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
exports.fromSharedEvent = fromSharedEvent;
const rxjs_1 = require("rxjs");
const sharedEvents = new WeakMap();
/**
 * Creates an observable for a specific event of an `EventEmitter` instance.
 *
 * The underlying event listener is set up only once across the application for that event emitter/name pair.
 */
function fromSharedEvent(emitter, event) {
    let emitterEvents = sharedEvents.get(emitter);
    if (!emitterEvents) {
        emitterEvents = new Map();
        sharedEvents.set(emitter, emitterEvents);
    }
    let observable = emitterEvents.get(event);
    if (!observable) {
        observable = (0, rxjs_1.fromEvent)(emitter, event).pipe((0, rxjs_1.share)());
        emitterEvents.set(event, observable);
    }
    return observable;
}
