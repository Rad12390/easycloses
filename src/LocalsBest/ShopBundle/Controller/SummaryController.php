<?php

namespace LocalsBest\ShopBundle\Controller;

use LocalsBest\CommonBundle\Controller\SuperController as Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SummaryController extends Controller
{
    /**
     * payments list for admin
     *
     * @return Response
     */
    public function summaryAction(Request $request)
    {
        $summary = $request->request->get('summary');
        if ($summary == 'manager') {
            return $this->redirectToRoute('shop_summary_manager', $request->request->all());
        } elseif ($summary == 'employee') {
            return $this->redirectToRoute('shop_summary_employee', $request->request->all());
        } else {
            return $this->redirectToRoute('shop_summary_charity', $request->request->all());
        }
    }

    public function managerAction(Request $request)
    {
        $parms['to'] = date($request->query->get('to'));
        $parms['from'] = ($request->query->get('from'));

        $business = $this->getBusiness();
        $orders = $this->getDoctrine()->getRepository('LocalsBestShopBundle:UserOrder')
            ->findPayoutSummary($business, $parms);

        return $this->render('@LocalsBestShop/summary/manager.html.twig', [
            'orders' => $orders,
        ]);
    }

    public function charityAction(Request $request)
    {
        $parms['to'] = date($request->query->get('to'));
        $parms['from'] = $request->query->get('from');
        $business = $this->getBusiness();
        $orders = $this->getDoctrine()->getRepository('LocalsBestShopBundle:UserOrder')
            ->findCharitySummary($business, $parms);
        return $this->render('@LocalsBestShop/summary/charity.html.twig', [
            'orders' => $orders,
        ]);
    }

    public function employeeAction(Request $request)
    {
        $parms['to'] = date($request->query->get('to'));
        $parms['from'] = $request->query->get('from');
        $business = $this->getBusiness();
        $orders = $this->getDoctrine()->getRepository('LocalsBestShopBundle:UserOrder')
            ->findEmployeeSummary($business, $parms);
        return $this->render('@LocalsBestShop/summary/employee.html.twig', [
            'orders' => $orders,
        ]);
    }
}
