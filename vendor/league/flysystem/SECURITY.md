# Security Policy

## Supported versions

| Version     | Phase        | End of Bugfix Support |
|-------------|--------------|-----------------------|
| Flysystem 3 | Supported    | Not specified yet     |

Flysystem 2 and earlier have reached End-of-Life.

> FYI: There is no bug-bounty program.

## Reporting a Vulnerability

If you believe you have found a security vulnerability in Flysystem, please report it to me through coordinated disclosure.

**Please do not report security vulnerabilities through public GitHub issues, discussions, or pull requests.**

Instead, please email `info+flysystem@frankdejonge.nl`.

Please include as much of the information listed below as you can to help me better understand and resolve the issue:

* The type of issue
* Full paths of source file(s) related to the manifestation of the issue
* The location of the affected source code (tag/branch/commit or direct URL)
* Any special configuration required to reproduce the issue
* Step-by-step instructions to reproduce the issue
* Proof-of-concept or exploit code (if possible)
* Impact of the issue, including how an attacker might exploit the issue

This information will help me triage your report more quickly.

If you used an AI assistant (LLM, coding agent, or similar) to find, reproduce, or write up the issue, please say so 
and describe how it was used. This does not disqualify a report, but it changes how I triage it.

### Path Traversal attacks

Path traversal attacks for the *root* path should not occur. Under standard configurations, using the security features
Flysystem ships an attacker should not be able to escape out of the configured root path through path traversal attacks.

Root paths are configured at the adapter level. Any path inside the root path can resolve relative paths. Breaking out
of a sub-directory is NOT considered a vulnerability. If you wish to prevent path traversal attacks, configure the
appropriate root path or disable relative path traveral.

When relative path traversal is disabled, any relative path traversal IS be considered a vulnerability.

Relative path resolution is enabled by default and can be disabled by setting the `allow_relative_path_traversal`
configuration option on the `Filesystem` instance to `false` or by passing a `PathNormalizer` instance configured to
reject relative path traversal.

```php
use League\Flysystem\Filesystem;use League\Flysystem\WhitespacePathNormalizer;

$filesystem = new Filesystem(
    $adapter,
    [
        'allow_relative_path_traversal' => false,
    ]
);

$filesystem = new Filesystem(
    $adapter,
    [],
    new WhitespacePathNormalizer(allowRelativePathTraversal: false),
);
```

> [!IMPORTANT]
> Note that, passing a `PathNormalizer` instance takes precedence over the `allow_relative_path_traversal` configuration
> option. If the configuration option is set to `false` but the configured `PathNormalizer` instance does not reject
> relative path traversal, path traversal attacks are still possible. This is considered a faulty configuration and is
> not considered a security vulnerability.
