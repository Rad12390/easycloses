<?php

namespace NimbleImps\DeployBundle\Controller;

use RuntimeException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Process\Process;

class DeployController extends Controller
{
    protected $commands = [
        '/usr/local/bin/php app/console cache:clear --env=prod --no-debug',
        '/usr/local/bin/php app/console assets:install --env=prod --no-debug',
        '/usr/local/bin/php app/console assets:install --env=prod',
    ];

    /**
     * @param Request $request
     * @Route("/deploy", name="deploy")
     * @Template("@NimbleImps/default/index.html.twig")
     * @return array
     */
    public function indexAction(Request $request)
    {
        chdir($this->get('kernel')->getRootDir() . '/..');
        $this->process();
        exit;
    }

    protected function process($index = 0)
    {
        $process = new Process($this->commands[$index]);

        $process->run(function ($type, $buffer) {
            echo $buffer . '<br />' . PHP_EOL;
        });

        if (!$process->isSuccessful()) {
            throw new RuntimeException($process->getErrorOutput());
            exit;
        }

        if (isset($this->commands[$index + 1])) {
            return $this->process($index + 1);
        }
    }
}
