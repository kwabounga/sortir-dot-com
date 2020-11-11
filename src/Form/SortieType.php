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
        $date = new \DateTime();
        $date->modify('+1 day');
        $builder
            ->add('nom')
            ->add('debut', DateTimeType::class, [
                'label' => 'Débute le',
                'required' => true,
                'widget' => 'choice',
                'format' => 'dd/MM/yyyy',
            ])
            ->add('duree', TimeType::class, [
                'label' => 'Durée',
                'required' => true,
            ])
            ->add('limiteInscription', DateTimeType::class, [
                'label' => 'Limite inscription',
                'required' => true,
                'widget' => 'choice',
                'format' => 'dd/MM/yyyy',
            ])
            ->add('inscriptionMax', IntegerType::class, [
                'required' => true,
                'data' => '20'
            ])
            ->add('infos', TextareaType::class)
            ->add('campus', TextType::class, [
                'data' => $options['user']->getCampus()->getNom(),
                'disabled' => true,
            ])
            ->add('lieu', EntityType::class, [
                'class' => Lieu::class,
                'choice_label' => 'nom',
                'choice_value' => 'id',
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

