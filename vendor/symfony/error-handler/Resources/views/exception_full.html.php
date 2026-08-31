<!-- <?= $_message = \sprintf('%s (%d %s)', $exceptionMessage, $statusCode, $statusText); ?> -->
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="<?= $this->charset; ?>" />
        <meta name="robots" content="noindex,nofollow" />
        <meta name="viewport" content="width=device-width,initial-scale=1" />
        <title><?= $_message; ?></title>
        <link rel="icon" type="image/png" href="<?= $this->include('assets/images/favicon.png.base64'); ?>" />
        <style><?= $this->include('assets/css/exception.css'); ?></style>
        <style><?= $this->include('assets/css/exception_full.css'); ?></style>
    </head>
    <body>
        <script>
            document.body.classList.add(
                localStorage.getItem('symfony/profiler/theme') || (matchMedia('(prefers-color-scheme: dark)').matches ? 'theme-dark' : 'theme-light')
            );
        </script>

        <?php if (class_exists(\Composer\InstalledVersions::class) && \Composer\InstalledVersions::isInstalled('symfony/http-kernel')) { ?>
            <header>
                <div class="container">
                    <h1 class="logo"><?= $this->include('assets/images/symfony-logo.svg'); ?> Symfony Exception</h1>

                    <div class="help-link">
                        <a href="https://symfony.com/doc/<?= preg_match('/^v?(\d+\.\d+)/', \Composer\InstalledVersions::getPrettyVersion('symfony/http-kernel') ?? \Composer\InstalledVersions::getPrettyVersion('symfony/symfony'), $m) ? $m[1] : 'current'; ?>/index.html">
                            <span class="icon"><?= $this->include('assets/images/icon-book.svg'); ?></span>
                            <span class="hidden-xs-down">Symfony</span> Docs
                        </a>
                    </div>
                </div>
            </header>
        <?php } ?>

        <?= $this->include('views/exception.html.php', $context); ?>

        <script>
            <?= $this->include('assets/js/exception.js'); ?>
        </script>
    </body>
</html>
<!-- <?= $_message; ?> -->
