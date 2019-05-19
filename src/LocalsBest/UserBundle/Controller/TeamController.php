<?php

namespace LocalsBest\UserBundle\Controller;

use LocalsBest\CommonBundle\Controller\SuperController as Controller;
use LocalsBest\UserBundle\Entity\Team;
use LocalsBest\UserBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\RedirectResponse;

class TeamController extends Controller
{
    /**
     * Display Team staff
     *
     * @return array|RedirectResponse
     * @Template
     */
    public function listAction()
    {
        // Check user password for default values
        if(!$this->checkPassword()) {
            return $this->redirect('profile#business_user_password');
        }

        // Check user Role for Team Leader
        if($this->getUser()->getRole()->getLevel() != 6) {
            return $this->redirectToRoute('locals_best_user_homepage');
        }

        $em = $this->getDoctrine()->getManager();
        $totalMembers = array();
        $user = $this->getUser();
        // Get Team Entity by Leader
        $team = $em->getRepository('LocalsBestUserBundle:Team')->findOneBy(['leader' => $user]);

        if($team !== null) {
            // Get Team stuff
            $users = $team->getAgents();
        }

        if(isset($users) && $users) {
            foreach ($users as $user) {
                $totalMembers[] = $user;
            }
        }
        // Return array of params to view
        return [
            'members' => $totalMembers
        ];
    }

    /**
     * Invite user to Team
     *
     * @param int $agentId
     *
     * @return RedirectResponse
     */
    public function inviteAction($agentId)
    {
        $em = $this->getDoctrine()->getManager();

        $leader = $this->getUser();
        // Get Team Entity by Leader
        $team = $em->getRepository('LocalsBestUserBundle:Team')->findOneBy(['leader' => $leader]);

        if($team === null) {
            // Create new Team
            $team = new Team();
            $team->setLeader($leader);
            $em->persist($team);
        }
        // Get User Entity
        /** @var User $newAgent */
        $newAgent = $em->getRepository('LocalsBestUserBundle:User')->find($agentId);
        // Add User to Team
        $team->addAgent($newAgent);
        $newAgent->setTeam($team);
        // Save Changes
        $em->flush();
        // Show flash message
        $this->addFlash('success', 'Agent was added to you team successfully.');
        // Redirect user
        return $this->redirectToRoute('users_index');
    }

    /**
     * Remove user from Team
     *
     * @param int $agentId
     *
     * @return RedirectResponse
     */
    public function removeAction($agentId)
    {
        $em = $this->getDoctrine()->getManager();

        // Get User Entity
        /** @var User $agent */
        $agent = $em->getRepository('LocalsBestUserBundle:User')->find($agentId);
        // Unset User Team
        $agent->setTeam(null);
        // Update DB
        $em->flush();
        // Show flash message
        $this->addFlash('success', 'Agent was removed from you team successfully.');
        // Redirect user
        return $this->redirect($this->getRequest()->server->get('HTTP_REFERER'));
    }
}