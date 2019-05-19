<?php

namespace LocalsBest\UserBundle\Controller;

use LocalsBest\UserBundle\Entity\Feedback;
use LocalsBest\UserBundle\Form\FeedbackType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class FeedbackController extends Controller
{
    /**
     * Create Feedback
     *
     * @param Request $request
     * @param int $productId
     * @param int $parentId
     * @return RedirectResponse
     *
     * @Route("/feedback-create/{productId}/{parentId}")
     */
    public function createAction(Request $request, $productId, $parentId = null)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = new Feedback();

        $product = $em->getRepository('LocalsBestUserBundle:Product')->find($productId);
        $form = $this->createForm(FeedbackType::class, $entity);

        $form->handleRequest($request);

        if ($form->isValid()) {

            $curUser = $this->getUser();
            if ($curUser !== null) {
                $entity->setUser($curUser);

                $entity->setFullName($curUser->getFullName());
                $entity->setEmail($curUser->getPrimaryEmail()->getEmail());
            }

            if ($parentId !== null) {
                $entity->setParent($em->getReference('LocalsBest\USerBundle\Entity\Feedback', $parentId));
            }

            $entity->setProduct($em->getReference('LocalsBest\USerBundle\Entity\Product', $productId));

            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('products_show', array('slug' => $product->getSlug())));
        }

        return $this->redirect($this->generateUrl('products_show', array('slug' => $product->getSlug())));
    }

}
