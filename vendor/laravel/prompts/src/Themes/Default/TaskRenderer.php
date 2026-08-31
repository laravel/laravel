<?php

namespace Laravel\Prompts\Themes\Default;

use Laravel\Prompts\Concerns\HasSpinner;
use Laravel\Prompts\Task;

class TaskRenderer extends Renderer
{
    use HasSpinner;

    /**
     * Render the task.
     */
    public function __invoke(Task $task): string
    {
        $maxWidth = $task->terminal()->cols() - 6;
        $labelMaxWidth = $maxWidth - 3;
        $leadPadding = str_repeat(' ', 3);
        $stableLineMaxWidth = $maxWidth - strlen($leadPadding) - 2; // symbol + space

        if ($task->static) {
            return $this->line(" {$this->cyan($this->staticFrame)} {$this->truncate($task->label, $maxWidth)}");
        }

        $task->interval = $this->interval;

        $stableMessages = array_slice($task->stableMessages, -$task->maxStableMessages);

        if ($task->finished && $task->keepSummary && count($stableMessages) > 0) {
            $this->line(" {$this->cyan('•')} {$this->truncate($task->label, $labelMaxWidth)}");

            foreach ($stableMessages as $stableMessage) {
                $this->line($leadPadding.$this->stableMessageSymbol($stableMessage['type']).' '.$this->truncate($stableMessage['message'], $stableLineMaxWidth));
            }

            $this->newLine();

            return $this;
        }

        $this->line(" {$this->cyan($this->spinnerFrame($task->count))} {$this->truncate($task->label, $labelMaxWidth)}");

        if ($task->subLabel !== null && $task->subLabel !== '') {
            $this->line($leadPadding.$this->dim($this->truncate($task->subLabel, $stableLineMaxWidth)));
        }

        foreach ($stableMessages as $stableMessage) {
            $this->line($leadPadding.$this->stableMessageSymbol($stableMessage['type']).' '.$this->truncate($stableMessage['message'], $stableLineMaxWidth));
        }

        if (count($task->stableMessages) > 0 || count($task->logs) > 0) {
            $this->line($this->gray(' '.str_repeat('─', $maxWidth)));
        } else {
            $this->newLine();
        }

        $logs = array_slice($task->logs, -$task->limit);

        foreach ($logs as $log) {
            $this->line(' '.$this->dim($log));
        }

        $remaining = $task->limit - count($task->logs);

        while ($remaining > 0) {
            $this->line('');
            $remaining--;
        }

        return $this;
    }

    protected function stableMessageSymbol(string $type): string
    {
        return match ($type) {
            'success' => $this->green('✔'),
            'error' => $this->red('✘'),
            'warning' => $this->yellow('⚠'),
            default => '',
        };
    }
}
