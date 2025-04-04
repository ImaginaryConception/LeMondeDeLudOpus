<?php

namespace App\Controller;

use Exception;
use App\Entity\User;
use App\Entity\Panier;
use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\Reservation;
use App\Form\ContactFormType;
use App\Form\RegisterFormType;
use App\Form\AddArticleFormType;
use App\Form\AddCommentFormType;
use App\Form\EditProfilFormType;
use App\Form\ContactUserFormType;
use Doctrine\ORM\Query\Expr\Join;
use Symfony\Component\Mime\Email;
use App\Entity\ConfirmReservations;
use App\Form\ConfirmReserveFormType;
use App\Form\DeclineReserveFormType;
use App\Recaptcha\RecaptchaValidator;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;

class MainController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function home(ManagerRegistry $doctrine): Response
    {

        $repository = $doctrine->getRepository(Article::class);

        $articles = $repository->createQueryBuilder('a')
            ->orderBy('RAND()')
            ->setMaxResults(3)
            ->getQuery()
            ->execute()
        ;

        return $this->render('main/home.html.twig', [
            'articles' => $articles,
        ]);
    }

    #[Route('/mentions-legales/', name: 'mentions_legales')]
    public function mentionsLegales(ManagerRegistry $doctrine): Response
    {
        return $this->render('main/mentions_legales.html.twig');
    }

    #[Route('/contacter-utilisateur/{id}/', name: 'contact_user')]
    #[ParamConverter('user', options: ['mapping' => ['id' => 'id']])]
    #[IsGranted('ROLE_ADMIN')]
    public function contactUser(Request $request, User $user, MailerInterface $mailer): Response
    {

        if (!$this->isCsrfTokenValid('contact_user_' . $user->getId(), $request->query->get("csrf_token"))) {

            $this->addFlash('error', 'Token sécurité invalide, veuillez réessayer !');
        } else {

            $contact_user_form = $this->createForm(ContactUserFormType::class);

            $contact_user_form->handleRequest($request);

            if ($contact_user_form->isSubmitted() && $contact_user_form->isValid()) {

                $email = (new TemplatedEmail())
                ->from('support@lemondedeludopus.fr')
                ->to($user->getEmail(), 'anishamouche@gmail.com')
                ->priority(Email::PRIORITY_HIGH)
                ->subject('Le Monde de Lud\'Opus Contact : ' . $contact_user_form['object']->getData())
                ->textTemplate('emails/contact_user.txt.twig')
                ->htmlTemplate('emails/contact_user.html.twig')
                ->context([
                    'contact_user_form_email' => $contact_user_form['email']->getData(),
                    'contact_user_form_object' => $contact_user_form['object']->getData(),
                    'contact_user_form_message' => $contact_user_form['message']->getData(),
                ]);

                $mailer->send($email);

                $this->addFlash('success', 'Message envoyé avec succès !');

                return $this->redirectToRoute('home');

            }

        }

        return $this->render('main/contact_user.html.twig', [
            'contact_user_form' => $contact_user_form->createView(),
        ]);
    }

    #[Route('/suppression-commentaire/{id}/', name: 'comment_delete', priority: 10)]
    #[IsGranted('ROLE_ADMIN')]
    public function commentDelete(Comment $comment, ManagerRegistry $doctrine, Request $request): Response
    {

        if (!$this->isCsrfTokenValid('comment_delete_' . $comment->getId(), $request->query->get("csrf_token"))) {

            $this->addFlash('error', 'Token sécurité invalide, veuillez réessayer !');
        } else {

            $em = $doctrine->getManager();
            $em->remove($comment);
            $em->flush();

            $this->addFlash('success', 'Commentaire supprimé avec succès !');
        }

        return $this->redirectToRoute('article_view', [
            'id' => $comment->getArticle()->getId(),
        ]);
    }

    #[Route('/terminer-reservation/{id}/', name: 'finish_reservation')]
    #[ParamConverter('reservation', options: ['mapping' => ['id' => 'id']])]
    public function finishReserve(Reservation $reservation, Request $request, MailerInterface $mailer, ManagerRegistry $doctrine): Response
    {

        if (!$this->isCsrfTokenValid('finish_reserve_' . $reservation->getId(), $request->query->get("csrf_token"))) {

            $this->addFlash('error', 'Token sécurité invalide, veuillez réessayer !');

        } else {

                try{

                    $this->addFlash('success', 'La réservation a bien été terminée !');

                    $em = $doctrine->getManager();
                    $em->remove($reservation->getConfirmReservations());
                    $em->remove($reservation);
                    $em->flush();

                } catch (Exception $exception){

                    $this->addFlash('error', 'Désolé, un problème est survenu !');

                }

            }

            return $this->redirectToRoute('reservations');

        }

    #[Route('/confirmer-reservation/{id}/', name: 'confirm_reservation')]
    #[ParamConverter('reservation', options: ['mapping' => ['id' => 'id']])]
    public function confirmReserve(Reservation $reservation, Request $request, MailerInterface $mailer, ManagerRegistry $doctrine): Response
    {

        if (!$this->isCsrfTokenValid('confirm_reserve_' . $reservation->getId(), $request->query->get("csrf_token"))) {

            $this->addFlash('error', 'Token sécurité invalide, veuillez réessayer !');

        } else {

            $form = $this->createForm(ConfirmReserveFormType::class);

            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()){

                $email = (new TemplatedEmail())
                ->from('support@lemondedeludopus.fr')
                ->to($reservation->getUser()->getEmail(), 'anishamouche@gmail.com')
            	->priority(Email::PRIORITY_HIGH)
            	->subject('Le Monde de Lud\'Opus Reservations')
            	->textTemplate('emails/confirm_reserve.txt.twig')
               	->htmlTemplate('emails/confirm_reserve.html.twig')
                ->context([
                    'confirm_reserve_form_email' => $form['email']->getData(),
                    'confirm_reserve_form_instructions' => $form['instructions']->getData(),
                    'reservation_id' => $reservation->getId(),
                    'reservation_price' => $reservation->getArticle()->getPrice(),
            	]);

                $mailer->send($email);

                $this->addFlash('success', 'La réservation a été confirmée avec succès !');

               	$confirmReservations = new ConfirmReservations();

            	$em = $doctrine->getManager();

            	$confirmReservations->setReservation($reservation);

        		$em->persist($confirmReservations);
            	$em->flush();

                return $this->redirectToRoute('reservations');

            }

            return $this->render('main/confirm_contact.html.twig', [
                'confirm_form' => $form->createView(),
            ]);
        }
    }

    #[Route('/confirmer-all-reservation/{id}/', name: 'confirm_all_reservation')]
    #[ParamConverter('reservation', options: ['mapping' => ['id' => 'id']])]
    public function confirmAllReserve(Reservation $reservation, Request $request, MailerInterface $mailer, ManagerRegistry $doctrine): Response
    {

        $form = $this->createForm(ConfirmReserveFormType::class);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $email = (new TemplatedEmail())
            ->from('support@lemondedeludopus.fr')
            ->to($reservation->getUser()->getEmail(), 'anishamouche@gmail.com')
        	->priority(Email::PRIORITY_HIGH)
            ->subject('Le Monde de Lud\'Opus Reservations')
        	->textTemplate('emails/confirm_all_reserve.txt.twig')
           	->htmlTemplate('emails/confirm_all_reserve.html.twig')
            ->context([
                'confirm_reserve_form_email' => $form['email']->getData(),
                'confirm_reserve_form_instructions' => $form['instructions']->getData(),
                'reservation_id' => $reservation->getId(),
                'reservation_price' => $reservation->getArticle()->getPrice(),
        	]);

            $mailer->send($email);

            $this->addFlash('success', 'Toutes les réservations ont bien été confirmées avec succès !');

           	$confirmReservations = new ConfirmReservations();

        	$em = $doctrine->getManager();

        	$reservations = $em->getRepository(Reservation::class)->findAll();
            foreach ($reservations as $reservation) {
                $confirmReservations = new ConfirmReservations();
                $confirmReservations->setReservation($reservation);
                $em->persist($confirmReservations);
            }
            $em->flush();

            return $this->redirectToRoute('reservations');

        }

        return $this->render('main/confirm_contact.html.twig', [
            'confirm_form' => $form->createView(),
        ]);
    }

    #[Route('/decliner-all-reservation/{id}/', name: 'decline_all_reservation')]
    #[ParamConverter('reservation', options: ['mapping' => ['id' => 'id']])]
    public function declineAllReserve(Reservation $reservation, Request $request, MailerInterface $mailer, ManagerRegistry $doctrine): Response
    {

        $form = $this->createForm(DeclineReserveFormType::class);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $email = (new TemplatedEmail())
            ->from('support@lemondedeludopus.fr')
            ->to($reservation->getUser()->getEmail(), 'anishamouche@gmail.com')
            ->priority(Email::PRIORITY_HIGH)
            ->subject('Le Monde de Lud\'Opus Reservations')
            ->textTemplate('emails/decline_all_reserve.txt.twig')
            ->htmlTemplate('emails/decline_all_reserve.html.twig')
            ->context([
                'decline_reserve_form_email' => $form['email']->getData(),
                'decline_reserve_form_reason' => $form['reason']->getData(),
                'reservation_id' => $reservation->getId(),
                'reservation_price' => $reservation->getArticle()->getPrice(),
            ]);

            $em = $doctrine->getManager();
            $entities = $em->getRepository(Reservation::class)->findAll();
            foreach ($entities as $entity) {
                $em->remove($entity);
            }
            $em->flush();

            $mailer->send($email);

            $this->addFlash('success', 'Toutes les réservations ont été déclinées avec succès !');

            return $this->redirectToRoute('reservations');

        }

        return $this->render('main/decline_contact.html.twig', [
            'decline_form' => $form->createView(),
        ]);

    }

    #[Route('/decliner-reservation/{id}/', name: 'decline_reservation')]
    #[ParamConverter('reservation', options: ['mapping' => ['id' => 'id']])]
    public function declineReserve(Reservation $reservation, Request $request, MailerInterface $mailer, ManagerRegistry $doctrine): Response
    {

        if (!$this->isCsrfTokenValid('decline_reserve_' . $reservation->getId(), $request->query->get("csrf_token"))) {

            $this->addFlash('error', 'Token sécurité invalide, veuillez réessayer !');

        } else {

            $form = $this->createForm(DeclineReserveFormType::class);

            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()){

                $email = (new TemplatedEmail())
                ->from('support@lemondedeludopus.fr')
                ->to($reservation->getUser()->getEmail(), 'anishamouche@gmail.com')
                ->priority(Email::PRIORITY_HIGH)
                ->subject('Le Monde de Lud\'Opus Reservations')
                ->textTemplate('emails/decline_reserve.txt.twig')
                ->htmlTemplate('emails/decline_reserve.html.twig')
                ->context([
                    'decline_reserve_form_email' => $form['email']->getData(),
                    'decline_reserve_form_reason' => $form['reason']->getData(),
                ]);

                $em = $doctrine->getManager();
                $em->remove($reservation);
                $em->flush();

                $mailer->send($email);

                $this->addFlash('success', 'La réservation a été déclinée avec succès !');

                return $this->redirectToRoute('reservations');

            }

            return $this->render('main/decline_contact.html.twig', [
                'decline_form' => $form->createView(),
            ]);

        }

    }

    #[Route('/article/{id}/', name: 'article_view')]
    #[ParamConverter('article', options: ['mapping' => ['id' => 'id']])]
    public function articleView(Article $article, Request $request, ManagerRegistry $doctrine): Response
    {

        if (!$this->getUser()) {
            return $this->render('main/article_view.html.twig', [
                'article' => $article,
            ]);
        }

        $comment = new Comment();

        $commentForm = $this->createForm(AddCommentFormType::class, $comment);

        $commentForm->handleRequest($request);

        if ($commentForm->isSubmitted() && $commentForm->isValid()) {

            $comment
                ->setPublicationDate(new \DateTime())
                ->setAuthor($this->getUser())
                ->setArticle($article)
            ;

           try{

               $em = $doctrine->getManager();
               $em->persist($comment);
               $em->flush();

               $this->addFlash('success', 'Commentaire publié avec succès !');

           }catch (\Exception $exception){

               $this->addFlash('error', 'Désolé, un problème est survenu !');

           }

            unset($comment);
            unset($commentForm);

            $comment = new Comment();
            $commentForm = $this->createForm(AddCommentFormType::class, $comment);
        }

        return $this->render('main/article_view.html.twig', [
            'article' => $article,
            'commentForm' => $commentForm->createView(),
        ]);

    }

    #[Route('/utilisateurs/', name: 'users')]
    public function users(ManagerRegistry $doctrine): Response
    {

        if(!$this->isGranted('ROLE_ADMIN')){

            $this->addFlash('error', 'Vous n\'êtes pas autorisé à accéder à cette page !');

            return $this->redirectToRoute('home');

        } else{

            $repository = $doctrine->getRepository(User::class);

            $users = $repository->findAll();

            return $this->render('main/users_list.html.twig', [
                'users' => $users,
            ]);

        }

    }

    #[Route('/ajouter-un-article/', name: 'add_article')]
    public function addArticle(Request $request, ManagerRegistry $doctrine): Response
    {

        if(!$this->isGranted('ROLE_ADMIN')){

            $this->addFlash('error', 'Vous n\'êtes pas autorisé à accéder à cette page !');

            return $this->redirectToRoute('home');

        } else{

            $article = new Article();

            $form = $this->createForm(AddArticleFormType::class, $article);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()){

                if(!$form->get('photo')->getData() == null){

                    $photo = $form->get('photo')->getData();

                    $newFileName = md5(time() . rand() . uniqid()) . '.' . $photo->guessExtension();

                    $article
                        ->setPhoto($newFileName)
                    ;

                    $photo->move(
                        $this->getParameter('app.user.photo.directory'),
                        $newFileName
                    );

                }

                $em = $doctrine->getManager();

                $em->persist($article);

                $em->flush();

                $this->addFlash('success', 'Article publié avec succès !');

                return $this->redirectToRoute('add_article');

            }

            return $this->render('main/add_article.html.twig', [
                'addarticle_form' => $form->createView(),
            ]);

        }

    }

    #[Route('/horaires-acces/', name: 'clock')]
    public function clock(): Response
    {
        return $this->render('main/clockAndAccess.html.twig');
    }

    #[Route('/contact/', name: 'contact')]
    public function contact(Request $request, MailerInterface $mailer): Response
    {

        $contact_form = $this->createForm(ContactFormType::class);

         $contact_form->handleRequest($request);

         if ($contact_form->isSubmitted() && $contact_form->isValid()) {

             $email = (new TemplatedEmail())
            ->from('support@lemondedeludopus.fr')
            ->to('support@lemondedeludopus.fr', 'anishamouche@gmail.com')
             ->priority(Email::PRIORITY_HIGH)
             ->subject('Le Monde de Lud\'Opus Contact : ' . $contact_form['object']->getData())
             ->textTemplate('emails/contact.txt.twig')
             ->htmlTemplate('emails/contact.html.twig')
             ->context([
                 'contact_form_firstname' => $contact_form['firstname']->getData(),
                 'contact_form_lastname' => $contact_form['lastname']->getData(),
                'contact_form_object' => $contact_form['object']->getData(),
                 'contact_form_message' => $contact_form['message']->getData(),
                'contact_form_email' => $contact_form['email']->getData(),
             ]);

            $mailer->send($email);

            $this->addFlash('success', 'Message envoyé avec succès !');

            return $this->redirectToRoute('home');

        }

        return $this->render('main/contact.html.twig', [
            'contact_form' => $contact_form->createView(),
        ]);
    }

    #[Route('/modification-article/{id}/', name: 'edit_article', priority: 10)]
    #[ParamConverter('article', options: ['mapping' => ['id' => 'id']])]
    #[IsGranted('ROLE_ADMIN')]
    public function publicationEdit(Article $article, Request $request, ManagerRegistry $doctrine): Response
    {

        $form = $this->createForm(AddArticleFormType::class, $article);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $photo = $form->get('photo')->getData();

            if(!empty($photo)){

                if(
                    $article->getPhoto() != null &&
                    file_exists( $this->getParameter('app.user.photo.directory') . $article->getPhoto())
                ){

                    unlink( $this->getParameter('app.user.photo.directory') . $article->getPhoto() );
                }

                do {

                    $newFileName = md5(random_bytes(100)) . '.' . $photo->guessExtension();
                } while (file_exists($this->getParameter('app.user.photo.directory') . $newFileName));

                $photo->move(
                    $this->getParameter('app.user.photo.directory'),
                    $newFileName
                );

                $article->setPhoto($newFileName);

            }

            $em = $doctrine->getManager();
            $em->flush();

            $this->addFlash('success', 'Article modifié avec succès !');

            return $this->redirectToRoute('article_view', [
                'id' => $article->getId(),
            ]);

        }

        return $this->render('main/article_edit.html.twig', [
            'article_edit_form' => $form->createView(),
        ]);
    }

    #[Route('/suppression-utilisateur/{id}/', name: 'delete_user', priority: 10)]
    #[ParamConverter('user', options: ['mapping' => ['id' => 'id']])]
    #[IsGranted('ROLE_ADMIN')]
    public function deleteUser(User $user, Request $request, ManagerRegistry $doctrine): Response
    {

        if(!$this->isCsrfTokenValid('delete_user_' . $user->getId(), $request->query->get('csrf_token'))){

            $this->addFlash('error', 'Token sécurité invalide, veuillez réessayer.');

        } else {

            $em = $doctrine->getManager();
            $em->remove($user);
            $em->flush();

            $this->addFlash('success', 'L\'utilisateur a été supprimé avec succès !');
        }

        return $this->redirectToRoute('users');
    }

    #[Route('/suppression-article/{id}/', name: 'delete_article', priority: 10)]
    #[ParamConverter('article', options: ['mapping' => ['id' => 'id']])]
    #[IsGranted('ROLE_ADMIN')]
    public function deleteArticle(Article $article, Request $request, ManagerRegistry $doctrine): Response
    {

        if(!$this->isCsrfTokenValid('delete_article_' . $article->getId(), $request->query->get('csrf_token'))){

            $this->addFlash('error', 'Token sécurité invalide, veuillez réessayer.');

        } else {

            $em = $doctrine->getManager();
            $em->remove($article);
            $em->flush();

            $this->addFlash('success', 'L\'article a été supprimé avec succès !');
        }

        return $this->redirectToRoute('library');
    }

    #[Route('/annulation-reservation/{id}/', name: 'cancelReservation')]
    #[ParamConverter('reservation', options: ['mapping' => ['id' => 'id']])]
    public function cancelReservation(Reservation $reservation, MailerInterface $mailer, ManagerRegistry $doctrine): Response
    {

        $email = (new TemplatedEmail())
            ->from('support@lemondedeludopus.fr')
            ->to('support@lemondedeludopus.fr', 'anishamouche@gmail.com')
            ->priority(Email::PRIORITY_HIGH)
            ->subject('Le Monde de Lud\'Opus Reservations')
            ->textTemplate('emails/cancel_reserve.txt.twig')
            ->htmlTemplate('emails/cancel_reserve.html.twig')
            ->context([
                'user_firstname' => $reservation->getUser()->getFirstname(),
                'user_lastname' => $reservation->getUser()->getLastname(),
                'user_email' => $reservation->getUser()->getEmail(),
                'reservation_id' => $reservation->getId(),
                'reservation_price' => $reservation->getArticle()->getPrice(),
                'reservation_title' => $reservation->getArticle()->getTitle(),
                'reservation_author' => $reservation->getArticle()->getAuthor(),
                'reservation_edition' => $reservation->getArticle()->getEdition(),
            ]);

        $mailer->send($email);

        $entityManager = $doctrine->getManager();

        $entityManager->remove($reservation);

        $entityManager->flush();

        $this->addFlash('success', 'Réservation annulée avec succès !');

        return $this->redirectToRoute('reservation_list');
    }

    #[Route('/reservation/{id}/', name: 'reservation')]
    #[ParamConverter('article', options: ['mapping' => ['id' => 'id']])]
    public function reservation(Article $article): Response
    {

        if(!$this->isGranted('ROLE_USER')){

            $this->addFlash('error', 'Vous devez être connecté pour ajouter un livre !');

            return $this->redirectToRoute('login');

        }

        return $this->render('main/reservation.html.twig', [
            'article' => $article,
            'user' => $this->getUser(),
        ]);
    }

    #[Route('/ajouter/{id}/{userId}/', name: 'reservationExec')]
    #[ParamConverter('reservation', options: ['mapping' => ['id' => 'id']])]
    #[ParamConverter('user', options: ['mapping' => ['userId' => 'id']])]
    public function addExec(User $user, Article $article, ManagerRegistry $doctrine): Response
    {

        if(!$this->isGranted('ROLE_USER')){

            $this->addFlash('error', 'Vous devez être connecté pour réserver un livre !');

            return $this->redirectToRoute('login');

        } else{

            $this->addFlash('success', 'Cet article a été ajouté dans votre panier !');

            $entityManager = $doctrine->getManager();

            $reservation = new Panier();

            $reservation->setUser($user);

            $reservation->setArticle($article);

            $entityManager->persist($reservation);

            $entityManager->flush();

            return $this->redirectToRoute('reservation_list');
        }
    }

    #[Route('/reserver/{id}/{userId}/', name: 'reservationExec2')]
    #[ParamConverter('reservation', options: ['mapping' => ['id' => 'id']])]
    #[ParamConverter('user', options: ['mapping' => ['userId' => 'id']])]
    public function reservationExec(User $user, Article $article, MailerInterface $mailer, ManagerRegistry $doctrine): Response
    {

        if(!$this->isGranted('ROLE_USER')){

            $this->addFlash('error', 'Vous devez être connecté pour réserver un livre !');

            return $this->redirectToRoute('login');

        } else{

            $this->addFlash('success', 'Cet article a été ajouté dans votre panier !');

            $entityManager = $doctrine->getManager();

            $reservation = new Reservation();

            $reservation->setUser($user);

            $reservation->setArticle($article);

            $entityManager->persist($reservation);

            $entityManager->flush();

            return $this->redirectToRoute('reservation_list');
        }
    }

    #[Route('/reservations/', name: 'reservations')]
    public function reservations(ManagerRegistry $doctrine): Response
    {

        if(!$this->isGranted('ROLE_ADMIN')){

            $this->addFlash('error', 'Vous n\'êtes pas autorisé à accéder à cette page !');

            return $this->redirectToRoute('home');

        } else{

            $repository = $doctrine->getRepository(Reservation::class);

            $reservations = $repository->findAll();

            $repositoryConfirm = $doctrine->getRepository(ConfirmReservations::class);

            $allConfirmReservations = $repositoryConfirm->findAll();

            return $this->render('main/reservations_admin.html.twig', [
                'reservations' => $reservations,
                'confirmReservations' => $allConfirmReservations,
            ]);
        }
    }

    #[Route('/panier/', name: 'reservation_list')]
    public function myReserve(ManagerRegistry $doctrine): Response
    {

        if(!$this->isGranted('ROLE_USER')){

            $this->addFlash('error', 'Vous devez être connecté pour accéder à cette page !');

            return $this->redirectToRoute('login');

        } else{

            $repository = $doctrine->getRepository(Reservation::class);

            $reservations = $repository->createQueryBuilder('r')
                ->select('r')
                ->where('r.user = :id')
                ->setParameter('id', $this->getUser()->getId())
                ->getQuery()
                ->execute()
            ;

            return $this->render('main/reservation_list.html.twig', [
                'reservations' => $reservations,
            ]);
        }
    }

    #[Route('/modifier-profil/{id}/', name: 'edit_profil')]
    #[ParamConverter('user', options: ['mapping' => ['id' => 'id']])]
    public function editProfil(User $user, ManagerRegistry $doctrine, Request $request): Response
    {

        if(!$this->isGranted('ROLE_USER')){

            $this->addFlash('error', 'Vous devez être connecté pour accéder à cette page !');

            return $this->redirectToRoute('login');

        } else{

            $form = $this->createForm(EditProfilFormType::class, $user);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()){

                $em = $doctrine->getManager();
                $em->flush();

                $this->addFlash('success', 'Profil modifié avec succès !');

                return $this->redirectToRoute('profil');

            }

            return $this->render('main/edit_profil.html.twig', [
                'edit_profil_form' => $form->createView(),
            ]);
        }
    }

    #[Route('/profil/', name: 'profil')]
    public function profil(): Response
    {

        if(!$this->isGranted('ROLE_USER')){

            $this->addFlash('error', 'Vous n\'êtes pas connecté');

            return $this->redirectToRoute('login');

        } else{

            $user = $this->getUser();

            return $this->render('main/profil.html.twig', [
                'user' => $user,
            ]);
        }
    }

    #[Route('/bibliotheque/', name: 'library')]
    public function library(ManagerRegistry $doctrine, Request $request, PaginatorInterface $paginator): Response
    {

        $requestedPage = $request->query->getInt('page', 1);

        if ($requestedPage < 1) {
            throw new NotFoundHttpException();
        }

        $em = $doctrine->getManager();

        $query = $em->createQuery('SELECT a FROM App\Entity\Article a');

        $articles = $paginator->paginate(
            $query,
            $requestedPage,
            10,
        );

        return $this->render('main/library.html.twig', [
            'articles' => $articles,
        ]);
    }

    #[Route('/recherche/', name: 'article_search')]
    public function search(ManagerRegistry $doctrine, Request $request, PaginatorInterface $paginator): Response
    {
        // Récupération de $_GET['page'], 1 si elle n'existe pas
        $requestedPage = $request->query->getInt('page', 1);

        // Vérification que le nombre est positif

        if($requestedPage < 1 ){
            throw new NotFoundHttpException();
        }

        // On récupère la recherche de l'utilisateur depuis l'URL ( $_GET['search'] )
        $search = $request->query->get('search', '');

        $em = $doctrine->getManager();

        //Création de la requête de recherche
        $query = $em
            ->createQuery('SELECT a FROM App\Entity\Article a WHERE a.title LIKE :search OR a.author LIKE :search OR a.edition LIKE :search OR a.genre LIKE :search OR a.other LIKE :search OR a.price LIKE :search')
            ->setParameters([
                'search' => '%' . $search . '%'
            ]);

        $articles = $paginator->paginate(
            $query,     // Requête créée juste avant
            $requestedPage,     // Page qu'on souhaite voir
            10,     // Nombre d'article à afficher par page
        );

        return $this->render('main/article_search.html.twig', [
            'articles' => $articles,
        ]);
    }

}
