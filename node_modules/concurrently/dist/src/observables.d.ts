import EventEmitter from 'events';
import { Observable } from 'rxjs';
/**
 * Creates an observable for a specific event of an `EventEmitter` instance.
 *
 * The underlying event listener is set up only once across the application for that event emitter/name pair.
 */
export declare function fromSharedEvent(emitter: EventEmitter, event: string): Observable<unknown>;
