<?php

namespace App\Modules\Subscription\Domain\ValueObjects\Frequency;

use App\Exceptions\ValidationException;

enum FrequencyType: string
{
    case DAILY = 'daily';
    case HOURLY = 'hourly';

    public function getIntervalMinutes(): int
    {
        return match ($this) {
            self::DAILY => 24 * 60,   // 1440 minutes
            self::HOURLY => 60,       // 60 minutes
        };
    }

    /**
     * Get all available frequency type values
     * @return array<string>
     */
    public static function values(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }

    /**
     * Create from string with validation
     *
     * @throws ValidationException
     */
    public static function fromString(string $name): self
    {
        $name = trim($name);

        $type = self::tryFrom($name);

        if ($type === null) {
            throw new ValidationException([
                'name' => ["Invalid frequency name: {$name}"]
            ]);
        }

        return $type;
    }
}
