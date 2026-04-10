<?php

namespace App\Form;

use App\Entity\CommandeLocation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommandeLocationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date_commande', null, [
                'widget' => 'single_text',
            ])
            ->add('date_debut_loc', null, [
                'widget' => 'single_text',
            ])
            ->add('date_fin_loc', null, [
                'widget' => 'single_text',
            ])
            ->add('location')
            ->add('statut')
            ->add('montant_total')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CommandeLocation::class,
        ]);
    }
}
