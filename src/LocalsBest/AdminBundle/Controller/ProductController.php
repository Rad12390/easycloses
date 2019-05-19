<?php

namespace LocalsBest\AdminBundle\Controller;

use LocalsBest\AdminBundle\Form\ProductType;
use LocalsBest\UserBundle\Entity\Product;
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
 * @Route("/products")
 */
class ProductController extends Controller
{

    /**
     * Lists all Product entities.
     *
     * @Route("/", name="admin_products")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        // Get Product entities
        $entities = $em->getRepository('LocalsBestUserBundle:Product')->findBy([], ['title' => 'ASC']);
        // Get Product Types for Shop
        $forShop = $em->getRepository('LocalsBestUserBundle:Product')->forShop($this->getUser());
        // Render View
        return array(
            'entities' => $entities,
            'forShop' => $forShop,
        );
    }

    /**
     * Creates a new Product entity.
     *
     * @Route("/", name="admin_products_create")
     * @Method("POST")
     * @Template("@LocalsBestAdmin/product/new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Product();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $entity->setCreatedBy($this->getUser());
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_products_edit', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Product entity.
     *
     * @param Product $entity The entity
     *
     * @return Form The form
     */
    private function createCreateForm(Product $entity)
    {
        $form = $this->createForm(ProductType::class, $entity, array(
            'action' => $this->generateUrl('admin_products_create'),
            'method' => 'POST',
            'user' => $this->getUser(),
        ));

        $form->add('submit', Type\SubmitType::class, array('label' => 'Create', 'attr' => ['class' => 'btn btn-success']));

        return $form;
    }

    /**
     * Displays a form to create a new Product entity.
     *
     * @Route("/new", name="admin_products_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Product();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Product entity.
     *
     * @Route("/{id}", name="admin_products_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LocalsBestUserBundle:Product')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Product entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $tags = $em->getRepository('LocalsBestUserBundle:Tag')->getProductTags($id);

        return array(
            'entity'      => $entity,
            'tags'        => $tags,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Product entity.
     *
     * @Route("/{id}/edit", name="admin_products_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LocalsBestUserBundle:Product')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Product entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        $tags = $em->getRepository('LocalsBestUserBundle:Tag')->getProductTags($id);

        return array(
            'entity'      => $entity,
            'tags'        => implode(',', $tags),
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a Product entity.
    *
    * @param Product $entity The entity
    *
    * @return Form The form
    */
    private function createEditForm(Product $entity)
    {
        $form = $this->createForm(ProductType::class, $entity, array(
            'action' => $this->generateUrl('admin_products_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'user' => $this->getUser(),
        ));

        $form->add('submit', Type\SubmitType::class, array('label' => 'Update', 'attr' => ['class' => 'btn btn-warning']));

        return $form;
    }

    /**
     * Edits an existing Product entity.
     *
     * @Route("/{id}", name="admin_products_update")
     * @Method("PUT")
     * @Template("@LocalsBestAdmin/product/edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LocalsBestUserBundle:Product')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Product entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);
        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirectToRoute('admin_products');;
        }

        $tags = $em->getRepository('LocalsBestUserBundle:Tag')->getProductTags($id);

        return array(
            'entity'      => $entity,
            'tags'        => implode(',', $tags),
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Product entity.
     *
     * @Route("/{id}", name="admin_products_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        if ($request->isMethod('DELETE')) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('LocalsBestUserBundle:Product')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Product entity.');
            }

            if (count($entity->getUsers()) > 0) {
                $this->addFlash('danger', 'You can not delete this product. This product attached to users.');
            } elseif (count($entity->getTypes()) > 0) {
                $this->addFlash('danger', 'This item must be deleted in the product table before it can be deleted.');
            } else {
                $em->remove($entity);
                $em->flush();
            }
        }

        return $this->redirect($this->generateUrl('admin_products'));
    }

    /**
     * Creates a form to delete a Product entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_products_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', Type\SubmitType::class, array('label' => 'Delete product for shop', 'attr' => ['class' => 'btn btn-danger']))
            ->getForm()
        ;
    }
}
