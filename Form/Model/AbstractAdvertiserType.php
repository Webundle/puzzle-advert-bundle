<?php

namespace Puzzle\AdvertBundle\Form\Model;

use Puzzle\AdvertBundle\Entity\Advertiser;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

/**
 * @author AGNES Gnagne Cédric <cecenho55@gmail.com>
 */
class AbstractAdvertiserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options){
        parent::buildForm($builder, $options);
        $builder->add('name', TextType::class)
                ->add('description', TextareaType::class, ['required' => false])
                ->add('email', TextType::class, ['required' => false])
                ->add('phoneNumber', TextType::class, ['required' => false])
                ->add('picture', HiddenType::class, ['mapped' => false, 'required' => false])
        ;
    }
    
    public function configureOptions(OptionsResolver $resolver){
        $resolver->setDefaults(array(
            'data_class' => Advertiser::class,
            'validation_groups' => array(
                Advertiser::class,
                'determineValidationGroups',
            ),
        ));
    }
}