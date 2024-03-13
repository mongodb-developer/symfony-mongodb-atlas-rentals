<?php

declare(strict_types=1);

namespace App\Controller;

use App\Document\Availability;
use App\Document\Booking;
use App\Document\Rental;
use App\Form\RentalType;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\MongoDB\DocumentManager;
use MongoDB\BSON\Regex;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
/*
    * RentalController
    ---------------------
    * This class is responsible for handling the rental query and booking operations.
*/

class RentalController extends AbstractController
{
    // DocumentManager instance
    private $documentManager;
    private $logger;
    // Constructor to initialize the DocumentManager and LoggerInterface
    public function __construct(DocumentManager $documentManager, LoggerInterface $logger)
    {
        $this->documentManager = $documentManager;
        $this->logger          = $logger;
    }

    // Index action to display all the rentals or filter by city and availability

    #[Route('/', name: 'rental_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        // Get the city, check-in and check-out dates from the query parameters
        $city     = $request->query->get('city');
        $checkIn  =  new DateTime($request->query->get('check_in'));
        $checkOut =  new DateTime($request->query->get('check_out'));

        // Fetch all the rentals
        if ($city && $checkIn && $checkOut) {
            // Fetch rentals based on city and availability
            // The availability field is an array of objects with start_date and end_date fields
            // So we use elemMatch to query the availability array and see if the user requested dates are available
            $rentalRepository = $this->documentManager->getRepository(Rental::class);
            $queryBuilder     = $rentalRepository->createQueryBuilder();

            $rentals = $queryBuilder
                ->field('location')->equals(new Regex($city, 'i'))
                ->field('availability')->elemMatch(
                    $queryBuilder->expr()
                        ->field('start_date')->lte($checkIn)
                        ->field('end_date')->gte($checkOut),
                )
                ->getQuery()
                ->execute();
        } else {
            // Fetch all the rentals
            $rentals = $this->documentManager->getRepository(Rental::class)->findAll();
        }

        // Render the rentals page
        return $this->render('rental/index.html.twig', ['rentals' => $rentals]);
    }

    // Details action to display the rental details and calculate the total price

    #[Route('/rental/{id}', name: 'rental_details', methods: ['GET'])]
    public function details(Request $request, string $id): Response
    {
        $checkIn  =  new DateTime($request->query->get('check_in'));
        $checkOut =  new DateTime($request->query->get('check_out'));
        $rental   = $this->documentManager->getRepository(rental::class)->find($id);

        // Calculate total price based on night cost and number of days
        $interval   = $checkIn->diff($checkOut);
        $days       = $interval->days;
        $totalPrice = $days * $rental->getNightCost();

        if (! $rental) {
            throw $this->createNotFoundException('No rental found for id ' . $id);
        }

        // Render the rental details page
        return $this->render('rental/details.html.twig', [
            'rental' => $rental,
            'total_price' => $totalPrice,
        ]);
    }

    // Create action to create a new rental

    #[Route('/rental/create', name: 'rental_create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        // Create a new rental instance
        $rental = new rental();
        // print request
        $this->logger->info('Request: ' . $request->getContent());

        // Create a form to create a new rental
        $form = $this->createForm(RentalType::class, $rental);
        $form->handleRequest($request);

        // If the form is submitted and valid, persist the rental and redirect to the rentals page
        if ($form->isSubmitted() && $form->isValid()) {
            $this->documentManager->persist($rental);
            $this->documentManager->flush();

            return $this->redirectToRoute('rental_index');
        }

        // Render the create rental page
        return $this->render('rental/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // Book action to book a rental

    #[Route('/rental/book/{rentalId}', name: 'rental_book', methods: ['POST'])]
    public function book(Request $request, $rentalId): Response
    {
        // Fetch the rental by id
        $rental = $this->documentManager->getRepository(Rental::class)->find($rentalId);

        // If the rental is not found, throw a 404 exception
        if (! $rental) {
            throw $this->createNotFoundException('rental not found');
        }

        // Get the start and end dates from the request
        $startDate = new DateTime($request->request->get('startDate'));
        $endDate   = new DateTime($request->request->get('endDate'));

        // Calculate total price based on night cost and number of days
        $interval   = $startDate->diff($endDate);
        $days       = $interval->days;
        $totalPrice = $days * $rental->getNightCost();

        // Calculate new availability based on the booking dates
        $newAvailability = $this->calcAvailabilitySlots($startDate, $endDate, $rental->getAvailability()->toArray());

        // Create and persist the booking
        $booking = new Booking();
        $booking->setRentalId($rentalId);
        $booking->setRentalName($rental->getName());
        $booking->setStartDate($startDate);
        $booking->setEndDate($endDate);
        $booking->setTotalCost($totalPrice);
        $rental->setAvailability($newAvailability);

        // Persist the booking and rental
        $this->documentManager->persist($booking);
        $this->documentManager->persist($rental);
        $this->documentManager->flush();

        // Redirect to a confirmation page or show confirmation message
        return $this->render('rental/confirmation.html.twig', [
            'rental' => $rental,
            'booking' => $booking,
            'totalPrice' => $totalPrice,
        ]);
    }

    private function calcAvailabilitySlots(DateTime $bookingStart, DateTime $bookingEnd, array $availability): ArrayCollection
    {
        // Create a new ArrayCollection to store the new availability
        $newAvailability = new ArrayCollection();

        // Loop through each period in the availability to calculate the new availability
        foreach ($availability as $period) {
            $periodStart = $period->getStartDate();
            $periodEnd   = $period->getEndDate();

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
                $newPeriod->setStartDate($bookingEnd->modify('+1 day'));
                $newPeriod->setEndDate($periodEnd);
                $newAvailability->add($newPeriod);
                continue;
            }

            // Booking starts during the period and ends after it
            if ($bookingStart > $periodStart && $bookingEnd >= $periodEnd) {
                $newPeriod = new Availability();
                $newPeriod->setStartDate($periodStart);
                $newPeriod->setEndDate($bookingStart->modify('-1 day'));
                $newAvailability->add($newPeriod);
                continue;
            }

            // Booking is entirely within the period
            if ($bookingStart > $periodStart && $bookingEnd < $periodEnd) {
                $newPeriod1 = new Availability();
                $newPeriod1->setStartDate($periodStart);
                $newPeriod1->setEndDate($bookingStart->modify('-1 day'));
                $newAvailability->add($newPeriod1);

                $newPeriod2 = new Availability();
                $newPeriod2->setStartDate($bookingEnd->modify('+1 day'));
                $newPeriod2->setEndDate($periodEnd);
                $newAvailability->add($newPeriod2);
                continue;
            }

            // Booking covers the entire period
            // Do not add the period to newAvailability (effectively removing it)
        }

        return $newAvailability;
    }
}
