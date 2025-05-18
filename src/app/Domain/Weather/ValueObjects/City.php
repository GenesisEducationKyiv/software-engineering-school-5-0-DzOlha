<?php
namespace App\Domain\Weather\ValueObjects;

class City
{
    public function __construct(
        private readonly string $name,
        private readonly ?string $country = null,
        private readonly ?int $id = null
    ) {
        $this->validateCityName($name);
    }

    public function validateCityName($name): void
    {
        $name = htmlspecialchars(trim($name));
        $len = strlen($name);

        if($len < 2 || $len > 50) {
            throw new \InvalidArgumentException("Invalid city name: {$name}");
        }
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'country' => $this->country,
        ];
    }
}
