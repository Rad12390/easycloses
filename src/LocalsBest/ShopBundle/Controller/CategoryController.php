<?php

namespace LocalsBest\ShopBundle\Controller;

use LocalsBest\CommonBundle\Controller\SuperController as Controller;
use LocalsBest\ShopBundle\Entity\Category;
use LocalsBest\ShopBundle\Entity\Sku;
use LocalsBest\ShopBundle\Form\CategoryType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CategoryController extends Controller
{
    /**
     * Lists all Category entities.
     *
     * @return Response
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('LocalsBestShopBundle:Category')->findAll();

        return $this->render('@LocalsBestShop/category/index.html.twig', array(
            'entities' => $entities,
        ));
    }

    /**
     * Creates a new Category entity.
     *
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    public function createAction(Request $request)
    {
        $entity = new Category();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('categories_show', array('id' => $entity->getId())));
        }

        return $this->render('@LocalsBestShop/category/new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Category entity.
     *
     * @param Category $entity The entity
     *
     * @return Form The form
     */
    private function createCreateForm(Category $entity)
    {
        $form = $this->createForm(CategoryType::class, $entity, array(
            'action' => $this->generateUrl('categories_create'),
            'method' => 'POST',
        ));

        $form->add('submit', Type\SubmitType::class, array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Category entity.
     *
     * @return Response
     */
    public function newAction()
    {
        $entity = new Category();
        $form   = $this->createCreateForm($entity);

        return $this->render('@LocalsBestShop/category/new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Category entity.
     *
     * @param $id
     *
     * @return Response
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('LocalsBestShopBundle:Category')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Category entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('@LocalsBestShop/category/show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Category entity.
     *
     * @param $id
     *
     * @return Response
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('LocalsBestShopBundle:Category')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Category entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('@LocalsBestShop/category/edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Category entity.
    *
    * @param Category $entity The entity
    *
    * @return Form The form
    */
    private function createEditForm(Category $entity)
    {
        $form = $this->createForm(CategoryType::class, $entity, array(
            'action' => $this->generateUrl('categories_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', Type\SubmitType::class, array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing Category entity.
     *
     * @param Request $request
     * @param $id
     *
     * @return RedirectResponse|Response
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('LocalsBestShopBundle:Category')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Category entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('categories_edit', array('id' => $id)));
        }

        return $this->render('@LocalsBestShop/category/edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Category entity.
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
            $entity = $em->getRepository('LocalsBestShopBundle:Category')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Category entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('categories'));
    }

    /**
     * Creates a form to delete a Category entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('categories_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', Type\SubmitType::class, array('label' => 'Delete'))
            ->getForm()
        ;
    }

    /**
     * Generate Block with Categories for shop
     *
     * @param string $url
     * @param int $selected
     *
     * @return string
     */
    public function listAction($url, $selected)
    {
        $em = $this->getDoctrine()->getManager();
        $categories = $em->getRepository('LocalsBestShopBundle:Category')->findBy([], ['title' => 'ASC']);
        $array_child=[];
        foreach($categories as $child){
            foreach($child->getChildren() as $childern){
               array_push($array_child,$childern); 
            }
        }

        return $this->render('@LocalsBestShop/category/list.html.twig', [
            'categories' => $categories,
            'url' => $url,
            'selected' => $selected,
            'array_child' => $array_child
        ]);
    }

    public function indTypesListAction($url, $selected)
    {
        $skus = $this->getDoctrine()->getRepository('LocalsBestShopBundle:Sku')->findAllBy(false, [
            'business'  => $this->getBusiness(),
            'user' => $this->getUser(),
        ]);

        $indTypes = [];
        /** @var Sku $sku */
        foreach ($skus as $sku) {
            if ($sku->getPackage() !== null && $sku->getPackage()->getType() == 5) {
                $indTypes[] = $sku->getPackage()->getIndustryType();
            }
        }

        return $this->render('@LocalsBestShop/category/ind-types-list.html.twig', [
            'indTypes' => $indTypes,
            'url' => $url,
            'selected' => $selected,
        ]);
    }
}
