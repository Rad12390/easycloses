<?php

namespace LocalsBest\AdminBundle\Controller;

use LocalsBest\AdminBundle\Form\IndustryTypeType;
use LocalsBest\UserBundle\Entity\IndustryType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * IndustryType controller.
 *
 * @Route("/industry-type")
 */
class IndustryTypeController extends Controller
{
    /**
     * Lists all IndustryType entities.
     *
     * @return array
     *
     * @Route("/", name="admin_industry_type")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        if(!$this->getUser()->isAdmin()) {
            return $this->redirectToRoute('locals_best_user_homepage');
        }

        $em = $this->getDoctrine()->getManager();

        // Get Industries Types in ABC order
        $entities = $em->getRepository('LocalsBestUserBundle:IndustryType')->findBy([], ['name' => 'asc']);

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new IndustryType entity.
     *
     * @param Request $request
     *
     * @return array
     *
     * @Route("/", name="admin_industry_type_create")
     * @Method("POST")
     * @Template("@LocalsBestAdmin/industry_type/new.html.twig")
     */
    public function createAction(Request $request)
    {
        if(!$this->getUser()->isAdmin()) {
            return $this->redirectToRoute('locals_best_user_homepage');
        }

        $entity = new IndustryType();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_industry_type_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a IndustryType entity.
     *
     * @param IndustryType $entity The entity
     *
     * @return Form The form
     */
    private function createCreateForm(IndustryType $entity)
    {
        $form = $this->createForm(IndustryTypeType::class, $entity, array(
            'action' => $this->generateUrl('admin_industry_type_create'),
            'method' => 'POST',
        ));

        $form->add('submit', Type\SubmitType::class, array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new IndustryType entity.
     *
     * @return array
     *
     * @Route("/new", name="admin_industry_type_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        if(!$this->getUser()->isAdmin()) {
            return $this->redirectToRoute('locals_best_user_homepage');
        }

        $entity = new IndustryType();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a IndustryType entity.
     *
     * @param int $id
     * @return array
     *
     * @Route("/{id}", name="admin_industry_type_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        if(!$this->getUser()->isAdmin()) {
            return $this->redirectToRoute('locals_best_user_homepage');
        }

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LocalsBestUserBundle:IndustryType')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find IndustryType entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing IndustryType entity.
     *
     * @param int $id
     *
     * @return array
     *
     * @Route("/{id}/edit", name="admin_industry_type_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        if(!$this->getUser()->isAdmin()) {
            return $this->redirectToRoute('locals_best_user_homepage');
        }

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LocalsBestUserBundle:IndustryType')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find IndustryType entity.');
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
    * Creates a form to edit a IndustryType entity.
    *
    * @param IndustryType $entity The entity
    *
    * @return Form The form
    */
    private function createEditForm(IndustryType $entity)
    {
        $form = $this->createForm(IndustryTypeType::class, $entity, array(
            'action' => $this->generateUrl('admin_industry_type_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', Type\SubmitType::class, array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing IndustryType entity.
     *
     * @param Request $request
     * @param int $id
     *
     * @return array|RedirectResponse
     *
     * @Route("/{id}", name="admin_industry_type_update")
     * @Method("PUT")
     * @Template("@LocalsBestAdmin/industry_type/edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        if(!$this->getUser()->isAdmin()) {
            return $this->redirectToRoute('locals_best_user_homepage');
        }

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LocalsBestUserBundle:IndustryType')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find IndustryType entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirectToRoute('admin_industry_type_edit', array('id' => $id));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a IndustryType entity.
     *
     * @param Request $request
     * @param int $id
     *
     * @return array|RedirectResponse
     *
     * @Route("/{id}", name="admin_industry_type_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        if(!$this->getUser()->isAdmin()) {
            return $this->redirectToRoute('locals_best_user_homepage');
        }

        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('LocalsBestUserBundle:IndustryType')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find IndustryType entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirectToRoute('industry_type');
    }

    /**
     * Creates a form to delete a IndustryType entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_industry_type_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', Type\SubmitType::class, array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
