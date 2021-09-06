<?php

namespace LSNepomuceno\LaravelAutentiqueV2;

class Signers
{
  protected array $signers = [], $currentSigner = [];

  protected int $position = 0;

  const POSITIONS = 'positions';

  public function setSigner(): Signers
  {
    if (isset($this->currentSigner) && !empty($this->currentSigner)) {
      $this->currentSigner['action'] = 'SIGN';
      array_push($this->signers, $this->currentSigner);
    }
    $this->currentSigner = [];
    return $this;
  }

  public function setPosition(int $position = 0): Signers
  {
    $this->position = $position;
    return $this;
  }

  public function setFullSigner(string $email, float $x, float $y, float $z): Signers
  {
    $this->email($email)->x($x)->y($y)->z($z);
    return $this;
  }

  public function name(string $name): Signers
  {
    $this->currentSigner['name'] = $name;
    return $this;
  }

  public function email(string $email): Signers
  {
    $this->currentSigner['email'] = $email;
    return $this;
  }

  public function x(float $x): Signers
  {
    $this->currentSigner[self::POSITIONS][$this->position]['x'] = $x;
    return $this;
  }

  public function y(float $y): Signers
  {
    $this->currentSigner[self::POSITIONS][$this->position]['y'] = $y;
    return $this;
  }

  public function z(float $z): Signers
  {
    $this->currentSigner[self::POSITIONS][$this->position]['z'] = $z;
    return $this;
  }

  public function getSigners(): array
  {
    return $this->signers;
  }
}
