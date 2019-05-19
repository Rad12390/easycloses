<?php

namespace LocalsBest\ShopBundle\Controller;

use LocalsBest\ShopBundle\Entity\Comment;
use LocalsBest\ShopBundle\Form\CommentType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class CommentController extends Controller
{
    /**
     * Create Comment for Item from Sku.
     * @param Request $request
     * @param integer $skuId
     *
     * @return RedirectResponse
     */
    public function createAction(Request $request, $skuId)
    {
        $em = $this->getDoctrine()->getManager();
        $sku = $em->getRepository('LocalsBestShopBundle:Sku')->find($skuId);
        
        $comment = new Comment();
        $comment->setCreatedBy($this->getUser());
        $comment->setSku($sku);

        $commentForm = $this->createForm(CommentType::class, $comment, [
            'sku' => $sku,
            'action' => $this->generateUrl(
                'comment_create',
                [
                    'skuId' => $sku->getId(),
                ]
            ),
            'method' => 'POST',
        ]);
        $commentForm->handleRequest($request);
        $referrer = $request->headers->get('referer');
        
        
        if ($commentForm->isValid()) {
            $em->persist($comment);
            $em->flush();

            $this->addFlash('success', 'Comment added successfully');
            return new RedirectResponse($referrer);
        }
         
        $this->addFlash('danger', 'There was error while we try to add Comment.');
        return new RedirectResponse($referrer);
    }
    
    public function deleteAction(Request $request, $skuId, $feedback){
        $user = $this->getUser();
       
        if ($user->getRole()->getName()!= 'Admin') {
            $this->addFlash('danger', "You are not authorized to access this");
        }
        else{
            $em = $this->getDoctrine()->getManager();
            $feedback = $em->getRepository('LocalsBestShopBundle:Comment')->find($feedback);
            $em->remove($feedback);
            $em->flush();
            $this->addFlash('danger', 'Comment deleted successfully.');
        }
        return $this->redirectToRoute('sku_show',['id'=>$skuId]);
    }
}
