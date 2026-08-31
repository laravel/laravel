<?php declare(strict_types = 1);
namespace TheSeer\Tokenizer;

class NamespaceUri {

    /** @var string */
    private $value;

    public function __construct(string $value) {
        $this->ensureValidUri($value);
        $this->value = $value;
    }

    public function asString(): string {
        return $this->value;
    }

    private function ensureValidUri($value): void {
        if (\strpos($value, ':') === false) {
            throw new NamespaceUriException(
                \sprintf("Namespace URI '%s' must contain at least one colon", $value)
            );
        }
    }
}
