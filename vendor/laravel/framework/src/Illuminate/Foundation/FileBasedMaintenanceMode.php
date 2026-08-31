<?php

namespace Illuminate\Foundation;

use Illuminate\Contracts\Foundation\MaintenanceMode as MaintenanceModeContract;

class FileBasedMaintenanceMode implements MaintenanceModeContract
{
    /**
     * Take the application down for maintenance.
     *
     * @param  array  $payload
     * @return void
     */
    public function activate(array $payload): void
    {
        file_put_contents(
            $this->path(),
            json_encode($payload, JSON_PRETTY_PRINT)
        );
    }

    /**
     * Take the application out of maintenance.
     *
     * @return void
     */
    public function deactivate(): void
    {
        if ($this->active()) {
            unlink($this->path());
        }
    }

    /**
     * Determine if the application is currently down for maintenance.
     *
     * @return bool
     */
    public function active(): bool
    {
        return file_exists($this->path());
    }

    /**
     * Get the data array which was provided when the application was placed into maintenance.
     *
     * @return array
     */
    public function data(): array
    {
        return json_decode(file_get_contents($this->path()), true);
    }

    /**
     * Get the path where the file is stored that signals that the application is down for maintenance.
     *
     * @return string
     */
    protected function path(): string
    {
        return storage_path('framework/down');
    }
}
