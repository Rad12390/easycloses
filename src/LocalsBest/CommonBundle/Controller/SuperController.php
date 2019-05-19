<?php

namespace LocalsBest\CommonBundle\Controller;

use LocalsBest\CommonBundle\Entity\BaseEntity;
use LocalsBest\UserBundle\Entity\Business;
use LocalsBest\UserBundle\Entity\Job;
use LocalsBest\UserBundle\Entity\Transaction;
use LocalsBest\UserBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Role\SwitchUserRole;

class SuperController extends Controller
{
    /**
     * Get current User
     *
     * @return \LocalsBest\UserBundle\Entity\User
     */
    public function getUser()
    {
        return parent::getUser();
    }
    
    /**
     * Get Repository
     * 
     * @param string $className
     *
     * @return \Doctrine\Common\Persistence\ObjectRepository
     */
    protected function getRepository($className)
    {
        return $this->getDoctrine()->getRepository($className);
    }

    
    /**
     * Gets the MailMan Service
     * 
     * @return \LocalsBest\NotificationBundle\Service\MailMan
     */
    protected function getMailMan()
    {
        return $this->get('localsbest.mailman');
    }

    /**
     * @param User $user
     * @param $firewall
     */
    protected function logUserIn(User $user, $firewall)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository('LocalsBestUserBundle:User')->loadUserByUserName($user->getUsername());
        
        if (!$user) {
            throw new UsernameNotFoundException("User not found");
        }
    
        $token = new UsernamePasswordToken($user, null, "your_firewall_name", $user->getRoles());
        
        $this->get("security.token_storage")->setToken($token); //now the user is logged in

        //now dispatch the login event
        $request = $this->get("request");
        $event = new InteractiveLoginEvent($request, $token);
        $this->get("event_dispatcher")->dispatch("security.interactive_login", $event);
    }

    /**
     * Add message to flash bag
     *
     * @param string $type
     * @param string $message
     *
     * @return mixed
     */
    protected function addMessage($type, $message)
    {
        return $this->get('session')->getFlashBag()->add($type, $message);
    }

    /**
     * Check user role for Client level
     *
     * @throws AccessDeniedException
     */
    protected function isClient()
    {
        if ($this->getUser()->getRole()->getRole() == 'ROLE_CLIENT') {
            throw $this->createAccessDeniedException('Unable to access this page!');
        }
    }

    /**
     * Limit for business with id 155
     *
     * @throws AccessDeniedException
     */
    protected function isLimitAccess()
    {
        $businessId = $this->get('session')->get('current_business');

        if ($businessId == 155) {
            throw $this->createAccessDeniedException('Unable to access this page!');
        }
    }

    /**
     * Check user password for default values
     *
     * @return bool
     */
    protected function checkPassword()
    {
        // Get current user
        $user = $this->getUser();

        // if User property "isDefaultPassword" is empty or true
        if ($user->isPasswordDefault() || $user->isPasswordDefault() === null) {
            // Get Encoder object
            $factory = $this->get('security.encoder_factory');
            $encoder = $factory->getEncoder($user);

            // check User password for default values
            if(
                $encoder->isPasswordValid($user->getPassword(), 'admin', $user->getSalt())
                || $encoder->isPasswordValid($user->getPassword(), 'ambiance', $user->getSalt())
                || $encoder->isPasswordValid($user->getPassword(), 'tomorrow', $user->getSalt())
                || $encoder->isPasswordValid($user->getPassword(), 'premier', $user->getSalt())
            ) {
                // Set flash message status and text
                $this->addFlash('warning', 'You must change your temporary password to a new password to gain access to the program.');
                return false;
            }

            // set User isDefaultPassword to false
            $user->setIsPasswordDefault(false);
            // update DB
            $this->getDoctrine()->getManager()->flush();
        }

        return true;
    }

    /**
     * Add message to flash bag with success type
     *
     * @param $message
     *
     * @return mixed
     */
    protected function addSuccessMessage($message)
    {
        return $this->addMessage('success', $message);
    }

    /**
     * Add message to flash bag with info type
     *
     * @param $message
     *
     * @return mixed
     */
    protected function addInfoMessage($message)
    {
        return $this->addMessage('info', $message);
    }

    /**
     * Add message to flash bag with danger type
     *
     * @param $message
     *
     * @return mixed
     */
    protected function addErrorMessage($message)
    {
        return $this->addMessage('danger', $message);
    }

    /**
     * Check permissions for role
     *
     * @param string $role
     *
     * @throws \Exception
     */
    protected function restrictTo($role)
    {
        $role = strtoupper($role);
        
        if (substr($role, 0, strlen('ROLE_')) !== 'ROLE_') {
            $role = 'ROLE_' . $role;
        }

        /** @var \LocalsBest\UserBundle\Entity\Role $role */
        $role = $this->getDoctrine()->getRepository('LocalsBestUserBundle:Role')->findOneBy(['role' => $role]);

        if (!$role) {
            throw new \Exception(sprintf('Invalid level %s', $role->getLevel()));
        }
        
        if ($this->getUser()->getRole()->getLevel() > $role->getLevel()) {
            throw new AccessDeniedException('403 Forbidden');
        }
    }

    /**
     * Check permissions for role
     *
     * @param $role
     *
     * @throws \Exception
     */
    protected function restrictToRole($role)
    {
        return $this->restrictTo($role);
    }

    /**
     * Check permissions for role level
     *
     * @param $level
     *
     * @throws \Exception
     */
    protected function restrictToLevel($level)
    {
        /** @var \LocalsBest\UserBundle\Entity\Role $role */
        $role = $this->getRepository('LocalsBestUserBundle:Role')->findOneBy(array('level' => $level));
        
        if (!$role) {
            throw new Exception(sprintf('Invalid level %s', $level));
        }
        
        return $this->restrictToRole($role->getRole());
    }

    /**
     * Get needed Entity by params
     *
     * @param string $className
     * @param array $params
     *
     * @return null|object
     */
    protected function find($className, array $params = array())
    {
        return $this->getRepository($className)->findOneBy($params);
    }

    /**
     * Get needed Entity by params or 404 error
     *
     * @param string $className
     * @param array $params
     * @param string $notFoundMessage
     *
     * @throws NotFoundHttpException
     *
     * @return null|object
     */
    protected function findOr404($className, array $params=[], $notFoundMessage ='No Records found matching criteria')
    {
        $object = $this->find($className, $params);

        if ($object && is_object($object)) {
            return $object;
        }

        throw $this->createNotFoundException($notFoundMessage);
    }

    /**
     * Get current Business
     *
     * @return null|Business
     */
    protected function getBusiness()
    {
        $businessId = $this->get('session')->get('current_business');
        if ($businessId) {
            return $this->getRepository('LocalsBestUserBundle:Business')->find($businessId);
        } else {
            return null;
        }
    }

    /**
     * Get Business Staff
     *
     * @param null User $u
     * @param array $matching
     *
     * @return mixed
     */
    protected function getStaffs($u = null, $matching = [])
    {
        if(is_null($u)){
            $u = $this->getUser();
        }
        return $this->getDoctrine()->getRepository('LocalsBestUserBundle:User')
            ->findStaffs($u, $this->getBusiness(), $matching);
    }

    /**
     * Get Business Staff IDs
     *
     * @param null|User $u
     *
     * @return mixed
     */
    protected function getStaffIds($u = null)
    {
        if (is_null($u)){
            $u = $this->getUser();
        }
        return $this->getDoctrine()->getRepository('LocalsBestUserBundle:User')
            ->findStaffIds($u);
    }

    /**
     * Check User permissions for current object
     *
     * @param $object
     *
     * @return bool
     */
    protected function canEdit($object)
    {
        $em = $this->getDoctrine()->getManager();
        $em->getFilters()->disable('softdeleteable');

        /** @var BaseEntity $object */
        if ($object instanceof Job) {
            return (
                $object->getCreatedBy() === $this->getUser()
                || in_array($object->getCreatedBy()->getId(), $this->getStaffIds())
                || $object->getVendor()->getId() === $this->getUser()->getId()
            );
        } else {
            return (
                $object->getCreatedBy() === $this->getUser()
                || in_array($object->getCreatedBy()->getId(), $this->getStaffIds())
                || $this->getUser()->getRole()->getLevel() == 5
            );
        }
    }

    /**
     * Get Transaction Color Status
     *
     * @param Transaction $transaction
     *
     * @return null|string
     */
    protected function getTransactionDocumentsStatus(Transaction $transaction)
    {
        $colorClosing = null;
        $levelClosing = null;
        $colorListing = null;
        $levelListing = null;

        if (!is_null($transaction->getClosing())) {
            list($colorClosing, $levelClosing) = $this->getTransactionStatusColor(
                $transaction->getClosing()->getDocumentTypes()
            );
        }

        if (!is_null($transaction->getListing())) {
            list($colorListing, $levelListing) = $this->getTransactionStatusColor(
                $transaction->getListing()->getDocumentTypes()
            );
        }
        $this->getDoctrine()->getManager()->getFilters()->enable('softdeleteable');
        $this->getDoctrine()->getManager()->getFilters()->disable('softdeleteable');

        if (!is_null($levelClosing) && !is_null($levelListing)) {
            $color = ($levelClosing >= $levelListing ) ? $colorClosing : $colorListing;
        } elseif (!is_null($levelClosing)) {
            $color = $colorClosing;
        } elseif (!is_null($levelListing)) {
            $color = $colorListing;
        } else {
            $color = '#d84a38';
        }

        if (
            $color == '#3cc051'
            && (
                strtolower($transaction->getTransactionStatus()) == 'sold_paid'
                || strtolower($transaction->getTransactionStatus()) == 'leased_paid'
            )
        ) {
            $color = '#4d90fe';
        }

        if (
            !in_array($color, ['#d84a38', '#ffb848'])
            && (
                strtolower($transaction->getTransactionStatus()) == 'contract_fell_thru'
                || strtolower($transaction->getTransactionStatus()) == 'withdrawn'
            )
        ) {
            $color = '#000000';
        }

        return $color;
    }

    /**
     * Calculate Transaction Color
     *
     * @param $docTypes
     *
     * @return array
     */
    protected function getTransactionStatusColor($docTypes)
    {
        $color = '#d84a38';
        $level = 0;
        $break = false;
        /** @var \LocalsBest\UserBundle\Entity\DocumentType $documentType */
        foreach ($docTypes as $documentType) {
            if( $documentType->getDeleted() !== null ) {
                continue;
            }
            if ($break != true) {
                if ($documentType->getIsRequired() == true && $documentType->getDocument() !== null) {
                    if (
                        ($documentType->getApproved() == null || $documentType->getApproved() == 0)
                        && ($documentType->getRejected() == null || $documentType->getRejected() == 0)
                    ) {
                        if ($level <= 2) {
                            $color = '#ffb848';
                            $level = 2;
                        }
                    } elseif ($documentType->getApproved() == true) {
                        if ($level <= 1) {
                            $color = '#3cc051';
                            $level = 1;
                        }
                    } elseif ($documentType->getRejected() == true) {
                        $color = '#000000';
                        $break = true;
                    }
                } elseif ($documentType->getIsRequired() == true && $documentType->getDocument() == null) {
                    $color = '#d84a38';
                    $level = 3;
                    $break = true;
                }
            }
        }

        return [ $color, $level ];
    }

    /**
     * Get real User object who use masking
     *
     * @return \LocalsBest\UserBundle\Entity\User
     */
    protected function getUnmaskingUser()
    {
        $authChecker = $this->get('security.authorization_checker');
        $tokenStorage = $this->get('security.token_storage');

        // Check need Role for this
        if ($authChecker->isGranted('ROLE_PREVIOUS_ADMIN')) {
            // Look for needed Role
            foreach ($tokenStorage->getToken()->getRoles() as $role) {
                if ($role instanceof SwitchUserRole) {
                    // Get real user
                    $impersonatingUser = $role->getSource()->getUser();
                    break;
                }
            }
        }
        // Get Current User Object
        $user = $this->getUser();

        // If app get real user
        if (isset($impersonatingUser)) {
            $em = $this->getDoctrine()->getManager();
            // Get Real User Object
            $user = $em = $em->getRepository('LocalsBestUserBundle:User')->find($impersonatingUser->getId());
        }

        return $user;
    }
}
