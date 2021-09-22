<?php

namespace App\Command;

use App\Entity\UserNewsletter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Twig\Environment;

class SendNewsletterCommand extends Command
{
    protected static $defaultName = 'app:send-newsletter';
    protected static $defaultDescription = 'Send a email to all users newsletter' ;
    private $mailer;
    private $em;
    private $twig;
    private $limit;
    private $offset;

    public function __construct(EntityManagerInterface $entityManager,\Swift_Mailer $mailer, Environment $twig) {
        parent::__construct();
        $this->limit = 1;
        $this->offset = 0;
        $this->mailer = $mailer;
        $this->em = $entityManager;
        $this->twig = $twig;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('news_letter_id', InputArgument::REQUIRED, 'The newsletter id');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $arg1 = $input->getArgument('news_letter_id');

        if ($arg1) {
            $newsletter = $this->em->getRepository('App:Newsletter')->find($arg1);

            if ($newsletter){

                $io->note(sprintf('Newsletter found: %s', $arg1));

                do {
                    $subscribers = $this->em->getRepository('App:UserNewsletter')->findByNewsLetter($arg1, $this->limit, $this->offset);
                    $this->offset = $this->limit + $this->offset;
                    $this->em->clear();

                    $this->sendMail($subscribers);

                } while ($subscribers);


            }else{

                $io->note(sprintf('Newsletter not found: %s', $arg1));
            }

        }

        //$io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }

    private function sendMail($subscribers){

        foreach ( $subscribers as $subscriber ){

            $message = (new \Swift_Message('Newsletter '.$subscriber->getNews()->getName()))
                ->setTo($subscriber->getUser()->getEmail())
                ->setBody($this->twig->render(
                    'emails/newsletter.html.twig', [
                        'newsletter' => $subscriber->getNews()->getName(),
                        'prenom' => $subscriber->getUser()->getName(),

                    ]
                ), 'text/html');

            $this->mailer->send($message);
        }

    }
}
