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

    private $mailer;
    private $emailSender;
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
//        $dateStart = $request->request->get('date_start', (new \DateTime())->format('Y-m-d'));
//        $dateEnd   = $request->request->get('date_end', (new \DateTime())->format('Y-m-d'));
        $dateStart = new \DateTime('midnight');
        $dateEnd   = new \DateTime();

        $tests = $this->mapTest->readTests($dateStart, $dateEnd);

        $macs = $this->mapTest->getMacs($tests);

        $macTests = reset($macs);
        $macList  = [];

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

        $bufferedOutput = new BufferedOutput();
        $io             = new SymfonyStyle($input, $bufferedOutput);

        $io->title('Hosts report');
        $io->text($dateStart->format('Y-m-d'));

        if ($macList) {
            $io->table(
                ['MAC', 'Vendor', 'Hostname'],
                $macList
            );
        } else {
            $io->text('No se han detectado intrusos :)');
        }

        $content = $bufferedOutput->fetch();

        $output->write($content);

        $this->sendReport($content, $this->emailSender);
    }

    /**
     * Sends the given $contents to the $recipient email address.
     *
     * @param string $contents
     * @param string $recipient
     */
    private function sendReport($contents, $recipient)
    {
        // See https://symfony.com/doc/current/cookbook/email/email.html
        $message = $this->mailer->createMessage()
            ->setSubject(sprintf('app:list-users report (%s)', date('Y-m-d H:i:s')))
            ->setFrom($this->emailSender)
            ->setTo($recipient)
            ->setBody($contents, 'text/plain');

        $this->mailer->send($message);
    }
}
