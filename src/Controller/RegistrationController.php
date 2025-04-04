<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    #[Route('/inscription/', name: 'register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, MailerInterface $mailer, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
            $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Votre compte a bien été créé !');

            $email = (new TemplatedEmail())
                ->from('support@lemondedeludopus.fr')
                ->to($user->getEmail(), 'anishamouche@gmail.com')
                ->priority(Email::PRIORITY_HIGH)
                ->subject('Le Monde de Lud\'Opus')
                ->textTemplate('emails/helloRegister.txt.twig')
                ->htmlTemplate('emails/helloRegister.html.twig')
                ->context([
                    'user_email' => $user->getEmail(),
                    'user_firstname' => $user->getFirstname(),
                    'user_lastname' => $user->getLastname(),
                ]);

            $mailer->send($email);

            $email = (new TemplatedEmail())
                ->from('support@lemondedeludopus.fr')
                ->to('support@lemondedeludopus.fr', 'anishamouche@gmail.com')
                ->priority(Email::PRIORITY_HIGH)
                ->subject('Le Monde de Lud\'Opus')
                ->textTemplate('emails/register.txt.twig')
                ->htmlTemplate('emails/register.html.twig')
                ->context([
                    'user_email' => $user->getEmail(),
                    'user_firstname' => $user->getFirstname(),
                    'user_lastname' => $user->getLastname(),
                ]);

            $mailer->send($email);

            return $this->redirectToRoute('login');
        }

        return $this->render('registration/register.html.twig', [
            'register_form' => $form->createView(),
        ]);
    }
}
