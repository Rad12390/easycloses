<?php

namespace LocalsBest\UserBundle\Controller;

use LocalsBest\CommonBundle\Controller\SuperController;
use LocalsBest\UserBundle\Entity\PaidInvite;
use LocalsBest\UserBundle\Form\MainType;
use LocalsBest\UserBundle\Form\PaidInviteType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Swift_Message;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ServiceController extends SuperController
{
    /**
     * Lists all Vendors for current user.
     *
     * @Template
     * @return array|RedirectResponse
     */
    public function indexAction()
    {
        // Check users password for default values
        if (!$this->checkPassword()) {
            return $this->redirect('profile#business_user_password');
        }

        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        if (
            !$this->get('localsbest.checker')->forAddon('featured business directory-free', $user)
            && !$this->get('localsbest.checker')->forAddon('featured business directory-paid', $user)
        ) {
            $this->addFlash(
                'danger',
                'Sorry you are not authorized to use this feature. If you feel this is in error please open a support ticket'
            );
            return $this->redirectToRoute('locals_best_user_homepage');
        }

        $biz = $this->getBusiness();
        $u = $biz->getOwner();

        // Get business staff
        if ($user->getRole()->getRole() === 'ROLE_NETWORK_MANAGER') {
            $users = array();
            $businesses = $this->getUser()->getBusinesses();
           
           foreach($businesses as $business) {
               $owner = $em->getRepository('LocalsBest\UserBundle\Entity\User')
                   ->findOneBy(['id' => $business->getOwner()->getid()]);
               $users[] = $owner;
           }
        } else {
            $users = $this->getStaffs();
        }
       
        if (
            $this->getBusiness()->getTypes()->first()->getId() == 23
            || $this->getUser()->getRole()->getLevel() == 1
            || in_array($biz->getId(), [173 ])
        ) {
            $vendors = $this->getUser()->getVendorsByCategory2($u);
            // Create form object with able industry types for current user
            $form = $this->createForm(MainType::class, null, [
                'categories' => $this->getUser()->getAbleVendorCategories($vendors),
            ]);
        } else {
            // Get able vendors for NOT Real Estate user
            $vUsers = [];
            $vUsersRaw = $u->getVendorsWithMe();
           
           // 
            foreach($vUsersRaw as $item) {
                $vUsers[] = $item->getId();
            }
          //  dd($vUsers);
            $vendors = $em->getRepository('LocalsBest\UserBundle\Entity\User')
                ->findVendorsForVendor($u, implode(', ', $vUsers));

            // Create form object with able industry types for current user
            $form = $this->createForm(MainType::class, null, [
                'categories' => $u->getAbleVendorCategories($vendors),
            ]);
        }
        // Render view with params
        return array(
            'form'      => $form->createView(),
            'vendors'   => $vendors
        );
    }

    /**
     * Display Page for Paid Invite
     *
     * @param Request $request
     *
     * @return Response
     */
    public function invitePaidAction(Request $request)
    {
        // Create PaidInvite Entity
        $paidInvite = new PaidInvite();
        // Create PaidInvite Form object
        $form = $this->createForm(PaidInviteType::class, $paidInvite, [
            'user' => $this->getUser(),
        ]);

        $curUser = $this->getUser();

        if($request->isMethod('POST')) {
            $form->handleRequest($request);

            if($form->isValid()) {

                $emailExists = false;

                // Send error message
                if ($emailExists) {
                    $this->addFlash('danger', 'This email already exists. Please choose a different one');
                } else {
                    // Set information to PaidInvite
                    $paidInvite->setCreatedBy($this->getUser());
                    $paidInvite->setToken(md5(uniqid(rand(), true)));

                    // Save Paid Invite
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($paidInvite);
                    $em->flush();

                    // Send Email about this
                    $message = (new Swift_Message('Check Out this Advertising Opportunity with Easy Closes'))
                        ->setFrom(
                            $curUser->getPrimaryEmail()->getEmail(),
                            $curUser->getFullName() . ' ' . $curUser->getBusinesses()->first()->getName())
                        ->setTo($paidInvite->getRecipient())
                        ->setBody(
                            $this->renderView(
                                '@LocalsBestNotification/mails/paid-invite.html.twig',
                                ['invite' => $paidInvite]
                            ),
                            'text/html'
                        )
                    ;
                    $this->container->get('mailer')->send($message);
                    // Show flash message
                    $this->addFlash('success', 'Email with invite link was sent.');
                }
            }
        }
        // Render View with params
        return $this->render(
            '@LocalsBestUser/service/invite-paid-service.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }
}
