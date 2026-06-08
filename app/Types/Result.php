<?php

namespace App\Types;

class Result
{
    private bool $isSuccess;
    private mixed $value;
    private ?\Throwable $exception;

    private function __construct(bool $isSuccessful, mixed $newValue)
    {
        $this->isSuccess = $isSuccessful;
        $this->value = $newValue;
        $this->exception = null;
    }

    public static function Ok(mixed $newValue): self
    {
        return new self(true, $newValue);
    }

    public static function Fail( \Throwable $error): self
    {
        return new self(false, null, $error);
    }

    public function IsSuccess(): bool
    {
        return $this->isSuccess;
    }

    public function IsFailure(): bool
    {
        return !$this->isSuccess;
    }

    public function GetValue(): mixed
    {
        if ($this->isFailure()) throw new LogicException("Cannot get value from error result");

        return $this->value;
    }

    public function GetException(): \Throwable
    {
        if ($this->isSuccess()) throw new LogicException("Cannot get exception from successful result");

        return $this->exception;
    }


}

