<?php

namespace Illuminate\Queue;

enum WorkerStopReason: string
{
    case Interrupted = 'interrupted';
    case LostConnection = 'lost_connection';
    case MaxJobsExceeded = 'max_jobs';
    case MaxMemoryExceeded = 'memory';
    case MaxTimeExceeded = 'max_time';
    case QueueEmpty = 'empty';
    case QueueEmptyFor = 'empty_for';
    case ReceivedRestartSignal = 'restart_signal';
    case TimedOut = 'timed_out';
}
