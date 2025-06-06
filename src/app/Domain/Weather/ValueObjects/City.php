<?php

namespace App\Domain\Weather\ValueObjects;

use App\Exceptions\ValidationException;

class City
{
    /**
     * @throws ValidationException
     */
    public function __construct(
        private readonly string $name,
        private readonly ?int $id = null
    ) {
        $this->validateCityName($name);
    }

    /**
     * @throws ValidationException
     */
    public function validateCityName(string $name): void
    {
        $name = htmlspecialchars(trim($name));
        $len = strlen($name);

        if ($len < 2 || $len > 50) {
            throw new ValidationException([
                'city' => ["City name must be between 2 and 50 characters."]
            ]);
        }
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return array{id: int|null, name: string}
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name
        ];
    }
}
