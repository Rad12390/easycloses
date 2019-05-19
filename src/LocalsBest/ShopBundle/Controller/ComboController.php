<?php

namespace LocalsBest\ShopBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use LocalsBest\CommonBundle\Controller\SuperController as Controller;
use LocalsBest\ShopBundle\Entity\Combo;
use LocalsBest\ShopBundle\Entity\ComboSkuSet;
use LocalsBest\ShopBundle\Entity\Item;
use LocalsBest\ShopBundle\Form\ComboType;
use Stripe\Error\Base;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ComboController extends Controller
{
    /**
     * Lists all Combo entities.
     *
     * @return Response
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        if ($user->getRole()->getLevel() > 1) {
            $entities = $em->getRepository('LocalsBestShopBundle:Combo')->findBy([
                'createdBy' => $user,
            ]);
        } else {
            $entities = $em->getRepository('LocalsBestShopBundle:Combo')->findAll();
        }

        return $this->render('@LocalsBestShop/combo/index.html.twig', array(
            'entities' => $entities,
        ));
    }

    /**
     * Creates a new Combo entity.
     *
     * @param Request $request
     * @throws Base
     *
     * @return RedirectResponse|Response
     */
    public function createAction(Request $request)
    {
        $entity = new Combo();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->setCreatedBy($this->getUser());

            $quantity = $entity->getQuantity();
            /** @var ComboSkuSet $set */
            foreach ($entity->getSkuSets() as $set) {
                $sku = $set->getSku();
                $skuQuantity = $sku->getProductEntity()->getQuantity();
                $skuSetQuantity = $set->getQuantity();

                $sku->getProductEntity()->setQuantity($skuQuantity - $quantity * $skuSetQuantity);
            }

            $productSku = $entity->getSku();
            $stripeAccountId = $entity->getSku()->getCreatedBy()->getStripeAccountId();
            foreach ($productSku->getPrices() as $priceMetric) {
                if ($priceMetric->getType() == "subscription") {
                    $planCreds = array(
                        'wholesaler' => $entity->getSku()->getCreatedBy(),
                        'name' => $entity->getTitle(),
                        'amount' => $priceMetric->getRetailAmount($entity->getItems()->first()->getMarkup()),
                        'description' => $entity->getDescription(),
                        'interval' => $priceMetric->getSubscriptionType(),
                        'stripe_account' =>	$stripeAccountId != '' ? $stripeAccountId : $this->container->getParameter('stripe_client_id')
                    );

                    $result = $this->get('app.client.stripe')->createSubscriptionPlan($planCreds);

                    if ($result->id){
                        $priceMetric->setStripeplanid($result->id);
                        $priceMetric->setStripeproductid($result->product);
                    }
                }
            }

            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('combo_show', array('id' => $entity->getId())));
        }

        return $this->render('@LocalsBestShop/combo/new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Combo entity.
     *
     * @param Combo $entity The entity
     *
     * @return Form The form
     */
    private function createCreateForm(Combo $entity)
    {
        $form = $this->createForm(ComboType::class, $entity, array(
            'action' => $this->generateUrl('combo_create'),
            'method' => 'POST',
            'user' => $this->getUser(),
            'business' => $this->getBusiness(),
            'isApproved' => $entity->getStatus() == Item::STATUS_APPROVED ? true : false,
            'combo' => $entity,
        ));

        $form->add('submit', Type\SubmitType::class, array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Combo entity.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function newAction(Request $request)
    {
        $isCopy = false;

        if ($request->query->has('copy_of')) {
            $parentEntity = $this->getDoctrine()->getRepository('LocalsBestShopBundle:Combo')
                ->find($request->query->get('copy_of'));
            $entity = clone $parentEntity;
            $isCopy = true;
        } else {
            $entity = new Combo();
        }
        $form   = $this->createCreateForm($entity);

        return $this->render('@LocalsBestShop/combo/new.html.twig', array(
            'isCopy' => $isCopy,
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }


    /**
     * Finds and displays a Combo entity.
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
            $entity = $em->getRepository('LocalsBestShopBundle:Combo')->findOneBy([
                'id' => $id,
                'createdBy' => $user,
            ]);
        } else {
            $entity = $em->getRepository('LocalsBestShopBundle:Combo')->find($id);
        }

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Combo entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('@LocalsBestShop/combo/show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Combo entity.
     *
     * @param $id
     *
     * @return Response
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $this->getUser();

        /** @var Combo $entity */
        if ($user->getRole()->getLevel() > 1) {
            $entity = $em->getRepository('LocalsBestShopBundle:Combo')->findOneBy([
                'id' => $id,
                'createdBy' => $user,
            ]);
        } else {
            $entity = $em->getRepository('LocalsBestShopBundle:Combo')->find($id);
        }

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Combo entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('@LocalsBestShop/combo/edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Combo entity.
    *
    * @param Combo $entity The entity
    *
    * @return Form The form
    */
    private function createEditForm(Combo $entity)
    {
        $form = $this->createForm(ComboType::class, $entity, array(
            'action' => $this->generateUrl('combo_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'user' => $this->getUser(),
            'business' => $this->getBusiness(),
            'isApproved' => $entity->getStatus() == Item::STATUS_APPROVED ? true : false,
            'combo' => $entity,
        ));

        $form->add('submit', Type\SubmitType::class, array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing Combo entity.
     *
     * @param Request $request
     * @param $id
     *
     * @throws Base
     * @return RedirectResponse|Response
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        /** @var Combo $entity */
        if ($user->getRole()->getLevel() > 1) {
            $entity = $em->getRepository('LocalsBestShopBundle:Combo')->findOneBy([
                'id' => $id,
                'createdBy' => $user,
            ]);
        } else {
            $entity = $em->getRepository('LocalsBestShopBundle:Combo')->find($id);
        }

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Combo entity.');
        }

        $originalSkuSets = new ArrayCollection();
        $originalPrices = new ArrayCollection();

        foreach ($entity->getSkuSets() as $set) {
            $originalSkuSets->add($set);
        }

        foreach ($entity->getSku()->getPrices() as $price) {
            $originalPrices->add($price);
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {

            $uow = $em->getUnitOfWork();
            $uow->computeChangeSets();

            $comboChangeSet = $uow->getEntityChangeSet($entity);
            if (isset($comboChangeSet['quantity'])) {
                list($comboOldQuantity, $comboNewQuantity) = $comboChangeSet['quantity'];
            }

            foreach ($originalSkuSets as $set) {
                if (false === $entity->getSkuSets()->contains($set)) {
                    $em->remove($set);
                }
            }

            /** @var ComboSkuSet $set */
            foreach ($entity->getSkuSets() as $set) {
                $changeSet = $uow->getEntityChangeSet($set);

                if (isset($comboChangeSet['quantity']) && isset($changeSet['quantity'])) {
                    list($oldQuantity, $newQuantity) = $changeSet['quantity'];
                    $sku = $set->getSku();
                    $skuQuantity = $sku->getProductEntity()->getQuantity();
                    $sku->getProductEntity()
                        ->setQuantity($skuQuantity + $comboOldQuantity * $oldQuantity - $comboNewQuantity * $newQuantity);
                } elseif (isset($comboChangeSet['quantity'])) {
                    $sku = $set->getSku();
                    $skuQuantity = $sku->getProductEntity()->getQuantity();
                    $sku->getProductEntity()
                        ->setQuantity($skuQuantity + ($comboOldQuantity - $comboNewQuantity) * $set->getQuantity());
                } elseif (isset($changeSet['quantity'])) {
                    list($oldQuantity, $newQuantity) = $changeSet['quantity'];
                    $sku = $set->getSku();
                    $skuQuantity = $sku->getProductEntity()->getQuantity();
                    $sku->getProductEntity()
                        ->setQuantity($skuQuantity + ($oldQuantity - $newQuantity) * $entity->getQuantity());
                }
            }

            foreach ($originalPrices as $price) {
                if (false === $entity->getSku()->getPrices()->contains($price)) {
                    $em->remove($price);
                }
            }

            $stripeAccountId = $entity->getSku()->getCreatedBy()->getStripeAccountId();
            foreach ($entity->getSku()->getPrices() as $price) {
                $changePrice = $uow->getEntityChangeSet($price);
                $priceMetadata = $em->getClassMetadata('LocalsBestShopBundle:Price');

                if (isset($changePrice['type'])) {
                    list($oldPriceType, $newPriceType) = $changePrice['type'];
                    if ($newPriceType == 'subscription') {
                        $planCreds = array(
                            'wholesaler' => $entity->getSku()->getCreatedBy(),
                            'name' => $entity->getTitle(),
                            'amount' => $price->getRetailAmount($entity->getItems()->first()->getMarkup()),
                            'description' => $entity->getDescription(),
                            'interval' => $price->getSubscriptionType(),
                            'stripe_account' =>	$stripeAccountId != '' ? $stripeAccountId : $this->container->getParameter('stripe_client_id')
                        );

                        $result = $this->get('app.client.stripe')->createSubscriptionPlan($planCreds);
                        if ($result->id) {
                            $price->setStripeplanid($result->id);
                            $price->setStripeproductid($result->product);
                        }
                        $uow->recomputeSingleEntityChangeSet($priceMetadata, $price);
                    }
                } elseif ($price->getType() == 'subscription' && isset($changePrice['amount'])) {
                    list($oldPriceAmount, $newPriceAmount) = $changePrice['amount'];
                    if ($oldPriceAmount != $newPriceAmount) {
                        $planCreds = array(
                            'wholesaler' => $entity->getSku()->getCreatedBy(),
                            'name' => $entity->getTitle(),
                            'amount' => $price->getRetailAmount($entity->getItems()->first()->getMarkup()),
                            'description' => $entity->getDescription(),
                            'interval' => $price->getSubscriptionType(),
                            //'interval_count' => $price->getSubscriptionInterval(),
                            'stripe_account' =>	$stripeAccountId != '' ? $stripeAccountId : $this->container->getParameter('stripe_client_id')
                        );
                        $result = $this->get('app.client.stripe')->createSubscriptionPlan($planCreds);
                        if ($result->id) {
                            $price->setStripeplanid($result->id);
                            $price->setStripeproductid($result->product);
                        }
                        $uow->recomputeSingleEntityChangeSet($priceMetadata, $price);
                    }
                } elseif ($price->getType() == 'subscription' && $price->getStripeplanid() === null) {
                    $planCreds = array(
                        'wholesaler' => $entity->getSku()->getCreatedBy(),
                        'name' => $entity->getTitle(),
                        'amount' => $price->getRetailAmount($entity->getItems()->first()->getMarkup()),
                        'description' => $entity->getDescription(),
                        'interval' => $price->getSubscriptionType(),
                        'stripe_account' =>	$stripeAccountId != '' ? $stripeAccountId : $this->container->getParameter('stripe_client_id')
                    );
                    $result = $this->get('app.client.stripe')->createSubscriptionPlan($planCreds);
                    if ($result->id) {
                        $price->setStripeplanid($result->id);
                        $price->setStripeproductid($result->product);
                    }
                    $uow->recomputeSingleEntityChangeSet($priceMetadata, $price);
                } elseif ($price->getType() == 'one_time') {
                    $price->setStripeplanid(null);
                    $price->setStripeproductid(null);
                }
            }
            $em->flush();

            return $this->redirect($this->generateUrl('combo_edit', array('id' => $id)));
        }

        return $this->render('@LocalsBestShop/combo/edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Combo entity.
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

            /** @var Combo $entity */
            if ($user->getRole()->getLevel() > 1) {
                $entity = $em->getRepository('LocalsBestShopBundle:Combo')->findOneBy([
                    'id' => $id,
                    'createdBy' => $user,
                ]);
            } else {
                $entity = $em->getRepository('LocalsBestShopBundle:Combo')->find($id);
            }

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Combo entity.');
            }

            $quantity = $entity->getQuantity();
            /** @var ComboSkuSet $set */
            foreach ($entity->getSkuSets() as $set) {
                $sku = $set->getSku();
                $skuQuantity = $sku->getProductEntity()->getQuantity();
                $skuSetQuantity = $set->getQuantity();

                $sku->getProductEntity()->setQuantity($skuQuantity + $quantity * $skuSetQuantity);
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('combo'));
    }

    /**
     * Creates a form to delete a Combo entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('combo_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', Type\SubmitType::class, array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
