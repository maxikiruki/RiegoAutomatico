<?php

namespace App\Form;

use App\Entity\Schedule;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ScheduleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array('label' => "Nombre",'attr' => array('maxlength' => '20', 'rows' => '1','required' => true, 'resize' => 'none')))
    
            ->add('description', TextAreaType::class, array('label' => "Descripcion",'attr' => array('maxlength' => '200', 'rows' => '1','required' => true)))
            ->add('start_time_morning', TimeType::class, array('label' => "Hora de inicio por la mañana", 'placeholder' => '' ))
            ->add('end_time_morning', TimeType::class, array('label' => "Hora de fin por la mañana", 'placeholder' => '' ))
            ->add('start_time_afternoon', TimeType::class, array('label' => "Hora de inicio por la tarde", 'placeholder' => '' ))
            ->add('end_time_afternoon', TimeType::class, array('label' => "Hora de fin por la tarde", 'placeholder' => '' ))
            // ->add('visible')
            ->add('monday', CheckboxType::class, array('label' => "Lunes " ))
            ->add('tuesday', CheckboxType::class, array('label' => "Martes " ))
            ->add('wednesday', CheckboxType::class, array('label' => "Miercoles " ))
            ->add('thursday', CheckboxType::class, array('label' => "Jueves " ))
            ->add('friday', CheckboxType::class, array('label' => "Viernes " ))
            ->add('saturday', CheckboxType::class, array('label' => "Sabado " ))
            ->add('sunday', CheckboxType::class, array('label' => "Domingo " ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Schedule::class,
        ]);
    }
}
