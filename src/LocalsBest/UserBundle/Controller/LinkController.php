<?php

namespace LocalsBest\UserBundle\Controller;

use LocalsBest\UserBundle\Entity\Link;
use LocalsBest\UserBundle\Form\LinkType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class LinkController extends Controller
{
    /**
     * Display Links block
     *
     * @return Response
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        // Get Links for each Group

        $personLinks = $em->getRepository('LocalsBestUserBundle:Link')->findBy([
            'createdBy' => $this->getUser(),
            'group'     => $em->getRepository('LocalsBestUserBundle:LinkGroup')->findOneBy(['title' => 'Personal'])
        ]);

        $companyLinks = $em->getRepository('LocalsBestUserBundle:Link')->findBy([
            'business'  => $this->getUser()->getBusinesses()[0],
            'group'     => $em->getRepository('LocalsBestUserBundle:LinkGroup')->findOneBy(['title' => 'Company'])
        ]);

        if($this->getUser()->getRole()->getLevel() == 8) {
            $clientsLinks = $em->getRepository('LocalsBestUserBundle:Link')->findBy([
                'business'  => $this->getUser()->getBusinesses()[0],
                'group'     => $em->getRepository('LocalsBestUserBundle:LinkGroup')->findOneBy(['title' => 'Clients'])
            ]);
        } else {
            $clientsLinks = $em->getRepository('LocalsBestUserBundle:Link')->findBy([
                'createdBy' => $this->getUser(),
                'group'     => $em->getRepository('LocalsBestUserBundle:LinkGroup')->findOneBy(['title' => 'Clients'])
            ]);
        }

        $staffLinks = $em->getRepository('LocalsBestUserBundle:Link')->findBy([
            'business'  => $this->getUser()->getBusinesses()[0],
            'group'     => $em->getRepository('LocalsBestUserBundle:LinkGroup')->findOneBy(['title' => 'Staff'])
        ]);

        // Render View
        return $this->render(
            '@LocalsBestUser/link/index.html.twig',
            [
                'personLinks'   => $personLinks,
                'companyLinks'  => $companyLinks,
                'clientsLinks'  => $clientsLinks,
                'staffLinks'    => $staffLinks,
            ]
        );
    }

    /**
     * Create Link
     *
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function createAction(Request $request)
    {
        // create new Link Entity
        $link = new Link();
        // create Link Form object
        $form = $this->createForm(LinkType::class, $link, array(
            'action' => $this->generateUrl('link_create'),
            'method' => 'POST',
            'user' => $this->getUser()
        ))->add('save', Type\SubmitType::class, array('label' => 'Save'));

        // if form was submit
        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {

                $em = $this->getDoctrine()->getManager();
                // set information for Link
                $link->setCreatedBy($this->getUser());
                $link->setBusiness($this->getUser()->getBusinesses()[0]);
                // Save changes
                $em->persist($link);
                $em->flush();
            }
            // Redirect user
            return $this->redirectToRoute('locals_best_user_homepage');
        }
        // Render View
        return $this->render('@LocalsBestUser/link/create.html.twig', array('form' => $form->createView()));
    }

    /**
     * Delete Link
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        // Get Link
        $link = $em->getRepository('LocalsBestUserBundle:Link')->find($id);
        // Remove Link
        $em->remove($link);
        // Update DB
        $em->flush();
        // Show flash message
        $this->addFlash('success', 'Link was deleted!');
        //Redirect User
        return $this->redirectToRoute('locals_best_user_homepage');
    }
}
