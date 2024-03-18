<?php

declare(strict_types=1);

namespace App\Form;

use App\Document\Booking;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/*
    * BookingType
    ---------------------
    * This class is responsible for creating the form for the booking entity.
*/
class BookingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('rentalId', TextType::class)
            ->add('rentalName', TextType::class)
            ->add('totalCost', NumberType::class)
            ->add('startDate', NumberType::class)
            ->add('endDate', NumberType::class);
            // Add other fields as needed
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Booking::class,
        ]);
    }
}
