<?php
namespace App\Form;

use App\Document\Booking;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

/*
    * BookingType
    ---------------------
    * This class is responsible for creating the form for the booking entity.
*/
class BookingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('rentalId', TextType::class)
            ->add('rentalName', TextType::class)
            ->add('totalCost', NumberType::class)
            ->add('startDate', NumberType::class)
            ->add('endDate', NumberType::class);
            // Add other fields as needed
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Booking::class,
        ]);
    }
}