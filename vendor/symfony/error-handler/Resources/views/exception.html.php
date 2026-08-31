<div class="exception-summary <?= !$exceptionMessage ? 'exception-without-message' : ''; ?>">
    <div class="exception-metadata">
        <div class="container">
            <h2 class="exception-hierarchy">
                <?php foreach (array_reverse($exception->getAllPrevious(), true) as $index => $previousException) { ?>
                    <a href="#trace-box-<?= $index + 2; ?>"><?= $this->abbrClass($previousException->getClass()); ?></a>
                    <span class="icon"><?= $this->include('assets/images/chevron-right.svg'); ?></span>
                <?php } ?>
                <a href="#trace-box-1"><?= $this->abbrClass($exception->getClass()); ?></a>
            </h2>
            <h2 class="exception-http">
                HTTP <?= $statusCode; ?> <small><?= $statusText; ?></small>
            </h2>
        </div>
    </div>
    <div class="exception-message-wrapper">
        <div class="container">
            <h1 class="break-long-words exception-message<?= mb_strlen($exceptionMessage) > 180 ? ' long' : ''; ?>"><?= $this->formatFileFromText(nl2br($exceptionMessage)); ?></h1>

            <div class="exception-illustration hidden-xs-down">
                <?= $this->include('assets/images/symfony-ghost.svg.php'); ?>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <div class="sf-tabs">
        <div class="tab">
            <?php
            $exceptionAsArray = $exception->toArray();
            $exceptionWithUserCode = [];
            $exceptionAsArrayCount = count($exceptionAsArray);
            $last = $exceptionAsArrayCount - 1;
            foreach ($exceptionAsArray as $i => $e) {
                foreach ($e['trace'] as $trace) {
                    if ($trace['file'] && !str_contains($trace['file'], '/vendor/') && !str_contains($trace['file'], '/var/cache/') && $i < $last) {
                        $exceptionWithUserCode[] = $i;
                    }
                }
            }
            ?>
            <h3 class="tab-title">
                <?php if ($exceptionAsArrayCount > 1) { ?>
                    Exceptions <span class="badge"><?= $exceptionAsArrayCount; ?></span>
                <?php } else { ?>
                    Exception
                <?php } ?>
            </h3>

            <div class="tab-content">
                <?php
                foreach ($exceptionAsArray as $i => $e) {
                    echo $this->include('views/traces.html.php', [
                        'exception' => $e,
                        'index' => $i + 1,
                        'expand' => in_array($i, $exceptionWithUserCode, true) || ([] === $exceptionWithUserCode && 0 === $i),
                    ]);
                }
                ?>
            </div>
        </div>

        <?php if ($logger) { ?>
        <div class="tab <?= !$logger->getLogs() ? 'disabled' : ''; ?>">
            <h3 class="tab-title">
                Logs
                <?php if ($logger->countErrors()) { ?><span class="badge status-error"><?= $logger->countErrors(); ?></span><?php } ?>
            </h3>

            <div class="tab-content">
                <?php if ($logger->getLogs()) { ?>
                    <?= $this->include('views/logs.html.php', ['logs' => $logger->getLogs()]); ?>
                <?php } else { ?>
                    <div class="empty">
                        <p>No log messages</p>
                    </div>
                <?php } ?>
            </div>
        </div>
        <?php } ?>

        <div class="tab">
            <h3 class="tab-title">
                <?php if ($exceptionAsArrayCount > 1) { ?>
                    Stack Traces <span class="badge"><?= $exceptionAsArrayCount; ?></span>
                <?php } else { ?>
                    Stack Trace
                <?php } ?>
            </h3>

            <div class="tab-content">
                <?php
                foreach ($exceptionAsArray as $i => $e) {
                    echo $this->include('views/traces_text.html.php', [
                        'exception' => $e,
                        'index' => $i + 1,
                        'numExceptions' => $exceptionAsArrayCount,
                    ]);
                }
                ?>
            </div>
        </div>

        <?php if ($currentContent) { ?>
        <div class="tab">
            <h3 class="tab-title">Output content</h3>

            <div class="tab-content">
                <?= $currentContent; ?>
            </div>
        </div>
        <?php } ?>
    </div>
</div>
