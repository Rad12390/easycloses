<?php

namespace LocalsBest\AdminBundle\Controller;

use Doctrine\ORM\EntityRepository;
use LocalsBest\AdminBundle\Form\BusinessType;
use LocalsBest\UserBundle\Entity\Business;
use LocalsBest\UserBundle\Entity\Plan;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Business controller.
 *
 * @Route("/businesses")
 */
class BusinessController extends Controller
{
    /**
     * Lists all Businesses entities.
     *
     * @return array
     *
     * @Route("/", name="admin_businesses")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        if(!$this->getUser()->isAdmin()) {
            return $this->redirectToRoute('locals_best_user_homepage');
        }

        $em = $this->getDoctrine();
        $entities = $em->getRepository('LocalsBestUserBundle:Business')->createQueryBuilder('b')
            ->addSelect('CASE WHEN (b.plan IS NULL) THEN 0 ELSE 1 END as p_e')
            ->orderBy('p_e', 'desc')
            ->addOrderBy('b.name', 'asc')
            ->getQuery()
            ->getResult();

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Change Category.
     *
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @Route("/ajax-change-category", name="admin_ajax_change_category")
     * @Method("POST")
     */
    public function changeCategoryAction(Request $request)
    {
        if(!$this->getUser()->isAdmin()) {
            return $this->redirectToRoute('locals_best_user_homepage');
        }

        $data = $request->request->all();

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('LocalsBestUserBundle:Business')->find($data['business']);
        $user = $entity->getOwner();

        if($entity === null || $user === null) {
            return new JsonResponse(
                ['result' => 0, 'message' => 'There was some problems on server', 'color' => 'ruby']
            );
        }

        $user->setVendorCategory($data['category']);
        $em->flush();

        return new JsonResponse(['result' => 1, 'message' => 'Category was changed successfully', 'color' => 'lime']);
    }

    /**
     * Displays a form to create a new Business entity.
     *
     * @return array
     *
     * @Route("/new", name="admin_businesses_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        if(!$this->getUser()->isAdmin()) {
            return $this->redirectToRoute('locals_best_user_homepage');
        }

        $entity = new Business();

        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a view page for existing Business entity.
     *
     * @param int $id
     *
     * @return array
     *
     * @Route("/{id}", name="admin_businesses_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        if(!$this->getUser()->isAdmin()) {
            return $this->redirectToRoute('locals_best_user_homepage');
        }

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('LocalsBestUserBundle:Business')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Business entity.');
        }

        return array(
            'businessCategories'    => [0 => 'Free', 1 => 'Bronze', 2 => 'Silver', 3 => 'Gold', 4 => 'Platinum'],
            'entity'                => $entity,
        );
    }

    /**
     * Creates a new Business entity.
     *
     * @param Request $request
     *
     * @return array|RedirectResponse
     *
     * @Route("/", name="admin_businesses_create")
     * @Method("POST")
     * @Template("@LocalsBestAdmin/business/new.html.twig")
     */
    public function createAction(Request $request)
    {
        if(!$this->getUser()->isAdmin()) {
            return $this->redirectToRoute('locals_best_user_homepage');
        }

        $entity = new Business();

        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            if (!$entity->getStaffs()->contains($entity->getOwner())) {
                $entity->addStaff($entity->getOwner());
                $entity->getOwner()->addBusiness($entity);
            }
            
            //set default plan to business
            $get_plan = $em->getRepository("LocalsBestUserBundle:Plan")->find(12);
            $plan = clone $get_plan;
            //add new plan
            $plan->setParent($get_plan);
            $em->persist($plan);
            
            $entity->setPlan($plan);
                
            $em->persist($entity);
            $em->flush();

            return $this->redirectToRoute('admin_businesses', array('id' => $entity->getId()));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Business entity.
     *
     * @param Business $entity The entity
     * @return Form The form
     */
    private function createCreateForm(Business $entity)
    {
        $form = $this->createForm(BusinessType::class, $entity, array(
            'action' => $this->generateUrl('admin_businesses_create'),
            'method' => 'POST',
        ));

        $form->add('submit', Type\SubmitType::class, array('label' => 'Create', 'attr' => ['class' => 'btn btn-success']));

        return $form;
    }

    /**
     * Change Plan for Business entity.
     *
     * @param Business $business
     * @param Request $request
     *
     * @return array|RedirectResponse
     *
     * @Route("/change-plan/{id}", name="admin_businesses_change_plan")
     * @Method("GET|POST")
     * @ParamConverter("business", class="LocalsBestUserBundle:Business")
     * @Template()
     */
    public function changePlanAction(Business $business, Request $request)
    {
       
        if(!$this->getUser()->isAdmin()) {
            return $this->redirectToRoute('locals_best_user_homepage');
        }

        $form = $this->createChangePlanForm($business);

        if ($request->isMethod("POST")) {
            $em = $this->getDoctrine()->getManager();

            $oldPlan = $business->getPlan();

            $formData = $request->request->get('form');
            
            $plan = $em->getRepository("LocalsBestUserBundle:Plan")->find($formData['plan']);

            $businessPlan = clone $plan;
            $businessPlan->setParent($plan);
            $em->persist($businessPlan);

            $business->setPlan($businessPlan);
            $em->flush();

            if($oldPlan !== null) {
                $em->remove($oldPlan);
            }
            $em->flush();
            
            return $this->redirectToRoute('admin_businesses');
        }

        return $this->render('@LocalsBestAdmin/business/changePlan.html.twig', array(
            'entity' => $business,
            'form'   => $form->createView()
            )
        ); 
    }

    /**
     * Creates a form to change Plan for Business entity.
     *
     * @param Business $entity The entity
     * @return Form The form
     */
    private function createChangePlanForm(Business $entity)
    {
        $form = $this->createFormBuilder($entity)
            ->add('plan', EntityType::class, [
                'label' => 'All Plans',
                'class' => Plan::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('p')
                        ->where('p.parent IS NULL')
                        ->orderBy('p.name', 'ASC');
                },
                'choice_label' => 'name',
                'required'=> true,
                'placeholder' => '-Select Plan-',
                'empty_data' => null,
                'multiple' => false,
            ])
            ->add('change', Type\SubmitType::class, ['label' => 'Change'])
            ->getForm();

        return $form;
    }

    /**
     * Displays a form to edit an existing Business entity.
     *
     * @param int $id
     *
     * @return array
     *
     * @Route("/{id}/edit", name="admin_businesses_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        if(!$this->getUser()->isAdmin()) {
            return $this->redirectToRoute('locals_best_user_homepage');
        }

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LocalsBestUserBundle:Business')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Business entity.');
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
     * Creates a form to edit a Business entity.
     *
     * @param Business $entity The entity
     *
     * @return Form The form
     */
    private function createEditForm(Business $entity)
    {
        $form = $this->createForm(BusinessType::class, $entity, array(
            'action' => $this->generateUrl('admin_businesses_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', Type\SubmitType::class, array('label' => 'Update', 'attr' => ['class' => 'btn btn-warning']));

        return $form;
    }

    /**
     * Edits an existing Business entity.
     *
     * @param Request $request
     * @param int $id
     *
     * @return array|RedirectResponse
     *
     * @Route("/{id}", name="admin_businesses_update")
     * @Method("PUT")
     * @Template("@LocalsBestAdmin/business/edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        if(!$this->getUser()->isAdmin()) {
            return $this->redirectToRoute('locals_best_user_homepage');
        }

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LocalsBestUserBundle:Business')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Business entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirectToRoute('admin_businesses_edit', array('id' => $id));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Business entity.
     *
     * @param Request $request
     * @param int $id
     *
     * @return RedirectResponse
     *
     * @Route("/{id}", name="admin_businesses_delete")
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
            $entity = $em->getRepository('LocalsBestUserBundle:Business')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Business entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirectToRoute('admin_businesses');
    }

    /**
     * Creates a form to delete a Business entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_businesses_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', Type\SubmitType::class, array('label' => 'Delete', 'attr' => ['class' => 'btn btn-danger']))
            ->getForm()
            ;
    }

    /**
     * Display a Business Packages and Addons.
     *
     * @param int $id
     * @return Response
     *
     * @Route("/{id}/addons", name="admin_businesses_addons")
     * @Method("GET")
     */
    public function showAddonsAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $business = $em->getRepository('LocalsBestUserBundle:Business')->find($id);

        return $this->render('@LocalsBestAdmin/business/show-addons.html.twig', ['business' => $business]);
    }
}
