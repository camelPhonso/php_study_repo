<?php

namespace App\Types;

class Result
{
    private bool $isSuccess;
    private mixed $value;
    private ?\Throwable $exception;

    /**
     * @template T
     * @param bool $isSuccessful
     * @param T $newValue
     * @param ?\Throwable $exception
     */
    private function __construct($isSuccessful, $newValue = null, $exception = null)
    {
        $this->isSuccess = $isSuccessful;
        $this->value = $newValue;
        $this->exception = null;
    }

    public static function Ok($newValue): self
    {
        return new self(true, $newValue, null);
    }

    public static function Fail($error): self
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

