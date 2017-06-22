<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class ConvertCommand
 * @package AppBundle\Command
 */
class ConvertCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('app:convert')
            ->setDescription('Hello PhpStorm')
            ->addArgument('file', InputArgument::REQUIRED);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $path = $input->getArgument('file');

        $fs = new Filesystem();


        if (false == $fs->exists($path)) {
            throw new \UnexpectedValueException('File not found '.getcwd());
        }

        $lines = file($path);
        $newLines = [];

        $parts = explode(DIRECTORY_SEPARATOR, $path);

        $fileName = end($parts);

        if (0 === strpos($fileName, 'maptest')) {
            $response = new \stdClass();

            foreach ($lines as $line) {
                $response->time = substr($line, 0, 5);
                $response->known = [];
                $response->unknown = json_decode(substr($line, 6));

                $newLines[] = json_encode($response);
            }

            //var_dump($newLines);

            $fs->dumpFile($path, implode("\n", $newLines));
        }
    }
}
