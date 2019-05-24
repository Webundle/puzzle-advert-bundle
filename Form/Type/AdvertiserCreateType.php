<?php

namespace Puzzle\AdvertBundle\Form\Type;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Puzzle\AdvertBundle\Form\Model\AbstractAdvertiserType;

/**
 * 
 * @author AGNES Gnagne Cédric <cecenho55@gmail.com>
 * 
 */
class AdvertiserCreateType extends AbstractAdvertiserType
{
    public function configureOptions(OptionsResolver $resolver) {
        parent::configureOptions($resolver);
        
        $resolver->setDefault('csrf_token_id', 'puzzle_advertiser_create');
        $resolver->setDefault('validation_groups', ['Create']);
    }
    
    public function getBlockPrefix() {
        return 'puzzle_admin_advert_advertiser_create';
    }
}