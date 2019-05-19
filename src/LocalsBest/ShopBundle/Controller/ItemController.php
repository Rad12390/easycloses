<?php

namespace LocalsBest\ShopBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use LocalsBest\CommonBundle\Controller\SuperController as Controller;
use LocalsBest\ShopBundle\Entity\Item;
use LocalsBest\ShopBundle\Entity\Restriction;
use LocalsBest\ShopBundle\Form\ItemType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ItemController extends Controller
{

    /**
     * Lists all Item entities.
     *
     * @return Response
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        $entities = $em->getRepository('LocalsBestShopBundle:Item')->findItem($user);

        return $this->render('@LocalsBestShop/item/index.html.twig', array(
            'entities' => $entities,
        ));
    }

    /**
     * Creates a new Item entity.
     *
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    public function createAction(Request $request)
    {
        $data = $request->request->all();
        $entity = new Item();
        $form = $this->createCreateForm($entity);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $entity->setCreatedBy($this->getUser());

            if ($this->getUser()->getRole()->getLevel() !== 1) {
                if (in_array($entity->getType(), [1, 4])) {
                    $entity->setMarkup(10);
                } elseif ($entity->getType() == 2) {
                    $entity->setMarkup(50);
                } else {
                    $entity->setMarkup(0);
                }
            }

            if (isset($data['localsbest_shopbundle_item']['submit_draft'])) {
                $entity->setStatus(1);
            } else {
                $entity->setStatus(2);
            }

            $em = $this->getDoctrine()->getManager();

            // New Restrictions
            $this->additemRestrictions($data, array(), $entity, $em);
            foreach ($entity->getRestrictions() as $test) {
                $test->setType('use');
            }

            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('items'));
        }
        //code for first upgrade of state dropdown
        $state_status = 1;
        if ($this->get('localsbest.checker')->forAddon('first item state limit', $this->getUser())) {
            $state_status = 2;
        }
        if ($this->get('localsbest.checker')->forAddon('second item state limit', $this->getUser())) {
            $state_status = 3;
        }
        if ($this->get('localsbest.checker')->forAddon('third item state limit', $this->getUser())) {
            $state_status = 4;
        }

        $external_link_status = 0;
        if ($this->get('localsbest.checker')->forAddon('Links Tab and Custom Login', $this->getUser())) {
            $external_link_status = 1;
        }
        return $this->render('@LocalsBestShop/item/new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
            'role' => $this->getUser()->getRole()->getName(),
            'state_status' => $state_status,
            'external_link_status' => $external_link_status
        ));
    }

    /**
     * Creates a form to create a Item entity.
     *
     * @param Item $entity The entity
     *
     * @return Form The form
     */
    private function createCreateForm(Item $entity)
    {
        foreach ($this->getUser()->getBusinesses() as $business) {
            foreach ($business->getTypes() as $type) {
                $result1[] = $type->getId();
            }
        }

        foreach ($this->getUser()->getBusinesses() as $business) {
            foreach ($business->getWorkingStates() as $states) {
                $result[] = $states->getId();
            }
        }
        $form = $this->createForm(ItemType::class, $entity, array(
            'action' => $this->generateUrl('items_create'),
            'method' => 'POST',
            'isApproved' => $entity->getStatus() == Item::STATUS_APPROVED ? true : false,
            'user' => $this->getUser(),
            'business' => $this->getBusiness(),
            'states' => $result,
            'industryType' => $result1
        ));

        $form->add('submit_draft', Type\SubmitType::class, ['label' => 'Save as Draft', 'attr' => ['class' => 'btn']]);
        $form->add('submit_active', Type\SubmitType::class, ['label' => 'Save as Active']);

        return $form;
    }

    /**
     * Displays a form to create a new Item entity.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function newAction(Request $request)
    {
        $isCopy = false;

        if ($request->query->has('copy_of')) {
            $parentEntity = $this->getDoctrine()->getRepository('LocalsBestShopBundle:Item')
                ->find($request->query->get('copy_of'));
            $entity = clone $parentEntity;
            $isCopy = true;
        } else {
            $entity = new Item();
        }
        $entity->addRestriction(new Restriction());
        $form = $this->createCreateForm($entity);

        //code for first upgrade of state dropdown
        $state_status = 1;
        if ($this->get('localsbest.checker')->forAddon('first item state limit', $this->getUser())) {
            $state_status = 2;
        }
        if ($this->get('localsbest.checker')->forAddon('second item state limit', $this->getUser())) {
            $state_status = 3;
        }
        if ($this->get('localsbest.checker')->forAddon('third item state limit', $this->getUser())) {
            $state_status = 4;
        }
        $external_link_status = 0;
        if ($this->get('localsbest.checker')->forAddon('Links Tab and Custom Login', $this->getUser())) {
            $external_link_status = 1;
        }
        return $this->render('@LocalsBestShop/item/new.html.twig', array(
            'isCopy' => $isCopy,
            'entity' => $entity,
            'form' => $form->createView(),
            'role' => $this->getUser()->getRole()->getName(),
            'state_status' => $state_status,
            'external_link_status' => $external_link_status
        ));
    }

    /**
     * Finds and displays a Item entity.
     *
     * @param $id
     *
     * @return Response
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        if ($user->getRole()->getLevel() > 1) {
            $entity = $em->getRepository('LocalsBestShopBundle:Item')->findOneBy([
                'id' => $id,
                'createdBy' => $user
            ]);
        } else {
            $entity = $em->getRepository('LocalsBestShopBundle:Item')->find($id);
        }

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Item entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('@LocalsBestShop/item/show.html.twig', array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Item entity.
     *
     * @param $id
     *
     * @return Response
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        if ($user->getRole()->getLevel() > 1) {
            $entity = $em->getRepository('LocalsBestShopBundle:Item')->findOneBy([
                'id' => $id,
                'createdBy' => $user
            ]);
        } else {
            $entity = $em->getRepository('LocalsBestShopBundle:Item')->find($id);
        }

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Item entity.');
        }
        $use_entity = $entity;
        $use_status = 0;
        //code to get the details of use type
        foreach ($use_entity->getRestrictions() as $test) {
            if ($test->getType() == 'use') {
                $use_status = 1;
                //get value of type for the use type
                $use_type = $test->getType();
                $use_role = [];
                $use_industries = [];
                $use_businesses = [];
                $use_states = [];
                $use_cities = [];

                foreach ($test->getRoles() as $role) {
                    array_push($use_role, $role->getId());
                }
                $use_role = implode(',', $use_role); // use role

                $use_industriesSwitch = $test->getIndustriesSwitch();  // use industriesSwitch
                $use_businessesSwitch = $test->getBusinessesSwitch();  // use businessesSwitch
                $use_citiesSwitch = $test->getCitiesSwitch();  // use citiesSwitch

                foreach ($test->getIndustries() as $industry) {
                    array_push($use_industries, $industry->getId());
                }
                $use_industries = implode(',', $use_industries); // use industries

                foreach ($test->getBusinesses() as $business) {
                    array_push($use_businesses, $business->getId());
                }
                $use_businesses = implode(',', $use_businesses); // use businesses

                foreach ($test->getStates() as $states) {
                    array_push($use_states, $states->getId());
                }
                $use_states = implode(',', $use_states); // use states

                foreach ($test->getCities() as $cities) {
                    array_push($use_cities, $cities->getId());
                }
                $use_cities = implode(',', $use_cities); // use states
                break;
            }
        }


        if ($entity->getRestrictions()->isEmpty()) {
            $entity->setRestriction([new Restriction()]);
        } else {
            foreach ($entity->getRestrictions() as $test) {
                if ($test->getType() != 'use') {
                    $entity->setRestriction([$test]);
                }
            }
        }

        //code for first upgrade of state dropdown
        $state_status = 1;
        if ($this->get('localsbest.checker')->forAddon('first item state limit', $this->getUser())) {
            $state_status = 2;
        }
        if ($this->get('localsbest.checker')->forAddon('second item state limit', $this->getUser())) {
            $state_status = 3;
        }
        if ($this->get('localsbest.checker')->forAddon('third item state limit', $this->getUser())) {
            $state_status = 4;
        }

        $external_link_status = 0;
        if ($this->get('localsbest.checker')->forAddon('Links Tab and Custom Login', $this->getUser())) {
            $external_link_status = 1;
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);
        $item_id = $entity->getId();
        $city_id = [];

        foreach ($entity->getRestrictions() as $restrict) {
            if ($restrict->getCities()) {
                foreach ($restrict->getCities() as $city) {
                    array_push($city_id, $city->getId());
                }
            }
        }

        $city = implode(',', $city_id);

        if ($use_status == 1) {
            return $this->render('@LocalsBestShop/item/edit.html.twig', array(
                'entity' => $entity,
                'use_entity' => $use_entity,
                'edit_form' => $editForm->createView(),
                'delete_form' => $deleteForm->createView(),
                'role' => $this->getUser()->getRole()->getName(),
                'state_status' => $state_status,
                'city_id' => $city,
                'use_type' => $use_type,
                'use_role' => $use_role,
                'use_industriesSwitch' => $use_industriesSwitch,
                'use_businessesSwitch' => $use_businessesSwitch,
                'use_citiesSwitch' => $use_citiesSwitch,
                'use_industries' => $use_industries,
                'use_businesses' => $use_businesses,
                'use_states' => $use_states,
                'use_cities' => $use_cities,
                'use_status' => $use_status,
                'external_link_status' => $external_link_status
            ));
        } else {
            return $this->render('@LocalsBestShop/item/edit.html.twig', array(
                'entity' => $entity,
                'use_entity' => $use_entity,
                'edit_form' => $editForm->createView(),
                'delete_form' => $deleteForm->createView(),
                'role' => $this->getUser()->getRole()->getName(),
                'state_status' => $state_status,
                'city_id' => $city,
                'use_status' => $use_status,
                'use_type' => '',
                'use_role' => '',
                'use_industriesSwitch' => '',
                'use_businessesSwitch' => '',
                'use_citiesSwitch' => '',
                'use_industries' => '',
                'use_businesses' => '',
                'use_states' => '',
                'use_cities' => '',
                'external_link_status' => $external_link_status
            ));
        }
    }

    /**
     * Creates a form to edit a Item entity.
     *
     * @param Item $entity The entity
     *
     * @return Form The form
     */
    private function createEditForm(Item $entity)
    {
        foreach ($this->getUser()->getBusinesses() as $business) {
            foreach ($business->getTypes() as $type) {
                $result1[] = $type->getId();
            }
        }

        foreach ($this->getUser()->getBusinesses() as $business) {
            foreach ($business->getWorkingStates() as $states) {
                $result[] = $states->getId();
            }
        }

        $form = $this->createForm(ItemType::class, $entity, array(
            'action' => $this->generateUrl('items_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'isApproved' => $entity->getStatus() == Item::STATUS_APPROVED ? true : false,
            'user' => $this->getUser(),
            'business' => $this->getBusiness(),
            'states' => $result,
            'industryType' => $result1
        ));

        $form->add('submit_draft', Type\SubmitType::class, ['label' => 'Save as Draft']);
        $form->add('submit_active', Type\SubmitType::class, ['label' => 'Save as Active']);

        return $form;
    }

    /**
     * Edits an existing Item entity.
     *
     * @param Request $request
     * @param $id
     *
     * @return RedirectResponse|Response
     */
    public function updateAction(Request $request, $id)
    {
        $data = $request->request->all();
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        /** @var Item $entity */
        if ($user->getRole()->getLevel() > 1) {
            $entity = $em->getRepository('LocalsBestShopBundle:Item')->findOneBy([
                'id' => $id,
                'createdBy' => $user
            ]);
        } else {
            $entity = $em->getRepository('LocalsBestShopBundle:Item')->find($id);
        }

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Item entity.');
        }

        $originalDispositions = new ArrayCollection();
        $originalRestrictions = new ArrayCollection();
        $originalImages = new ArrayCollection();

        foreach ($entity->getDispositions() as $disposition) {
            $originalDispositions->add($disposition);
        }

        foreach ($entity->getRestrictions() as $restriction) {
            $originalRestrictions->add($restriction);
        }

        foreach ($entity->getImages() as $image) {
            $originalImages->add($image);
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            foreach ($originalDispositions as $disposition) {
                if (false === $entity->getDispositions()->contains($disposition)) {
                    $em->remove($disposition);
                }
            }

            foreach ($originalImages as $image) {
                if (false === $entity->getImages()->contains($image)) {
                    $em->remove($image);
                }
            }

            if ($this->getUser()->getRole()->getLevel() !== 1) {
                if (in_array($entity->getType(), [1, 4])) {
                    $entity->setMarkup(10);
                } elseif ($entity->getType() == 2) {
                    $entity->setMarkup(50);
                } else {
                    $entity->setMarkup(0);
                }
            }

            if (isset($data['localsbest_shopbundle_item']['submit_draft'])) {
                $entity->setStatus(1);
            } else {
                $entity->setStatus(2);
            }
            // New Restrictions
            $this->additemRestrictions($data, $originalRestrictions, $entity, $em);


            $em->flush();
            return $this->redirect($this->generateUrl('items'));
        }

        return $this->render('@LocalsBestShop/item/edit.html.twig', array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Item entity.
     *
     * @param Request $request
     * @param $id
     *
     * @return RedirectResponse
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $user = $this->getUser();

            if ($user->getRole()->getLevel() > 1) {
                $entity = $em->getRepository('LocalsBestShopBundle:Item')->findOneBy([
                    'id' => $id,
                    'createdBy' => $user
                ]);
            } else {
                $entity = $em->getRepository('LocalsBestShopBundle:Item')->find($id);
            }

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Item entity.');
            }

            if (count($entity->getSets()) > 0) {
                $this->addFlash('danger', 'You are not able to delete Item that is a part of Packages.');
                return $this->redirectToRoute('items_show', ['id' => $id]);
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('items'));
    }

    /**
     * Creates a form to delete a Item entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
                ->setAction($this->generateUrl('items_delete', array('id' => $id)))
                ->setMethod('DELETE')
                ->add('submit', Type\SubmitType::class, array('label' => 'Delete', 'attr' => ['class' => 'btn btn-danger']))
                ->getForm()
        ;
    }

    /**
     * Lists of non approved Sku entities.
     *
     * @return array
     * @Template
     */
    public function forApproveListAction()
    {
        if ($this->getUser()->getRole()->getLevel() > 5) {
            throw $this->createNotFoundException('Unable to find page.');
        }

        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('LocalsBestShopBundle:Item')->findAllBy($this->getBusiness(), true);

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Update status for Item entity
     *
     * @param Request $request
     *
     * @return RedirectResponse|Response|static
     */
    public function statusChangeAction(Request $request)
    {
        if ($request->isMethod('POST')) {
            $data = $request->request->all();
        } else {
            $data = $request->query->all();
        }

        $em = $this->getDoctrine()->getManager();
        $item = $em->getRepository('LocalsBestShopBundle:Item')->find($data['item']);
        $item->setStatus(Item::STATUS_APPROVED);
        $em->flush();

        if ($request->isXmlHttpRequest()) {
            return JsonResponse::create([
                    'success' => true,
            ]);
        } else {
            return $this->redirectToRoute('items_for_approve_list');
        }
    }

    public function getSingleItemAction(Request $request)
    {
        $data = $request->request->all();
        $em = $this->getDoctrine()->getManager();
        $item = $em->getRepository('LocalsBestShopBundle:Item')->find($data['item']);
        $result = [];
        foreach ($item->getImages() as $image) {
            $split = explode("ec_shop", $image->getFile());
            $split = $this->getParameter('temp_download_aws_folder') . 'ec_shop' . $split[2];
            array_push($result, $split);
        }

        return JsonResponse::create([
                'title' => $item->getTitle(),
                'description' => $item->getDescription(),
                'short_description' => $item->getShortDescription(),
                'images' => $item->getImages()->getValues(),
                'quantity' => $item->getQuantity(),
                'result' => $result
        ]);
    }

    private function additemRestrictions($data, $originalRestrictions = array(), $entity, $em)
    {
        if (isset($data['localsbest_shopbundle_item']['restrictions'][0])) {
            $restrictionData = $data['localsbest_shopbundle_item']['restrictions'][0];
            if (!isset($restrictionData['cities'])) {
                $restrictionData['citiesSwitch'] = '';
            }

            if ($restrictionData['buyerType'] == 'type1') {
                $roleType = [4, 5];
            }
            if ($restrictionData['buyerType'] == 'type2') {
                $roleType = [4, 5, 6, 7];
            }
            if ($restrictionData['buyerType'] == 'type3') {
                $roleType = [4, 5, 6, 7, 9];
            }

            $type = ['view', 'purchase'];

            // Before add remove all restrictions

            foreach ($originalRestrictions as $restriction) {
                $em->remove($restriction);
            }
            // code added so as to save the restriction for view & purchase fields
            foreach ($type as $view) {
                $restrictionEn = new Restriction();
                $restrictionEn->setItem($entity);
                $restrictionEn->setType($view);
                $restrictionEn->setBuyerType($restrictionData['buyerType']);
                if (isset($restrictionData['rolesSwitch'])) {
                    $restrictionEn->setRolesSwitch($restrictionData['rolesSwitch']);
                }
                $restrictionEn->setCitiesSwitch($restrictionData['citiesSwitch']);
                $restrictionEn->setIndustriesSwitch($restrictionData['industriesSwitch']);
                $restrictionEn->setBusinessesSwitch($restrictionData['businessesSwitch']);

                $role = $em->getRepository('LocalsBestUserBundle:Role')->findById($roleType);
                $restrictionEn->setRole($role);

                if (isset($restrictionData['states'])) {
                    $states = $em->getRepository('LocalsBestUserBundle:State')->findById($restrictionData['states']);
                    $restrictionEn->setState($states);
                }

                if (isset($restrictionData['cities'])) {
                    $cities = $em->getRepository('LocalsBestUserBundle:City')->findById($restrictionData['cities']);
                    $restrictionEn->setCity($cities);
                }

                if (isset($restrictionData['industries'])) {
                    $IndustryType = $em->getRepository('LocalsBestUserBundle:IndustryType')->findById($restrictionData['industries']);
                    $restrictionEn->setIndustry($IndustryType);
                }

                if (isset($restrictionData['businesses'])) {
                    $business = $em->getRepository('LocalsBestUserBundle:Business')->findById($restrictionData['businesses']);
                    $restrictionEn->setBusiness($business);
                }

                $em->persist($restrictionEn);
            }

            // code added so as to save the restriction for use field
            if (isset($data['localsbest_shopbundle_item']['restrictions'][1])) {
                $restrictionData1 = $data['localsbest_shopbundle_item']['restrictions'][1];
                if ($restrictionData1['type'] == 'use' && $data['localsbest_shopbundle_item']['type'] == 2) {
                    $type = ['use'];
                    $roleType = [];
                    if (isset($restrictionData1['roles'])) {
                        foreach ($restrictionData1['roles'] as $roles) {
                            array_push($roleType, $roles);
                        }
                    }
                    foreach ($type as $view) {
                        $restrictionEn = new Restriction();
                        $restrictionEn->setItem($entity);
                        $restrictionEn->setType($view);
                        $restrictionEn->setBuyerType($restrictionData['buyerType']);
                        if (isset($restrictionData['rolesSwitch'])) {
                            $restrictionEn->setRolesSwitch($restrictionData1['rolesSwitch']);
                        }
                        $restrictionEn->setCitiesSwitch($restrictionData1['citiesSwitch']);
                        $restrictionEn->setIndustriesSwitch($restrictionData1['industriesSwitch']);
                        $restrictionEn->setBusinessesSwitch($restrictionData1['businessesSwitch']);

                        $role = $em->getRepository('LocalsBestUserBundle:Role')->findById($roleType);
                        $restrictionEn->setRole($role);

                        if (isset($restrictionData1['states'])) {
                            $states = $em->getRepository('LocalsBestUserBundle:State')->findById($restrictionData1['states']);
                            $restrictionEn->setState($states);
                        }

                        if (isset($restrictionData1['cities'])) {
                            $cities = $em->getRepository('LocalsBestUserBundle:City')->findById($restrictionData1['cities']);
                            $restrictionEn->setCity($cities);
                        }

                        if (isset($restrictionData1['industries'])) {
                            $IndustryType = $em->getRepository('LocalsBestUserBundle:IndustryType')->findById($restrictionData1['industries']);
                            $restrictionEn->setIndustry($IndustryType);
                        }

                        if (isset($restrictionData1['businesses'])) {
                            $business = $em->getRepository('LocalsBestUserBundle:Business')->findById($restrictionData1['businesses']);
                            $restrictionEn->setBusiness($business);
                        }

                        $em->persist($restrictionEn);
                    }
                }
            }
        }
    }

    public function getCityBasedStateAction(Request $request)
    {
        $data = $request->request->all();
        $em = $this->getDoctrine()->getManager();
        $data = implode(",", $data['states']);
        
        $query = 'SELECT * FROM `cities` where state_id IN (:data)';
        $statement = $em->getConnection()->prepare($query);
        $statement->bindValue(':data', $data);
        $statement->execute();
        $result = $statement->fetchAll();
        return JsonResponse::create([
                'result' => $result,
        ]);
    }

}
