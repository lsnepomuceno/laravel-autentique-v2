<?php

namespace LSNepomuceno\LaravelAutentiqueV2\Exception;

use Exception;

final class InvalidSignaturePositionException extends Exception
{
  /**
   * __construct
   *
   * @param  string $position
   * @param  int $code 0
   * @param  \Exception $previous null
   * @return void
   */
  public function __construct(string $position, int $code = 0, Exception $previous = null)
  {
    $message = "Invalid signature {$position} position.";
    parent::__construct($message, $code, $previous);
  }

  /**
   * __toString
   *
   * @return string
   */
  public function __toString(): string
  {
    return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
  }
}
