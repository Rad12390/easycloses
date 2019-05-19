<?php

namespace LocalsBest\UserBundle\Command;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class PdfSplitterClearCommand extends ContainerAwareCommand
{
    protected $em;

    protected function configure()
    {
        $this
            ->setName('pdf-splitter:clear')
            ->setDescription('Will clear files that used for PDF Splitter')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Create Filesystem object
        $fileSystem = new Filesystem();

        // Get global path to /app folder
        $appPath = $this->getContainer()->getParameter('kernel.root_dir');

        // Change /app part to /web
//        $path = str_replace('/app', '/web/uploads/transaction', $appPath);
        $path = "/data/www/app.easycloses.com/public_html/web/uploads/transaction";

        // Create Finder object
        $finder = new Finder();

        // Get all files and directories inside directory
        $finder->in($path);

        // Get date 1 month before
        $thirtyDaysAgo = strtotime('-30 days');

        // Check file by file
        foreach (iterator_to_array($finder, true) as $file) {

            // If it's file with images of pdf page => remove it
            if (strpos($file->getRealPath(), '/thumbs/')) {
                $fileSystem->remove(array($file->getRealPath()));
            } else {
                // if file was created more then 1 month ago => remove it
                if( $thirtyDaysAgo >= filectime($file->getRealPath()) ) {
                    $fileSystem->remove(array($file->getRealPath()));
                }
            }
        }
        // Show message about end of script
        $output->writeln('done');
    }
}