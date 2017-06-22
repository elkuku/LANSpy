<?php

namespace AppBundle\Command;

use AppBundle\Entity\Host;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class RmCommand
 * @package AppBundle\Command
 */
class MapTestCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('maptest')
            ->setDescription('Scan');
            //->addArgument('project', InputArgument::REQUIRED, 'The project name');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $myIp = trim(shell_exec('hostname -I'));

        $parts = explode('.', $myIp);

        if (count($parts) !== 4) {
            throw new \UnexpectedValueException('Unable to determine ip');
        }

        $ipRange = '192.168.'.$parts[2].'.0-255';

        //$io->text(sprintf('Starting nmap scan on %s...', $ipRange));

        $command = 'nmap -sn '.$ipRange;

        $s = shell_exec($command);

        //echo $s;

        $lines = explode("\n", trim($s));

        if (!count($lines)) {
            throw new \DomainException('The nmap command failed');
        }

        if (0 !== strpos($lines[0], 'Starting Nmap')) {
            throw new \UnexpectedValueException($lines[0]);
        }

        $hosts = [];
        $host = new Host();

        foreach ($lines as $i => $line) {
            if (preg_match('/Nmap scan report for (.+) \((.+)\)/', $line, $matches)) {
                $host = new Host();

                $host->hostname = $matches[1];
                $host->ip = $matches[2];
            } elseif (preg_match('/Nmap scan report for (.+)/', $line, $matches)) {
                $host = new Host();

                $host->ip = $matches[1];
            }

            // MAC Address: CC:B2:55:FC:88:46 (D-Link International)
            if (preg_match('/MAC Address: (.{17}) \((.+)\)/', $line, $matches)) {
                $host->mac = $matches[1];
                $host->vendor = $matches[2];

                $hosts[] = $host;
            } elseif (preg_match('/MAC Address: (.+)/', $line, $matches)) {
                $host->mac = $matches[1];

                $hosts[] = $host;
            }
        }

       // echo json_encode($hosts);
        $outputFile = $this->getContainer()->get('kernel')->getProjectDir().'/results/maptest-'.(new \DateTime())->format('Y-m-d').'.txt';

        $response = new \stdClass();

        $response->time = (new \DateTime())->format('H:i');
        $response->known = [];
        $response->unknown = $hosts;

        $io->text('write to '.$outputFile);

        $fs = new Filesystem();
        $fs->appendToFile($outputFile, json_encode($response)."\n");
    }

    private function text(string $message)
    {

    }
}
