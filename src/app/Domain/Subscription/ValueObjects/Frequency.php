<?php

namespace App\Domain\Subscription\ValueObjects;

use App\Exceptions\ValidationException;

class Frequency
{
    private const DAILY = 'daily';
    private const HOURLY = 'hourly';

    public const FREQUENCY_TYPES = [
        self::DAILY,
        self::HOURLY
    ];

    /**
     * @throws ValidationException
     */
    private function __construct(
        private readonly string $name,
        private readonly int $intervalMinutes,
        private readonly ?int $id = null
    ) {
        $this->validateName($name);
    }

    /**
     * @throws ValidationException
     */
    private function validateName(string $name): void
    {
        $name = trim($name);

        if (!in_array($name, self::FREQUENCY_TYPES)) {
            throw new ValidationException([
                'name' => ["Invalid frequency name: {$name}"]
            ]);
        }
    }

    /**
     * @throws ValidationException
     */
    public static function daily(?int $id = null): self
    {
        return new self(self::DAILY, 24 * 60, $id);
    }

    /**
     * @throws ValidationException
     */
    public static function hourly(?int $id = null): self
    {
        return new self(self::HOURLY, 60, $id);
    }

    /**
     * @throws ValidationException
     */
    public static function fromName(string $name, ?int $id = null): self
    {
        return match ($name) {
            self::DAILY => self::daily($id),
            self::HOURLY => self::hourly($id),

            default => throw new ValidationException([
                'name' => ["Invalid frequency name: {$name}"]
            ])
        };
    }

    /**
     * @throws ValidationException
     */
    public static function fromId(int $id, string $name, int $intervalMinutes): self
    {
        return new self($name, $intervalMinutes, $id);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getIntervalMinutes(): int
    {
        return $this->intervalMinutes;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function equals(Frequency $other): bool
    {
        return $this->name === $other->getName();
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
