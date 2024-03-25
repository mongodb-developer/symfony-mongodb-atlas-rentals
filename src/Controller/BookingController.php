<?php
/**
 * BookingController
 * ---------------------
 * This class is responsible for handling the booking query operations.
 * 
 * @category Controller
 * @package  App\Controller
 * @author   pavel.duchovny
 * @license  apache-2.0
 */

declare(strict_types=1);

namespace App\Controller;

use App\Document\Booking;
use Doctrine\ODM\MongoDB\DocumentManager;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/*
    * BookingController
    ---------------------
    * This class is responsible for handling the booking query operations.
*/
class BookingController extends AbstractController
{
    // DocumentManager instance
    private $_documentManager;
    private $_logger;
    /**
     * __construct -
     * 
     * This function is responsible for initializing the BookingController class.
     * 
     * @param DocumentManager $_documentManager - The document manager
     * @param LoggerInterface $_logger          - The _logger interface
     */
    public function __construct(DocumentManager $_documentManager, LoggerInterface $_logger)
    {
        $this->_documentManager = $_documentManager;
        $this->_logger          = $_logger;
    }

    
    /**
     * index -
     * 
     * This function is responsible for rendering the bookings page.
     * 
     * @param Request $request - The request object
     * 
     * @return Response
     */
    #[Route('/booking', name: 'booking_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        // Fetch all the bookings
        $bookings = $this->_documentManager->getRepository(Booking::class)->findAll();

        // Render the bookings page
        return $this->render('rental/bookings.html.twig', ['bookings' => $bookings]);
    }
}
