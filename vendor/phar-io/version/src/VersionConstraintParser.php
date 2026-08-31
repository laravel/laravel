<?php declare(strict_types = 1);
/*
 * This file is part of PharIo\Version.
 *
 * (c) Arne Blankerts <arne@blankerts.de>, Sebastian Heuer <sebastian@phpeople.de>, Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PharIo\Version;

class VersionConstraintParser {
    /**
     * @throws UnsupportedVersionConstraintException
     */
    public function parse(string $value): VersionConstraint {
        if (\strpos($value, '|') !== false) {
            return $this->handleOrGroup($value);
        }

        if (!\preg_match('/^[\^~*]?v?[\d.*]+(?:-.*)?$/i', $value)) {
            throw new UnsupportedVersionConstraintException(
                \sprintf('Version constraint %s is not supported.', $value)
            );
        }

        switch ($value[0]) {
            case '~':
                return $this->handleTildeOperator($value);
            case '^':
                return $this->handleCaretOperator($value);
        }

        $constraint = new VersionConstraintValue($value);

        if ($constraint->getMajor()->isAny()) {
            return new AnyVersionConstraint();
        }

        if ($constraint->getMinor()->isAny()) {
            return new SpecificMajorVersionConstraint(
                $constraint->getVersionString(),
                $constraint->getMajor()->getValue() ?? 0
            );
        }

        if ($constraint->getPatch()->isAny()) {
            return new SpecificMajorAndMinorVersionConstraint(
                $constraint->getVersionString(),
                $constraint->getMajor()->getValue() ?? 0,
                $constraint->getMinor()->getValue() ?? 0
            );
        }

        return new ExactVersionConstraint($constraint->getVersionString());
    }

    private function handleOrGroup(string $value): OrVersionConstraintGroup {
        $constraints = [];

        foreach (\preg_split('{\s*\|\|?\s*}', \trim($value)) as $groupSegment) {
            $constraints[] = $this->parse(\trim($groupSegment));
        }

        return new OrVersionConstraintGroup($value, $constraints);
    }

    private function handleTildeOperator(string $value): AndVersionConstraintGroup {
        $constraintValue = new VersionConstraintValue(\substr($value, 1));

        if ($constraintValue->getPatch()->isAny()) {
            return $this->handleCaretOperator($value);
        }

        $constraints = [
            new GreaterThanOrEqualToVersionConstraint(
                $value,
                new Version(\substr($value, 1))
            ),
            new SpecificMajorAndMinorVersionConstraint(
                $value,
                $constraintValue->getMajor()->getValue() ?? 0,
                $constraintValue->getMinor()->getValue() ?? 0
            )
        ];

        return new AndVersionConstraintGroup($value, $constraints);
    }

    private function handleCaretOperator(string $value): AndVersionConstraintGroup {
        $constraintValue = new VersionConstraintValue(\substr($value, 1));

        $constraints = [
            new GreaterThanOrEqualToVersionConstraint($value, new Version(\substr($value, 1)))
        ];

        if ($constraintValue->getMajor()->getValue() === 0) {
            $constraints[] = new SpecificMajorAndMinorVersionConstraint(
                $value,
                $constraintValue->getMajor()->getValue() ?? 0,
                $constraintValue->getMinor()->getValue() ?? 0
            );
        } else {
            $constraints[] = new SpecificMajorVersionConstraint(
                $value,
                $constraintValue->getMajor()->getValue() ?? 0
            );
        }

        return new AndVersionConstraintGroup(
            $value,
            $constraints
        );
    }
}
