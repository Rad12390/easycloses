<?php

namespace LocalsBest\ShopBundle\Controller;

use LocalsBest\ShopBundle\Entity\Tag;
use LocalsBest\ShopBundle\Form\TagType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TagController extends Controller
{
    /**
     * @Template
     * @return array
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $tags = $em->getRepository('LocalsBestShopBundle:Tag')->findAll();

        return [
            'entities' => $tags,
        ];
    }

    /**
     * Creates a new Tag entity.
     *
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    public function createAction(Request $request)
    {
        $entity = new Tag();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('tags_show', array('id' => $entity->getId())));
        }

        return $this->render('@LocalsBestShop/tag/new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Tag entity.
     *
     * @param Tag $entity The entity
     *
     * @return Form The form
     */
    private function createCreateForm(Tag $entity)
    {
        $form = $this->createForm(TagType::class, $entity, array(
            'action' => $this->generateUrl('tags_create'),
            'method' => 'POST',
        ));

        $form->add('submit', Type\SubmitType::class, array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Tag entity.
     *
     * @return Response
     */
    public function newAction()
    {
        $entity = new Tag();
        $form = $this->createCreateForm($entity);

        return $this->render('@LocalsBestShop/tag/new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Tag entity.
     *
     * @param $id
     *
     * @return Response
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LocalsBestShopBundle:Tag')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Tag entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('@LocalsBestShop/tag/show.html.twig', array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Tag entity.
     *
     * @param $id
     *
     * @return Response
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LocalsBestShopBundle:Tag')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Tag entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('@LocalsBestShop/tag/edit.html.twig', array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a Tag entity.
     *
     * @param Tag $entity The entity
     *
     * @return Form The form
     */
    private function createEditForm(Tag $entity)
    {
        $form = $this->createForm(TagType::class, $entity, array(
            'action' => $this->generateUrl('tags_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', Type\SubmitType::class, array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing Tag entity.
     *
     * @param Request $request
     * @param $id
     *
     * @return RedirectResponse|Response
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LocalsBestShopBundle:Tag')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Tag entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('tags_show', array('id' => $id)));
        }

        return $this->render('@LocalsBestShop/tag/edit.html.twig', array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Tag entity.
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
            $entity = $em->getRepository('LocalsBestShopBundle:Tag')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Tag entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('tags'));
    }

    /**
     * Creates a form to delete a Tag entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
                ->setAction($this->generateUrl('tags_delete', array('id' => $id)))
                ->setMethod('DELETE')
                ->add('submit', Type\SubmitType::class, array('label' => 'Delete'))
                ->getForm()
        ;
    }

    /* This function is to add tags from packages */

    public function createTagsPackageAction(Request $request)
    {
        $params = $request->request->all();
        $tag_name = $params['tag'];
        $entity = new Tag();
        $entity->setName($tag_name);
        $em = $this->getDoctrine()->getManager();
        $em->persist($entity);
        $em->flush();
        $id = $entity->getId();
        $result['success'] = 1;
        $result['id'] = $id;

        return new Response(json_encode($result));
    }
}
