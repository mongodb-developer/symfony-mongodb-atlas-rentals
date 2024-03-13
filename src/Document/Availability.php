<?php

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
    protected $start_date;

    #[ODM\Field(type: Type::DATE)]
    protected $end_date;

    public function getStartDate(): DateTime|null
    {
        return $this->start_date;
    }

    public function setStartDate(DateTime $start_date): self
    {
        $this->start_date = $start_date;

        return $this;
    }

    public function getEndDate(): DateTime|null
    {
        return $this->end_date;
    }

    public function setEndDate(DateTime $end_date): self
    {
        $this->end_date = $end_date;

        return $this;
    }
}
