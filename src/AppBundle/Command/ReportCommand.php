<?php

namespace AppBundle\Command;

use AppBundle\Entity\Host;
use AppBundle\Service\MapTest;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class ReportCommand
 * @package AppBundle\Command
 */
class ReportCommand extends ContainerAwareCommand
{
    /**
     * @var MapTest
     */
    private $mapTest;

    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var string
     */
    private $emailSender;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * ReportCommand constructor.
     *
     * @param MapTest                $mapTest
     * @param \Swift_Mailer          $mailer
     * @param EntityManagerInterface $entityManager
     * @param string                 $emailSender
     */
    public function __construct(
        MapTest $mapTest,
        \Swift_Mailer $mailer,
        EntityManagerInterface $entityManager,
        string $emailSender
    ) {
        $this->mapTest       = $mapTest;
        $this->mailer        = $mailer;
        $this->emailSender   = $emailSender;
        $this->entityManager = $entityManager;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('app:report')
            ->setDescription('Hello PhpStorm');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dateStart = new \DateTime('midnight');
        $dateEnd   = new \DateTime();

        $tests   = $this->mapTest->readTests($dateStart, $dateEnd);
        $macList = [];

        if ($tests) {
            $macs = $this->mapTest->getMacs($tests);

            $macTests = reset($macs);

            $knownHosts = $this->entityManager
                ->getRepository(Host::class)
                ->findMACs();

            foreach ($macTests as $times) {
                $mac = reset($times);

                if (in_array($mac->mac, $knownHosts)) {
                    continue;
                }

                $macList[] = [$mac->mac, $mac->vendor.$mac->hostname];
            }
        }

        $bufferedOutput = new BufferedOutput();
        $io             = new SymfonyStyle($input, $bufferedOutput);
        $hostName       = trim(shell_exec('hostname'));
        $ip             = trim(shell_exec('hostname -I'));
        $subject = sprintf('%d - LANSpy report from %s %s', count($macList), $hostName, $ip);

        $io->title($subject);
        $io->text($dateStart->format('Y-m-d H:i:s').' - '.$dateEnd->format('Y-m-d H:i:s'));

        if ($macList) {
            $io->table(
                ['MAC', 'Vendor', 'Hostname'],
                $macList
            );
        } else {
            $io->text('No se han detectado intrusos =:)');
        }

        $content = $bufferedOutput->fetch();

        $output->write($content);

        $this->sendReport($subject, $content, $this->emailSender);
    }

    /**
     * Sends the given $contents to the $recipient email address.
     *
     * @param string $subject
     * @param string $contents
     * @param string $recipient
     */
    private function sendReport(string $subject, string $contents, string $recipient)
    {
        $message = $this->mailer->createMessage()
            ->setSubject($subject)
            ->setFrom($this->emailSender)
            ->setTo($recipient)
            ->setBody($contents, 'text/plain');

        $this->mailer->send($message);
    }
}
