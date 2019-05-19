<?php

namespace LocalsBest\UserBundle\Controller;

use LocalsBest\CommonBundle\Controller\SuperController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FormBuilderController extends SuperController
{
    /**
     * Display Form Builder block for Business owner
     *
     * @return array
     * @Template
     */
    public function indexAction()
    {
        $business = $this->getUser()->getBusinesses()[0];

        $form = json_decode($business->getBusinessForm(), true);

        return [
            'form' => $form,
        ];
    }

    /**
     * Save Changes for Form Builder
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function saveAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $business = $this->getUser()->getBusinesses()[0];

        // Get Data from Request
        $data = $request->request->get('fields');

        // Update data array
        foreach($data as $key => $item) {
            $data[$key]['label'] = ucwords(strtolower($item['label']));
        }
        // Set new Form to Business
        $business->setBusinessForm(json_encode($data));
        // Save changes
        $em->getRepository('LocalsBestUserBundle:Business')->save($business);
        // Return JSON response
        return new JsonResponse(['success' => true]);
    }

    /**
     * Display Form Builder block for Job create Page
     *
     * @param int $id
     *
     * @return JsonResponse|Response
     */
    public function getFormAction($id)
    {
        if($id == 594) {
            // Render special form for id = 594
            return $this->render('@LocalsBestUser/form_builder/vendor_594.html.twig' );
        }

        $em = $this->getDoctrine()->getManager();
        // Get User Entity by ID
        $user = $em->getRepository('LocalsBestUserBundle:User')->find($id);

        $business = $user->getBusinesses()[0];
        // Get Business Form
        $form = json_decode($business->getBusinessForm());
        // Send fail response
        if($form == '' || $form === null) {
            return new JsonResponse(['success' => false, 'message' => 'Vendor does not have sign form.'], 404);
        }
        // Render Business Form
        return $this->render('@LocalsBestUser/form_builder/getForm.html.twig', ['form' => $form] );
    }
}
