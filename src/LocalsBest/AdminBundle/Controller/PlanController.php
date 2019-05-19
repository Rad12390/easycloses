<?php

namespace LocalsBest\AdminBundle\Controller;

use LocalsBest\AdminBundle\Form\PlanType;
use LocalsBest\UserBundle\Entity\Business;
use LocalsBest\UserBundle\Entity\IndustryType;
use LocalsBest\UserBundle\Entity\Plan;
use LocalsBest\UserBundle\Entity\PlanRow;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Plan controller.
 *
 * @Route("/plans")
 */
class PlanController extends Controller
{
    /**
     * Lists all Plan entities.
     *
     * @return array
     *
     * @Route("/", name="admin_plans")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        if(!$this->getUser()->isAdmin()) {
            return $this->redirectToRoute('locals_best_user_homepage');
        }

        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('LocalsBestUserBundle:Plan')->findBy(['parent' => null]);

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Creates a new Plan entity.
     *
     * @param Request $request
     *
     * @return array|RedirectResponse
     *
     * @Route("/", name="admin_plans_create")
     * @Method("POST")
     * @Template("@LocalsBestAdmin/plan/new.html.twig")
     */
    public function createAction(Request $request)
    {
        if(!$this->getUser()->isAdmin()) {
            return $this->redirectToRoute('locals_best_user_homepage');
        }

        $entity = new Plan();

        foreach([1, 2, 3, 4, 5, 6] as $iGroup) {
            $row = new PlanRow();
            $row->setIndustryGroup($iGroup);
            $entity->getRows()->add($row);
        }

        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();

        if ($form->isValid()) {
            $em->persist($entity);
            /** @var PlanRow $rowForm */
            foreach($entity->getRows() as $rowForm) {
                $rowForm->setPlan($entity);
                $em->persist($rowForm);
            }

            $em->flush();

            $industryGroups = $request->request->get('industry_groups');
            foreach ($industryGroups as $key => $value) {
                /** @var IndustryType $indGroup */
                $indType = $em->getRepository('LocalsBestUserBundle:IndustryType')->find($key);
                /** @var PlanRow $indGroup */
                $indGroup = $em->getRepository('LocalsBestUserBundle:PlanRow')
                    ->findOneBy(['industry_group' => $value, 'plan' => $entity]);

                $indGroup->addIndustryType($indType);
            }
            $em->flush();

            return $this->redirectToRoute('admin_plans_show', array('id' => $entity->getId()));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'industryTypes' => $em->getRepository('LocalsBestUserBundle:IndustryType')->findBy([], ['name' => 'ASC']),
        );
    }

    /**
     * Creates a form to create a Plan entity.
     *
     * @param Plan $entity The entity
     *
     * @return Form The form
     */
    private function createCreateForm(Plan $entity)
    {
        $form = $this->createForm(PlanType::class, $entity, array(
            'action' => $this->generateUrl('admin_plans_create'),
            'method' => 'POST',
        ));

        $form->add('submit', Type\SubmitType::class, array('label' => 'Create', 'attr' => ['class' => 'btn btn-success']));

        return $form;
    }

    /**
     * Displays a form to create a new Plan entity.
     *
     * @return array
     *
     * @Route("/new", name="admin_plans_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        if(!$this->getUser()->isAdmin()) {
            return $this->redirectToRoute('locals_best_user_homepage');
        }

        $entity = new Plan();

        foreach([1, 2, 3, 4, 5, 6] as $iGroup) {
            $row = new PlanRow();
            $row->setIndustryGroup($iGroup);
            $entity->getRows()->add($row);
        }

        $em = $this->getDoctrine()->getManager();

        $form = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'industryTypes' => $em->getRepository('LocalsBestUserBundle:IndustryType')->findBy([], ['name' => 'ASC']),
        );
    }

    /**
     * Finds and displays a Plan entity.
     *
     * @param int $id
     *
     * @return array
     *
     * @Route("/{id}", name="admin_plans_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        if(!$this->getUser()->isAdmin()) {
            return $this->redirectToRoute('locals_best_user_homepage');
        }

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LocalsBestUserBundle:Plan')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Plan entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Plan entity.
     *
     * @param int $id
     *
     * @return array
     *
     * @Route("/{id}/edit", name="admin_plans_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        if(!$this->getUser()->isAdmin()) {
            return $this->redirectToRoute('locals_best_user_homepage');
        }

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LocalsBestUserBundle:Plan')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Plan entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'industryTypes' => $em->getRepository('LocalsBestUserBundle:IndustryType')->findBy([], ['name' => 'ASC']),
        );
    }

    /**
    * Creates a form to edit a Plan entity.
    *
    * @param Plan $entity The entity
    *
    * @return Form The form
    */
    private function createEditForm(Plan $entity)
    {
        $form = $this->createForm(PlanType::class, $entity, array(
            'action' => $this->generateUrl('admin_plans_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', Type\SubmitType::class, array('label' => 'Update', 'attr' => ['class' => 'btn btn-warning']));

        return $form;
    }

    /**
     * Edits an existing Plan entity.
     *
     * @param Request $request
     * @param int $id
     *
     * @return array|RedirectResponse
     *
     * @Route("/{id}", name="admin_plans_update")
     * @Method("PUT")
     * @Template("@LocalsBestUser/plan/edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        if(!$this->getUser()->isAdmin()) {
            return $this->redirectToRoute('locals_best_user_homepage');
        }

        $em = $this->getDoctrine()->getManager();

        /** @var Plan $entity */
        $entity = $em->getRepository('LocalsBestUserBundle:Plan')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Plan entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            /** @var PlanRow $row */
            foreach($entity->getRows() as $row) {
                /** @var IndustryType $type */
                foreach($row->getIndustryType() as $type) {
                    $row->removeIndustryType($type);
                }
            }

            $industryGroups = $request->request->get('industry_groups');
            foreach ($industryGroups as $key => $value) {
                /** @var IndustryType $indGroup */
                $indType = $em->getRepository('LocalsBestUserBundle:IndustryType')->find($key);
                /** @var PlanRow $indGroup */
                $indGroup = $em->getRepository('LocalsBestUserBundle:PlanRow')
                    ->findOneBy(['industry_group' => $value, 'plan' => $entity]);

                $indGroup->addIndustryType($indType);
            }
            $em->flush();

            /** @var Plan $plan */
            foreach($entity->getChildren() as $plan) {
                if(!$plan->isDefaultPrices()) {
                    continue;
                }

                /** @var PlanRow $industryGroup */
                foreach($plan->getRows() as $industryGroup) {
                    $parentIndGroup = $em->getRepository("LocalsBestUserBundle:PlanRow")
                        ->findOneBy(['plan' => $entity, 'industry_group' => $industryGroup->getIndustryGroup()]);

                    $industryGroup->setGoldPrice($parentIndGroup->getGoldPrice());
                    $industryGroup->setSetupGoldPrice($parentIndGroup->getSetupGoldPrice());
                    $industryGroup->setSilverPrice($parentIndGroup->getSilverPrice());
                    $industryGroup->setSetupSilverPrice($parentIndGroup->getSetupSilverPrice());
                    $industryGroup->setBronzePrice($parentIndGroup->getBronzePrice());
                    $industryGroup->setSetupBronzePrice($parentIndGroup->getSetupBronzePrice());
                }
            }
            $em->flush();

            return $this->redirectToRoute('admin_plans_edit', array('id' => $id));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'industryTypes' => $em->getRepository('LocalsBestUserBundle:IndustryType')->findBy([], ['name' => 'ASC']),
        );
    }

    /**
     * Deletes a Plan entity.
     *
     * @param Request $request
     * @param int $id
     *
     * @return RedirectResponse
     *
     * @Route("/{id}", name="admin_plans_delete")
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
            $entity = $em->getRepository('LocalsBestUserBundle:Plan')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Plan entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirectToRoute('admin_plans');
    }

    /**
     * Creates a form to delete a Plan entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_plans_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', Type\SubmitType::class, array('label' => 'Delete', 'attr' => ['class' => 'btn btn-danger']))
            ->getForm()
        ;
    }

    /**
     * Customize Industry Groups.
     *
     * @param Business $business
     * @param Request $request
     *
     * @return RedirectResponse|Response
     *
     * @Route("/customize-industry-groups/{id}", name="admin_customize_industry_groups")
     * @Method("GET|POST")
     * @ParamConverter("business", class="LocalsBestUserBundle:Business")
     */
    public function customizeIndustryGroupsAction(Business $business, Request $request)
    {
        if(!$this->getUser()->isAdmin()) {
            return $this->redirectToRoute('locals_best_user_homepage');
        }

        $em = $this->getDoctrine()->getManager();

        if ($request->isMethod("POST")) {
            /** @var PlanRow $row */
            foreach($business->getPlan()->getRows() as $row) {
                /** @var IndustryType $type */
                foreach($row->getIndustryType() as $type) {
                    $row->removeIndustryType($type);
                }
            }

            $industryGroups = $request->request->get('industry_groups');
            foreach ($industryGroups as $key => $value) {
                /** @var IndustryType $indGroup */
                $indType = $em->getRepository('LocalsBestUserBundle:IndustryType')->find($key);
                /** @var PlanRow $indGroup */
                $indGroup = $em->getRepository('LocalsBestUserBundle:PlanRow')
                    ->findOneBy(['industry_group' => $value, 'plan' => $business->getPlan()]);

                $indGroup->addIndustryType($indType);
            }
            $business->getPlan()->setIsDefaultIndustryGroups(false);
            $em->flush();
            return $this->redirectToRoute('admin_businesses');
        }

        return $this->render(
            '@LocalsBestAdmin/plan/customize-industry-groups.html.twig',
            [
                'entity' => $business,
                'industryTypes' => $em->getRepository('LocalsBestUserBundle:IndustryType')->findBy([], ['name'=>'ASC']),
            ]
        );
    }
}
