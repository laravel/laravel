<?php

/* vim: set shiftwidth=2 expandtab softtabstop=2: */

namespace Boris;

/**
 * The Readline client is what the user spends their time entering text into.
 *
 * Input is collected and sent to {@link \Boris\EvalWorker} for processing.
 */
class ReadlineClient {
  private $_socket;
  private $_prompt;
  private $_historyFile;
  private $_clear = false;

  /**
   * Create a new ReadlineClient using $socket for communication.
   *
   * @param resource $socket
   */
  public function __construct($socket) {
    $this->_socket = $socket;
  }

  /**
   * Start the client with an prompt and readline history path.
   *
   * This method never returns.
   *
   * @param string $prompt
   * @param string $historyFile
   */
  public function start($prompt, $historyFile) {
    readline_read_history($historyFile);

    declare(ticks = 1);
    pcntl_signal(SIGCHLD, SIG_IGN);
    pcntl_signal(SIGINT, array($this, 'clear'), true);

    // wait for the worker to finish executing hooks
    if (fread($this->_socket, 1) != EvalWorker::READY) {
      throw new \RuntimeException('EvalWorker failed to start');
    }

    $parser = new ShallowParser();
    $buf    = '';
    $lineno = 1;

    for (;;) {
      $this->_clear = false;
      $line = readline(
        sprintf(
          '[%d] %s',
          $lineno,
          ($buf == ''
            ? $prompt
            : str_pad('*> ', strlen($prompt), ' ', STR_PAD_LEFT))
        )
      );

      if ($this->_clear) {
        $buf = '';
        continue;
      }

      if (false === $line) {
        $buf = 'exit(0);'; // ctrl-d acts like exit
      }

      if (strlen($line) > 0) {
        readline_add_history($line);
      }

      $buf .= sprintf("%s\n", $line);

      if ($statements = $parser->statements($buf)) {
        ++$lineno;

        $buf = '';
        foreach ($statements as $stmt) {
          if (false === $written = fwrite($this->_socket, $stmt)) {
            throw new \RuntimeException('Socket error: failed to write data');
          }

          if ($written > 0) {
            $status = fread($this->_socket, 1);
            if ($status == EvalWorker::EXITED) {
              readline_write_history($historyFile);
              echo "\n";
              exit(0);
            } elseif ($status == EvalWorker::FAILED) {
              break;
            }
          }
        }
      }
    }
  }

  /**
   * Clear the input buffer.
   */
  public function clear() {
    // FIXME: I'd love to have this send \r to readline so it puts the user on a blank line
    $this->_clear = true;
  }
}
