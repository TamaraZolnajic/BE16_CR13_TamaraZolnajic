<?php

namespace App\Form;

use App\Entity\Events;
use App\Entity\Location;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use App\Service\FileUploader;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class EventsType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options): void
  {
      $builder
          ->add('name', TextType::class, [
              'attr' => ['class' => 'form-control', 'style' => 'margin-bottom:15px', "placeholder"=>"Event Name"]
          ])
          ->add('Date', DateTimeType::class, [
            'attr' => ['style' => 'margin-bottom:15px']
        ])
          ->add('description', TextType::class, [
            'attr' => ["class"=>"form-control mb-2", "placeholder"=>"Event Description"]
        ])
          ->add('picture', FileType::class, [
            'label' => 'Upload Picture',
            //unmapped means that is not associated to any entity property
                        'mapped' => false,
            //not mandatory to have a file
                        'required' => false,
            
            //in the associated entity, so you can use the PHP constraint classes as validators
                        'constraints' => [
                            new File([
                                'maxSize' => '5048k', // 14
                                'mimeTypes' => [
                                    'image/png',
                                    'image/jpeg',
                                    'image/jpg',
                                ],
                                'mimeTypesMessage' => 'Please upload a valid image file',
                            ])
                        ],        ])
          
          ->add('capacity', NumberType::class, [
            'attr' => ["class"=>"form-control mb-2", "placeholder"=>"Event Capacity"]
        ])
        ->add('email', TextType::class, [
              'attr' => ["class"=>"form-control mb-2", "placeholder"=>"Email"]
          ])
          ->add('phone', NumberType::class, [
              'attr' => ["class"=>"form-control mb-2", "placeholder"=>"Phone Number"]
          ])
          ->add('URL', TextType::class, [
            'attr' => ["class"=>"form-control mb-2", "placeholder"=>"Events URL"]
        ])   
        ->add('type', ChoiceType::class, [
            'choices' => ['Music' => 'Music', 'Sport' => 'Sport', 'Movie' => 'Movie', 'Theater' => 'Theater', 'Food' => 'Food', 'Exhibition' => 'Exhibition'],
            'attr' => ['class' => 'form-control', 'style' => 'margin-bottom:15px']
        ])
        ->add('fklocation', EntityType::class, [
            'class' => Location::class,
            'choice_label' => 'address',
        ])   
        ->add('save', SubmitType::class, [
              'label' => 'Create Event',
              'attr' => ['class' => 'btn-primary', 'style' => 'margin-bottom:15px']
          ]);
  }

  public function configureOptions(OptionsResolver $resolver): void
  {
      $resolver->setDefaults([
          'data_class' => Events::class,
      ]);
  }
}