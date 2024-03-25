<?php
/**
 * Availability
 * 
 * This class is responsible for defining the availability 
 * sub document and its properties.
 * 
 * @category Document
 * @package  App\Document
 * @author   pavel.duchovny
 * @license  apache-2.0
 */

declare(strict_types=1);

namespace App\Document;

use DateTime;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Doctrine\ODM\MongoDB\Types\Type;
/*
    * Availability
    ---------------------
    * This class is responsible for defining the availability sub document and its properties.
*/

#[
    ODM\EmbeddedDocument
]
class Availability
{
    #[ODM\Field(type: Type::DATE)]
    public DateTime $startDate;

    #[ODM\Field(type: Type::DATE)]
    public DateTime $endDate;

   
}
