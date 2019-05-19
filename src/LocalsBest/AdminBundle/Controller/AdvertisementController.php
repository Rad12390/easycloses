<?php

namespace LocalsBest\AdminBundle\Controller;

use LocalsBest\AdminBundle\Form\AdvertisementType;
use LocalsBest\UserBundle\Entity\Advertisement;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

/**
 * Advertisement controller.
 *
 * @Route("/advertisement")
 */
class AdvertisementController extends Controller
{
    /**
     * Lists all Advertisement entities.
     *
     * @Route("/", name="admin_advertisement")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('LocalsBestUserBundle:Advertisement')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new Advertisement entity.
     *
     * @Route("/", name="admin_advertisement_create")
     * @Method("POST")
     * @Template("@LocalsBestUser/advertisement/new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Advertisement();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_advertisement_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Advertisement entity.
     *
     * @param Advertisement $entity The entity
     *
     * @return Form The form
     */
    private function createCreateForm(Advertisement $entity)
    {
        $form = $this->createForm(AdvertisementType::class, $entity, array(
            'action' => $this->generateUrl('admin_advertisement_create'),
            'method' => 'POST',
        ));

        $form->add('submit', Type\SubmitType::class, array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Advertisement entity.
     *
     * @Route("/new", name="admin_advertisement_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Advertisement();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Advertisement entity.
     *
     * @Route("/{id}", name="admin_advertisement_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LocalsBestUserBundle:Advertisement')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Advertisement entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Advertisement entity.
     *
     * @Route("/{id}/edit", name="admin_advertisement_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LocalsBestUserBundle:Advertisement')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Advertisement entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Creates a form to edit a Advertisement entity.
     *
     * @param Advertisement $entity The entity
     *
     * @return Form The form
     */
    private function createEditForm(Advertisement $entity)
    {
        $form = $this->createForm(AdvertisementType::class, $entity, array(
            'action' => $this->generateUrl('admin_advertisement_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', Type\SubmitType::class, array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Advertisement entity.
     *
     * @Route("/{id}", name="admin_advertisement_update")
     * @Method("PUT")
     * @Template("@LocalsBestUser/advertisement/edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LocalsBestUserBundle:Advertisement')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Advertisement entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('admin_advertisement_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Advertisement entity.
     *
     * @Route("/{id}", name="admin_advertisement_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('LocalsBestUserBundle:Advertisement')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Advertisement entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('admin_advertisement'));
    }

    /**
     * Creates a form to delete a Advertisement entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_advertisement_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', Type\SubmitType::class, array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
