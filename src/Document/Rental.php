<?php
/**
 * Rental
 * ---------------------
 * This class is responsible for defining the rental document and its properties.
 *
 * @category Document
 * @package  App\Document
 * @author   pavel.duchovny 
 * @license  apache-2.0
 */

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
    public  $id;

    #[ODM\Field(type: Type::STRING)]
    public string $name;

    #[ODM\Field(type: Type::STRING)]
    public string $location;

    #[ODM\Field(type: Type::INT)]
    public int $nightCost;

    #[ODM\EmbedMany(targetDocument: Availability::class)]
    public Collection $availability;

    /**
     * __construct - 
     * This function is responsible for initializing the Rental class.
     * 
     * @return void
     */
    public function __construct()
    {
        $this->availability = new ArrayCollection();

        // Create an instance of Availability and add it to the collection
        $initialAvailability = new Availability();
        $initialAvailability->startDate =  new DateTime('2024-01-01');
        $initialAvailability->endDate = new DateTime('2026-01-01');
        $this->availability->add($initialAvailability);
    }

   
    /**
     * Function : calcAvailabilitySlots 
     * 
     * This function is responsible for calculating the 
     * availability slots based on the booking start and end dates.
     * 
     * @param DateTime $bookingStart - The booking start date
     * @param DateTime $bookingEnd   - The booking end date
     * @param array    $availability - The availability array
     * 
     * @return ArrayCollection - The new availability slots
     */
    public function calcAvailabilitySlots(DateTime $bookingStart, 
        DateTime $bookingEnd, 
        array $availability
    ): ArrayCollection {
        // Create a new ArrayCollection to store the new availability
        $newAvailability = new ArrayCollection();

        // Loop through each period in the availability to calculate the new availability
        foreach ($availability as $period) {
            $periodStart = $period->startDate;
            $periodEnd = $period->endDate;

            // Booking is entirely before this period
            if ($bookingEnd < $periodStart) {
                $newAvailability->add($period);
                continue;
            }

            // Booking is entirely after this period
            if ($bookingStart > $periodEnd) {
                $newAvailability->add($period);
                continue;
            }

            // Booking starts before the period and ends within it
            if ($bookingStart <= $periodStart && $bookingEnd < $periodEnd) {
                $newPeriod = new Availability();
                $newPeriod->startDate = $bookingEnd->modify('+1 day');
                $newPeriod->endDate = $periodEnd;
                $newAvailability->add($newPeriod);
                continue;
            }

            // Booking starts during the period and ends after it
            if ($bookingStart > $periodStart && $bookingEnd >= $periodEnd) {
                $newPeriod = new Availability();
                $newPeriod->startDate= $periodStart;
                $newPeriod->endDate = $bookingStart->modify('-1 day');
                $newAvailability->add($newPeriod);
                continue;
            }

            // Booking is entirely within the period
            if ($bookingStart > $periodStart && $bookingEnd < $periodEnd) {
                $newPeriod1 = new Availability();
                $newPeriod1->startDate = $periodStart;
                $newPeriod1->endDate = $bookingStart->modify('-1 day');
                $newAvailability->add($newPeriod1);

                $newPeriod2 = new Availability();
                $newPeriod2->startDate = $bookingEnd->modify('+1 day');
                $newPeriod2->endDate = $periodEnd;
                $newAvailability->add($newPeriod2);
                continue;
            }

            // Booking covers the entire period
            // Do not add the period to newAvailability (effectively removing it)
        }

        return $newAvailability;
    }
}
