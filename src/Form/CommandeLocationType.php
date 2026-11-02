<?php

namespace App\Form;

use App\Entity\CommandeLocation;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommandeLocationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'email',
            ])

            ->add('statut', ChoiceType::class, [
                'choices' => [
                    'En attente' => 'en_attente',
                    'Validée' => 'valide',
                    'Refusée' => 'refusee',
                ],
            ])

            ->add('commentaire', TextareaType::class, [
                'required' => false,
            ])

            ->add('creneaux_client', TextareaType::class, [
                'required' => false,
            ])
            
            ->add('proposition_debut', DateTimeType::class, [
                'widget' => 'single_text',
                'required' => false,
            ])

            ->add('proposition_fin', DateTimeType::class, [
                'widget' => 'single_text',
                'required' => false,
            ])

            ->add('date_debut_validee', DateTimeType::class, [
                'widget' => 'single_text',
                'required' => false,
            ])

            ->add('date_fin_validee', DateTimeType::class, [
                'widget' => 'single_text',
                'required' => false,
            ]);
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CommandeLocation::class,
        ]);
    }
}