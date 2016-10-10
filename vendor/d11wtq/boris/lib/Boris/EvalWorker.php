<?php

/* vim: set shiftwidth=2 expandtab softtabstop=2: */

namespace Boris;

/**
 * EvalWorker is responsible for evaluating PHP expressions in forked processes.
 */
class EvalWorker {
  const ABNORMAL_EXIT = 255;
  const DONE   = "\0";
  const EXITED = "\1";
  const FAILED = "\2";
  const READY  = "\3";

  private $_socket;
  private $_exports = array();
  private $_startHooks = array();
  private $_failureHooks = array();
  private $_ppid;
  private $_pid;
  private $_cancelled;
  private $_inspector;
  private $_exceptionHandler;

  /**
   * Create a new worker using the given socket for communication.
   *
   * @param resource $socket
   */
  public function __construct($socket) {
    $this->_socket    = $socket;
    $this->_inspector = new DumpInspector();
    stream_set_blocking($socket, 0);
  }

  /**
   * Set local variables to be placed in the workers's scope.
   *
   * @param array|string $local
   * @param mixed $value, if $local is a string
   */
  public function setLocal($local, $value = null) {
    if (!is_array($local)) {
      $local = array($local => $value);
    }

    $this->_exports = array_merge($this->_exports, $local);
  }

  /**
   * Set hooks to run inside the worker before it starts looping.
   *
   * @param array $hooks
   */
  public function setStartHooks($hooks) {
    $this->_startHooks = $hooks;
  }

  /**
   * Set hooks to run inside the worker after a fatal error is caught.
   *
   * @param array $hooks
   */
  public function setFailureHooks($hooks) {
    $this->_failureHooks = $hooks;
  }

  /**
   * Set an Inspector object for Boris to output return values with.
   *
   * @param object $inspector any object the responds to inspect($v)
   */
  public function setInspector($inspector) {
    $this->_inspector = $inspector;
  }

  /**
   * Start the worker.
   *
   * This method never returns.
   */
  public function start() {
    $__scope = $this->_runHooks($this->_startHooks);
    extract($__scope);

    $this->_write($this->_socket, self::READY);

    /* Note the naming of the local variables due to shared scope with the user here */
    for (;;) {
      declare(ticks = 1);
      // don't exit on ctrl-c
      pcntl_signal(SIGINT, SIG_IGN, true);

      $this->_cancelled = false;

      $__input = $this->_transform($this->_read($this->_socket));

      if ($__input === null) {
        continue;
      }

      $__response = self::DONE;

      $this->_ppid = posix_getpid();
      $this->_pid  = pcntl_fork();

      if ($this->_pid < 0) {
        throw new \RuntimeException('Failed to fork child labourer');
      } elseif ($this->_pid > 0) {
        // kill the child on ctrl-c
        pcntl_signal(SIGINT, array($this, 'cancelOperation'), true);
        pcntl_waitpid($this->_pid, $__status);

        if (!$this->_cancelled && $__status != (self::ABNORMAL_EXIT << 8)) {
          $__response = self::EXITED;
        } else {
          $this->_runHooks($this->_failureHooks);
          $__response = self::FAILED;
        }
      } else {
        // user exception handlers normally cause a clean exit, so Boris will exit too
        if (!$this->_exceptionHandler =
          set_exception_handler(array($this, 'delegateExceptionHandler'))) {
          restore_exception_handler();
        }

        // undo ctrl-c signal handling ready for user code execution
        pcntl_signal(SIGINT, SIG_DFL, true);
        $__pid = posix_getpid();

        $__result = eval($__input);

        if (posix_getpid() != $__pid) {
          // whatever the user entered caused a forked child
          // (totally valid, but we don't want that child to loop and wait for input)
          exit(0);
        }

        if (preg_match('/\s*return\b/i', $__input)) {
          fwrite(STDOUT, sprintf("%s\n", $this->_inspector->inspect($__result)));
        }
        $this->_expungeOldWorker();
      }

      $this->_write($this->_socket, $__response);

      if ($__response == self::EXITED) {
        exit(0);
      }
    }
  }

  /**
   * While a child process is running, terminate it immediately.
   */
  public function cancelOperation() {
    printf("Cancelling...\n");
    $this->_cancelled = true;
    posix_kill($this->_pid, SIGKILL);
    pcntl_signal_dispatch();
  }

  /**
   * If any user-defined exception handler is present, call it, but be sure to exit correctly.
   */
  public function delegateExceptionHandler($ex) {
    call_user_func($this->_exceptionHandler, $ex);
    exit(self::ABNORMAL_EXIT);
  }

  // -- Private Methods

  private function _runHooks($hooks) {
    extract($this->_exports);

    foreach ($hooks as $__hook) {
      if (is_string($__hook)) {
        eval($__hook);
      } elseif (is_callable($__hook)) {
        call_user_func($__hook, $this, get_defined_vars());
      } else {
        throw new \RuntimeException(
          sprintf(
            'Hooks must be closures or strings of PHP code. Got [%s].',
            gettype($__hook)
          )
        );
      }

      // hooks may set locals
      extract($this->_exports);
    }

    return get_defined_vars();
  }

  private function _expungeOldWorker() {
    posix_kill($this->_ppid, SIGTERM);
    pcntl_signal_dispatch();
  }

  private function _write($socket, $data) {
    if (!fwrite($socket, $data)) {
      throw new \RuntimeException('Socket error: failed to write data');
    }
  }

  private function _read($socket)
  {
    $read   = array($socket);
    $except = array($socket);

    if ($this->_select($read, $except) > 0) {
      if ($read) {
        return stream_get_contents($read[0]);
      } else if ($except) {
        throw new \UnexpectedValueException("Socket error: closed");
      }
    }
  }

  private function _select(&$read, &$except) {
    $write = null;
    set_error_handler(function(){return true;}, E_WARNING);
    $result = stream_select($read, $write, $except, 10);
    restore_error_handler();
    return $result;
  }

  private function _transform($input) {
    if ($input === null) {
      return null;
    }

    $transforms = array(
      'exit' => 'exit(0)'
    );

    foreach ($transforms as $from => $to) {
      $input = preg_replace('/^\s*' . preg_quote($from, '/') . '\s*;?\s*$/', $to . ';', $input);
    }

    return $input;
  }
}
