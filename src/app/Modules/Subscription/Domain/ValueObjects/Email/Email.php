<?php

namespace App\Modules\Subscription\Domain\ValueObjects\Email;

use App\Exceptions\ValidationException;
use App\Modules\Subscription\Domain\ValueObjects\Email\Validator\EmailValidator;
use App\Modules\Subscription\Domain\ValueObjects\Email\Validator\EmailValidatorInterface;

class Email
{
    /**
     * @throws ValidationException
     */
    public function __construct(
        private readonly string $value,
        private ?EmailValidatorInterface $validator = null
    ) {
        if (!$this->validator) {
            $this->validator = new EmailValidator();
        }

        $this->validator->assertValid($value);
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function equals(Email $other): bool
    {
        return $this->value === $other->getValue();
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
