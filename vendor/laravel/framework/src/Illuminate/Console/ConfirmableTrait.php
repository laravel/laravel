<?php

namespace Illuminate\Console;

use function Laravel\Prompts\confirm;

trait ConfirmableTrait
{
    /**
     * Confirm before proceeding with the action.
     *
     * This method only asks for confirmation in production.
     *
     * @template TReturn of bool = bool
     *
     * @param  string  $warning
     * @param  (\Closure(): TReturn)|TReturn|null  $callback
     * @return (TReturn is false ? true : bool)
     */
    public function confirmToProceed($warning = 'Application In Production', $callback = null)
    {
        $callback = is_null($callback) ? $this->getDefaultConfirmCallback() : $callback;

        $shouldConfirm = value($callback);

        if ($shouldConfirm) {
            if ($this->hasOption('force') && $this->option('force')) {
                return true;
            }

            $this->components->alert($warning);

            $confirmed = confirm('Are you sure you want to run this command?', default: false);

            if (! $confirmed) {
                $this->components->warn('Command cancelled.');

                return false;
            }
        }

        return true;
    }

    /**
     * Get the default confirmation callback.
     *
     * @return \Closure(): bool
     */
    protected function getDefaultConfirmCallback()
    {
        return function () {
            return $this->getLaravel()->environment() === 'production';
        };
    }
}
