<?php

namespace LocalsBest\UserBundle\Controller;

use DateTime;
use LocalsBest\CommonBundle\Controller\SuperController as Controller;
use LocalsBest\UserBundle\Dbal\Types\InviteStatusType;
use LocalsBest\UserBundle\Entity\Business;
use LocalsBest\UserBundle\Entity\IndustryType;
use LocalsBest\UserBundle\Entity\Invite;
use LocalsBest\UserBundle\Entity\PaidInvite;
use LocalsBest\UserBundle\Entity\Payment;
use net\authorize\api\constants\ANetEnvironment;
use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;
use Swift_Message;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PaymentController extends Controller
{
    /**
     * Not used action
     *
     * @param $token
     *
     * @return array
     */
    public function formAction($token)
    {
        $em = $this->getDoctrine()->getManager();
        $pInvite = $em->getRepository('LocalsBestUserBundle:PaidInvite')->findOneBy(['token' => $token]);
        return array();
    }

    /**
     * Display checkout page with ordered items
     *
     * @param Request $request
     *
     * @return Response
     */
    public function checkoutAction(Request $request)
    {
        $token = $request->query->get('token', null);
        $invite = null;

        $em = $this->getDoctrine()->getManager();

        // If token set try to Get PaidInvite
        if($token !== null) {
            $pInvite = $em->getRepository('LocalsBestUserBundle:PaidInvite')->findOneBy(['token' => $token]);

            if($pInvite !== null) {
                $invite = $pInvite;
            }
        }
        // Get Price level from Request (Free, Bronze, Silver, Gold, Platinum)
        $priceLevel = ucfirst($request->request->get('priceLevel'));
        // Get ID of Business Attached to
        $businessId = $request->request->get('businessId');
        // Get ID of selected Industry Type
        $industryTypeId = $request->request->get('industryTypeId');
        // Get ID of Industry Group
        $industryGroupId = $request->request->get('industryGroupId');
        // Get Industry Type Entity
        $industryType = $em->getRepository('LocalsBestUserBundle:IndustryType')->find($industryTypeId);
        // Get Industry Group Entity
        $industryGroup = $em->getRepository('LocalsBestUserBundle:PlanRow')->find($industryGroupId);
        // Get Business Entity
        $business = $em->getRepository('LocalsBestUserBundle:Business')->find($businessId);
        // Get Price
        $price = $industryGroup->{'get' . $priceLevel . 'Price'}();
        // Get Setup Price
        $setupPrice = $industryGroup->{'getSetup' . $priceLevel . 'Price'}();

        // Render View with params
        return $this->render(
            '@LocalsBestUser/payment/checkout.html.twig',
            [
                'business'      => $business,
                'price'         => $price,
                'setupPrice'    => $setupPrice,
                'industryType'  => $industryType,
                'industryGroup' => $industryGroup,
                'priceLevel'    => $priceLevel,
                'invite'        => $invite,
                'token'         => $token,
            ]
        );
    }

    /**
     * Charge users Credit Card
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function chargeAction(Request $request)
    {
        if ($request->isMethod('POST')) {
            $em = $this->getDoctrine()->getManager();

            // Get Request data
            $data = $request->request->all();
            // Get token from request
            $token = $request->query->get('token', null);
            $referral = null;

            if($token !== null) {
                // Get Paid or Free Invite by token
                /** @var \LocalsBest\USerBundle\Entity\PaidInvite $invt */
                $invt = $em->getRepository('LocalsBestUserBundle:PaidInvite')->findOneBy(['token' => $token]);

                if($invt === null) {
                    $invt = $em->getRepository('LocalsBestUserBundle:Invite')->findOneBy(['token' => $token]);
                }

                if($invt !== null) {
                    $referral = $invt->getCreatedBy();
                }
            }

            // Get Credit Card information
            $cardNumber = $data['credit_card_number'];
            $cvv2 = $data['cvv2'];
            $expirationDate = $data['expiration_year'] . '-' . $data['expiration_month'];

            // Get Amount for Payment
            $amount = str_replace(['$'], '', $data['charge_total']);

            $industryGroupId = $data['industry_group'];
            // Get Industry Group Entity
            $industryGroup = $em->getRepository('LocalsBestUserBundle:PlanRow')->find($industryGroupId);
            // Get Industry Type Entity
            /** @var IndustryType $industryType */
            $industryType = $em->getRepository('LocalsBestUserBundle:IndustryType')->find($data['industry_type']);
            $priceLevel = ucfirst($data['price_level']);
            // Get Prices
            $price = $industryGroup->{'get' . $priceLevel . 'Price'}();
            $setupPrice = $industryGroup->{'getSetup' . $priceLevel . 'Price'}();

            $userEmail = $data['user_email'];
            // Get Business Entity
            /** @var Business $business */
            $business = $em->getRepository('LocalsBestUserBundle:Business')->find($data['business_network']);
            // Save in file Payment request and response
            define("AUTHORIZENET_LOG_FILE", '../app/logs/payment_copy.log');

            // Authorize Functionality
            // Common setup for API credentials
            $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
            $merchantAuthentication->setName($this->getParameter('auth_login_id'));
            $merchantAuthentication->setTransactionKey($this->getParameter('auth_transaction_key'));
            $refId = 'ref' . time();

            // Create one time Payment

            // Create the payment data for a credit card
            $creditCard = new AnetAPI\CreditCardType();
            $creditCard->setCardNumber($cardNumber);
            $creditCard->setExpirationDate($expirationDate);
            $creditCard->setCardCode($cvv2);

            $paymentOne = new AnetAPI\PaymentType();
            $paymentOne->setCreditCard($creditCard);

            $order = new AnetAPI\OrderType();
            $order->setDescription(
                $business->getName() . '; ' .
                $industryType->getName() . '; ' .
                $priceLevel . '; '
            );

            $billToSingle = new AnetAPI\CustomerAddressType();
            $billToSingle->setFirstName($data['first_name']);
            $billToSingle->setLastName($data['last_name']);

            $customer = new AnetAPI\CustomerDataType();
            $customer->setEmail($userEmail);

            //create a transaction
            $transactionRequestType = new AnetAPI\TransactionRequestType();
            $transactionRequestType->setTransactionType("authCaptureTransaction");
            $transactionRequestType->setAmount($setupPrice + $price);
            $transactionRequestType->setOrder($order);
            $transactionRequestType->setCustomer($customer);
            $transactionRequestType->setPayment($paymentOne);
            $transactionRequestType->setBillTo($billToSingle);

            $requestP = new AnetAPI\CreateTransactionRequest();
            $requestP->setMerchantAuthentication($merchantAuthentication);
            $requestP->setRefId( $refId);
            $requestP->setTransactionRequest( $transactionRequestType);
            $controller = new AnetController\CreateTransactionController($requestP);
            $response = $controller->executeWithApiResponse( ANetEnvironment::SANDBOX);

            if ($response != null) {
                if ($response->getMessages()->getResultCode() == 'Ok') {
                    $tresponse = $response->getTransactionResponse();

                    if ($tresponse != null && $tresponse->getMessages() != null) {
                        $pInvite = null;

                        if ($token !== null) {
                            /** @var PaidInvite $pInvite */
                            $pInvite = $em->getRepository('LocalsBestUserBundle:PaidInvite')->findOneBy(['token' => $token]);
                        }

                        file_put_contents('../app/logs/payment.log', serialize($response) . PHP_EOL, FILE_APPEND);
                        $payment = new Payment();
                        $payment->setAmount(number_format($amount, 2));
                        $payment->setEmail($userEmail);
                        $payment->setPriceLevel($priceLevel);
                        $payment->setFirstName($data['first_name']);
                        $payment->setLastName($data['last_name']);
                        $payment->setStatus('Paid');
                        $payment->setBusinessType($data['industry_type']);
                        $payment->setOathSubscriptionId('n/a');
                        $payment->setCreatedAt(new DateTime('now'));

                        $time = time();
                        if ($priceLevel == 'Gold') {
                            $priceLevelIndex = 3;
                        } elseif ($priceLevel == 'Silver') {
                            $priceLevelIndex = 2;
                        } elseif ($priceLevel == 'Bronze') {
                            $priceLevelIndex = 1;
                        } else {
                            $priceLevelIndex = 0;
                        }
                        $randNum = json_encode(array('category' => $priceLevelIndex, 'businessType' => $data['industry_type']));
                        $token = base64_encode($time . ':' . $randNum);
                        $payment->setToken($token);

                        $em->persist($payment);
                        $em->flush();

                        if ($referral !== null) {

                            // Get information who send email
                            $fromAddress = $this->container->getParameter('mailer_from_address');
                            $fromName = $this->container->getParameter('mailer_from_name');

                            // Create email entity
                            $message = (new Swift_Message('Referral Fee'))
                                ->setFrom($fromAddress, $fromName)
                                ->setTo('accounting@easycloses.com')
                                ->setBody(
                                    $this->renderView(
                                        '@LocalsBestNotification/mails/about-referral.html.twig',
                                        [
                                            'userFullName' => $data['first_name'] . ' ' . $data['last_name'],
                                            'indType' => $industryType->getName(),
                                            'priceLevel' => $priceLevel,
                                            'amount' => $amount,
                                            'referral' => $referral,
                                        ]
                                    ),
                                    'text/html'
                                );

                            // Send Email
                            $this->get('mailer')->send($message);
                        }

                        $invite = new Invite();

                        $invite->setToken($token)
                            ->setEmail($userEmail)
                            ->setRole($em->getRepository('LocalsBestUserBundle:Role')->find(8))
                            ->setCreatedBy($business->getOwner())
                            ->setStatus(InviteStatusType::INVITE)
                            ->setBusiness($business);

                        if ($pInvite !== null) {
                            $pInvite->setPayment($payment);
                            $em->flush();

                            return $this->redirectToRoute('register', ['token' => $pInvite->getToken()]);
                        }

                        $em->persist($invite);
                        $em->flush();

                        $message = (new Swift_Message('Membership Notifications'))
                            ->setFrom($this->container->getParameter('mailer_from_address'), $this->container->getParameter('mailer_from_name'))
                            ->setTo($userEmail)
                            ->setBody(
                                $this->renderView('@LocalsBestNotification/mails/join.html.twig',
                                    ['invite' => $invite]
                                ), 'text/html');

                        $this->container->get('mailer')->send($message);

                        $this->addFlash('success', 'Please check your email box for mail with registration link.');

                        // Subscription Type Info
                        $subscription = new AnetAPI\ARBSubscriptionType();
                        $subscription->setName('EasyCloses ARB - ' . date("m/d/Y H:i", time()));

                        $interval = new AnetAPI\PaymentScheduleType\IntervalAType();
                        $interval->setLength(6);
                        $interval->setUnit("months");

                        $paymentSchedule = new AnetAPI\PaymentScheduleType();
                        $paymentSchedule->setInterval($interval);
                        $paymentSchedule->setStartDate(new DateTime('+6 months'));
                        $paymentSchedule->setTotalOccurrences("9999");
                        $paymentSchedule->setTrialOccurrences("0");

                        $order = new AnetAPI\OrderType();

                        $order->setDescription(
                            $business->getName() . '; ' .
                            $industryType->getName() . '; ' .
                            $priceLevel . '; '
                        );

                        $customer = new AnetAPI\CustomerType();
                        $customer->setEmail($userEmail);


                        $subscription->setPaymentSchedule($paymentSchedule);
                        $subscription->setAmount($price);
                        $subscription->setTrialAmount(0);
                        $subscription->setCustomer($customer);

                        $paymentAuth = new AnetAPI\PaymentType();
                        $paymentAuth->setCreditCard($creditCard);

                        $subscription->setPayment($paymentAuth);

                        $billTo = new AnetAPI\NameAndAddressType();
                        $billTo->setFirstName($data['first_name']);
                        $billTo->setLastName($data['last_name']);

                        $subscription->setBillTo($billTo);
                        $subscription->setOrder($order);

                        $request = new AnetAPI\ARBCreateSubscriptionRequest();
                        $request->setMerchantAuthentication($merchantAuthentication);
                        $request->setRefId($refId);
                        $request->setSubscription($subscription);
                        $controller = new AnetController\ARBCreateSubscriptionController($request);

                        $response = $controller->executeWithApiResponse(ANetEnvironment::SANDBOX);
                        file_put_contents('../app/logs/payment.log', serialize($response) . PHP_EOL, FILE_APPEND);

                        if (($response != null) && ($response->getMessages()->getResultCode() == "Ok")) {
                            $payment->setOathSubscriptionId($response->getSubscriptionId());
                            $em->flush();
                        } else {
                            $errorMessages = $response->getMessages()->getMessage();
                            $this->getMailMan()->paymentErrorMail(
                                [
                                    'user' => $data['first_name'] . ' ' . $data['last_name'],
                                    'item' => 'Join Page',
                                    'code' => $errorMessages[0]->getCode(),
                                    'message' => $errorMessages[0]->getText(),
                                ],
                                'assistant@mylocalsbest.com'
                            );
                        }

                        return $this->redirectToRoute('register', ['token' => $token]);

                    } else {
                        if($tresponse->getErrors() != null) {
                            $this->getMailMan()->paymentErrorMail(
                                [
                                    'user' => $data['first_name'] . ' ' . $data['last_name'],
                                    'item' => 'Join Page',
                                    'code' => $tresponse->getErrors()[0]->getErrorCode(),
                                    'message' => $tresponse->getErrors()[0]->getErrorText(),
                                ],
                                'assistant@mylocalsbest.com'
                            );
                            $this->addFlash('danger', 'There was problem with payments server. Please contact with support service.');
                            return $this->redirectToRoute('join');
                        } else {
                            $this->getMailMan()->paymentErrorMail(
                                [
                                    'user' => $data['first_name'] . ' ' . $data['last_name'],
                                    'item' => 'Join Page',
                                    'code' => 'n/a',
                                    'message' => 'Undefined error',
                                ],
                                'assistant@mylocalsbest.com'
                            );
                            $this->addFlash('danger', "There was problem with payments server. Please contact with support service.");
                            return $this->redirectToRoute('join');
                        }
                    }
                } else {
                    $tresponse = $response->getTransactionResponse();

                    if($tresponse != null && $tresponse->getErrors() != null) {
                        $this->getMailMan()->paymentErrorMail(
                            [
                                'user' => $data['first_name'] . ' ' . $data['last_name'],
                                'item' => 'Join Page',
                                'code' => $tresponse->getErrors()[0]->getErrorCode(),
                                'message' => $tresponse->getErrors()[0]->getErrorText(),
                            ],
                            'assistant@mylocalsbest.com'
                        );
                        $this->addFlash('danger', 'There was problem with payments server. Please contact with support service.');
                        return $this->redirectToRoute('join');
                    } else {
                        $this->getMailMan()->paymentErrorMail(
                            [
                                'user' => $data['first_name'] . ' ' . $data['last_name'],
                                'item' => 'Join Page',
                                'code' => $response->getMessages()->getMessage()[0]->getCode(),
                                'message' => $response->getMessages()->getMessage()[0]->getText(),
                            ],
                            'assistant@mylocalsbest.com'
                        );
                        $this->addFlash('danger', 'There was problem with payments server. Please contact with support service.');
                        return $this->redirectToRoute('join');
                    }
                }
            } else {
                $this->getMailMan()->paymentErrorMail(
                    [
                        'user' => $data['first_name'] . ' ' . $data['last_name'],
                        'item' => 'Join Page',
                        'code' => 'n/a',
                        'message' => 'Charge Credit card Null response returned',
                    ],
                    'assistant@mylocalsbest.com'
                );
                $this->addFlash('danger', "There was problem with payments server. Please contact with support service.");
                // Redirect user
                return $this->redirectToRoute('join');
            }
        } else {
            // No action in this case
            die('Wrong method!');
        }
    }
}
