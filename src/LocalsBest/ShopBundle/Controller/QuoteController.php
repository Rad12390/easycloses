<?php

namespace LocalsBest\ShopBundle\Controller;

use LocalsBest\CommonBundle\Controller\SuperController as Controller;
use LocalsBest\ShopBundle\Entity\Quotes;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class QuoteController extends Controller
{

    public function customQuotesAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $quotes = $this->get('request_stack')->getCurrentRequest()->request->get('quote');
        $sku = $this->get('request_stack')->getCurrentRequest()->request->get('sku');
        //get the package object based on id
        $entity_package = $em->getRepository('LocalsBestShopBundle:Package')->find($id);

        //get vendor complete entity
        $vendor_id = $entity_package->getCreatedBy()->getId();
        $entity_vendor = $user = $em->getRepository('LocalsBest\UserBundle\Entity\User')
            ->findOneBy(['id' => $vendor_id]);

        $entity_user = $this->getUser(); //current user entity
        //save in to datatbase
        $custom_quote = new Quotes();
        $custom_quote->setPackageId($entity_package);
        $custom_quote->setUserId($entity_user);
        $custom_quote->setVendorId($entity_vendor);
        $custom_quote->setQuote($quotes);
        $em->persist($custom_quote);
        $em->flush();

        //send notification to vendor regarding new note created
        if ($custom_quote->getId() !== null) {
            $object = $custom_quote;
            //send notification to Vender for status change of package
            $this->createQuoteNotification($object);
        }
        //redirect to a route
        $this->addFlash('success', 'Thank you, Your custom quote will be available in the shop when the Business responds to your request.');
        return $this->redirect($this->generateUrl('sku_show', array('id' => $sku)));
    }

    public function customQuotesListingAction(Request $request)
    {
        return $this->render('@LocalsBestShop/quotes/index.html.twig', array(
            'params' => http_build_query($request->query->all())
        ));
    }

    public function customQuotesDatatableAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $params = $request->query->all();
        $vendor_id = $this->getUser()->getId();
        $entities_length = $em->getRepository('LocalsBestShopBundle:Quotes')->findBy(['vendorid' => $vendor_id]);
        //sorting on the basis of created at date
        if (isset($params['order']) && in_array($params['order'][0]['column'], [3])) {
            $entities = $em->getRepository('LocalsBestShopBundle:Quotes')->findAllOrderByCreated($vendor_id, $params);
        }

        //sorting on the basis of package name
        if (isset($params['order']) && in_array($params['order'][0]['column'], [0])) {
            $entities = $em->getRepository('LocalsBestShopBundle:Quotes')->findAllOrderByPackageName($vendor_id, $params);
        }

        //sorting on the basis of quoted user
        if (isset($params['order']) && in_array($params['order'][0]['column'], [1])) {
            $entities = $em->getRepository('LocalsBestShopBundle:Quotes')->findAllOrderByUserName($vendor_id, $params);
        }

        //sorting on the basis of quoted comment
        if (isset($params['order']) && in_array($params['order'][0]['column'], [2])) {
            $entities = $em->getRepository('LocalsBestShopBundle:Quotes')->findAllOrderByQuote($vendor_id, $params);
        }

        //sorting on the basis of package creation 
        if (isset($params['order']) && in_array($params['order'][0]['column'], [4])) {
            $entities = $em->getRepository('LocalsBestShopBundle:Quotes')->findAllOrderByUpdated($vendor_id, $params);
        }

        //searching
        if (!empty($params['search']['value'])) {
            $entities = $em->getRepository('LocalsBestShopBundle:Quotes')->findAllOrderBySearch($params);
        }

        $result = [];
        $result = [
            'draw' => (int) $params['draw'],
            'data' => '',
            'recordsTotal' => count($entities_length),
            'recordsFiltered' => count($entities_length),
        ];
        if ($entities != null) {
            foreach ($entities as $entity) {
                $firstname = $entity->getUserId()->getFirstName();
                $lastname = $entity->getUserId()->getLastName();

                $package_name = $entity->getPackageId()->getTitle();
                $quoted_user = $firstname . ' ' . $lastname;
                $comment = $entity->getQuote();
                $quoted_date = $entity->getCreatedAt()->format('Y-m-d');
                $package_publish = $entity->getUpdatedAt()->format('Y-m-d');
                if ($entity->getCreatedAt()->format('Y-m-d H:i:s') == $entity->getUpdatedAt()->format('Y-m-d H:i:s')) {
                    $package_publish = '00-00-00';
                    $class = "";
                } else {
                    $class = "disabled";
                }
                $data[] = [
                    $package_name,
                    $quoted_user,
                    $comment,
                    $quoted_date,
                    $package_publish,
                    '<a class="btn btn-info btn-xs ' . $class . '" href="' . $this->generateUrl('packages_new', array('quote' => $entity->getId())) . '" target="_blank">Create Package</a>'
                ];
            }
            $result['data'] = $data;
        }

        return new Response(json_encode($result));
    }

    private function createQuoteNotification($object)
    {
        // Notification to vender that new quote arises
        $message = 'New Quote added by ' . $object->getUserId()->getFirstName() . ' ' . $object->getUserId()->getLastName() . ' on ' . $object->getPackageId()->getTitle();
        // Send Notification about this
        $this->get('localsbest.notification')
            ->addNotification(
                $message,
                'listing_custom_quotes',
                array(),
                array($object->getPackageId()->getCreatedBy()),
                array($object->getPackageId()->getCreatedBy())
            )
        ;

        return true;
    }
}
