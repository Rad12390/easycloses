<?php

namespace LocalsBest\UserBundle\Controller;

use LocalsBest\CommonBundle\Controller\SuperController;
use LocalsBest\UserBundle\Entity\DocRule;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class DocumentRuleController extends SuperController
{
    /**
     * Create Document Rule
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function createAction(Request $request)
    {
        $data = $request->request->all();

        $rule = new DocRule();
        $rule->setCreating($data['creating'])
            ->setRepresent($data['represent'])
            ->setStatus($data['status'])
            ->setTransactionType($data['transactionType'])
            ->setPropertyType($data['propertyType'])
            ->setYearBuiltAfter($data['yearBuiltAfter'])
            ->setYearBuiltBefore($data['yearBuiltBefore'])
            ->setDocumentName($data['documentName'])
            ->setBusiness($this->getBusiness());

        $em = $this->getDoctrine()->getManager();

        $em->persist($rule);
        $em->flush();

        return new JsonResponse(['id' => $rule->getId()]);
    }

    /**
     * Update Document Rule
     *
     * @param Request $request
     * @param $id
     *
     * @return JsonResponse
     */
    public function updateAction(Request $request, $id)
    {
        $data = $request->request->all();

        $em = $this->getDoctrine()->getManager();

        $rule = $em->getRepository('LocalsBestUserBundle:DocRule')->find($id);
        $rule->setCreating($data['creating'])
            ->setRepresent($data['represent'])
            ->setStatus($data['status'])
            ->setTransactionType($data['transactionType'])
            ->setPropertyType($data['propertyType'])
            ->setYearBuiltAfter($data['yearBuiltAfter'])
            ->setYearBuiltBefore($data['yearBuiltBefore'])
            ->setDocumentName($data['documentName']);

        $em->flush();

        return new JsonResponse(['id' => $rule->getId()]);
    }

    /**
     * Delete Document Rule
     *
     * @param $id
     *
     * @return JsonResponse
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $rule = $em->getRepository('LocalsBestUserBundle:DocRule')->find($id);

        $em->remove($rule);
        $em->flush();

        return new JsonResponse(['result' => 'success']);
    }
}
