<?php

namespace App\Form;

use App\Entity\CommandeLocation;
use App\Entity\LigneCommandeLocation;
use App\Entity\StockLocation;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LigneCommandeLocationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('prix')
            ->add('quantite')
            ->add('id_commandeloc', EntityType::class, [
                'class' => CommandeLocation::class,
                'choice_label' => 'id',
            ])
            ->add('id_stockloc', EntityType::class, [
                'class' => StockLocation::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => LigneCommandeLocation::class,
        ]);
    }
}
