<?php
namespace src\Form;

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
            ->add('nom')
            ->add('debut', DateTimeType::class, [
                'label' => 'Débute le',
                'required' => true,
                'widget' => 'choice',
                'format' => 'yyyy-MM-dd',
                'data' => new \DateTime(),
                'invalid_message' => 'The password fields must match.',
            ])
            ->add('duree', TimeType::class, [
                'label' => 'Durée',
                'required' => true,
                'invalid_message' => 'The password fields must match.',
            ])
            ->add('limiteInscription', DateTimeType::class, [
                'label' => 'Limite inscription',
                'required' => true,
                'widget' => 'choice',
                'format' => 'dd/MM/yyyy',
                'data' => new \DateTime(),
                'invalid_message' => 'The password fields must match.',
            ])
            ->add('inscriptionMax', IntegerType::class, [
                'required' => true,
                'data' => '2'
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
                'attr' => ['class' => 'save'],
            ])
            ->add('publish', SubmitType::class, [
                'attr' => ['class' => 'publish'],
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

