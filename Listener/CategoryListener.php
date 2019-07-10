<?php

namespace Puzzle\AdvertBundle\Listener;

use Doctrine\ORM\EntityManager;
use Puzzle\AdminBundle\Event\AdminInstallationEvent;
use Puzzle\AdvertBundle\Entity\Category;
use Symfony\Component\Translation\TranslatorInterface;

class CategoryListener
{
    /**
     * @var EntityManager
     */
    private $em;
    
    /**
     * @var TranslatorInterface
     */
    private $translator;
    
    public function __construct(EntityManager $em, TranslatorInterface $translator){
        $this->em = $em;
        $this->translator = $translator;
    }
    
    public function onAdminInstalling(AdminInstallationEvent $event) {
        if (! $this->em->getRepository(Category::class)->findOneBy(['default' => true])) {
            $category = new Category();
            $category->setName($this->translator->trans('advert.category.default.name', [], 'advert'))
                     ->setDescription($this->translator->trans('advert.category.default.description', [], 'advert'))
                     ->setDefault(true);
            
            $this->em->persist($category);
            $this->em->flush($category);
            
            $event->notifySuccess($this->translator->trans('advert.category.default.success', [], 'advert'));
        }
        
        return;
    }
}

?>
