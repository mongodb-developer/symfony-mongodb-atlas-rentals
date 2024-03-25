<?php
/** 
 * RentalType - This class is responsible for creating the form for the rental entity.]
 **/
declare(strict_types=1);

namespace App\Form;

use App\Document\Rental;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * RentalType
    ---------------------
 * This class is responsible for creating the form for the rental entity.
 **/
class RentalType extends AbstractType
{
    /** 
     *  buildForm -  This function is responsible for building the form for the rental entity.
     * 
     * @param FormBuilderInterface $builder - The form builder interface
     * @param array                $options - The options for the form
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class)
            ->add('location', TextType::class)
            ->add('nightCost', NumberType::class);
            // Add other fields as needed
    }

    /** 
     *  configureOptions - This function is responsible for setting the default options for the form.
     * 
     * @param OptionsResolver $resolver - The options resolver
     * */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
            'data_class' => Rental::class,
            ]
        );
    }
}