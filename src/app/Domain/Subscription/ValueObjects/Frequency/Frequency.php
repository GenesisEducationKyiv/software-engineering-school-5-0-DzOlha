<?php

namespace App\Domain\Subscription\ValueObjects\Frequency;

use App\Exceptions\ValidationException;

class Frequency
{
    private function __construct(
        private readonly FrequencyType $type,
        private readonly ?int $id = null
    ) {
    }

    public static function daily(?int $id = null): self
    {
        return new self(FrequencyType::DAILY, $id);
    }

    public static function hourly(?int $id = null): self
    {
        return new self(FrequencyType::HOURLY, $id);
    }

    /**
     * @throws ValidationException
     */
    public static function fromName(string $name, ?int $id = null): self
    {
        $type = FrequencyType::fromString($name);
        return new self($type, $id);
    }

    /**
     * @throws ValidationException
     */
    public static function fromId(int $id, string $name): self
    {
        $type = FrequencyType::fromString($name);
        return new self($type, $id);
    }

    /**
     * Create from FrequencyType enum directly
     */
    public static function fromType(FrequencyType $type, ?int $id = null): self
    {
        return new self($type, $id);
    }

    public function getName(): string
    {
        return $this->type->value;
    }

    public function getIntervalMinutes(): int
    {
        return $this->type->getIntervalMinutes();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): FrequencyType
    {
        return $this->type;
    }

    public function equals(Frequency $other): bool
    {
        return $this->type === $other->getType();
    }

    public function __toString(): string
    {
        return $this->type->value;
    }

    /**
     * Get all available frequency types
     * @return array<string>
     */
    public static function getAvailableTypes(): array
    {
        return FrequencyType::values();
    }
}
