<?php

namespace App\Controller;

use App\Entity\MyUser;
use App\Entity\User;
use App\Entity\UserNewsletter;
use App\Entity\UsersNewsletter;
use App\Form\NewsletterType;
use App\Form\UserType;
use Swift_Mailer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class NewsletterController extends AbstractController
{

    protected $swiftMailer;

    public function __construct(Swift_Mailer $swiftMailer){
        $this->swiftMailer = $swiftMailer;
    }

    /**
     * @Route("/", name="user")
     */
    public function index(Request $request): Response
    {

        $user = new MyUser();
        $form = $this->createForm(UserType::class, $user, ['validation_groups'=>['New',"Default"]]);

        $form->handleRequest($request);
        if ( $form->isSubmitted() && $form->isValid()) {

            $token = new UsernamePasswordToken($user->getUser(), '', 'main', $user->getUser()->getRoles());
            $this->get('security.token_storage')->setToken($token);

            return $this->redirectToRoute('newsletter');
        }

        return $this->render('users/index.html.twig', [
            'controller_name' => 'NewsletterController',
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/newsletter", name="newsletter")
     */
    public function newsletter(Request $request): Response
    {

        $subscribe = new UserNewsletter();
        $subscribe->setUser($this->getUser());
        $form = $this->createForm(NewsletterType::class, $subscribe, ['validation_groups'=>["Default"]]);

        $form->handleRequest($request);
        if ( $form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($subscribe);
            $em->flush();

            $this->sendValidationMail($subscribe);

        }

        return $this->render('newsletter/index.html.twig', [
            'controller_name' => 'NewsletterController',
            'form' => $form->createView(),
        ]);
    }

    private function sendValidationMail(UserNewsletter $subscribe){

        $message = (new \Swift_Message('Registration'))
            ->setTo($subscribe->getUser()->getEmail())
            ->setBody(
                $this->renderView(
                    'emails/registration.html.twig',
                    ['newsletter' => $subscribe->getNews()->getName()]
                ),
                'text/html'
            );

        $this->swiftMailer->send($message);


    }

}
