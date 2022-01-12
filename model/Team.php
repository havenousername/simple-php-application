<?php

use JetBrains\PhpStorm\ArrayShape;

class Team
{
    private static int $counter = 0;
    private int $id;
    private string $name;
    private string $city;
    private bool $isFavourite;


    public function __construct(string $name, string $city) {
        $this->id = self::$counter++;
        $this->name = $name;
        $this->city = $city;
        $this->isFavourite = false;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getCity(): string
    {
        return $this->city;
    }

    /**
     * @return bool
     */
    public function isFavourite(): bool
    {
        return $this->isFavourite;
    }

    /**
     * @param bool $isFavourite
     * @return Team
     */
    public function setIsFavourite(bool $isFavourite): Team
    {
        $this->isFavourite = $isFavourite;
        return $this;
    }

    #[ArrayShape(['id' => "int", 'name' => "string", 'city' => "string", 'isFavourite' => "bool"])] public function getArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'city' => $this->city,
            'isFavourite' => $this->isFavourite,
        ];
    }
}