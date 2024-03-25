<?php
/**
 * Booking
 * ---------------------
 * This class is responsible for defining the booking document and its properties.
 *
 * @category Document
 * @package  App\Document
 * @author   pavel.duchovny 
 * @license  apache-2.0
 */

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
    public  $id;

    #[ODM\ReferenceOne(targetDocument: Rental::class)]
    public Rental $rental;

    #[ODM\Field(type: Type::STRING)]
    public string $rentalName;

    #[ODM\Field(type: Type::STRING)]
    public string $location;

    #[ODM\Field(type: Type::INT)]
    public int $totalCost;

    #[ODM\Field(type: Type::DATE_IMMUTABLE)]
    public DateTimeImmutable $startDate;

    #[ODM\Field(type: Type::DATE_IMMUTABLE)]
    public DateTimeImmutable $endDate;

    // Add getters and setters for each property
   
}
