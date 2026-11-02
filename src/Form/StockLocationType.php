<?php

namespace App\Form;

use App\Entity\Couleur;
use App\Entity\ModeleLocation;
use App\Entity\StockLocation;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StockLocationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('taille')
            ->add('quantite')
            ->add('couleur', EntityType::class, [
                'class' => Couleur::class,
                'choice_label' => 'id',
            ])
            ->add('modeleLocation', EntityType::class, [
                'class' => ModeleLocation::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => StockLocation::class,
        ]);
    }
}
