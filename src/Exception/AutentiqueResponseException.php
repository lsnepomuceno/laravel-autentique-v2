<?php

namespace LSNepomuceno\LaravelAutentiqueV2\Exception;

use Exception;

final class AutentiqueResponseException extends Exception
{
  /**
   * __construct
   *
   * @param  string $responseMessage
   * @param  int $code 0
   * @param  \Exception $previous null
   * @return void
   */
  public function __construct(string $responseMessage, int $code = 0, Exception $previous = null)
  {
    $message = "Autentique response error: {$responseMessage}.";
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
