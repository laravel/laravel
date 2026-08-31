<?php declare(strict_types = 1);
namespace PharIo\Version;

class PreReleaseSuffix {
    private const valueScoreMap = [
        'dev'   => 0,
        'a'     => 1,
        'alpha' => 1,
        'b'     => 2,
        'beta'  => 2,
        'rc'    => 3,
        'p'     => 4,
        'pl'    => 4,
        'patch' => 4,
    ];

    /** @var string */
    private $value;

    /** @var int */
    private $valueScore;

    /** @var int */
    private $number = 0;

    /** @var string */
    private $full;

    /**
     * @throws InvalidPreReleaseSuffixException
     */
    public function __construct(string $value) {
        $this->parseValue($value);
    }

    public function asString(): string {
        return $this->full;
    }

    public function getValue(): string {
        return $this->value;
    }

    public function getNumber(): ?int {
        return $this->number;
    }

    public function isGreaterThan(PreReleaseSuffix $suffix): bool {
        if ($this->valueScore > $suffix->valueScore) {
            return true;
        }

        if ($this->valueScore < $suffix->valueScore) {
            return false;
        }

        return $this->getNumber() > $suffix->getNumber();
    }

    private function mapValueToScore(string $value): int {
        $value = \strtolower($value);

        return self::valueScoreMap[$value];
    }

    private function parseValue(string $value): void {
        $regex = '/-?((dev|beta|b|rc|alpha|a|patch|p|pl)\.?(\d*)).*$/i';

        if (\preg_match($regex, $value, $matches) !== 1) {
            throw new InvalidPreReleaseSuffixException(\sprintf('Invalid label %s', $value));
        }

        $this->full  = $matches[1];
        $this->value = $matches[2];

        if ($matches[3] !== '') {
            $this->number = (int)$matches[3];
        }

        $this->valueScore = $this->mapValueToScore($matches[2]);
    }
}
