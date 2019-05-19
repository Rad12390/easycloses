<?php

namespace LocalsBest\UserBundle\Controller;

use LocalsBest\CommonBundle\Controller\SuperController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FirmController extends SuperController
{
    /**
     * Display Firm Summary Page
     *
     * @param Request $request
     *
     * @return array|RedirectResponse
     * @Template
     */
    public function indexAction(Request $request)
    {
        // Don't allow client open this page
        $this->isClient();

        // Check user password for default values
        if (!$this->checkPassword()) {
            return $this->redirect('profile#business_user_password');
        }

        // Send params to render view
        return array(
            'params' => http_build_query($request->query->all()),
        );
    }

    /**
     * Display Advance search block
     *
     * @return Response
     */
    public function advanceSearchAction()
    {
        $em = $this->getDoctrine()->getManager();

        // Enable Soft Delete Filter and disable it for  User Entity
        $em->getFilters()->enable('softdeleteable')->disableForEntity('LocalsBest\UserBundle\Entity\User');

        // Get options for view
        $result = $em->getRepository('LocalsBestUserBundle:Transaction')
            ->findOptionsAdvanceSearch($this->getUser(), $this->getStaffs());

        // Render view with params
        return $this->render('@LocalsBestUser/firm/advanceSearch.html.twig', ['results' => $result]);
    }
}
