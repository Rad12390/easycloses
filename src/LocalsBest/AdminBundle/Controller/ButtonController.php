<?php

namespace LocalsBest\AdminBundle\Controller;

use LocalsBest\UserBundle\Entity\Buttons;
use LocalsBest\UserBundle\Entity\Tooltip;
use LocalsBest\UserBundle\Form\ButtonsType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Button;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Tooltip controller.
 *
 * @Route("/buttons")
 */
class ButtonController extends Controller
{
    /**
     * Lists all Buttons entities.
     *
     * @Route("/", name="admin_buttons")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        if ($this->getUser()->getRole()->getLevel()!=1) {
	    throw $this->createNotFoundException('Unable to find page.');
	}
        $entities = $this->getDoctrine()->getRepository('LocalsBestUserBundle:Buttons')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new Button entity.
     *
     * @Route("/", name="admin_buttons_create")
     * @Method("POST")
     * @Template("@LocalsBestAdmin/button/new.html.twig")
     */
    public function createAction(Request $request)
    {
        if ($this->getUser()->getRole()->getLevel()!=1) {
	    throw $this->createNotFoundException('Unable to find page.');
	}
        $entity = new Buttons();
        $buttons = $this->getDoctrine()->getRepository('LocalsBestUserBundle:Buttons')->findAll();
        $form = $this->createCreateForm($entity, $buttons);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_buttons_show', array('id' => $entity->getId())));
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
    private function createCreateForm(Buttons $entity, $buttons)
    {
        $form = $this->createForm(ButtonsType::class, $entity, array(
            'action' => $this->generateUrl('admin_buttons_create'),
            'method' => 'POST',
            'buttons' => $buttons,
        ));

        $form->add('submit', Type\SubmitType::class, array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Button entity.
     *
     * @Route("/new", name="admin_buttons_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        if ($this->getUser()->getRole()->getLevel()!=1) {
	    throw $this->createNotFoundException('Unable to find page.');
	}
        $entity = new Buttons();
        $buttons = $this->getDoctrine()->getRepository('LocalsBestUserBundle:Buttons')->findAll();
        $form   = $this->createCreateForm($entity, $buttons);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Button entity.
     *
     * @Route("/{id}", name="admin_buttons_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        if ($this->getUser()->getRole()->getLevel()!=1) {
	    throw $this->createNotFoundException('Unable to find page.');
	}
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LocalsBestUserBundle:Buttons')->find($id);

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
     * Displays a form to edit an existing Button entity.
     *
     * @Route("/{id}/edit", name="admin_buttons_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        if ($this->getUser()->getRole()->getLevel()!=1) {
	    throw $this->createNotFoundException('Unable to find page.');
	}
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LocalsBestUserBundle:Buttons')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Tooltip entity.');
        }

        $buttons = $em->getRepository('LocalsBestUserBundle:Buttons')->findAll();

        $editForm = $this->createEditForm($entity, $buttons);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Creates a form to edit a Button entity.
     *
     * @param Button $entity The entity
     *
     * @return Form The form
     */
    private function createEditForm(Buttons $entity, $buttons)
    {
        $form = $this->createForm(ButtonsType::class, $entity, array(
            'action' => $this->generateUrl('admin_buttons_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'buttons' => $buttons,
            'currentValue' => $entity->getName(),
        ));

        $form->add('submit', Type\SubmitType::class, array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Button entity.
     *
     * @Route("/{id}", name="admin_buttons_update")
     * @Method("PUT")
     * @Template("@LocalsBestAdmin/button/edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LocalsBestUserBundle:Buttons')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Button entity.');
        }

        $buttons = $em->getRepository('LocalsBestUserBundle:Buttons')->findAll();

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity, $buttons);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('admin_buttons_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Button entity.
     *
     * @Route("/{id}", name="admin_buttons_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('LocalsBestUserBundle:Buttons')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Button entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('admin_buttons'));
    }

    /**
     * Creates a form to delete a Button entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_buttons_delete', array('id' => $id)))
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


/**
     * Edits an existing Button entity.
     *
     * @Route("/updatecount", name="update_button_count")
     * @Method("POST") 
     */
    public function updateCountAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $params = $request->request->all();
        $id= $params['id'];
        $entity = $em->getRepository('LocalsBestUserBundle:Buttons')->find($id);
        $count= $entity->getClicked();
        $count= $count+1;
        $entity->setClicked($count);
        $em->persist($entity);
        $em->flush();
        $result['success']=1;
        return new Response(json_encode($result));
    }
}
