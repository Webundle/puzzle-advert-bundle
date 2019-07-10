<?php
namespace Puzzle\AdvertBundle\Controller;

use Puzzle\AdvertBundle\Entity\Archive;
use Puzzle\AdvertBundle\Entity\Category;
use Puzzle\AdvertBundle\Entity\Post;
use Puzzle\AdvertBundle\Form\Type\CategoryCreateType;
use Puzzle\AdvertBundle\Form\Type\CategoryUpdateType;
use Puzzle\AdvertBundle\Form\Type\PostCreateType;
use Puzzle\AdvertBundle\Form\Type\PostUpdateType;
use Puzzle\MediaBundle\MediaEvents;
use Puzzle\MediaBundle\Event\FileEvent;
use Puzzle\MediaBundle\Util\MediaUtil;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Puzzle\AdvertBundle\Entity\Advertiser;
use Puzzle\AdvertBundle\Form\Type\AdvertiserCreateType;
use Puzzle\AdvertBundle\Form\Type\AdvertiserUpdateType;
use Puzzle\AdvertBundle\Entity\Postulate;

/**
 * @author AGNES Gnagne Cedric <cecenho55@gmail.com>
 */
class AdminController extends Controller
{
    /***
     * Show categories
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listCategoriesAction(Request $request) {
        return $this->render("AdvertBundle:Admin:list_categories.html.twig", array(
            'categories' => $this->getDoctrine()->getRepository(Category::class)->findBy(['parentNode' => null])
        ));
    }
    
    /***
     * Show category
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showCategoryAction(Request $request, Category $category) {
        $rep = $this->getDoctrine()->getRepository(Category::class);
        return $this->render("AdvertBundle:Admin:show_category.html.twig", array(
            'category' => $category,
            'childNodes' => $rep->findBy(['parentNode' => $category->getId()])
        ));
    }
    
    /***
     * Create category
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createCategoryAction(Request $request) {
        $category = new Category();
        $em = $this->getDoctrine()->getManager();
        $parentId = $request->query->get('parent');
        
        if ($parentId !== null && $parent = $em->getRepository(Category::class)->find($parentId)){
            $category->setParentNode($parent);
        }
        
        $form = $this->createForm(CategoryCreateType::class, $category, [
            'method' => 'POST',
            'action' => $parentId ? 
                        $this->generateUrl('puzzle_admin_advert_category_create', ['parent' => $parentId]) : 
                        $this->generateUrl('puzzle_admin_advert_category_create')
        ]);
        $form->handleRequest($request);
       
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $em->persist($category);
            $em->flush();
            
            $message = $this->get('translator')->trans('advert.category.create.success', [
                '%categoryName%' => $category->getName()
            ], 'advert');
            
            if ($request->isXmlHttpRequest() === true) {
                return new JsonResponse($message);
            }
            
            $this->addFlash('success', $message);
            
            if (null !== $parentId) {
                return $this->redirectToRoute('puzzle_admin_advert_category_show', ['id' => $parentId]);
            }
            
            return $this->redirectToRoute('puzzle_admin_advert_category_list');
        }
        
        return $this->render("AdvertBundle:Admin:create_category.html.twig", array(
            'form' => $form->createView()
        ));
    }
    
    
    /***
     * Update category
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateCategoryAction(Request $request, Category $category) {
        $parentId = $request->query->get('parent');
        
        $form = $this->createForm(CategoryUpdateType::class, $category, [
            'method' => 'POST', 
            'action' => $parentId ?
                        $this->generateUrl('puzzle_admin_advert_category_update', ['id' => $category->getId(), 'parent' => $parentId]) :
                        $this->generateUrl('puzzle_admin_advert_category_update', ['id' => $category->getId()])
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            
            $message = $this->get('translator')->trans('advert.category.update.success', [
                '%categoryName%' => $category->getName()
            ], 'advert');
            
            if ($request->isXmlHttpRequest() === true) {
                return new JsonResponse($message);
            }
            
            $this->addFlash('success', $message);
            
            if (null !== $parentId) {
                return $this->redirectToRoute('puzzle_admin_advert_category_show', ['id' => $parentId]);
            }
            
            return $this->redirectToRoute('puzzle_admin_advert_category_show', ['id' => $category->getId()]);
        }
        
        return $this->render("AdvertBundle:Admin:update_category.html.twig", array(
            'category' => $category,
            'form' => $form->createView()
        ));
    }
    
    /***
     * Delete category
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteCategoryAction(Request $request, Category $category) {
        $message = $this->get('translator')->trans('advert.category.delete.success', [
            '%categoryName%' => $category->getName()
        ], 'advert');
        $parent = $category->getParentNode();
        
        $em = $this->getDoctrine()->getManager();
        $em->remove($category);
        $em->flush();
        
        if ($request->isXmlHttpRequest() === true) {
            return new JsonResponse($message);
        }
        
        $this->addFlash('success', $message);
        
        if (null !== $parent) {
            return $this->redirectToRoute('puzzle_admin_advert_category_show', ['id' => $parent->getId()]);
        }
        
        return $this->redirectToRoute('puzzle_admin_advert_category_list');
    }
    
    /***
     * Show Advertisers
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAdvertisersAction(Request $request) {
        return $this->render("AdvertBundle:Admin:list_advertisers.html.twig", array(
            'advertisers' => $this->getDoctrine()->getRepository(Advertiser::class)->findAll()
        ));
    }
    
    /***
     * Show advertiser
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAdvertiserAction(Request $request, Advertiser $advertiser) {
        return $this->render("AdvertBundle:Admin:show_advertiser.html.twig", ['advertiser' => $advertiser]);
    }
    
    /***
     * Create advertiser
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createAdvertiserAction(Request $request){
        $advertiser = new Advertiser();
        
        $form = $this->createForm(AdvertiserCreateType::class, $advertiser, [
            'method' => 'POST',
            'action' => $this->generateUrl('puzzle_admin_advert_advertiser_create')
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $data = $request->request->all()['puzzle_admin_advert_advertiser_create'];
            $picture = $request->request->get('picture') !== null ? $request->request->get('picture') : $data['picture'];
            
            if ($picture !== null) {
                $this->get('event_dispatcher')->dispatch(MediaEvents::COPY_FILE, new FileEvent([
                    'path' => $picture,
                    'context' => MediaUtil::extractContext(Advertiser::class),
                    'user' => $this->getUser(),
                    'closure' => function($filename) use ($advertiser) {$advertiser->setPicture($filename);}
                ]));
            }
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($advertiser);
            $em->flush();
            
            $this->addFlash('success', $this->get('translator')->trans('advert.advertiser.create.success', [
                '%advertiserName%' => $advertiser->getName()
            ], 'advert'));
            return $this->redirectToRoute('puzzle_admin_advert_advertiser_show', ['id' => $advertiser->getId()]);
        }
        
        return $this->render("AdvertBundle:Admin:create_advertiser.html.twig", array(
            'form' => $form->createView()
        ));
    }
    
    
    /***
     * Update advertiser
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateAdvertiserAction(Request $request, Advertiser $advertiser) {
        $form = $this->createForm(AdvertiserUpdateType::class, $advertiser, [
            'method' => 'POST',
            'action' => $this->generateUrl('puzzle_admin_advert_advertiser_update', ['id' => $advertiser->getId()])
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $data = $request->request->all()['puzzle_admin_advert_advertiser_update'];
            $picture = $request->request->get('picture') !== null ? $request->request->get('picture') : $data['picture'];
            
            if ($advertiser->getPicture() === null || $advertiser->getPicture() !== $picture) {
                $this->get('event_dispatcher')->dispatch(MediaEvents::COPY_FILE, new FileEvent([
                    'path' => $picture,
                    'context' => MediaUtil::extractContext(Advertiser::class),
                    'user' => $this->getUser(),
                    'closure' => function($filename) use ($advertiser) {$advertiser->setPicture($filename);}
                ]));
            }
            
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            
            $this->addFlash('success', $this->get('translator')->trans('advert.advertiser.update.success', [
                '%advertiserName%' => $advertiser->getName()
            ], 'advert'));
            return $this->redirectToRoute('puzzle_admin_advert_advertiser_show', ['id' => $advertiser->getId()]);
        }
        
        return $this->render("AdvertBundle:Admin:update_advertiser.html.twig", array(
            'advertiser' => $advertiser,
            'form' => $form->createView()
        ));
    }
    
    /***
     * Delete advertiser
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteAdvertiserAction(Request $request, Advertiser $advertiser) {
        $message = $this->get('translator')->trans('advert.advertiser.delete.success', [
            '%advertiserName%' => $advertiser->getName()
        ], 'advert');
        
        $em = $this->getDoctrine()->getManager();
        $em->remove($advertiser);
        $em->flush();
        
        if ($request->isXmlHttpRequest() === true) {
            return new JsonResponse($message);
        }
        
        $this->addFlash('success', $message);
        return $this->redirectToRoute('puzzle_admin_advert_advertiser_list');
    }
    
    /***
     * Show Posts
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listPostsAction(Request $request){
        return $this->render("AdvertBundle:Admin:list_posts.html.twig", array(
            'posts' => $this->getDoctrine()->getRepository(Post::class)->findAll()
        ));
    }
    
    /***
     * Show post
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showPostAction(Request $request, Post $post) {
        return $this->render("AdvertBundle:Admin:show_post.html.twig", array(
            'post' => $post
        ));
    }
    
    /***
     * Create post
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createPostAction(Request $request) {
        $post = new Post();
        $em = $this->getDoctrine()->getManager();
        
        $form = $this->createForm(PostCreateType::class, $post, [
            'method' => 'POST',
            'action' => $this->generateUrl('puzzle_admin_advert_post_create')
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $data = $request->request->all()['puzzle_admin_advert_post_create'];
            
            if (empty($data['expiresAt']) === false) {
                $post->setExpiresAt(new \DateTime($data['expiresAt']));
            }
            
            $em->persist($post);
            $em->flush();
            
            $message = $this->get('translator')->trans('advert.post.create.success', [
                '%postName%' => $post->getName()
            ], 'advert');
            
            if ($request->isXmlHttpRequest() === true) {
                return new JsonResponse($message);
            }
            
            $this->addFlash('success', $message);
            return $this->redirectToRoute('puzzle_admin_advert_post_show', ['id' => $post->getId()]);
        }
        
        return $this->render("AdvertBundle:Admin:create_post.html.twig", array(
            'form' => $form->createView()
        ));
    }
    
    
    /***
     * Update post
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updatePostAction(Request $request, Post $post) {
        $form = $this->createForm(PostUpdateType::class, $post, [
            'method' => 'POST',
            'action' => $this->generateUrl('puzzle_admin_advert_post_update', ['id' => $post->getId()])
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $data = $request->request->all()['puzzle_admin_advert_post_update'];
            
            $enablePostulate = $post->getEnablePostulate() == "on" ? true : false;
            $post->setEnablePostulate($enablePostulate);
            
            if (empty($data['expiresAt']) === false) {
                $post->setExpiresAt(new \DateTime($data['expiresAt']));
            }
            
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            
            $message = $this->get('translator')->trans('advert.post.update.success', [
                '%postName%' => $post->getName()
            ], 'advert');
            
            if ($request->isXmlHttpRequest() === true) {
                return new JsonResponse($message);
            }
            
            $this->addFlash('success', $message);
            return $this->redirectToRoute('puzzle_admin_advert_post_show', ['id' => $post->getId()]);
        }
        
        return $this->render("AdvertBundle:Admin:update_post.html.twig", array(
            'post' => $post,
            'form' => $form->createView()
        ));
    }
    
    /***
     * Delete Post
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deletePostAction(Request $request, Post $post) {
        $message = $this->get('translator')->trans('advert.post.delete.success', [
            '%postName%' => $post->getName()
        ], 'advert');
        
        $em = $this->getDoctrine()->getManager();
        $em->remove($post);
        $em->flush();
        
        if ($request->isXmlHttpRequest() === true) {
            return new JsonResponse($message);
        }
        
        $this->addFlash('success', $message);
        return $this->redirectToRoute('puzzle_admin_advert_post_list');
    }
    
    /***
     * List postulates
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listPostulatesAction(Request $request, Post $post) {
        return $this->render("AdvertBundle:Admin:list_postulates.html.twig", array(
            'post' => $post,
            'postulates' => $this->getDoctrine()->getRepository(Postulate::class)->findBy(['post' => $post->getId()])
        ));
    }
    
    
    /***
     * Confirm postulate
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function confirmPostulateAction(Request $request, Postulate $postulate) {
        $postulate->setConfirmed(true);
        $postulate->setConfirmedAt(new \DateTime());
        
        $em = $this->getDoctrine()->getManager();
        $em->flush();
        
        $message = $this->get('translator')->trans('advert.postulate.confirm.success', [
            '%postulateUser%' => $postulate->getUser()->getFullName()
        ], 'advert');
        
        if ($request->isXmlHttpRequest()) {
            return new JsonResponse($message);
        }
        $this->addFlash('success', $message);
        return $this->redirectToRoute('puzzle_admin_advert_postulate_list', ['id' => $postulate->getPost()->getId()]);
    }
    
    /***
     * Delete advertiser
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deletePostulateAction(Request $request, Postulate $postulate) {
        $post = $postulate->getPost();
        $message = $this->get('translator')->trans('advert.postulate.delete.success', [
            '%postulateUser%' => $postulate->getUser()->getFullName()
        ], 'advert');
        
        $em = $this->getDoctrine()->getManager();
        $em->remove($postulate);
        $em->flush();
        
        if ($request->isXmlHttpRequest() === true) {
            return new JsonResponse($message);
        }
        
        $this->addFlash('success', $message);
        return $this->redirectToRoute('puzzle_admin_advert_postulate_list', ['id' => $post->getId()]);
    }
}
