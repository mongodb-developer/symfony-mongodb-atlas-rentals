<?php

declare(strict_types=1);

namespace App\Controller;

use App\Document\Booking;
use App\Document\Rental;
use App\Form\RentalType;
use DateTime;
use DateTimeImmutable;
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
        $checkInInput = $request->query->get('check_in');
        $checkOutInput = $request->query->get('check_out');

        $checkIn = $checkInInput ? new DateTime($checkInInput) : null;
        $checkOut = $checkOutInput ? new DateTime($checkOutInput) : null;
        // Fetch all the rentals
        if ($city && $checkIn && $checkOut) {
            // Fetch rentals based on city and availability
            // The availability field is an array of objects with start_date and end_date fields
            // So we use elemMatch to query the availability array and see if the user requested dates are available
            $rentalRepository = $this->documentManager->getRepository(Rental::class);
            $queryBuilder = $rentalRepository->createQueryBuilder();

            $rentals = $queryBuilder
                ->field('location')->equals(new Regex($city, 'i'))
                ->field('availability')->elemMatch(
                    $queryBuilder->expr()
                        ->field('startDate')->lte($checkIn)
                        ->field('endDate')->gte($checkOut),
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
        $checkInInput = $request->query->get('check_in');
        $checkOutInput = $request->query->get('check_out');

        $checkIn = $checkInInput ? new DateTime($checkInInput) : null;
        $checkOut = $checkOutInput ? new DateTime($checkOutInput) : null;
        $rental   = $this->documentManager->getRepository(rental::class)->find($id);

        // Calculate total price based on night cost and number of days
        if ($checkIn && $checkOut) {
            $interval   = $checkIn->diff($checkOut);
            $days       = $interval->days;
            $totalPrice =  $days * $rental->nightCost;
        } else {
            $totalPrice= 0;
        }
    


        if (! $rental) {
            throw $this->createNotFoundException('No rental found for id ' . $id);
        }

        // Render the rental details page
        return $this->render('rental/details.html.twig', [
            'rental' => $rental,
            'totalPrice' => $totalPrice,
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

        $startDateInput = $request->request->get('startDate');
        $endDateInput = $request->request->get('endDate');

        $startDate = $startDateInput ? new DateTime($startDateInput) : null;
        $endDate = $endDateInput ? new DateTime($endDateInput) : null;




        // Calculate total price based on night cost and number of days
        $interval   = $startDate->diff($endDate);
        $days       = $interval->days;
        $totalPrice = $days * $rental->nightCost;

        // Calculate new availability based on the booking dates
        $newAvailability = $rental->calcAvailabilitySlots($startDate, $endDate, $rental->availability->toArray());

        // Create and persist the booking
        $booking = new Booking();
        $booking->rental = $rental;
        $booking->rentalName = $rental->name;
        $booking->startDate =  DateTimeImmutable::createFromMutable($startDate);
        $booking->endDate =  DateTimeImmutable::createFromMutable($endDate);
        $booking->totalCost = (int)$totalPrice;
        $rental->availability = $newAvailability;

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

    
}
