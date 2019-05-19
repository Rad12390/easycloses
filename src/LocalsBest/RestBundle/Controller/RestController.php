<?php
namespace LocalsBest\RestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;


/**
 * The Super REST Controller
 *
 * @author Abhinav Kumar <work@abhinavkumar.in>
 */
class RestController extends Controller
{
    const STATUS_ERROR          = -9;
    const STATUS_EXCEPTION      = -10;
    const STATUS_SERVER_ERROR   = 500;
    const STATUS_NOT_FOUND      = 404;
    
    const VERSION               = 'v2';
    
    static $entityClass;

    private $response;
    
    
    
    /**
     * Gets the current request payload
     * 
     * @return Array An array of current payload
     */
    private function getPayload()
    {
        return $this->getRequest()->request->all();
    }

    /**
     * Returns a response
     * 
     * @return Response The response object to return
     */
    private function returnResponse()
    {
        return $this->response;
    }
    
    /**
     * Returns a JSON encoded response
     * 
     * @param Array $payload The response payload
     * @param int $responseCode The response code
     * @param Array $params An array of additional response parameters
     * @return Mixed
     */
    private function returnJSON($payload, $responseCode = 200, Array $params = array())
    {
        //ob_clean();
        
        $defaultParams = array(
            'Content-type'  => 'application/json'
        );
        
        $encodedPayload = json_encode($payload);
        
        if ($encodedPayload) {
            $this->response = new Response($encodedPayload, $responseCode, array_merge($defaultParams, $params));
            
            return $this->returnResponse($encodedPayload);
        }
    }
    
    /**
     * Returns an Exception Response
     * 
     * @param String $message The exception message
     * @param int $status The response code (defaults to 500 for server errors)
     * @return Response The response object
     */
    private function Exception($message, $status = self::STATUS_EXCEPTION)
    {
        $payload = array(
            'status'    => $status,
            'message'   => $message
        );
        
        return $this->returnJSON($payload, self::STATUS_SERVER_ERROR);
    }
    
    /**
     * Returns an Error Response
     * 
     * @param String $message The error message
     * @param int $status The response code (defaults to -9 for errors)
     * @return Response The response object
     */
    private function Error($message, $status = self::STATUS_ERROR)
    {
        $payload = array(
            'status'    => $status,
            'message'   => $message
        );
        
        return $this->returnJSON($payload, self::STATUS_SERVER_ERROR);
    }
    
    /**
     * Returns a Not Found Error
     * 
     * @param String $message The error message to return
     * @return Response The Response object to return
     */
    private function notFoundError($message)
    {
        $payload = array(
            'status'    => self::STATUS_NOT_FOUND,
            'message'   => $message
        );
        
        return $this->returnJSON($payload, self::STATUS_NOT_FOUND);
    }
    
    /**
     * 
     * @return Response $payload or Error
     */
    public function getAllAction()
    {
        if (count($_GET)) {
            
            $params = array_keys($_GET);
            $param  = $params[0];
            $value  = $_GET[$param];
            
            $associatedObject = $this->getDoctrine()->getManager()
                ->getRepository('SynergicBee\PlatformBundle\Entity\\' . ucfirst($param))
                ->find($value);
            
            if ($associatedObject) {
                $objectArray = call_user_func_array(
                    array($this->getRepository(), 'findBy' . $param), 
                    array($associatedObject)
                );
                
                if (empty($objectArray)) {
                    return $this->notFoundError('No mathing records found!');
                }
                
                $objectArraySerialized = array();
                
                foreach ($objectArray as $entity) {
                    $objectArraySerialized[] = $this->getRepository()->toArray($entity);
                }
                
                return $this->returnJSON($objectArraySerialized);
            } else {
                return $this->notFoundError('Invalid ' . $param . ' id: ' . $value);
            }
        }
        
        $objectCollection = $this->getRepository()->findAll();
        
        if (!$objectCollection || !count($objectCollection)) {
            return $this->notFoundError(sprintf("No matching records found"));
        }
        
        $objectArray = array();
        
        foreach ($objectCollection as $entity) {
            $objectArray[]  = $this->getRepository()->toArray($entity);
        }
        
        return $this->returnJSON($objectArray);
    }
    
    private function getRepository()
    {
        if (empty(static::$entityClass) || !class_exists(static::$entityClass)) {
            throw new \Exception('Entity class is not set in the controller');
        }
        
        return $this->getDoctrine()->getManager()->getRepository(static::$entityClass);
    }
    
    public function getAction($id)
    {
        $entityObject = $this->getRepository()->find($id);
        
        if ($entityObject && $entityObject instanceof static::$entityClass) {
            return $this->returnJSON($this->getRepository()->toArray($entityObject));
        }
        
        return $this->notFoundError(sprintf("Object with id '%s' does not exist", $id));
    }
    
    /**
     * Lists all the Users
     * @Template
     * @todo Default GET action for Users endpoint
     */
    public function indexAction($name)
    {
        $method = $this->getRequest()->getMethod();
        
        if ('/' === substr($name, -1)) {
            $name = substr($name, 0, strlen($name) - 1);
        }
        
        $explodedParams = explode('/', $name);
        
        if (!$explodedParams || count($explodedParams)<2) {
            return $this->notFoundError(sprintf("No RESTController specified"));
        }
        
        if (self::VERSION !== $explodedParams[0]) {
            return $this->Exception(sprintf("Invalid version %s", $explodedParams[0]));
        }

        //$bundle = $this->getRequest()->attributes->get('_template')->get('bundle');
        /**
         * @todo - Temporary fix
         */
        $bundle = 'SyngergicBeePlatformBundle';
        
        if ('GET' === $method && count($explodedParams) < 3) {
            $action = $this->getDefaultAction();
            
            return $this->forward($bundle . ':' . ucfirst($explodedParams[1]) . ':' . ($action));
        }
        
        return $this->forward($bundle . ':' . ucfirst($explodedParams[1]) . ':' . strtolower($method), array(
            'id'    => $explodedParams[2]
        ));
    }
    
    /**
     * The PUT Controller for the HTTP PUT action
     * Handles the UPDATEs for the User objects
     * 
     * @param int $id The User ID
     * @return Response The Response Object
     */
    public function putAction($id = null)
    {
        $id = (int) $id;

        if (!$id) {
            return $this->notFoundError('User id is blank');
        }

        $repository = $this->getRepository();

        $userExists = $repository->find($id);

        if (!$userExists || !$userExists instanceof \SynergicBee\UserBundle\Entity\User) {
            return $this->notFoundError('User not found');
        }

        $payload = $this->getPayload();

        if (isset($payload['password']) && !empty($payload['password'])) {
            if ($this->container->getParameter('password_minlength') > strlen($payload['password'])) {
                return $this->Exception('Password must be 8 characters long');
            }

            if (User::STATUS_NOACTIVATED === $userExists->getStatus()) {
                //First time account activation
                $userExists->renewSalt();

                $factory = $this->container->get('security.encoder_factory');
                $encoder = $factory->getEncoder($userExists);

                $password = $encoder->encodePassword($payload['password'], $userExists->getSalt()); //where $user->password has been bound in plaintext by the form

                $userExists->setPassword($password);

                $userExists->setStatus(User::STATUS_ACTIVE);

                $userExists->resetFailedLogins();

                $this->getDoctrine()->getManager()->persist($userExists);
                $this->getDoctrine()->getManager()->flush();

                return $this->returnJSON(array(
                            'content' => $userExists->toArray()
                ));
            }
        }
    }

    /**
     * Gets the default action
     * 
     * @return string The default Action
     */
    public function getDefaultAction()
    {
        return 'getAll';
    }
}