<?php

namespace Tests\Feature\Queue;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeDebounced;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class DebouncedJobIntegrationTest extends TestCase
{
    public function test_queue_fake_captures_debounced_job_dispatches(): void
    {
        Queue::fake();

        dispatch(new FakeableDebouncedJob);

        Queue::assertPushed(FakeableDebouncedJob::class);
    }

    public function test_bus_fake_captures_debounced_job_dispatches(): void
    {
        Bus::fake();

        FakeableDebouncedJob::dispatch();

        Bus::assertDispatched(FakeableDebouncedJob::class);
    }

    public function test_sync_driver_executes_debounced_job_immediately(): void
    {
        config(['queue.default' => 'sync']);

        SyncDebouncedJob::$handled = false;

        dispatch(new SyncDebouncedJob);

        $this->assertTrue(SyncDebouncedJob::$handled);
    }
}

class FakeableDebouncedJob implements ShouldBeDebounced, ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public string $debounceOwner = '';

    public function debounceId(): string
    {
        return 'fakeable-test';
    }

    public function debounceFor(): int
    {
        return 30;
    }

    public function handle(): void {}
}

class SyncDebouncedJob implements ShouldBeDebounced, ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public static bool $handled = false;

    public string $debounceOwner = '';

    public function debounceId(): string
    {
        return 'sync-test';
    }

    public function debounceFor(): int
    {
        return 0;
    }

    public function handle(): void
    {
        static::$handled = true;
    }
}
