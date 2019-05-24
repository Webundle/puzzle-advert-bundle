<?php

namespace Puzzle\AdvertBundle\Form\Model;

use Puzzle\AdvertBundle\Entity\Advertiser;
use Puzzle\AdvertBundle\Entity\Category;
use Puzzle\AdvertBundle\Entity\Post;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * 
 * @author AGNES Gnagne Cédric <cecenho55@gmail.com>
 * 
 */
class AbstractPostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options){
        parent::buildForm($builder, $options);
        $builder
            ->add('name', TextType::class)
            ->add('description', TextareaType::class, ['required' => false])
            ->add('email', TextType::class, ['required' => false])
            ->add('tag', TextType::class, ['required' => false])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
            ])
            ->add('advertiser', EntityType::class, [
                'class' => Advertiser::class,
                'choice_label' => 'name',
            ])
            ->add('pinned', CheckboxType::class, ['required' => false])
            ->add('enablePostulate', CheckboxType::class, ['required' => false])
            ->add('expiresAt', TextType::class, [
                'mapped' => false,
                'required' => false
            ])
        ;
    }
    
    public function configureOptions(OptionsResolver $resolver){
        $resolver->setDefaults(array(
            'data_class' => Post::class,
            'validation_groups' => array(
                Post::class,
                'determineValidationGroups',
            ),
        ));
    }
}