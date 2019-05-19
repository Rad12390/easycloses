<?php

namespace LocalsBest\ShopBundle\Controller;

use LocalsBest\CommonBundle\Controller\SuperController as Controller;
use Symfony\Component\HttpFoundation\Request;
use Stripe\Stripe;
use Stripe\Event;

class StripeController extends Controller
{
    /**
     * @param Request $request
     *
     * @throws \Exception
     */
    public function webhookAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $logger = $this->get('logger');

        // Retrieve the request's body and parse it as JSON
        $data = json_decode($request->getContent(), true);
        if ($data === null) {
            throw new \Exception('Bad JSON body from Stripe!');
        }

        $logger->info('Received event with ID: ' . $data->id . ' Type: ' . $data->type);

        Stripe::setApiKey($this->getParameter('stripe_secret_key'));
        # Retrieving the event from the Stripe API guarantees its authenticity
        $stripeEvent = Event::retrieve($data['id']);

        switch ($stripeEvent->type) {
            case 'invoice.payment_succeeded':
                $stripeSubscriptionId = $stripeEvent->data->object->subscription;
                if ($stripeSubscriptionId) {
                    $orderItem = $this->findSubscription($stripeSubscriptionId);
                    $stripeSubscription = $this->get('app.client.stripe')->findSubscription($stripeSubscriptionId);

                    $newPeriodEnd = \DateTime::createFromFormat('U', $stripeSubscription->current_period_end);
                    $orderItem->setSubscriptionEndedAt($newPeriodEnd);
                    $orderItem->setStatus($stripeSubscription->status);
                    $orderItem->setSubscriptionstatus(1);

                    $em->flush();
                }
                break;
            case 'invoice.payment_failed':
                $stripeSubscriptionId = $stripeEvent->data->object->subscription;
                if ($stripeSubscriptionId) {
                    $orderItem = $this->findSubscription($stripeSubscriptionId);
                    $stripeSubscription = $this->get('app.client.stripe')->findSubscription($stripeSubscriptionId);
                    if ($stripeEvent->data->object->attempt_count == 1) {
                        $orderItem->setStatus('failed');
                        $orderItem->setSubscriptionstatus(0);

                        $em->flush();
                    }
                }

                $stripeCustomerToken = $stripeEvent->data->object->customer;
                $customer = $em->getRepository('LocalsBestShopBundle:StripeCustomer')->findOneBy([
                    'stripeAccountId' => $stripeCustomerToken,
                ]);
                $user = $customer->getUser();

                // Send email

                break;
            default:
                // allow this - we'll have Stripe send us everything
                // throw new \Exception('Unexpected webhook type form Stripe! '.$stripeEvent->type);
                $logger->info('Webhook received params.inspect. Did not handle this event.');
        }

        http_response_code(200);
    }

    /**
     * @param $stripeSubscriptionId
     *
     * @return \LocalsBest\ShopBundle\Entity\OrderItem|null|object
     *
     * @throws \Exception
     */
    private function findSubscription($stripeSubscriptionId)
    {
        $subscription = $this->getDoctrine()
            ->getRepository('LocalsBestShopBundle:OrderItem')
            ->findOneBy([
                'txnid' => $stripeSubscriptionId
            ]);
        if (!$subscription) {
            throw new \Exception('Somehow we have no subscription id ' . $stripeSubscriptionId);
        }
        return $subscription;
    }
}