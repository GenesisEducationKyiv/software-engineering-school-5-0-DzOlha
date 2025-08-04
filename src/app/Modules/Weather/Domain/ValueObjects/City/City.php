<?php

namespace App\Modules\Weather\Domain\ValueObjects\City;

use App\Exceptions\ValidationException;
use App\Modules\Weather\Domain\ValueObjects\City\Validator\CityValidator;
use App\Modules\Weather\Domain\ValueObjects\City\Validator\CityValidatorInterface;

class City
{
    /**
     * @throws ValidationException
     */
    public function __construct(
        private readonly string $name,
        private readonly ?int $id = null,
        private ?CityValidatorInterface $validator = null
    ) {
        if (!$this->validator) {
            $this->validator = new CityValidator();
        }

        $this->validator->assertValid($name);
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
