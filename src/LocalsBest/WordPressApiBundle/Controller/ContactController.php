<?php

namespace LocalsBest\WordPressApiBundle\Controller;

use LocalsBest\CommonBundle\Entity\Note;
use LocalsBest\UserBundle\Entity\AllContact;
use LocalsBest\WordPressApiBundle\Entity\Content;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

use Nelmio\ApiDocBundle\Annotation\ApiDoc as ApiDoc;

class ContactController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     * @Operation(
     *     tags={""},
     *     summary="This is a description of your API method",
     *     @SWG\Parameter(
     *         name="token",
     *         in="formData",
     *         description="Application Token",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="wp_agent_id",
     *         in="formData",
     *         description="Agent ID from WP",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="wp_id",
     *         in="formData",
     *         description="User ID that register",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="first_name",
     *         in="formData",
     *         description="User first name that register",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="last_name",
     *         in="formData",
     *         description="User last name that register",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="email",
     *         in="formData",
     *         description="User e-mail that register",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="phone",
     *         in="formData",
     *         description="User phone number that register",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful"
     *     )
     * )
     *
     */
    public function createAction(Request $request)
    {
        if(!$request->isMethod("POST")) {
            return new JsonResponse(
                [
                    'error' => 1,
                    'message' => 'Method Not Allowed',
                    'data' => '',
                ],
                405
            );
        }

        $data = $request->request->all();

        if(!isset($data['token'])) {
            return new JsonResponse(
                [
                    'error' => 1,
                    'message' => 'Token does not set.',
                    'data' => '',
                ],
                400
            );
        }

        if($data['token'] != $this->getParameter('wp_token')) {
            return new JsonResponse(
                [
                    'error' => 1,
                    'message' => 'Wrong Token.',
                    'data' => '',
                ],
                403
            );
        }

        $em = $this->getDoctrine()->getManager();
        /** @var \LocalsBest\UserBundle\Entity\User $user */
        $user = $em->getRepository('LocalsBestUserBundle:User')->findOneBy([
            'wp_agent_id' => $data['wp_agent_id'],
//            'wp_website_url' => '',
        ]);

        if($user === null) {
            return new JsonResponse(
                [
                    'error' => 1,
                    'message' => 'User for for Agent ID don\'t exist.',
                    'data' => '',
                ],
                403
            );
        }

        /** @var \LocalsBest\UserBundle\Entity\AllContact $contact */
        $contact = $em->getRepository('LocalsBestUserBundle:AllContact')->findOneBy(
            [
                'email' => $data['email'],
            ]
        );

        if($contact === null) {
            $contact = new AllContact();
            $contact->setFirstName($data['first_name']);
            $contact->setLastName($data['last_name']);
            $contact->setEmail($data['email']);
            $contact->setNumber($data['phone']);
            $contact->setStatus($em->getReference('LocalsBest\CommonBundle\Entity\Status', 1));
            $contact->setWpId($data['wp_id']);
            $contact->setAssignTo($user);
            $contact->setGeneratedBy($user->getBusinesses()->first()->getOwner());
            $contact->setCreatedBy($user->getBusinesses()->first()->getOwner());

            $em->persist($contact);
            $em->flush();

            $message = "You have new contact updates from your website.";

            $this->get('localsbest.notification')->addNotification(
                $message,
                'contact_view',
                ['id' => $contact->getId()],
                [$user],
                [$user]
            );

            return new JsonResponse(
                [
                    'error' => 0,
                    'message' => 'New Contact was created.',
                    'data' => [
                        'first_name' => $contact->getFirstName(),
                        'last_name' => $contact->getLastName(),
                        'email' => $contact->getEmail(),
                        'phone' => $contact->getNumber(),
                    ],
                ],
                200
            );
        } else {
            if($data['phone'] == $contact->getNumber()) {
                $contact->setWpId($data['wp_id']);
                $em->flush();

                $message = "Your contact was updated from your website.";

                $this->get('localsbest.notification')->addNotification(
                    $message,
                    'contact_view',
                    ['id' => $contact->getId()],
                    [$user],
                    [$user]
                );

                return new JsonResponse(
                    [
                        'error' => 0,
                        'message' => 'Contact was updated.',
                        'data' => [
                            'first_name' => $contact->getFirstName(),
                            'last_name' => $contact->getLastName(),
                            'email' => $contact->getEmail(),
                            'phone' => $contact->getNumber(),
                        ],
                    ],
                    200
                );
            } else {
                if($contact->getNumber() === null || $contact->getNumber() == '') {
                    $contact->setWpId($data['wp_id']);
                    $contact->setNumber($data['phone']);
                    $em->flush();

                    $message = "Your contact was updated from your website.";

                    $this->get('localsbest.notification')->addNotification(
                        $message,
                        'contact_view',
                        ['id' => $contact->getId()],
                        [$user],
                        [$user]
                    );

                    return new JsonResponse(
                        [
                            'error' => 0,
                            'message' => 'Contact was updated with Phone Number.',
                            'data' => [
                                'first_name' => $contact->getFirstName(),
                                'last_name' => $contact->getLastName(),
                                'email' => $contact->getEmail(),
                                'phone' => $contact->getNumber(),
                            ],
                        ],
                        200
                    );
                } else {
                    $note = new Note();
                    $note->setStatus($em->getRepository('LocalsBestCommonBundle:Note')->getDefaultStatus());
                    $note->setNote('Phone number from WP Site ' . $data['phone'])
                        ->setPrivate(true)
                        ->setImportant(false)
                        ->setObjectType('LocalsBestUserBundle:AllContact')
                        ->setUser($user)
                        ->setCreatedBy($user);
                    $note->setObjectId($contact->getId());
                    $note->setOwner($user->getBusinesses()->first());

                    $em->persist($note);
                    $em->flush();

                    return new JsonResponse(
                        [
                            'error' => 0,
                            'message' => 'Add Phone Number for Contact.',
                            'data' => [
                                'first_name' => $contact->getFirstName(),
                                'last_name' => $contact->getLastName(),
                                'email' => $contact->getEmail(),
                                'phone' => $contact->getNumber(),
                            ],
                        ],
                        200
                    );
                }
            }
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @Operation(
     *     tags={""},
     *     summary="Will save information about property and comment for it.",
     *     @SWG\Parameter(
     *         name="token",
     *         in="formData",
     *         description="Application Token",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="wp_id",
     *         in="formData",
     *         description="User ID from WP",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="property_name",
     *         in="formData",
     *         description="Property Name from WP",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="property_link",
     *         in="formData",
     *         description="Property Link from WP",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="comment_text",
     *         in="formData",
     *         description="Text of Comment form WP",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful"
     *     )
     * )
     *
     */
    public function leavingCommentAction(Request $request)
    {
        if(!$request->isMethod("POST")) {
            return new JsonResponse(
                [
                    'error' => 1,
                    'message' => 'Method Not Allowed',
                    'data' => '',
                ],
                405
            );
        }

        $data = $request->request->all();

        if(!isset($data['token'])) {
            return new JsonResponse(
                [
                    'error' => 1,
                    'message' => 'Token does not set.',
                    'data' => '',
                ],
                400
            );
        }

        if($data['token'] != $this->getParameter('wp_token')) {
            return new JsonResponse(
                [
                    'error' => 1,
                    'message' => 'Wrong Token.',
                    'data' => '',
                ],
                403
            );
        }

        $em = $this->getDoctrine()->getManager();
        /** @var \LocalsBest\UserBundle\Entity\AllContact $contact */
        $contact = $em->getRepository('LocalsBestUserBundle:AllContact')->findOneBy(
            [
                'wp_id' => $data['wp_id'],
            ]
        );

        if($contact === null) {
            return new JsonResponse(
                [
                    'error' => 1,
                    'message' => 'Contact for WP User ID don\'t exist.',
                    'data' => '',
                ],
                404
            );
        }

        $user = $contact->getAssignTo();

        $content = new Content();
        $content->setType('comment')
            ->setContact($contact)
            ->setContent(
                json_encode([
                    'property_name' => $data['property_name'],
                    'property_link' => $data['property_link'],
                    'comment_text'  => $data['comment_text'],
                ])
            );

        $em->persist($content);
        $em->flush();

        $message = "You have new contact updates from your website";

        $this->get('localsbest.notification')->addNotification(
            $message,
            'contact_view',
            ['id' => $contact->getId()],
            [$user],
            [$user]
        );

        return new JsonResponse(
            [
                'error' => 0,
                'message' => 'Add Comment for Contact.',
                'data' => [
                    'first_name' => $contact->getFirstName(),
                    'last_name' => $contact->getLastName(),
                    'email' => $contact->getEmail(),
                    'phone' => $contact->getNumber(),
                ],
            ],
            200
        );

    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @Operation(
     *     tags={""},
     *     summary="Will save Search Criteria.",
     *     @SWG\Parameter(
     *         name="token",
     *         in="formData",
     *         description="Application Token",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="wp_id",
     *         in="formData",
     *         description="User ID from WP",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="search_string",
     *         in="formData",
     *         description="Search Criteria from WP",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful"
     *     )
     * )
     *
     */
    public function saveSearchAction(Request $request)
    {
        if(!$request->isMethod("POST")) {
            return new JsonResponse(
                [
                    'error' => 1,
                    'message' => 'Method Not Allowed',
                    'data' => '',
                ],
                405
            );
        }

        $data = $request->request->all();

        if(!isset($data['token'])) {
            return new JsonResponse(
                [
                    'error' => 1,
                    'message' => 'Token does not set.',
                    'data' => '',
                ],
                400
            );
        }

        if($data['token'] != $this->getParameter('wp_token')) {
            return new JsonResponse(
                [
                    'error' => 1,
                    'message' => 'Wrong Token.',
                    'data' => '',
                ],
                403
            );
        }

        $em = $this->getDoctrine()->getManager();
        /** @var \LocalsBest\UserBundle\Entity\AllContact $contact */
        $contact = $em->getRepository('LocalsBestUserBundle:AllContact')->findOneBy(
            [
                'wp_id' => $data['wp_id'],
            ]
        );

        if($contact === null) {
            return new JsonResponse(
                [
                    'error' => 1,
                    'message' => 'Contact for WP User ID don\'t exist.',
                    'data' => '',
                ],
                404
            );
        }

        $user = $contact->getAssignTo();

        $content = new Content();
        $content->setType('search')
            ->setContact($contact)
            ->setContent($data['search_string']);

        $em->persist($content);
        $em->flush();

        $message = "You have new contact updates from your website";

        $this->get('localsbest.notification')->addNotification(
            $message,
            'contact_view',
            ['id' => $contact->getId()],
            [$user],
            [$user]
        );

        return new JsonResponse(
            [
                'error' => 0,
                'message' => 'Add Criteria of Search for Contact.',
                'data' => [
                    'first_name' => $contact->getFirstName(),
                    'last_name' => $contact->getLastName(),
                    'email' => $contact->getEmail(),
                    'phone' => $contact->getNumber(),
                ],
            ],
            200
        );

    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @Operation(
     *     tags={""},
     *     summary="Will save Property info that was marked like Favourite.",
     *     @SWG\Parameter(
     *         name="token",
     *         in="formData",
     *         description="Application Token",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="wp_id",
     *         in="formData",
     *         description="User ID from WP",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="property_name",
     *         in="formData",
     *         description="Property Name from WP",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="property_link",
     *         in="formData",
     *         description="Property Link from WP",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful"
     *     )
     * )
     *
     */
    public function favoritePropertyAction(Request $request)
    {
        if(!$request->isMethod("POST")) {
            return new JsonResponse(
                [
                    'error' => 1,
                    'message' => 'Method Not Allowed',
                    'data' => '',
                ],
                405
            );
        }

        $data = $request->request->all();

        if(!isset($data['token'])) {
            return new JsonResponse(
                [
                    'error' => 1,
                    'message' => 'Token does not set.',
                    'data' => '',
                ],
                400
            );
        }

        if($data['token'] != $this->getParameter('wp_token')) {
            return new JsonResponse(
                [
                    'error' => 1,
                    'message' => 'Wrong Token.',
                    'data' => '',
                ],
                403
            );
        }

        $em = $this->getDoctrine()->getManager();
        /** @var \LocalsBest\UserBundle\Entity\AllContact $contact */
        $contact = $em->getRepository('LocalsBestUserBundle:AllContact')->findOneBy(
            [
                'wp_id' => $data['wp_id'],
            ]
        );

        if($contact === null) {
            return new JsonResponse(
                [
                    'error' => 1,
                    'message' => 'Contact for WP User ID don\'t exist.',
                    'data' => '',
                ],
                404
            );
        }

        $user = $contact->getAssignTo();

        $content = new Content();
        $content->setType('favourite')
            ->setContact($contact)
            ->setContent(
                json_encode([
                    'property_name' => $data['property_name'],
                    'link'          => $data['property_link'],
                ])
            );

        $em->persist($content);
        $em->flush();

        $message = "You have new contact updates from your website";

        $this->get('localsbest.notification')->addNotification(
            $message,
            'contact_view',
            ['id' => $contact->getId()],
            [$user],
            [$user]
        );

        return new JsonResponse(
            [
                'error' => 0,
                'message' => 'Mark Property as Favourite for Contact.',
                'data' => [
                    'first_name' => $contact->getFirstName(),
                    'last_name' => $contact->getLastName(),
                    'email' => $contact->getEmail(),
                    'phone' => $contact->getNumber(),
                ],
            ],
            200
        );

    }

}
