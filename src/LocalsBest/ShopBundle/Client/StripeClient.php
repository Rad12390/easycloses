<?php

namespace LocalsBest\ShopBundle\Client;

use Doctrine\ORM\EntityManagerInterface;
use LocalsBest\ShopBundle\Entity\Sku;
use LocalsBest\ShopBundle\Entity\StripeCustomer;
use LocalsBest\UserBundle\Entity\User;
use Symfony\Component\DependencyInjection\Container;
use Psr\Log\LoggerInterface;
use Stripe\Charge;
use Stripe\Account;
use Stripe\Customer;
use Stripe\Transfer;
use Stripe\Token;
use Stripe\Subscription;
use Stripe\Refund;
use Stripe\Product;
use Stripe\Plan;
use Stripe\Error\Base as Base;
use Stripe\Stripe;

class StripeClient
{
    private $container;
    private $config;
    private $em;
    private $logger;

    public function __construct(Container $container, array $config, EntityManagerInterface $em, LoggerInterface $logger)
    {
        $this->container = $container;
        $this->config = $config;
        $this->em = $em;
        $this->logger = $logger;

        Stripe::setApiKey($this->container->getParameter('stripe_secret_key'));
    }

    /**
     * @param User $user
     * @param $token
     * @param $products
     *
     * @return \stdClass charge
     *
     * @throws \Exception
     */
    public function createPremiumCharge(User $user, $token, $products)
    {
        try {
            $charge = new \stdClass();
            $subPlans = [];
            $oneTimePayments = [];
            $charge->connsimplecustomer = [];
            $charge->connsubcustomer = [];
            
            foreach ($products as $item) {
                if ($item['type'] == "onetime") {
                    $oneTimePayments[] = $item;
                } elseif ($item['type'] == "subscription") {
                    $subPlans[] = $item;
                }
            }
            
            $customerExist = $this->em->getRepository('LocalsBestShopBundle:StripeCustomer')->getLBCustomer([
                'user' => $user,
            ]);

            if ($customerExist === null) {
                $customer = Customer::create([
                    'email' => $user->getPrimaryEmail()->getEmail(),
                    'source' => $token,
                ]);
                $this->saveStripeCustomer($customer, $user);
            } else {
                $customer = Customer::retrieve($customerExist->getStripeAccountId());
                $customer->source = $token; // obtained with Checkout
                $customer->save();
            }
           
            if (!empty($oneTimePayments)) {
                foreach ($oneTimePayments as $loop => $payment) {
                    
                    $vendor = $payment['vendor'];
                  //  if ($vendor->getId() != 1) {
                        /** @var Sku $sku */
                        $sku = $payment['sku'];
                        $fee_percentage = $sku->getMarkup(); // For Now Application Fee is Static Here. Need to be added by Admin in Vendors Accounts
                        $application_fee = ($payment['retailprice'] - $payment['price'])*100;
                        $wholesalerCustomer = $this->em->getRepository('LocalsBestShopBundle:StripeCustomer')->findOneBy([
                            'user' => $user,
                            'wholesaler' => $vendor,
                        ]);

                        if ($wholesalerCustomer === null) {
                            $newToken = Token::create(
                                ["customer" => $customer->id],
                                ["stripe_account" => $payment['account_id']]
                            );

                            $vendorCustomer = Customer::create(
                                ["email" => strip_tags(trim($customer->email)), 'source' => $newToken],
                                ["stripe_account" => $payment['account_id']]
                            );
                            $this->saveStripeCustomer($vendorCustomer, $user, $vendor);
                        } else {
                            $vendorCustomer = Customer::retrieve($wholesalerCustomer->getStripeAccountId(), ["stripe_account" => $payment['account_id']]);
                        }

                        $charge->simplepayment[] = $this
                            ->createdChargeForAccount($vendorCustomer, $payment, $customer, $application_fee);
//                    } else {
//                        $charge->simplepayment[] = $this->createdCharge($customer, $payment, $customer);
//                    }
                    $charge->simplepayment[$loop]->product_id = $payment['product_id'];
                }
            }

            // Subscribe Customer With Plans
            if (!empty($subPlans)) {
                foreach ($subPlans as $loop => $plan) {
                    $vendor = $plan['vendor'];

//                    if ($vendor->getId() != 1) {
                        $sku = $plan['sku'];
                        //$fee_percentage = $sku->getMarkup();
                        $fee_percentage = ceil($plan['price']);
                        $application_fee = ($plan['retailprice'] - $plan['price'])*100;
                        $wholesalerCustomer = $this->em->getRepository('LocalsBestShopBundle:StripeCustomer')->findOneBy([
                            'user' => $user,
                            'wholesaler' => $vendor,
                        ]);

                        if ($wholesalerCustomer === null) {
                            $newToken = Token::create(
                                ["customer" => $customer->id],
                                ["stripe_account" => $plan['account_id']]
                            );

                            $vendorCustomer = Customer::create(
                                ["email" => strip_tags(trim($customer->email)), 'source' => $newToken],
                                ["stripe_account" => $plan['account_id']]
                            );
                            $this->saveStripeCustomer($vendorCustomer, $user, $vendor);
                        } else {
                            $vendorCustomer = Customer::retrieve($wholesalerCustomer->getStripeAccountId(), ["stripe_account" => $plan['account_id']]);
                        }

                        $charge->subscription[] = Subscription::create([
                            'customer' => $vendorCustomer->id,
                            'plan' => $plan['stripeplanid'],
                            'application_fee_percent' => $plan['rebate'], // Subscription Application fee is in Percentage, Static Now
                        ],
                            ['stripe_account' => $plan['account_id']]);
//                    } else {
//                        $charge->subscription[] = Subscription::create([
//                            'customer' => $customer->id,
//                            'plan' => $plan['stripeplanid'],
//                        ]);
//                    }
                    $charge->subscription[$loop]->product_id = $plan['product_id'];
                }
            }

            return $charge;
        } catch (Base $e) {
            $this->logger->error(
                sprintf(
                    '%s exception encountered when creating a premium payment: "%s"',
                    get_class($e),
                    $e->getMessage()
                ),
                ['exception' => $e]
            );

            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Create Stripe Transfer (Payment Split)
     *
     * @param string $stripeAccount
     * @param $transferGroup
     * @param int|float $amount
     *
     * @return Transfer
     */
    private function createTransfer($stripeAccount, $transferGroup, $amount)
    {
        $transfer = Transfer::create(array(
            "amount" => $this->config['decimal'] ? $amount * 100 : $amount,
            "currency" => $this->config['currency'],
            "destination" => $stripeAccount,
            "transfer_group" => $transferGroup,
        ));

        return $transfer;
    }

    /**
     * Refund
     *
     * @param $chargeId
     * @param $connect_account
     * @param string $amount
     * @param array $groupUsers
     *
     * @return Refund
     * @throws Base
     */
    public function refundPremiumCharge($chargeId, $connect_account, $amount = '', $groupUsers = '')
    {
        try {
            if (!empty($groupUsers)) {
                foreach ($groupUsers as $account) {
                    $transfer = Transfer::retrieve($account->getTransferid());
                    $transfer->reversals->create();
                }
            }

            if(!empty($amount)){
                $refund = Refund::create(
                    [
                        'charge' => $chargeId,
                        "amount" => $amount,
                        "refund_application_fee" => true,           // Application Fee true to refund the Application fee. Depends on amount
                    ],
                    ["stripe_account" => $connect_account]
                );
            }else{
                $refund = Refund::create(
                    [
                        "charge" => $chargeId,
                        "refund_application_fee" => true,          // Application Fee true to refund the Application fee also
                    ],
                    ["stripe_account" => $connect_account]
                );
            }
            return $refund;
        } catch (Base $e) {
            $this->logger->error(
                sprintf(
                    '%s exception encountered when creating a premium payment: "%s"',
                    get_class($e),
                    $e->getMessage()
                ),
                ['exception' => $e]
            );
            throw $e;
        }
    }

    /**
     * Cancel
     *
     * @param $subscription_id
     * @param $Connectaccount
     *
     * @return Subscription
     *
     * @throws Base
     */
    public function CancelSubscription( $subscription_id, $Connectaccount){
        try {
            $sub  = Subscription::retrieve($subscription_id,["stripe_account" => $Connectaccount]);
            $sub->cancel();

            return $sub;
        } catch (Base $e) {
            $this->logger->error(
                sprintf(
                    '%s exception encountered when Cancelling Subscription: "%s"',
                    get_class($e),
                    $e->getMessage()
                ),
                ['exception' => $e]
            );
            throw $e;
        }
    }

    /**
     * @param $accountId
     *
     * @return bool|mixed
     */
    public function getUserStripCreds($accountId)
    {
        try {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_URL => "https://connect.stripe.com/oauth/token",
                CURLOPT_POST => 1,
                CURLOPT_HTTPHEADER => array(
                    "Authorization: Bearer " . $this->container->getParameter('stripe_secret_key')
                ),
                CURLOPT_POSTFIELDS => http_build_query(array(
                    "code" => $accountId,
                    "grant_type" => 'authorization_code'
                ))
            ));
            $resp = curl_exec($curl);
            $output = json_decode($resp);
            if (isset($output->stripe_user_id)) {
                $userAccount = Account::retrieve($output->stripe_user_id);
                $output->accountdata = $userAccount;
            }
            curl_close($curl);

            return $output;
        } catch (\OAuthException $e) {
            echo "Response: " . $e->lastResponse . "\n";

            return false;
        }
    }

    /**
     * @param $accountId
     *
     * @return Account
     * @throws Base
     */
    public function getStripeUserAccountData($accountId)
    {
        try {
            $userAccount = Account::retrieve($accountId);

            return $userAccount;
        } catch (Base $e) {
            $this->logger->error(
                sprintf('%s exception encountered : "%s"', get_class($e), $e->getMessage()),
                ['exception' => $e]
            );
            throw $e;
        }
    }

    /**
     *
     *
     * @param $account_id
     *
     * @return bool|mixed
     */
    public function deAuthorizeUser($account_id)
    {
        try {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_URL => "https://connect.stripe.com/oauth/deauthorize",
                CURLOPT_POST => 1,
                CURLOPT_HTTPHEADER => array(
                    "Authorization: Bearer " . $this->container->getParameter('stripe_secret_key')
                ),
                CURLOPT_POSTFIELDS => http_build_query(array(
                    'client_id' => $this->container->getParameter('stripe_client_id'),
                    'stripe_user_id' => $account_id,
                ))
            ));
            $resp = curl_exec($curl);
            $output = json_decode($resp);
            curl_close($curl);

            return $output;
        } catch (\OAuthException $e) {
            echo "Response: " . $e->lastResponse . "\n";

            return false;
        }
    }

    /**
     * @param $userEmail
     * @param $businessName
     * @return null|Account
     * @throws Base
     */
    public function createStandardAccount($userEmail, $businessName)
    {
        try {
            if (!empty($userEmail)) {
                $userAccount = Account::create(array(
                    "type" => "standard",
                    "country" => "US",
                    "email" => $userEmail,
                    "business_name" => $businessName
                ));
                return $userAccount;
            }
            return null;
        } catch (Base $e) {
            $this->logger->error(
                sprintf(
                    '%s exception encountered when creating a Standard Account: "%s"',
                    get_class($e),
                    $e->getMessage()
                ),
                ['exception' => $e]
            );
            throw $e;
        }
    }

    /**
     * @param $creds
     *
     * @return Plan
     * @throws Base
     */
    public function createSubscriptionPlan($creds){
        try {
            $wholesaler = $creds['wholesaler'];
            if ($wholesaler->getRole()->getLevel() == 1) {
                $product = Product::create(
                    [
                        'name' => $creds['name'],
                        'type' => 'service',
                    ]
                );

                $plan = Plan::create(
                    [
                        "amount" => $creds['amount'] * 100,
                        "interval" => $creds['interval'],
                       // "interval_count" => $creds['interval_count'],
                        "product" => $product->id,
                        "currency" => $this->config['currency'],
                    ]
                );
            } else {
                $product = Product::create(
                    [
                        'name' => $creds['name'],
                        'type' => 'service',
                    ],
                    ['stripe_account' => $creds['stripe_account']]
                );

                $plan = Plan::create(
                    [
                        "amount" => $creds['amount'] * 100,
                        "interval" => $creds['interval'],
                      //  "interval_count" => $creds['interval_count'],
                        "product" => $product->id,
                        "currency" => $this->config['currency'],
                    ],
                    ['stripe_account' => $creds['stripe_account']]
                );
            }

            return $plan;
        } catch (Base $e) {
            $this->logger->error(
                sprintf('%s exception encountered : "%s"', get_class($e), $e->getMessage()),
                ['exception' => $e]
            );

            throw $e;
        }
    }

    public function createdCharge($accountCustomer, $payment, $customer)
    {
        return Charge::create(
            [
                'currency' => $this->config['currency'],
                //'source' => $token,
                'customer' => $accountCustomer->id,
                'amount' => $payment['price'] ? $payment['price'] * 100 : $payment['price'],
                'receipt_email' => $customer->email,
            ]
        );
    }

    public function createdChargeForAccount($accountCustomer, $payment, $customer, $applicationFee)
    {
        return Charge::create(
            [
                'currency' => $this->config['currency'],
                //'source' => $token,
                'customer' => $accountCustomer->id,
                'amount' => $payment['retailprice'] ? $payment['retailprice'] * 100 : $payment['retailprice'],
                'receipt_email' => $customer->email,
                "application_fee" => $applicationFee,              // Fee is Integer Value in Cents
            ],
            ["stripe_account" => $payment['account_id']]
        );
    }

    public function saveStripeCustomer($stripeCustomer, User $user, $vendor=null)
    {
        $customer = new StripeCustomer();

        $customer->setUser($user);
        $customer->setWholesaler($vendor);
        $customer->setStripeAccountId($stripeCustomer->id);
        $customer->setCustomerEmail($stripeCustomer->email);
        $customer->setCustomerName($user->getFullName());

        $this->em->persist($customer);
        $this->em->flush();

        return $customer;
    }

    /**
     * @param $stripeSubscriptionId
     *
     * @return \Stripe\Subscription
     */
    public function findSubscription($stripeSubscriptionId)
    {
        return \Stripe\Subscription::retrieve($stripeSubscriptionId);
    }
}