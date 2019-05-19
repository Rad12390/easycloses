<?php

namespace LocalsBest\UserBundle\Controller;

use LocalsBest\CommonBundle\Controller\SuperController;
use LocalsBest\UserBundle\Entity\EmailTemplate;
use LocalsBest\UserBundle\Form\EmailTemplateType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class EmailTemplateController extends SuperController
{
    /**
     * List of Emails Templates for page
     *
     * @return Response
     */
    public function indexAction()
    {
        $business = $this->getBusiness();

        // Get Templates by Business
        $templates = $this->getRepository('LocalsBestUserBundle:EmailTemplate')->findBy([
            'business' => $business,
        ]);

        // Render view with params
        return $this->render('@LocalsBestUser/email_template/index.html.twig', ['templates' => $templates]);
    }

    /**
     * List of Emails Templates for block
     *
     * @return Response
     */
    public function panelIndexAction()
    {
        $business = $this->getBusiness();

        // Get Templates by Business
        $templates = $this->getRepository('LocalsBestUserBundle:EmailTemplate')->findBy([
            'business' => $business,
        ]);

        // Render view with params
        return $this->render('@LocalsBestUser/email_template/panel-index.html.twig', ['templates' => $templates]);
    }

    /**
     * Create Email Template
     *
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    public function createAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $template = new EmailTemplate();
        $business = $this->getBusiness();
        $template->setBusiness($business);
        // Create Email Template Form object
        $form = $this->createForm(EmailTemplateType::class, $template);
        $form->add('save', Type\SubmitType::class, ['label' => 'Create']);

        // if form was submit
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $template = $form->getData();

                // Save new Email Template
                $em->persist($template);
                $em->flush();
                // Redirect user to another page
                return $this->redirect($this->generateUrl('users_profile') . '#business_email_templates');
            }
        }

        // render view with params
        return $this->render(
            '@LocalsBestUser/email_template/create.html.twig',
            [
                'form' => $form->createView(),
                'nrd' => $em->getRepository('LocalsBestUserBundle:Product')->findOneBy(['addon_part'=>'non received documents']),
                'nrl' => $em->getRepository('LocalsBestUserBundle:Product')->findOneBy(['addon_part'=>'non received listings']),
                'el15' => $em->getRepository('LocalsBestUserBundle:Product')->findOneBy(['addon_part'=>'15 day expired listings']),
            ]
        );
    }

    /**
     * Update Email Template
     *
     * @param Request $request
     * @param int $id
     *
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        // Get Template
        $template = $em->getRepository('LocalsBestUserBundle:EmailTemplate')->find($id);
        // Create Email Template Form object
        $form = $this->createForm(EmailTemplateType::class, $template);
        $form->add('save', Type\SubmitType::class, ['label' => 'Save']);
        // if form was submit
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                // save updates
                $em->flush();
                // Redirect user to another page
                return $this->redirect( $this->generateUrl('users_profile') . '#business_email_templates');
            }
        }
        // render view with params
        return $this->render('@LocalsBestUser/email_template/edit.html.twig', [
            'form' => $form->createView(),
            'template' => $template,
        ]);
    }

    /**
     * Delete Email Template
     *
     * @param int $id
     *
     * @return RedirectResponse
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        // Get Email Template
        $template = $em->getRepository('LocalsBestUserBundle:EmailTemplate')->find($id);
        // Remove Email Template
        $em->remove($template);
        $em->flush();
        // Show flash message
        $this->addFlash('success', 'Email Template successfully deleted!');
        // Redirect user to another page
        return $this->redirect( $this->generateUrl('users_profile') . '#business_email_templates');
    }

    /**
     * Find Email Template
     *
     * @param Request $request
     *
     * @return Response
     */
    public function findAction(Request $request)
    {
        $category = $request->query->get('category');
        $number = (int)$request->query->get('number', 1);

        if ($number == 0 || $number > 4) {
            $number = 1;
        }

        if ($category == 'Non Received Documents') {
            return $this
                ->render('@LocalsBestUser/Transaction/non-received-docs-email-templates/'.$number.'.html.twig')
            ;
        }

        if ($category == 'Expired Listings') {
            return $this
                ->render('@LocalsBestNotification/Mails/expire-listing.html.twig')
                ;
        }

        if ($category == 'Non Received Listings') {
            if ($number > 1) {
                return $this
                    ->render('@LocalsBestUser/Transaction/non-received-email-'.$number.'.html.twig')
                ;
            } else {
                return $this
                    ->render('@LocalsBestUser/Transaction/_email-template-1.html.twig')
                ;
            }
        }
    }
}
