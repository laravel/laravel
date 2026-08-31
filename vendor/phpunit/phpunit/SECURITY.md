# Security Policy

## Supported versions

Security issues are fixed for versions of PHPUnit that are in the Bugfix Support phase.
Versions in the Life Support phase only receive changes required for compatibility with new versions of PHP; they do not receive bug or security fixes.
Versions that have reached End-of-Life do not receive any changes.

| Version    | Phase          | End of Bugfix Support |
|------------|----------------|-----------------------|
| PHPUnit 13 | Bugfix Support | February 4, 2028      |
| PHPUnit 12 | Bugfix Support | February 5, 2027      |
| PHPUnit 11 | Life Support   | February 6, 2026      |
| PHPUnit 10 | Life Support   | February 7, 2025      |
| PHPUnit 9  | Life Support   | February 2, 2024      |
| PHPUnit 8  | Life Support   | February 3, 2023      |

PHPUnit 7 and earlier have reached End-of-Life.

See [Supported Versions of PHPUnit](https://phpunit.de/supported-versions.html) for the authoritative list and for the definitions of the support phases.

## Reporting a vulnerability

If you believe you have found a security vulnerability in PHPUnit, please report it to me through coordinated disclosure.
Please report vulnerabilities in bundled libraries to their respective repositories.

**Please do not report security vulnerabilities through public GitHub issues, discussions, or pull requests.**

Instead, please email `sebastian@phpunit.de`.

Use my [PGP key](https://sebastian-bergmann.de/gpg.asc) for encrypted email, for example when your report includes proof-of-concept exploits against third-party systems.

Please include as much of the information listed below as you can to help me better understand and resolve the issue:

* The type of issue
* Full paths of source file(s) related to the manifestation of the issue
* The location of the affected source code (tag/branch/commit or direct URL)
* Any special configuration required to reproduce the issue
* Step-by-step instructions to reproduce the issue
* Proof-of-concept or exploit code (if possible)
* Impact of the issue, including how an attacker might exploit the issue

This information will help me triage your report more quickly.

If you used an AI assistant (LLM, coding agent, or similar) to find, reproduce, or write up the issue, please say so and describe how it was used.
This does not disqualify a report, but it changes how I triage it.

## How reports are handled

I do not operate a bug bounty program.
There is no monetary reward for reporting a vulnerability.

I will acknowledge receipt of a vulnerability report within 7 days.

A confirmed security issue is handled like any other bug.
It is not prioritized ahead of other work.
It is fixed when I have the time to fix it.

I will fix and release before a public advisory is published, but I will not agree to fixed-date embargoes, multi-vendor coordination, or NDAs.
I do not optimize for metrics such as the OpenSSF Scorecard.

Once a vulnerability has been fixed and a release with the fix is available, I publish a public advisory on GitHub.
I give credit in the advisory when I know the reporter's GitHub user name.

## Scope

PHPUnit is a framework for writing as well as a command-line tool for running tests.
Writing and running tests is a development-time activity.
PHPUnit is developed with a focus on development environments and the command-line.

### In scope

I treat a bug as a security issue when a documented, intended use of PHPUnit, or a reasonable extrapolation of it, causes PHPUnit itself to compromise the confidentiality, integrity, or availability of the environment it runs in. Examples:

* PHPUnit's console output, logfiles, and reports in any format contain a vulnerability that allows an attacker to extract secrets from or otherwise compromise the environment in which the output is viewed or processed.
* A regular test run, against trusted test code, trusted production code, and a trusted configuration, causes PHPUnit to transmit data from the environment to a third party.
* A regular test run, against trusted test code, trusted production code, and a trusted configuration, causes PHPUnit to write secrets from the environment to its own output, log files, or reports.

### Not in scope

No specific testing or hardening with regard to using PHPUnit in an HTTP or web context, in a CI/CD context, or with untrusted input data is performed.
The following classes of issue are explicitly not in scope.

#### CI/CD Pipelines

Running tests in a CI/CD pipeline is a legitimate and common use of PHPUnit.
However, a CI/CD pipeline is not a sandbox, and PHPUnit is not designed to defend one.
Executing test code, production code, or a configuration for PHPUnit or PHP that has not been reviewed is equivalent to executing arbitrary code on the infrastructure that hosts the pipeline.
This class of risk is documented as [CICD-SEC-04: Poisoned Pipeline Execution](https://owasp.org/www-project-top-10-ci-cd-security-risks/CICD-SEC-04-Poisoned-Pipeline-Execution) in the OWASP Top 10 CI/CD Security Risks.

**If your CI/CD pipeline automatically runs PHPUnit against a configuration for PHPUnit or PHP, test code, and/or production code from third parties without those changes having been reviewed first, then your development process is broken.**

Running code from a pull request on your infrastructure is, by definition, remote code execution.

Protecting the pipeline is the responsibility of the operator of the pipeline:
limit which events trigger workflows, require review before workflows run on contributions from outside collaborators, isolate jobs that run untrusted code, and do not expose secrets to such jobs.

#### Webserver

There is no reason why PHPUnit should be installed on a webserver and/or in a production environment.

**If you upload PHPUnit to a webserver then your deployment process is broken.**

On a more general note, if your `vendor` directory is publicly accessible on your webserver then your deployment process is also broken.

PHPUnit might contain functionality that intentionally exposes internal application data for debugging purposes.
If PHPUnit is used in a web application, the application developer is responsible for filtering inputs or escaping outputs as necessary and for verifying that the used functionality is safe for use within the intended context.
