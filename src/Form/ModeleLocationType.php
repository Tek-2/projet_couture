<?php

namespace App\Form;

use App\Entity\Entretien;
use App\Entity\ModeleLocation;
use App\Entity\TypeLocation;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ModeleLocationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('libelle')
            ->add('prixLocation')
            ->add('tissu')
            ->add('silhouette')
            ->add('longueur')
            ->add('longueurManches')
            ->add('formeCol')
            ->add('description')
            ->add('entretien', EntityType::class, [
                'class' => Entretien::class,
                'choice_label' => 'id',
            ])
            ->add('type', EntityType::class, [
                'class' => TypeLocation::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ModeleLocation::class,
        ]);
    }
}
