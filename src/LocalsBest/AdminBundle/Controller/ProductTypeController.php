<?php

namespace LocalsBest\AdminBundle\Controller;

use DateInterval;
use DateTime;
use LocalsBest\AdminBundle\Form\ProductAssignType;
use LocalsBest\AdminBundle\Form\ProductTypeType;
use LocalsBest\SocialMediaBundle\Entity\Bucket;
use LocalsBest\UserBundle\Entity\PaymentRow;
use LocalsBest\UserBundle\Entity\PaymentSet;
use LocalsBest\UserBundle\Entity\Product;
use LocalsBest\UserBundle\Entity\ProductType;
use LocalsBest\UserBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

/**
 * Product controller.
 *
 * @Route("/product-types")
 */
class ProductTypeController extends Controller
{
    /**
     * Creates a new Product entity.
     *
     * @Route("/", name="admin_product_type_create")
     * @Method("POST")
     * @Template("@LocalsBestAdmin/product/new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new ProductType();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            if($entity->getValue() === null) {
                $entity->setValue(0);
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirectToRoute('admin_products');
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Product entity.
     *
     * @param ProductType $entity The entity
     *
     * @return Form The form
     */
    private function createCreateForm(ProductType $entity)
    {
        $form = $this->createForm(ProductTypeType::class, $entity, array(
            'action' => $this->generateUrl('admin_product_type_create'),
            'method' => 'POST',
            'user' => $this->getUser(),
        ));

        $form->add('submit', Type\SubmitType::class, array('label' => 'Create', 'attr' => ['class' => 'btn btn-success']));

        return $form;
    }

    /**
     * Displays a form to create a new Product entity.
     *
     * @Route("/new", name="admin_product_type_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new ProductType();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new ProductType entity.
     *
     * @Route("/{id}/edit", name="admin_product_type_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LocalsBestUserBundle:ProductType')->find($id);
        $form   = $this->createEditForm($entity);

        return array(
            'entity' => $entity,
            'form_edit'   => $form->createView(),
        );
    }

    /**
     * Creates a form to edit a ProductType entity.
     *
     * @param ProductType $entity The entity
     *
     * @return Form The form
     */
    private function createEditForm(ProductType $entity)
    {
        $form = $this->createForm(ProductTypeType::class, $entity, array(
            'action' => $this->generateUrl('admin_product_type_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'user' => $this->getUser(),
        ));

        $form->add('submit', Type\SubmitType::class, array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing ProductType entity.
     *
     * @Route("/{id}", name="admin_product_type_update")
     * @Method("PUT")
     * @Template("@LocalsBestUser/advertisement/edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LocalsBestUserBundle:ProductType')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Product Type entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            if($entity->getValue() === null) {
                $entity->setValue(0);
            }

            $em->flush();

            return $this->redirectToRoute('admin_products');
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        );
    }

    /**
     * Displays a form to create a new Product entity.
     *
     * @Route("/{id}/delete", name="admin_product_type_delete")
     * @Method("DELETE")
     * @Template()
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('LocalsBestUserBundle:ProductType')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ProductCategory entity.');
        }

        $em->remove($entity);
        $em->flush();

        return $this->redirectToRoute('admin_products');
    }

    /**
     * Select Users to Assign Product Type entity.
     *
     * @Route("/assign/{id}/to", name="admin_product_type_assign_to")
     * @Method("GET")
     * @Template("@LocalsBestAdmin/product_type/assign_to.html.twig")
     */
    public function assignToAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LocalsBestUserBundle:ProductType')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Product Type entity.');
        }

        $assignForm = $this->assignForm($entity);

        $paymentSets = $em->getRepository('LocalsBestUserBundle:PaymentSet')
            ->findByAddon($entity->getProduct(), $entity->getType());

        return array(
            'entity'      => $entity,
            'paymentSets' => $paymentSets,
            'assign_form'   => $assignForm->createView(),
        );
    }

    /**
     * Creates a form to assign a ProductType entity to Users.
     *
     * @param Product $entity The entity
     *
     * @return Form The form
     */
    private function assignForm(ProductType $entity)
    {
        $form = $this->createForm(ProductAssignType::class, $entity, array(
            'action' => $this->generateUrl('admin_product_type_assign', array('id' => $entity->getId())),
            'method' => 'POST',
        ));

        $form->add('submit', Type\SubmitType::class, array('label' => 'Assign', 'attr' => ['class' => 'btn btn-primary']));

        return $form;
    }

    /**
     * Assign Product entity to Users.
     *
     * @Route("/assign/{id}/to", name="admin_product_type_assign")
     * @Method("POST")
     */
    public function assignAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $productType = $em->getRepository('LocalsBestUserBundle:ProductType')->find($id);

        if (!$productType) {
            throw $this->createNotFoundException('Unable to find Product entity.');
        }

        $assignForm = $this->assignForm($productType);
        $assignForm->handleRequest($request);

        if ($assignForm->isValid()) {

            $users  = $assignForm['users']->getData();

            /** @var User $user */
            foreach ($users as $user) {

                $haveType = $em->getRepository('LocalsBestUserBundle:PaymentRow')
                    ->checkTypeForUser($productType, $user);

                if ($haveType) {
                    $this->addFlash('warning', $user->getFullName().' already have this product');
                    continue;
                }

                $paymentSet = new PaymentSet();
                $paymentSet->setCreatedBy($user);

                $paymentRow = new PaymentRow();
                $paymentRow->setProductName($productType->getProduct()->getTitle());
                $paymentRow->setPrice($productType->getPrice()*(1+$productType->getMargin()/100));
                $paymentRow->setSetupFee($productType->getSetupFee());
                $paymentRow->setQuantity(1);

                $paymentRow->setProduct($productType->getProduct());
                $paymentRow->setProductType($productType->getType());

                $subResult = null;
                if ($productType->getType() == 'subscription') {
                    $endDate = $date = new DateTime('now');
                    $endDate->add(new DateInterval('P'.$productType->getSubscriptionPeriod()*$productType->getSubscriptionCharges().'M'));

                    $paymentRow->setEndDate($endDate);
                }

                if ($productType->getType() == 'counter') {
                    $paymentRow->setProductLimit($productType->getValue());
                    $paymentRow->setProductUses(0);

                    $endDate = $date = new DateTime('now');
                    $endDate->add(new DateInterval('P'.$productType->getSubscriptionPeriod().'M'));

                    $paymentRow->setEndDate($endDate);
                }

                $amount = $productType->getSetupFee()+1*($productType->getPrice()*(1+$productType->getMargin()/100));

                /** @var Bucket $bucket */
                $bucket = $em->getRepository('LocalsBestSocialMediaBundle:Bucket')->findOneBy([
                    'product' => $productType->getProduct()
                ]);

                if ($bucket !== null) {
                    $bucket->addBuyer($user);
                    $user->addBoughtBucket($bucket);
                }

                $paymentRow->setStatus('success');
                $paymentRow->setPaymentSet($paymentSet);
                $em->persist($paymentRow);

                $paymentSet->setAmount($amount);

                $em->persist($paymentSet);
                $em->flush();

                $this->addFlash('success', $user->getFullName().' get this product');
            }

            return $this->redirect($this->generateUrl('admin_product_type_assign_to', array('id' => $id)));
        }

        return $this->redirect($this->generateUrl('admin_products'));
    }
}
