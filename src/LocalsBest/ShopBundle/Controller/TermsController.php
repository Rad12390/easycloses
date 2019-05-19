<?php

namespace LocalsBest\ShopBundle\Controller;

use LocalsBest\CommonBundle\Controller\SuperController as Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use LocalsBest\ShopBundle\Entity\Terms;

class TermsController extends Controller {
    
    public function termsAction($mode=null) {
        
	return $this->render('@LocalsBestShop/payment/terms.html.twig', ['mode'=>$mode
	]);
    }
    
    public function saveTermsAction(Request $request,$mode=null) {
        $term= $request->request->get('terms');
        
        if($term=='on'){
            $em = $this->getDoctrine()->getManager();
            $term =new Terms();
            $user= $this->getUser();
            $term->setUserId($user);
            $term->setStatus('1');
            $em->persist($term);
            $em->flush();
            $this->addFlash(
                'success', sprintf('Successfully accepted terms & conditions')
            );
        }
        if($mode=='shop')
            return $this->redirectToRoute('shop_payment_success');
        elseif($mode=='package'){
            $result['data'] = 1;
            return new Response(json_encode($result));
        }
          //  return $this->redirectToRoute('packages_new');
        
    }
    
    public function getTermStatusAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $result['success']=0;
        if(!empty($user->getTerms())){
            $result['success']= 1;
        }
        return new Response(json_encode($result));
    }
}
