<?php
 
namespace LocalsBest\MedAppUserBundle\Listener;
 
use LocalsBest\MedAppUserBundle\Entity\Role;
use Symfony\Component\DependencyInjection\Container,
    Symfony\Component\HttpFoundation\RedirectResponse,
    Symfony\Component\HttpKernel\Event\GetResponseEvent,
    LocalsBest\MedAppUserBundle\Entity\User;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
 
/**
 * Custom Request Listener.
 */
class LoginListener
{
	/**
     * @var \Symfony\Component\Security\Core\SecurityContext
     */
	private $securityContext;
        
    /**
     * @var Container
     */
    protected $container;

    /**
	 * Constructor
	 * 
	 * @param AuthorizationCheckerInterface $securityContext
     * @param Container $container Service Container
	 */
	public function __construct(AuthorizationCheckerInterface $securityContext, Container $container)
	{
		$this->securityContext  = $securityContext;
		$this->container        = $container;
	}
	
	/**
	 * Do the magic.
	 * 
	 * @param GetResponseEvent $event
     * @return RedirectResponse|void
     */
	public function onKernelRequest(GetResponseEvent $event)
	{
        $token = $this->container->get('security.token_storage')->getToken();

        // Check variable for value
        if (!$token) {
            return;
        }

        // Get current User entity
        $user = $token->getUser();
        // List of OK routes
        $exemptedRoutes = ['select_business', 'switch_business', 'add_new_business', 'users_add'];
        // Get Route of page that User want to open
        $currentRoute  = $event->getRequest()->get('_route');
            
        if (!$currentRoute) {
            return;
        }

        if (in_array($currentRoute, $exemptedRoutes)) {
            return;
        }

        // Check User for Authenticated
        if (
            $this->securityContext->isGranted('IS_AUTHENTICATED_FULLY')
            || $this->securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')
        ) {
            // Get all user businesses
            $businesses = $user->getBusinesses();

            // If User have Role = ROLE_NETWORK_MANAGER
            if(
                $user instanceof User
                && in_array($user->getRole()->getRole(), ['ROLE_NETWORK_MANAGER'])
            ) {
                // Redirect User to Create New User Page
                if (count($businesses) < 1 ) {
                    $event->setResponse(
                        new RedirectResponse($this->container->get('router')->generate('users_add'), 302)
                    );
                } else {
                    return;
                }
            }

            // If businesses count is less than 1
            if(count($businesses) < 1 ) {
                // Redirect User to Create New Business Page
                $event->setResponse(
                    new RedirectResponse($this->container->get('router')->generate('add_new_business'), 302)
                );
            }

            // Get Session
            $session = $this->container->get('session');

            // Check Session for current Business variable
            if (!$session->has('current_business')) {

                if (count($businesses) === 1) {
                    $session->set('current_business', $businesses[0]->getId());

                    $referer = $event->getRequest()->headers->get('referer');
                    $referer = parse_url($referer);

                    $router = $this->container->get('router');
                    $route = $router->match($referer['path']);

                    if (
                        $route['_route'] == 'client_register'
                        && $route['referralId'] == 2326
                        && $businesses[0]->getId() == 50
                        && $user->getRole()->getLevel() == 7
                    ) {
                        $event->setResponse(new RedirectResponse($this->container->get('router')->generate('sign_form'), 302));
                    }

                    if ($businesses[0]->getId() == 173 && in_array($user->getRole()->getLevel(), [Role::ROLE_CLIENT])) {
                        $event->setResponse(new RedirectResponse($this->container->get('router')->generate('sign_form'), 302));
                    }
                } elseif (count($businesses) === 0) {
                    $event->setResponse(new RedirectResponse($this->container->get('router')->generate('select_business'), 302));
                }
            }
        }
    }
}
