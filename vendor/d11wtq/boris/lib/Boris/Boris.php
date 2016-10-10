<?php

/* vim: set shiftwidth=2 expandtab softtabstop=2: */

namespace Boris;

/**
 * Boris is a tiny REPL for PHP.
 */
class Boris {
  const VERSION = "1.0.8";

  private $_prompt;
  private $_historyFile;
  private $_exports = array();
  private $_startHooks = array();
  private $_failureHooks = array();
  private $_inspector;

  /**
   * Create a new REPL, which consists of an evaluation worker and a readline client.
   *
   * @param string $prompt, optional
   * @param string $historyFile, optional
   */
  public function __construct($prompt = 'boris> ', $historyFile = null) {
    $this->setPrompt($prompt);
    $this->_historyFile = $historyFile
      ? $historyFile
      : sprintf('%s/.boris_history', getenv('HOME'))
      ;
    $this->_inspector = new ColoredInspector();
  }

  /**
   * Add a new hook to run in the context of the REPL when it starts.
   *
   * @param mixed $hook
   *
   * The hook is either a string of PHP code to eval(), or a Closure accepting
   * the EvalWorker object as its first argument and the array of defined
   * local variables in the second argument.
   *
   * If the hook is a callback and needs to set any local variables in the
   * REPL's scope, it should invoke $worker->setLocal($var_name, $value) to
   * do so.
   *
   * Hooks are guaranteed to run in the order they were added and the state
   * set by each hook is available to the next hook (either through global
   * resources, such as classes and interfaces, or through the 2nd parameter
   * of the callback, if any local variables were set.
   *
   * @example Contrived example where one hook sets the date and another
   *          prints it in the REPL.
   *
   *   $boris->onStart(function($worker, $vars){
   *     $worker->setLocal('date', date('Y-m-d'));
   *   });
   *
   *   $boris->onStart('echo "The date is $date\n";');
   */
  public function onStart($hook) {
    $this->_startHooks[] = $hook;
  }

  /**
   * Add a new hook to run in the context of the REPL when a fatal error occurs.
   *
   * @param mixed $hook
   *
   * The hook is either a string of PHP code to eval(), or a Closure accepting
   * the EvalWorker object as its first argument and the array of defined
   * local variables in the second argument.
   *
   * If the hook is a callback and needs to set any local variables in the
   * REPL's scope, it should invoke $worker->setLocal($var_name, $value) to
   * do so.
   *
   * Hooks are guaranteed to run in the order they were added and the state
   * set by each hook is available to the next hook (either through global
   * resources, such as classes and interfaces, or through the 2nd parameter
   * of the callback, if any local variables were set.
   *
   * @example An example if your project requires some database connection cleanup:
   *
   *   $boris->onFailure(function($worker, $vars){
   *     DB::reset();
   *   });
   */
  public function onFailure($hook){
    $this->_failureHooks[] = $hook;
  }

  /**
   * Set a local variable, or many local variables.
   *
   * @example Setting a single variable
   *   $boris->setLocal('user', $bob);
   *
   * @example Setting many variables at once
   *   $boris->setLocal(array('user' => $bob, 'appContext' => $appContext));
   *
   * This method can safely be invoked repeatedly.
   *
   * @param array|string $local
   * @param mixed $value, optional
   */
  public function setLocal($local, $value = null) {
    if (!is_array($local)) {
      $local = array($local => $value);
    }

    $this->_exports = array_merge($this->_exports, $local);
  }

  /**
   * Sets the Boris prompt text
   *
   * @param string $prompt
   */
  public function setPrompt($prompt) {
    $this->_prompt = $prompt;
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
   * Start the REPL (display the readline prompt).
   *
   * This method never returns.
   */
  public function start() {
    declare(ticks = 1);
    pcntl_signal(SIGINT, SIG_IGN, true);

    if (!$pipes = stream_socket_pair(
      STREAM_PF_UNIX, STREAM_SOCK_STREAM, STREAM_IPPROTO_IP)) {
      throw new \RuntimeException('Failed to create socket pair');
    }

    $pid = pcntl_fork();

    if ($pid > 0) {
      if (function_exists('setproctitle')) {
        setproctitle('boris (master)');
      }

      fclose($pipes[0]);
      $client = new ReadlineClient($pipes[1]);
      $client->start($this->_prompt, $this->_historyFile);
    } elseif ($pid < 0) {
      throw new \RuntimeException('Failed to fork child process');
    } else {
      if (function_exists('setproctitle')) {
        setproctitle('boris (worker)');
      }

      fclose($pipes[1]);
      $worker = new EvalWorker($pipes[0]);
      $worker->setLocal($this->_exports);
      $worker->setStartHooks($this->_startHooks);
      $worker->setFailureHooks($this->_failureHooks);
      $worker->setInspector($this->_inspector);
      $worker->start();
    }
  }
}
