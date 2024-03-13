<?php

declare(strict_types=1);

namespace App\Document;

use DateTime;
use DateTimeImmutable;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Doctrine\ODM\MongoDB\Types\Type;

/*
    * Booking
    ---------------------
    * This class is responsible for defining the booking document and its properties.
*/
#[
    MongoDB\Document(collection: 'bookings')
]
class Booking
{
    #[ODM\Id]
    protected $id;

    #[ODM\Field(type: Type::STRING)]
    protected $rental_id;

    #[ODM\Field(type: Type::STRING)]
    protected $rental_name;

    #[ODM\Field(type: Type::STRING)]
    protected $location;

    #[ODM\Field(type: Type::INT)]
    protected $total_cost;

    #[ODM\Field(type: Type::DATE_IMMUTABLE)]
    protected $start_date;

    #[ODM\Field(type: Type::DATE_IMMUTABLE)]
    protected $end_date;

    // Add getters and setters for each property
    public function getId(): string|null
    {
        return $this->id;
    }

    public function getRentalId(): string|null
    {
        return $this->rental_id;
    }

    public function setRentalId(string $rental_id): self
    {
        $this->rental_id = $rental_id;

        return $this;
    }

    public function getRentalName(): string|null
    {
        return $this->rental_name;
    }

    public function setRentalName(string|null $rental_name): self
    {
        $this->rental_name = $rental_name;

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

    public function getTotalCost()
    {
        return $this->total_cost;
    }

    public function setTotalCost(int|null $total_cost): self
    {
        $this->total_cost = $total_cost;

        return $this;
    }

    public function getStartDate(): DateTimeImmutable
    {
        return $this->start_date;
    }

    public function setStartDate(DateTime $start_date): void
    {
        if ($start_date instanceof DateTime) {
            $start_date = DateTimeImmutable::createFromMutable($start_date);
        }

        $this->start_date = $start_date;
    }

    public function getEndDate(): DateTimeImmutable
    {
        return $this->end_date;
    }

    public function setEndDate(DateTime $end_date): void
    {
        if ($end_date instanceof DateTime) {
            $end_date = DateTimeImmutable::createFromMutable($end_date);
        }

        $this->end_date = $end_date;
    }
}
