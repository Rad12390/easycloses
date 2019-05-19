<?php

namespace LocalsBest\AdminBundle\Controller;

use LocalsBest\UserBundle\Entity\Tooltip;
use LocalsBest\UserBundle\Form\TooltipType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

/**
 * Tooltip controller.
 *
 * @Route("/tooltips")
 */
class TooltipController extends Controller
{
    /**
     * Lists all Tooltips entities.
     *
     * @Route("/", name="admin_tooltips")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $entities = $this->getDoctrine()->getRepository('LocalsBestUserBundle:Tooltip')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new Tooltip entity.
     *
     * @Route("/", name="admin_tooltips_create")
     * @Method("POST")
     * @Template("@LocalsBestAdmin/tooltip/new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Tooltip();
        $tooltips = $this->getDoctrine()->getRepository('LocalsBestUserBundle:Tooltip')->findAll();
        $form = $this->createCreateForm($entity, $tooltips);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $entity->setBody(str_replace(["\r\n", '"' ], ["&#10;", '\''], $entity->getBody()));

            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_tooltips_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Tooltip entity.
     *
     * @param Tooltip $entity The entity
     *
     * @return Form The form
     */
    private function createCreateForm(Tooltip $entity, $tooltips)
    {
        $form = $this->createForm(TooltipType::class, $entity, array(
            'action' => $this->generateUrl('admin_tooltips_create'),
            'method' => 'POST',
            'tooltips' => $tooltips,
        ));

        $form->add('submit', Type\SubmitType::class, array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Tooltip entity.
     *
     * @Route("/new", name="admin_tooltips_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Tooltip();
        $tooltips = $this->getDoctrine()->getRepository('LocalsBestUserBundle:Tooltip')->findAll();
        $form   = $this->createCreateForm($entity, $tooltips);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Tooltip entity.
     *
     * @Route("/{id}", name="admin_tooltips_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LocalsBestUserBundle:Tooltip')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Tooltip entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Tooltip entity.
     *
     * @Route("/{id}/edit", name="admin_tooltips_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LocalsBestUserBundle:Tooltip')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Tooltip entity.');
        }

        $tooltips = $em->getRepository('LocalsBestUserBundle:Tooltip')->findAll();

        $editForm = $this->createEditForm($entity, $tooltips);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Creates a form to edit a Tooltip entity.
     *
     * @param Tooltip $entity The entity
     *
     * @return Form The form
     */
    private function createEditForm(Tooltip $entity, $tooltips)
    {
        $form = $this->createForm(TooltipType::class, $entity, array(
            'action' => $this->generateUrl('admin_tooltips_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'tooltips' => $tooltips,
            'currentValue' => $entity->getName(),
        ));

        $form->add('submit', Type\SubmitType::class, array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Tooltip entity.
     *
     * @Route("/{id}", name="admin_tooltips_update")
     * @Method("PUT")
     * @Template("@LocalsBestAdmin/tooltip/edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LocalsBestUserBundle:Tooltip')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Tooltip entity.');
        }

        $tooltips = $em->getRepository('LocalsBestUserBundle:Tooltip')->findAll();

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity, $tooltips);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $entity->setBody(str_replace(["\r\n", '"' ], ["&#10;", '\''], $entity->getBody()));
            $em->flush();

            return $this->redirect($this->generateUrl('admin_tooltips_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Tooltip entity.
     *
     * @Route("/{id}", name="admin_tooltips_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('LocalsBestUserBundle:Tooltip')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Tooltip entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('admin_tooltips'));
    }

    /**
     * Creates a form to delete a Tooltip entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_tooltips_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', Type\SubmitType::class, array(
                'label' => 'Delete',
                'attr' => array(
                    'class' => 'btn btn-danger',
                ),
            ))
            ->getForm()
            ;
    }
}