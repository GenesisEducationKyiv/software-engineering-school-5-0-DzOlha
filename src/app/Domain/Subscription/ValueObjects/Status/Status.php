<?php

namespace App\Domain\Subscription\ValueObjects\Status;

use App\Exceptions\ValidationException;

class Status
{
    private function __construct(
        private readonly StatusType $type
    ) {
    }

    public static function pending(): self
    {
        return new self(StatusType::PENDING);
    }

    public static function active(): self
    {
        return new self(StatusType::ACTIVE);
    }

    public static function canceled(): self
    {
        return new self(StatusType::CANCELED);
    }

    /**
     * @throws ValidationException
     */
    public static function fromString(string $status): self
    {
        $type = StatusType::fromString($status);
        return new self($type);
    }

    /**
     * Create from StatusType enum directly
     */
    public static function fromType(StatusType $type): self
    {
        return new self($type);
    }

    public function isPending(): bool
    {
        return $this->type->isPending();
    }

    public function isActive(): bool
    {
        return $this->type->isActive();
    }

    public function isCanceled(): bool
    {
        return $this->type->isCanceled();
    }

    public function getValue(): string
    {
        return $this->type->value;
    }

    public function getType(): StatusType
    {
        return $this->type;
    }

    public function equals(Status $other): bool
    {
        return $this->type === $other->getType();
    }

    public function __toString(): string
    {
        return $this->type->value;
    }

    /**
     * Get all available status values
     * @return array<string>
     */
    public static function getAvailableStatuses(): array
    {
        return StatusType::values();
    }

    /**
     * Business logic methods - delegated to enum
     */
    public function canTransitionToActive(): bool
    {
        return $this->type->canTransitionToActive();
    }

    public function canBeCanceled(): bool
    {
        return $this->type->canBeCanceled();
    }

    /**
     * Transition to active status
     *
     * @throws ValidationException
     */
    public function activate(): self
    {
        if (!$this->canTransitionToActive()) {
            throw new ValidationException([
                'status' => ["Cannot transition from {$this->getValue()} to active"]
            ]);
        }

        return self::active();
    }

    /**
     * Transition to canceled status
     *
     * @throws ValidationException
     */
    public function cancel(): self
    {
        if (!$this->canBeCanceled()) {
            throw new ValidationException([
                'status' => ["Cannot cancel status {$this->getValue()}"]
            ]);
        }

        return self::canceled();
    }
}
