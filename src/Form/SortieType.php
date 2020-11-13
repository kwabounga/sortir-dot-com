<?php
namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Sortie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\GreaterThan;

class SortieType extends AbstractType
{
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // Nom
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'row_attr' => ['class' => 'form-row'],
                'attr' => ['class' => 'col-12 col-sm-10'],
                'label_attr' => ['class' => 'col-12 col-sm-2'],
            ])

            // Date de début
            ->add('debut', DateTimeType::class, [
                'date_widget' => 'single_text',
                'date_format' => 'yyyy-MM-dd',
                'time_widget' => 'single_text',
                'label' => 'Débute le',
                'required' => true,
            ])

            // Durée
            ->add('duree', TimeType::class, [
                'widget' => 'single_text',
                'label' => 'Durée',
                'row_attr' => ['class' => 'form-row'],
                'attr' => ['class' => 'col-12 col-sm-10'],
                'label_attr' => ['class' => 'col-12 col-sm-2'],
                'required' => true,
            ])

            // Date limite d'inscription
            ->add('limiteInscription', DateTimeType::class, [
                'date_widget' => 'single_text',
                'date_format' => 'yyyy-MM-dd',
                'time_widget' => 'single_text',
                'label' => 'Limite inscription',
                'required' => true,
            ])

            // Nombre maximum d'incription
            ->add('inscriptionMax', IntegerType::class, [
                'row_attr' => ['class' => 'form-row'],
                'attr' => ['class' => 'col-12 col-sm-10'],
                'label_attr' => ['class' => 'col-12 col-sm-2'],
                'required' => true,
                'data' => '20'
            ])

            // Campus
            ->add('campus', TextType::class, [
                'row_attr' => ['class' => 'form-row'],
                'attr' => ['class' => 'col-12 col-sm-10'],
                'label_attr' => ['class' => 'col-12 col-sm-2'],
                'data' => $options['user']->getCampus()->getNom(),
                'disabled' => true,
            ])

            // Lieu
            ->add('lieu', EntityType::class, [
                'class' => Lieu::class,
                'choice_label' => 'nom',
                'choice_value' => 'id',
                'attr' => ['class' => 'col-12 col-sm-10 form-control'],
                'label_attr' => ['class' => 'col-12 col-sm-2'],
            ])

            // Description
            ->add('infos', TextareaType::class, [
                'row_attr' => ['class' => 'form-row' ],
                'attr' => ['class' => 'col-12 col-sm-10', 'rows'=>5],
                'label_attr' => ['class' => 'col-12 col-sm-2'],
                'label' => 'Infos'
            ])

            ->add('save', SubmitType::class, [
                'attr' => ['class' => 'save bg-success'],
                'label' => 'Enregistrer',
            ])
            ->add('publish', SubmitType::class, [
                'attr' => ['class' => 'publish bg-primary'],
                'label' => 'Publier',
            ]);
            
       
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
        $resolver->setRequired('user');
    }
}

