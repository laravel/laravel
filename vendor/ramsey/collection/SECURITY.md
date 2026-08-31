<!--
    This policy template was created using the HackerOne Policy Builder [1],
    with guidance from the National Telecommunications and Information
    Administration Coordinated Vulnerability Disclosure Template [2].
 -->

# Vulnerability Disclosure Policy (VDP)

## Brand Promise

<!--
    This is your brand promise. Its objective is to "demonstrate a clear, good
    faith commitment to customers and other stakeholders potentially impacted by
    security vulnerabilities" [2].
-->

Keeping user information safe and secure is a top priority, and we welcome the
contribution of external security researchers.

## Scope

<!--
    This is your initial scope. It tells vulnerability finders and reporters
    "which systems and capabilities are 'fair game' versus 'off limits'" [2].
    For software packages, this is often a list of currently maintained versions
    of the package.
-->

If you believe you've found a security issue in software that is maintained in
this repository, we encourage you to notify us.

| Version | In scope | Source code |
| ------- | :------: | ----------- |
| latest  | ✅        | https://github.com/ramsey/collection |

## How to Submit a Report

<!--
    This is your communication process. It tells security researchers how to
    contact you to report a vulnerability. It may be a link to a web form that
    uses HTTPS for secure communication, or it may be an email address.
    Optionally, you may choose to include a PGP public key, so that researchers
    may send you encrypted messages.
-->

To submit a vulnerability report, please contact us at security@ramsey.dev.
Your submission will be reviewed and validated by a member of our team.

## Safe Harbor

<!--
    This section assures vulnerability finders and reporters that they will
    receive good faith responses to their good faith acts. In other words,
    "we will not take legal action if..." [2].
-->

We support safe harbor for security researchers who:

* Make a good faith effort to avoid privacy violations, destruction of data, and
  interruption or degradation of our services.
* Only interact with accounts you own or with explicit permission of the account
  holder. If you do encounter Personally Identifiable Information (PII) contact
  us immediately, do not proceed with access, and immediately purge any local
  information.
* Provide us with a reasonable amount of time to resolve vulnerabilities prior
  to any disclosure to the public or a third party.

We will consider activities conducted consistent with this policy to constitute
"authorized" conduct and will not pursue civil action or initiate a complaint to
law enforcement. We will help to the extent we can if legal action is initiated
by a third party against you.

Please submit a report to us before engaging in conduct that may be inconsistent
with or unaddressed by this policy.

## Preferences

<!--
    The preferences section sets expectations based on priority and submission
    volume, rather than legal objection or restriction [2].

    According to the NTIA [2]:

        This section is a living document that sets expectations for preferences
        and priorities, typically maintained by the support and engineering
        team. This can outline classes of vulnerabilities, reporting style
        (crash dumps, CVSS scoring, proof-of-concept, etc.), tools, etc. Too
        many preferences can set the wrong tone or make reporting findings
        difficult to navigate. This section also sets expectations to the
        researcher community for what types of issues are considered important
        or not.
-->

* Please provide detailed reports with reproducible steps and a clearly defined
  impact.
* Include the version number of the vulnerable package in your report
* Social engineering (e.g. phishing, vishing, smishing) is prohibited.

<!--
    References

    [1] HackerOne. Policy builder. Retrieved from https://hackerone.com/policy-builder/

    [2] NTIA Safety Working Group. 2016. "Early stage" coordinated vulnerability
    disclosure template: Version 1.1. (15 December 2016). Retrieved from
    https://www.ntia.doc.gov/files/ntia/publications/ntia_vuln_disclosure_early_stage_template.pdf
-->

## Encryption Key for security@ramsey.dev

For increased privacy when reporting sensitive issues, you may encrypt your
message using the following public key:

```
-----BEGIN PGP PUBLIC KEY BLOCK-----

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
nmgawbBHA02D5xEqB5SU3WJu
=eJNx
-----END PGP PUBLIC KEY BLOCK-----
```
