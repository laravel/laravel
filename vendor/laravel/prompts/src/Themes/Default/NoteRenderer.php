<?php

namespace Laravel\Prompts\Themes\Default;

use Laravel\Prompts\Note;

class NoteRenderer extends Renderer
{
    /**
     * Render the note.
     */
    public function __invoke(Note $note): string
    {
        $lines = explode(PHP_EOL, $note->message);

        switch ($note->type) {
            case 'intro':
            case 'outro':
                $lines = array_map(fn ($line) => " {$line} ", $lines);
                $longest = max(array_map(fn ($line) => mb_strlen($line), $lines));

                foreach ($lines as $line) {
                    $line = mb_str_pad($line, $longest, ' ');
                    $this->line(" {$this->bgCyan($this->black($line))}");
                }

                return $this;

            case 'warning':
                foreach ($lines as $line) {
                    $this->line($this->yellow(" {$line}"));
                }

                return $this;

            case 'error':
                foreach ($lines as $line) {
                    $this->line($this->red(" {$line}"));
                }

                return $this;

            case 'alert':
                foreach ($lines as $line) {
                    $this->line(" {$this->bgRed($this->white(" {$line} "))}");
                }

                return $this;

            case 'info':
                foreach ($lines as $line) {
                    $this->line($this->green(" {$line}"));
                }

                return $this;

            default:
                foreach ($lines as $line) {
                    $this->line(" {$line}");
                }

                return $this;
        }
    }
}
