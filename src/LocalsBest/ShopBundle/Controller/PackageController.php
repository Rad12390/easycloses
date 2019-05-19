<?php

namespace LocalsBest\ShopBundle\Controller;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use LocalsBest\CommonBundle\Controller\SuperController as Controller;
use LocalsBest\ShopBundle\Entity\Item;
use LocalsBest\ShopBundle\Entity\ItemSet;
use LocalsBest\ShopBundle\Entity\Package;
use LocalsBest\ShopBundle\Entity\Price;
use LocalsBest\ShopBundle\Entity\Sku;
use LocalsBest\ShopBundle\Entity\VendorChoice;
use LocalsBest\ShopBundle\Form\PackageType;
use LocalsBest\UserBundle\Dbal\Types\QuestionType;
use Stripe\Error\Base;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PackageController extends Controller
{

    private $message_403 = 'Sorry you are not authorized to use this feature. If you feel this is in error please open a support ticket';

    /**
     * Lists all Package entities.
     *
     * @return Response
     */
    public function indexAction()
    {
        $user = $this->getUser();

        if (!$this->get('localsbest.checker')->forAddon('packages', $user)) {
            $this->addFlash('danger', $this->message_403);
            return $this->redirectToRoute('locals_best_user_homepage');
        }

        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('LocalsBestShopBundle:Package')->findPackage($user);

        return $this->render('@LocalsBestShop/package/index.html.twig', array(
            'entities' => $entities,
        ));
    }

    /**
     * Creates a new Package entity.
     *
     * @param Request $request
     *
     * @return RedirectResponse|Response
     * @throws Base
     */
    public function createAction(Request $request)
    { // to check stripe account activated or not
        $data = $request->request->all();
        $user = $this->getUser();
        // check if stripe account is activated or not

        if ($user->getStripeAccountActivated() == false || $user->getStripeAccountActivated() === null) {
            $stripe_status = 0;
        } else {
            $stripe_status = 1;
        }
        $entity = new Package();
        $em = $this->getDoctrine()->getManager();

        $form = $this->createCreateForm($entity, $request);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            if (isset($data['quoted_user'])) {
                $user_quote = $em->getRepository('LocalsBestUserBundle:User')->find($data['quoted_user']);
                $entity->setAssignne($user_quote);
            }

            $entity->setCreatedBy($this->getUser());
            $entity->setStatus($this->packageStatus($form->getClickedButton()->getName()));

            $quantity = $entity->getQuantity();
            /** @var ItemSet $set */
            foreach ($entity->getSets() as $set) {
                $itemQuantity = 0;
                $item = $set->getItem();

                $itemSetQuantity = $set->getQuantity();
                if ($item) {
                    $itemQuantity = $item->getQuantity();
                    $item->setQuantity($itemQuantity - $quantity * $itemSetQuantity);
                }
            }

            $packageBundle = $request->request->get('localsbest_shopbundle_package');
            $planTitle = $packageBundle['title'];
            $planDesc = $packageBundle['shortDescription'];

            $productSku = $entity->getSku();
            $stripeAccountId = $entity->getSku()->getCreatedBy()->getStripeAccountId();
            foreach ($productSku->getPrices() as $priceMetric) {
                $retail = explode("$", $priceMetric->getretailPrice());
                $retail = $retail[1];
                if ($retail == NULL) {
                    $retail = 0;
                    $priceMetric->setAmount(0);
                    $priceMetric->setRebate(0);
                }
                $priceMetric->setretailPrice($retail);
                if ($priceMetric->getType() == "subscription") {
                    $planCreds = array(
                        'wholesaler' => $entity->getSku()->getCreatedBy(),
                        'name' => $planTitle,
                        'amount' => $priceMetric->getretailPrice(),
                        'description' => $planDesc,
                        'interval' => $priceMetric->getSubscriptionType(),
                        'stripe_account' => $stripeAccountId != '' ? $stripeAccountId : $this->container->getParameter('stripe_client_id')
                    );


                    $result = $this->get('app.client.stripe')->createSubscriptionPlan($planCreds);

                    if ($result->id) {
                        $priceMetric->setStripeplanid($result->id);
                        $priceMetric->setStripeproductid($result->product);
                    }
                }
            }
            // to upload image manually  on s3 server
            $manual_upload = false;
            if (!$entity->getFile()) {
                $manual_upload = true;
                $now = new DateTime();
                $package_image = explode("/", $form['imagename']->getdata());
                $packages_image = $now->getTimestamp() . '_' . $package_image[2];

                $this->imageUploader($entity, $packages_image, $form['imagename']->getdata());
            }
            $dir_to_save = getcwd() . '/uploads/package/';

            $em->persist($entity);
            $em->flush();

            if ($manual_upload) {
                if (!unlink($dir_to_save . $packages_image)) {
                    return true;
                }
            }

            //add vendor choices ask/require table
            if (isset($data['localsbest_shopbundle_package']['question']) && !empty($data['localsbest_shopbundle_package']['question'])) {
                // add new dependencies for vendor choices
                $array = ['1', '2', '3', '4', '5'];
                foreach ($array as $array1) {
                    $vendor_choice = new VendorChoice();
                    $vendor_choice->setpackageId($entity);
                    foreach ($data['localsbest_shopbundle_package']['question'] as $key => $value) {
                        if (!array_key_exists($array1, $data['localsbest_shopbundle_package']['question'])) {
                            $vendor_choice->setQuestionId($array1);
                            $vendor_choice->setRequire(0);
                            $vendor_choice->setAsk(0);
                        } else {
                            if ($key == $array1) {
                                $vendor_choice->setQuestionId($key);
                                if (count($value['status']) > 1) {
                                    $vendor_choice->setRequire(1);
                                    $vendor_choice->setAsk(1);
                                } else {
                                    if ($value['status'][0] == 'ask') {
                                        $vendor_choice->setRequire(0);
                                        $vendor_choice->setAsk(1);
                                    } else {
                                        $vendor_choice->setRequire(1);
                                        $vendor_choice->setAsk(0);
                                    }
                                }
                            } else {
                                continue;
                            }
                        }
                        $em->persist($vendor_choice);
                        $em->flush();
                    }
                }
                $em->flush();
            }

            if ($request->query->get('quote') == NULL) {
                return $this->redirect($this->generateUrl('packages_show', array('id' => $entity->getId())));
            } else {
                //update quotes table with updated time
                $entities_quote = $em->getRepository('LocalsBestShopBundle:Quotes')->findBy(['id' => $request->query->get('quote')]);
                foreach ($entities_quote as $quote) {
                    $quote->setUpdatedAt(new \DateTime('now'));
                    $em->persist($quote);
                    $em->flush();
                    //send notification to user who had quoted on the package
                    if ($quote->getId() !== null) {
                        $object = $quote;
                        //send notification to Vender for status change of package
                        $this->createQuoteNotification($object, $entity);
                    }
                }

                $this->addFlash('success', 'Custom Package Created Successfully.');
                return $this->redirect($this->generateUrl('listing_custom_quotes'));
            }

            $em->flush();
        }

        return $this->render('@LocalsBestShop/package/new.html.twig', array(
                'entity' => $entity,
                'form' => $form->createView(),
                'stripe_status' => $stripe_status,
                'quote_status' => null,
                'item_id' => 0,
                'term_status' => 0,
                'vendor_name' => $user->getOwner()->getName(),
                'external_link_status' => 0,
                'user' => $user,
        ));
    }

    /**
     *
     *
     * @param Package $entity The entity
     *
     * @return if image is uploaded on s3
     */
    private function imageUploader($entity, $imagename, $actual_image)
    {
        $dir_to_save = getcwd() . '/uploads/package/';

        if (!is_dir($dir_to_save)) {
            mkdir($dir_to_save);
        }
        file_put_contents($dir_to_save . $imagename, file_get_contents($this->getParameter('temp_download_aws_folder') . $actual_image));

        if (!$entity->getFile()) {
            $file = new UploadedFile($dir_to_save . $imagename, $imagename);
            $entity->setFile($file);
        }
    }

    /**
     * Creates a form to create a Package entity.
     *
     * @param Package $entity The entity
     *
     * @return Form The form
     */
    private function createCreateForm(Package $entity, $request)
    {
        foreach ($this->getUser()->getBusinesses() as $business) {
            foreach ($business->getTypes() as $type) {
                $result[] = $type->getId();
            }
        }

        foreach ($this->getUser()->getBusinesses() as $business) {
            foreach ($business->getWorkingStates() as $states) {
                $result1[] = $states->getId();
            }
        }
        $form = $this->createForm(PackageType::class, $entity, array(
            'action' => $this->generateUrl('packages_create', ["quote" => $request->query->get('quote')]),
            'method' => 'POST',
            'user' => $this->getUser(),
            'business' => $this->getBusiness(),
            'attr' => array('class' => 'mt-repeater'),
            'isApproved' => $entity->getStatus() == Item::STATUS_APPROVED ? true : false,
            'industryType' => $result,
            'states' => $result1,
            'quote' => $request->query->get('quote')
        ));

        $form->add('draft', Type\SubmitType::class, array('label' => 'Draft', 'attr' => ['class' => 'checkQuantity']))
            ->add('archive', Type\SubmitType::class, array('label' => 'Archive', 'attr' => ['class' => 'checkQuantity']));
        if (!$request->query->get('quote')) {
            $form->add('approval', Type\SubmitType::class, array('label' => ' Send for approval', 'attr' => ['class' => 'btn-success checkQuantity']));
        } else {
            $form->add('republish', Type\SubmitType::class, array('label' => 'Publish', 'attr' => ['class' => 'btn-success checkQuantity']));
        }

        return $form;
    }

    /**
     * Displays a form to create a new Package entity.
     *
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    public function newAction(Request $request)
    {
        $isCopy = false;

        if (!$this->get('localsbest.checker')->forAddon('packages', $this->getUser())) {
            $this->addFlash('danger', $this->message_403);
            return $this->redirectToRoute('locals_best_user_homepage');
        }

        //check if stripe is activated or not
        $user = $this->getUser();

        if ($user->getStripeAccountActivated() == false || $user->getStripeAccountActivated() === null) {
            $stripe_status = 0;
        } else {
            $stripe_status = 1;
        }

        if ($request->query->has('copy_of')) {
            $parentEntity = $this->getDoctrine()->getRepository('LocalsBestShopBundle:Package')
                ->find($request->query->get('copy_of'));
            $entity = clone $parentEntity;
            $isCopy = true;
        } else {
            $entity = new Package();
        }

        if (!$isCopy) {
            $entity->getSets()->add(new ItemSet());
        }
        $sku = new Sku();
        $sku->addPrice(new Price());
        $entity->setSku($sku);

        $form = $this->createCreateForm($entity, $request);
        //check if vendor has created custom item or not
        $item_id = 0;

        if ($request->query->has('quote')) {
            $em = $this->getDoctrine()->getManager();
            $entity_item = $em->getRepository('LocalsBestShopBundle:Item')->findOneBy([
                'type' => 5,
                'createdBy' => $user
            ]);

            if (count($entity_item) >= 1) {
                $item_id = $entity_item->getId();
            } else {
                $this->addFlash('danger', 'No Item of type custom quote has been created by the vendor.');
            }
            $entities = $em->getRepository('LocalsBestShopBundle:Quotes')->findBy(['id' => $request->query->get('quote')]);
            foreach ($entities as $entity) {
                $quote_user = $entity->getUserId()->getFirstName() . ' ' . $entity->getUserId()->getLastName();
                $quote_id = $entity->getUserId()->getId();
            }
        }

        $term_status = 0;
        if (!empty($user->getTerms())) {
            $term_status = 1;
        } elseif ($user->getRole()->getName() == 'Admin') {
            $term_status = 1;
        }

        $external_link_status = 0;
        if ($this->get('localsbest.checker')->forAddon('Links Tab and Custom Login', $this->getUser())) {
            $external_link_status = 1;
        }
        // define array for questions
        $question = QuestionType::$choices;
        return $this->render('@LocalsBestShop/package/new.html.twig', array(
            'isCopy' => $isCopy,
            'entity' => $entity,
            'form' => $form->createView(),
            'stripe_status' => $stripe_status,
            'quote_status' => $request->query->has('quote'),
            'item_id' => $item_id,
            'quote_user' => !empty($quote_user) ? $quote_user : '',
            'quote_id' => !empty($quote_id) ? $quote_id : '',
            'question' => $question,
            'vendor_name' => $user->getOwner()->getName(),
            'term_status' => $term_status,
            'user' => $user,
            'external_link_status' => $external_link_status
        ));
    }

    /**
     * Finds and displays a Package entity.
     *
     * @param $id
     *
     * @return Response
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        if (!$this->get('localsbest.checker')->forAddon('packages', $user)) {
            $this->addFlash('danger', $this->message_403);
            return $this->redirectToRoute('locals_best_user_homepage');
        }

        if ($user->getRole()->getLevel() > 1) {
            $entity = $em->getRepository('LocalsBestShopBundle:Package')->findOneBy([
                'id' => $id,
                'createdBy' => $this->getUser(),
            ]);
        } else {
            $entity = $em->getRepository('LocalsBestShopBundle:Package')->find($id);
        }

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Package entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('@LocalsBestShop/package/show.html.twig', array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Package entity.
     *
     * @param $id
     *
     * @return Response
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        if ($user->getStripeAccountActivated() == false || $user->getStripeAccountActivated() === null) {
            $stripe_status = 0;
        } else {
            $stripe_status = 1;
        }

        if (!$this->get('localsbest.checker')->forAddon('packages', $user)) {
            $this->addFlash('danger', $this->message_403);
            return $this->redirectToRoute('locals_best_user_homepage');
        }

        if ($user->getRole()->getLevel() > 1) {
            $entity = $em->getRepository('LocalsBestShopBundle:Package')->findOneBy([
                'id' => $id,
                'createdBy' => $this->getUser(),
            ]);
        } else {
            $entity = $em->getRepository('LocalsBestShopBundle:Package')->find($id);
        }

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Package entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);
        $result = [];
        $package_image = $this->getParameter('temp_download_aws_folder') . $entity->getImages();
        array_push($result, $package_image);
        foreach ($entity->getSets() as $itemset) {
            foreach ($itemset->getItem()->getImages() as $image) {
                $split = explode("ec_shop", $image->getFile());
                $split = $this->getParameter('temp_download_aws_folder') . 'ec_shop' . $split[2];
                array_push($result, $split);
            }
        }
        // define array for questions
        $question = [
            1 => 'Do you Ask/Require a Job Address?',
            2 => 'Do you ask/require Other Fields?',
            3 => 'Do you Ask/Require a Note?',
            4 => 'Do you Ask/Require an Image?',
            5 => 'Do you Ask/Require an Event Date?'
        ];
        // checked radio buttons for all the questions array
        $selected_ask = [];
        $selected_require = [];
        foreach ($question as $key => $value) {
            $entities_questions = $em->getRepository('LocalsBestShopBundle:VendorChoice')->findBy(['packageId' => $entity, 'question_id' => $key]);
            if (!empty($entities_questions)) {
                $ask = $entities_questions[0]->getAsk();
                $require = $entities_questions[0]->getRequire();
                array_push($selected_ask, $ask);
                array_push($selected_require, $require);
            }
        }
        $actual_title = str_replace($user->getOwner()->getName() . ' ', '', $entity->getTitle());

        $external_link_status = 0;
        if ($this->get('localsbest.checker')->forAddon('Links Tab and Custom Login', $this->getUser())) {
            $external_link_status = 1;
        }

        return $this->render('@LocalsBestShop/package/edit.html.twig', array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'stripe_status' => $stripe_status,
            'isPublished' => $entity->getStatus() == Item::STATUS_APPROVED ? true : false,
            'result' => $result,
            'question' => $question,
            'selected_ask' => $selected_ask,
            'selected_require' => $selected_require,
            'vendor' => $user->getOwner()->getName(),
            'actual_title' => $actual_title,
            'vendor_name' => $user->getOwner()->getName(),
            'external_link_status' => $external_link_status,
            'user' => $user,
        ));
    }

    /**
     * Creates a form to edit a Package entity.
     *
     * @param Package $entity The entity
     *
     * @return Form The form
     */
    private function createEditForm(Package $entity)
    {
        foreach ($this->getUser()->getBusinesses() as $business) {
            foreach ($business->getTypes() as $type) {
                $result[] = $type->getId();
            }
        }

        foreach ($this->getUser()->getBusinesses() as $business) {
            foreach ($business->getWorkingStates() as $states) {
                $result1[] = $states->getId();
            }
        }
        $form = $this->createForm(PackageType::class, $entity, array(
            'action' => $this->generateUrl('packages_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'user' => $this->getUser(),
            'business' => $this->getBusiness(),
            'isApproved' => $entity->getStatus() == Item::STATUS_APPROVED ? true : false,
            'industryType' => $result,
            'states' => $result1
        ));

        $form->add('draft', Type\SubmitType::class, array('label' => 'Draft', 'attr' => ['class' => 'checkQuantity']))
            ->add('archive', Type\SubmitType::class, array('label' => 'Archive', 'attr' => ['class' => 'checkQuantity']))
            ->add('approval', Type\SubmitType::class, array('label' => 'Send for approval', 'attr' => ['class' => 'btn-success checkQuantity']));

        // Re-publish button
        if ($entity->getStatus() == Item::STATUS_APPROVED) {
            $form->add('republish', Type\SubmitType::class, array('label' => 'Republish', 'attr' => ['class' => 'btn-success checkQuantity']));
        }

        return $form;
    }

    /**
     * Edits an existing Package entity.
     *
     * @param Request $request
     * @param $id
     *
     * @return RedirectResponse|Response
     *
     * @throws Base
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        if ($user->getStripeAccountActivated() == false || $user->getStripeAccountActivated() === null) {
            $stripe_status = 0;
        } else {
            $stripe_status = 1;
        }
        /** @var Package $entity */
        if ($user->getRole()->getLevel() > 1) {
            $entity = $em->getRepository('LocalsBestShopBundle:Package')->findOneBy([
                'id' => $id,
                'createdBy' => $this->getUser(),
            ]);
        } else {
            $entity = $em->getRepository('LocalsBestShopBundle:Package')->find($id);
        }

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Package entity.');
        }

        $originalItemSets = new ArrayCollection();
        $originalPrices = new ArrayCollection();
        $originalOptions = new ArrayCollection();

        foreach ($entity->getSets() as $set) {
            $originalItemSets->add($set);
        }

        /** @var Sku $sku */
        $sku = $entity->getSku();

        foreach ($sku->getPrices() as $price) {
            $originalPrices->add($price);
        }

        foreach ($sku->getOptions() as $option) {
            $originalOptions->add($option);
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);
        $entity->setStatus($this->packageStatus($editForm->getClickedButton()->getName()));
        if ($editForm->isValid()) {

            $uow = $em->getUnitOfWork();
            $uow->computeChangeSets();

            $packageChangeSet = $uow->getEntityChangeSet($entity);
            if (isset($packageChangeSet['quantity'])) {
                list($packageOldQuantity, $packageNewQuantity) = $packageChangeSet['quantity'];
            }

            /** @var Sku $updatedSku */
            $updatedSku = $entity->getSku();
            /** @var ItemSet $set */
            foreach ($originalItemSets as $set) {
                if (false === $entity->getSets()->contains($set)) {
                    $item = $set->getItem();
                    $itemQuantity = $item->getQuantity();
                    $itemSetQuantity = $set->getQuantity();

                    $item->setQuantity($itemQuantity + $entity->getQuantity() * $itemSetQuantity);
                    $em->remove($set);
                }
            }

            /** @var ItemSet $set */
            foreach ($entity->getSets() as $set) {
                $changeSet = $uow->getEntityChangeSet($set);

                if (isset($packageChangeSet['quantity']) && isset($changeSet['quantity'])) {
                    list($oldQuantity, $newQuantity) = $changeSet['quantity'];
                    $item = $set->getItem();
                    $itemQuantity = $item->getQuantity();
                    $item->setQuantity($itemQuantity + $packageOldQuantity * $oldQuantity - $packageNewQuantity * $newQuantity);
                } elseif (isset($packageChangeSet['quantity'])) {
                    $item = $set->getItem();
                    $itemQuantity = $item->getQuantity();
                    $item->setQuantity($itemQuantity + ($packageOldQuantity - $packageNewQuantity) * $set->getQuantity());
                } elseif (isset($changeSet['quantity'])) {
                    list($oldQuantity, $newQuantity) = $changeSet['quantity'];
                    $item = $set->getItem();
                    $itemQuantity = $item->getQuantity();
                    $item->setQuantity($itemQuantity + ($oldQuantity - $newQuantity) * $entity->getQuantity());
                }
            }
            foreach ($originalPrices as $price) {

                if (false === $updatedSku->getPrices()->contains($price)) {
                    $em->remove($price);
                }
            }

            foreach ($originalOptions as $option) {
                if (false === $updatedSku->getOptions()->contains($option)) {
                    $em->remove($option);
                }
            }

            $stripeAccountId = $entity->getSku()->getCreatedBy()->getStripeAccountId();

            foreach ($entity->getSku()->getPrices() as $price) {
                $retail = explode("$", $price->getretailPrice());
                if (count($retail) > 1) {
                    $retail = $retail[1];
                    $price->setretailPrice($retail);
                }

                $changePrice = $uow->getEntityChangeSet($price);
                $priceMetadata = $em->getClassMetadata('LocalsBestShopBundle:Price');
                if (isset($changePrice['type'])) {

                    list($oldPriceType, $newPriceType) = $changePrice['type'];
                    if ($newPriceType == 'subscription') {
                        $planCreds = array(
                            'wholesaler' => $entity->getSku()->getCreatedBy(),
                            'name' => $entity->getTitle(),
                            'amount' => $price->getretailPrice(),
                            'description' => $entity->getDescription(),
                            'interval' => $price->getSubscriptionType(),
                            'stripe_account' => $stripeAccountId != '' ? $stripeAccountId : $this->container->getParameter('stripe_client_id')
                        );

                        $result = $this->get('app.client.stripe')->createSubscriptionPlan($planCreds);
                        if ($result->id) {
                            $price->setStripeplanid($result->id);
                            $price->setStripeproductid($result->product);
                        }
                        $uow->recomputeSingleEntityChangeSet($priceMetadata, $price);
                    }
                } elseif ($price->getType() == 'subscription' && isset($changePrice['amount'])) {
                    list($oldPriceAmount, $newPriceAmount) = $changePrice['amount'];
                    if ($oldPriceAmount != $newPriceAmount) {
                        $planCreds = array(
                            'wholesaler' => $entity->getSku()->getCreatedBy(),
                            'name' => $entity->getTitle(),
                            'amount' => $price->getretailPrice(),
                            'description' => $entity->getDescription(),
                            'interval' => $price->getSubscriptionType(),
                            'stripe_account' => $stripeAccountId != '' ? $stripeAccountId : $this->container->getParameter('stripe_client_id')
                        );
                        $result = $this->get('app.client.stripe')->createSubscriptionPlan($planCreds);
                        if ($result->id) {
                            $price->setStripeplanid($result->id);
                            $price->setStripeproductid($result->product);
                        }
                        $uow->recomputeSingleEntityChangeSet($priceMetadata, $price);
                    }
                } elseif ($price->getType() == 'subscription' && $price->getStripeplanid() === null) {
                    $planCreds = array(
                        'wholesaler' => $entity->getSku()->getCreatedBy(),
                        'name' => $entity->getTitle(),
                        'amount' => $price->getretailPrice(),
                        'description' => $entity->getDescription(),
                        'interval' => $price->getSubscriptionType(),
                        'stripe_account' => $stripeAccountId != '' ? $stripeAccountId : $this->container->getParameter('stripe_client_id')
                    );
                    $result = $this->get('app.client.stripe')->createSubscriptionPlan($planCreds);
                    if ($result->id) {
                        $price->setStripeplanid($result->id);
                        $price->setStripeproductid($result->product);
                    }
                    $uow->recomputeSingleEntityChangeSet($priceMetadata, $price);
                } elseif ($price->getType() == 'onetime') {
                    $price->setAmount($price->getAmount());
                    $price->setRebate($price->getRebate());
                    $price->setStripeplanid(null);
                    $price->setStripeproductid(null);
                    $uow->recomputeSingleEntityChangeSet($priceMetadata, $price);
                }
            }

            // To upload image manually on s3 using vichuploader
            $manual_upload = false;

            if (!$entity->getFile() && !empty($editForm['imagename']->getdata())) {

                $now = new DateTime();
                $package_image = explode("/", $editForm['imagename']->getdata());
                $packages_image = $now->getTimestamp() . '_' . $package_image[2];

                $this->imageUploader($entity, $packages_image, $editForm['imagename']->getdata());

                $dir_to_save = getcwd() . '/uploads/package/';
                $manual_upload = true;
            }
            $em->flush();

            if ($manual_upload) {
                if (!unlink($dir_to_save . $packages_image)) {
                    return true;
                }
            }

            //update vendor choices ask/require table
            $question = QuestionType::$choices;
            $data = $request->request->all();
            // remove previous dependencies for vendor choices
            if (isset($data['localsbest_shopbundle_package']['question']) && !empty($data['localsbest_shopbundle_package']['question'])) {
                foreach ($question as $key => $value) {
                    $entities_questions = $em->getRepository('LocalsBestShopBundle:VendorChoice')->findBy(['packageId' => $entity, 'question_id' => $key]);
                    if (!empty($entities_questions))
                        $em->remove($entities_questions[0]);
                }
                // add new dependencies for vendor choices
                $array = ['1', '2', '3', '4', '5'];
                foreach ($array as $array1) {
                    $vendor_choice = new VendorChoice();
                    $vendor_choice->setpackageId($entity);
                    foreach ($data['localsbest_shopbundle_package']['question'] as $key => $value) {
                        if (!array_key_exists($array1, $data['localsbest_shopbundle_package']['question'])) {
                            $vendor_choice->setQuestionId($array1);
                            $vendor_choice->setRequire(0);
                            $vendor_choice->setAsk(0);
                        } else {
                            if ($key == $array1) {
                                $vendor_choice->setQuestionId($key);
                                if (count($value['status']) > 1) {
                                    $vendor_choice->setRequire(1);
                                    $vendor_choice->setAsk(1);
                                } else {
                                    if ($value['status'][0] == 'ask') {
                                        $vendor_choice->setRequire(0);
                                        $vendor_choice->setAsk(1);
                                    } else {
                                        $vendor_choice->setRequire(1);
                                        $vendor_choice->setAsk(0);
                                    }
                                }
                            } else {
                                continue;
                            }
                        }
                        $em->persist($vendor_choice);
                        $em->flush();
                    }
                }
                $em->flush();
            }

            if ($this->packageStatus($editForm->getClickedButton()->getName()) == 2) {
                return $this->redirect($this->generateUrl('packages'));
            }

            return $this->redirect($this->generateUrl('packages_edit', array('id' => $id)));
        }

        return $this->render('@LocalsBestShop/package/edit.html.twig', array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'stripe_status' => $stripe_status
        ));
    }

    /**
     * Deletes a Package entity.
     *
     * @param Request $request
     * @param $id
     *
     * @return RedirectResponse
     */
    public function deleteAction(Request $request, $id)
    {
        if (!$this->get('localsbest.checker')->forAddon('packages', $this->getUser())) {
            $this->addFlash('danger', $this->message_403);
            return $this->redirectToRoute('locals_best_user_homepage');
        }

        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $user = $this->getUser();

            if ($user->getRole()->getLevel() > 1) {
                $entity = $em->getRepository('LocalsBestShopBundle:Package')->findOneBy([
                    'id' => $id,
                    'createdBy' => $this->getUser(),
                ]);
            } else {
                $entity = $em->getRepository('LocalsBestShopBundle:Package')->find($id);
            }

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Package entity.');
            }

            /** @var Sku $sku */
            $sku = $entity->getSku();

            if (count($sku->getShopItems()) > 0) {
                $this->addFlash('danger', 'This Package was ordered and not able for delete!');
                return $this->redirectToRoute('packages');
            }

            $quantity = $entity->getQuantity();
            /** @var ItemSet $set */
            foreach ($entity->getSets() as $set) {
                $item = $set->getItem();
                $itemQuantity = $item->getQuantity();
                $itemSetQuantity = $set->getQuantity();

                $item->setQuantity($itemQuantity + $quantity * $itemSetQuantity);
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('packages'));
    }

    /**
     * Creates a form to delete a Package entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
                ->setAction($this->generateUrl('packages_delete', array('id' => $id)))
                ->setMethod('DELETE')
                ->add('submit', Type\SubmitType::class, array('label' => 'Delete'))
                ->getForm()
        ;
    }

    private function packageStatus($clickedSubmitName)
    {

        $statuses = [
            'draft' => 1,
            'archive' => 3,
            'approval' => 4,
            'republish' => 2
        ];
        return $statuses[$clickedSubmitName];
    }

    private function createQuoteNotification($object, $package_object)
    {

        // Notification to vender that new quote arises

        $message = 'New Package created by Vendor for package ' . $object->getPackageId()->getTitle() . ' on which you quoted.';
        // Send Notification about this
        $this->get('localsbest.notification')
            ->addNotification(
                $message,
                'sku_show',
                array('id' => $package_object->getSku()->getId()),
                array($object->getUserId()),
                array($object->getUserId())
            )
        ;

        return true;
    }

}
