<?php

namespace App\Form;

use App\Entity\Campus;
use App\Form\Model\FiltreHomeDTO;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FiltreHomeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // Recherche: campus
            ->add('campusSearch', EntityType::class, [
                'row_attr' => ['class' => 'form-group form-row align-items-center'],
                'attr' => ['class' => 'col-12 col-md-9 form-control'],
                'label' => 'Campus :',
                'label_attr' => ['class' => 'col-12 col-md-3'],
                'class' => Campus::class,
                'choice_label' => 'nom']
            )

            // Recherche: date de début
            ->add('dateDebutSearch', DateType::class, [
                'row_attr' => ['class' => 'form-group col-12 col-xl-6 form-row align-items-center'],
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'attr' => ['class' => 'col-12 col-md-9 form-control'],
                'label' => 'Après le :',
                'label_attr' => ['class' => 'col-12 col-md-3'],
                
                'required' => false
            ])

            // Recherche: date de fin
            ->add('dateFinSearch', DateType::class, [
                'row_attr' => ['class' => 'form-group col-12 col-xl-6 form-row align-items-center'],
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'attr' => ['class' => 'col-12 col-md-9 form-control'],
                'label' => 'Avant le :',
                'label_attr' => ['class' => 'col-12 col-md-3'],
                'required' => false
            ])

            // Recherche: Checkbox organisateur
            ->add('sortieOrgaSearch', CheckboxType::class, [
                'attr' => ['class' => 'form-check-input'],
                'label' => 'Sorties dont je suis l\'organisateur/trice',
                'label_attr' => ['class' => 'form-check-label'],
                'required' => false
            ])

            // Recherche: Checkbox organisateur
            ->add('sortieInscritSearch', CheckboxType::class, [
                'attr' => ['class' => 'form-check-input'],
                'label' => 'Sorties auxquelles je suis inscrit/e',
                'label_attr' => ['class' => 'form-check-label'],
                'required' => false
            ])

            // Recherche: Checkbox organisateur
            ->add('sortiePasInscritSearch', CheckboxType::class, [
                'attr' => ['class' => 'form-check-input'],
                'label' => 'Sorties auxquelles je ne suis pas inscrit/e',
                'label_attr' => ['class' => 'form-check-label'],
                'required' => false
            ])

            // Recherche: Checkbox organisateur
            ->add('sortiePasseeSearch', CheckboxType::class, [
                'attr' => ['class' => 'form-check-input'],
                'label' => 'Sorties passées',
                'label_attr' => ['class' => 'form-check-label'],
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => FiltreHomeDTO::class
        ]);
        $resolver->setRequired('user');
    }
}
