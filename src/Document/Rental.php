<?php

declare(strict_types=1);

namespace App\Document;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Doctrine\ODM\MongoDB\Types\Type;

/*
    * Rental
    ---------------------
    * This class is responsible for defining the rental document and its properties.
*/
#[
    MongoDB\Document(collection: 'rentals')
]
class Rental
{
    #[ODM\Id]
    protected $id;

    #[ODM\Field(type: Type::STRING)]
    protected $name;

    #[ODM\Field(type: Type::STRING)]
    protected $location;

    #[ODM\Field(type: Type::INT)]
    protected $night_cost;

    #[ODM\EmbedMany(targetDocument: Availability::class)]
    protected Collection $availability;

    public function __construct()
    {
        $this->availability = new ArrayCollection();

        // Create an instance of Availability and add it to the collection
        $initialAvailability = new Availability();
        $initialAvailability->setStartDate(new DateTime('2024-01-01'));
        $initialAvailability->setEndDate(new DateTime('2026-01-01'));
        $this->availability->add($initialAvailability);
    }

    // Add getters and setters for each property
    public function getId(): string|null
    {
        return $this->id;
    }

    public function getName(): string|null
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getNightCost(): float|null
    {
        return $this->night_cost;
    }

    public function setNightCost(float $night_cost): self
    {
        $this->night_cost = $night_cost;

        return $this;
    }

    public function getLocation(): string|null
    {
        return $this->location;
    }

    public function setLocation(string $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getAvailability(): Collection
    {
        return $this->availability;
    }

    public function setAvailability(Collection $availability): void
    {
        $this->availability = $availability;
    }
}
