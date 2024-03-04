<?php
namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use App\Document\Booking;

use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\Routing\Annotation\Route;


use Psr\Log\LoggerInterface;

/*
    * BookingController
    ---------------------
    * This class is responsible for handling the booking query operations.
*/
class BookingController extends AbstractController
{
    // DocumentManager instance
    private $documentManager;
    private $logger;
    // Constructor to initialize the DocumentManager and LoggerInterface
    public function __construct(DocumentManager $documentManager , LoggerInterface $logger)
    {
        $this->documentManager = $documentManager;
        $this->logger = $logger;
    }

    // Index action to display all the bookings
    #[Route('/booking', name: 'booking_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        // Fetch all the bookings
        $bookings = $this->documentManager->getRepository(Booking::class)->findAll();
        
        // Render the bookings page
        return $this->render('rental/bookings.html.twig', ['bookings' => $bookings]);
    }

    

}
