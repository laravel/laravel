<?php namespace Laravel;

interface Exception {
}

class BadFunctionCallException extends \BadFunctionCallException implements Exception {
}

class BadMethodCallException extends \BadMethodCallException implements Exception {
}

class DomainException extends \DomainException implements Exception {
}

class InvalidArgumentException extends \InvalidArgumentException implements Exception {
}

class LengthException extends \LengthException implements Exception {
}

class LogicException extends \LogicException implements Exception {
}

class OutOfBoundsException extends \OutOfBoundsException implements Exception {
}

class OutOfRangeException extends \OutOfRangeException implements Exception {
}

class RangeException extends \RangeException implements Exception {
}

class RuntimeException extends \RuntimeException implements Exception {
}

class UnderflowException extends \UnderflowException implements Exception {
}

class UnexpectedValueException extends \UnexpectedValueException implements Exception {
}
