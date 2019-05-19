<?php

namespace LocalsBest\UserBundle\Controller;

use LocalsBest\UserBundle\Entity\AssociationRow;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AssociationRowController extends Controller
{
    /**
     * Display Users or Businesses Associations List
     *
     * @param string $username
     * @param string $type
     * @return Response
     */
    public function indexAction($type, $username = null)
    {
        $em = $this->getDoctrine()->getManager();

        // Check what type of Associations we need to show user or business
        if ($type == 'business') {
            // take business of current user
            $business = $this->getUser()->getBusinesses()->first();
            // find associations for business
            $associationsRows = $em->getRepository('LocalsBestUserBundle:AssociationRow')->findBy(['business' => $business]);
        } else {
            // if $username is empty => take current user else search user by username
            if ($username === null) {
                $user = $this->getUser();
            } else {
                $user = $em->getRepository('LocalsBestUserBundle:User')->findOneBy(['username' => $username]);
            }
            // find associations for user
            $associationsRows = $em->getRepository('LocalsBestUserBundle:AssociationRow')->findBy(['user' => $user]);
        }

        // render view with params
        return $this->render('@LocalsBestUser/association_row/index.html.twig', array(
                'associationRows' => $associationsRows,
                'type'  => $type,
                'userProfile_username' => isset($user) ? $user->getUsername() : null,
            )
        );
    }

    /**
     * Display form for new Association and create new Association
     *
     * @param Request $request
     * @param string $username
     * @param string $type
     * @return Response|RedirectResponse
     */
    public function createAction(Request $request, $type, $username = null)
    {
        $em = $this->getDoctrine()->getManager();

        // if $username is empty => take current user else search user by username
        if ($username === null) {
            $user = $this->getUser();
        } else {
            $user = $em->getRepository('LocalsBestUserBundle:User')->findOneBy(['username' => $username]);
        }

        // take business of user
        $business = $user->getBusinesses()->first();

        // Look for Associations according to $type
        if ($type == 'business') {
            $associations = $em->getRepository('LocalsBestUserBundle:Association')->findAssociationsForBusiness($business);
        } else {
            $associations = $em->getRepository('LocalsBestUserBundle:Association')
                ->findAssociationsForUser($user, $business);
        }

        // If Create form was submited
        if ($request->getMethod() == 'POST') {

            $association = $em->getRepository('LocalsBestUserBundle:Association')
                ->find($request->request->get('association'));

            // Create new Association Entity
            $assocRow = new AssociationRow();
            // set Association information
            $assocRow->setAssociationMlsId($request->request->get('associationId'));
            $assocRow->setAssociation($association);

            // According to $type attach Association to user or to business
            if ($type == 'business') {
                $assocRow->setBusiness($business);
            } else {
                $assocRow->setUser($user);
            }
            // Save Association
            $em->persist($assocRow);
            $em->flush();

            // According to $type choose return back url
            if ($type == 'business') {
                return $this->redirect($this->generateUrl('users_profile') . '#business_information');
            } else {
                if ($username === null) {
                    return $this->redirect($this->generateUrl('users_profile') . '#new_fields');
                } else {
                    return $this->redirect(
                        $this->generateUrl('users_profile_edit', ['username' => $username]) . '#new_fields'
                    );
                }
            }
        }

        // render view with params
        return $this->render('@LocalsBestUser/association_row/create.html.twig',
            array(
                'associations' => $associations,
                'type' => $type,
                'userProfile_username' => isset($user) ? $user->getUsername() : null,
            )
        );
    }

    /**
     * Display form to edit Association and update Association
     *
     * @param Request $request
     * @param string $username
     * @param int $id
     * @return Response|RedirectResponse
     */
    public function editAction(Request $request, $id, $username = null)
    {
        $em = $this->getDoctrine()->getManager();

        // if $username is empty => take current user else search user by username
        if ($username === null) {
            $user = $this->getUser();
        } else {
            $user = $em->getRepository('LocalsBestUserBundle:User')->findOneBy(['username' => $username]);
        }

        // Look for Association using ID
        $assocRow = $em->find('LocalsBestUserBundle:AssociationRow', $id);

        // Check user permission for Association
        if (
            ($assocRow->getUser() !== null && $assocRow->getUser() != $user)
            || ($assocRow->getBusiness() !== null && $assocRow->getBusiness()->getOwner() != $user)
        ) {
            throw $this->createAccessDeniedException('Access Denied');
        }

        // If Edit form was submit
        if ($request->getMethod() == 'POST') {
            // Set new information for Association
            $assocRow->setAssociationMlsId($request->request->get('associationId'));
            // Save updated Association
            $em->flush();

            // According to Association object attached to choose return back url
            if ($assocRow->getBusiness() !== null) {
                return $this->redirect($this->generateUrl('users_profile') . '#business_information');
            } else {
                if ($username === null) {
                    return $this->redirect($this->generateUrl('users_profile') . '#new_fields');
                } else {
                    return $this->redirect(
                        $this->generateUrl('users_profile_edit', ['username' => $username]) . '#new_fields'
                    );
                }
            }
        }

        // render view with params
        return $this->render('@LocalsBestUser/association_row/edit.html.twig',
            array(
                'assocRow' => $assocRow,
                'userProfile_username' => isset($user) ? $user->getUsername() : null,
            )
        );
    }

    /**
     * Delete Association
     *
     * @param string $username
     * @param int $id
     * @return Response|RedirectResponse
     */
    public function deleteAction($id, $username = null)
    {
        $em = $this->getDoctrine()->getManager();

        // if $username is empty => take current user else search user by username
        if ($username === null) {
            $user = $this->getUser();
        } else {
            $user = $em->getRepository('LocalsBestUserBundle:User')->findOneBy(['username' => $username]);
        }

        // Look for Association using ID
        $assocRow = $em->find('LocalsBestUserBundle:AssociationRow', $id);

        // Check user permission for Association
        if (
            ($assocRow->getUser() !== null && $assocRow->getUser() != $user)
            || ($assocRow->getBusiness() !== null && $assocRow->getBusiness()->getOwner() != $user)
        ) {
            throw $this->createAccessDeniedException('Access Denied');
        }

        // Delete Association Entity
        $em->remove($assocRow);
        // update DB
        $em->flush();

        // According to Association object attached to choose return back url
        if ($assocRow->getBusiness() !== null) {
            return $this->redirect($this->generateUrl('users_profile') . '#business_information');
        } else {
            if($username === null) {
                return $this->redirect($this->generateUrl('users_profile') . '#new_fields');
            } else {
                return $this->redirect(
                    $this->generateUrl('users_profile_edit', ['username' => $username]) . '#new_fields'
                );
            }
        }
    }
}
