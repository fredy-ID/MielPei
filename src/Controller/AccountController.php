<?php

namespace App\Controller;

use App\Entity\User;
use Twig\Cache\NullCache;
use App\Entity\ProducerRequest;
use App\Repository\ProducerRepository;
use App\Repository\ProducerRequestRepository;
use Symfony\Component\Mime\Address;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class AccountController extends AbstractController
{
    /**
     * @Route("/profile/info", name="profile_info")
     */
    public function user(SerializerInterface $serializer): Response
    {
        $userData = $this->getDoctrine()->getRepository(User::class)->find($this->getUser()->getid());

        $user = $serializer->serialize(
            $userData,
            'json', ['groups' => ['user']]
        );

        return $this->json([
            'user' => json_decode($user)
        ]);
    }

    /**
     * @Route("/modify-profile/{firstName}/{lastName}/{adress}/{secAdress}/{postcode}/{region}/{country}/{number}", name="profile")
     */
    public function index(ValidatorInterface $validator,$firstName, $lastName, $adress, $secAdress, $postcode, $region, $country, $number): Response
    {
        if(($firstName == '' || $lastName == '') || ($firstName == null || $lastName == null)) return $this->json([
            'erreur' => 'Valeurs incorrectes',
            'msg' => 'Valeurs incorrectes'
        ]);

        $current_user = $this->getUser();

        $user = $this->getDoctrine()->getRepository(User::class)->find($current_user->getId());

        $user->setFirstName($firstName);
        $user->setLastName($lastName);

        $user->setAdress($adress);
        $user->setSecAdress($secAdress);
        $user->setPostcode(strval ($postcode));
        $user->setRegion($region);
        $user->setCountry($country);
        $user->setNumber(strval ($number));
        

        $errors = $validator->validate($user);
        
        if (count($errors) > 0) {
            /*
             * Uses a __toString method on the $errors variable which is a
             * ConstraintViolationList object. This gives us a nice string
             * for debugging.
             */
            $errorsString = (string) $errors;

            return $this->json([
                'erreur' => $errorsString,
                'msg' => "Erreur lors de la modification de l'utilisateur"
            ]);
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json([
            'succès' => 'Enregistré avec succès',
            'msg' => "Modifications enregistrés avec succès"
        ]);
    }

    /**
     * @Route("/account/producer-request/{name}/{description}/{phoneNumber}", name="producer-request_new", methods={"POST"})
     */
    public function new(ValidatorInterface $validator, $name, $description, $phoneNumber, ProducerRequestRepository $producerRequestRepository): Response
    {
        
        if(($name == '' || $phoneNumber == '') || $name === null) return $this->json([
            'erreur' => 'Valeurs incorrectes',
            'msg' => 'Valeurs incorrectes'
        ]);
        
        if($producerRequestRepository->findOneBy(["user" => $this->getUser()]) != null) {
            return $this->json([
                'requestExists' => 'La requête existe',
                'msg' => "Vous avez déjà formulé une demande."
            ]);
        }

        $_INITIAL_STATE = 'En attente';

        $p_request = new ProducerRequest();

        $p_request->setName($name);
        $p_request->setDescription($description);
        $p_request->setUser($this->getUser());
        $p_request->setPhoneNumber($phoneNumber);
        $p_request->setState($_INITIAL_STATE);
        $p_request->setCreatedAt(new \DateTime("now"));
        $p_request->setUpdatedAt(new \DateTime("now"));

        $errors = $validator->validate($p_request);
        
        if (count($errors) > 0) {
            /*
             * Uses a __toString method on the $errors variable which is a
             * ConstraintViolationList object. This gives us a nice string
             * for debugging.
             */
            $errorsString = (string) $errors;

            return $this->json([
                'erreur' => $errorsString,
                'msg' => "Erreur lors de l'enregistrement de la requête"
            ]);
        }

        // validate email confirmation link, sets User::isVerified=true and persists
        // try {
        //     // generate a signed url and email it to the user
        //     $this->emailVerifier->sendEmailConfirmation(
        //         $request,
        //         $user,
        //         (new TemplatedEmail())
        //             ->from(new Address('fredy.dev.master@gmail.com', 'MielPéï - Ne pas répondre'))
        //             ->to($user->getEmail())
        //             ->subject('Une nouvelle requête a été établie')
        //             ->htmlTemplate('<html>Nouvelle requête !</html>')
        //     );
        // } catch (VerifyEmailExceptionInterface $exception) {

        //     return $this->json([
        //         'succès' => 'Une exception a été levée',
        //         'msg' => "Erreur lors de l'envois de l'email"
        //     ]);
        // }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($p_request);
        $entityManager->flush();



        return $this->json([
            'success' => 'Enregistré avec succès',
            'msg' => "La requête a été envoyé avec succès"
        ]);
       
    }


    /**
     * @Route("/send-email", name="send_email")
     */
    public function sendVerificationEmail() {

        $user = $this->getUser();

        $lastSentDate = $user->getEmailSentAt();
        $currentTime = new \DateTime("now");

        if ($lastSentDate !== null) {
            $diff = $lastSentDate->diff($currentTime);
            $days = $diff->d;
            $hours = $diff->h;
            $minutes = $diff->i;

            if ($days === 0 && $hours === 0 && $minutes < 30) {
                return $this->json(['already_sent_email' => "Vous avez déjà envoyé un mail de vérification. Veuillez reessayer plus tard."], 200);
            }
        }

        try
        {
            // generate a signed url and email it to the user
            $this->emailVerifier->sendEmailConfirmation(
                'dashboard_verify_email',
                $user,
                (new TemplatedEmail())
                    ->from(new Address('fredy.dev.master@gmail.com', 'C2L Dashboard - Ne pas répondre'))
                    ->to($user->getEmail())
                    ->subject('tableau de bord C2L - Confirmez votre email')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );
            // do anything else you need here, like send an email

        } catch (\Exception $error) {

            return $this->json([
                'error' => $error
            ], 200);

        }
        
        $entityManager = $this->getDoctrine()->getManager();
        $user->setEmailSentAt(new \DateTime("now"));
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json([
            'success' => 'send email successfull'
        ], 200);
    }

    /**
     * @Route("/producer/update", name="update_producer")
     */
    public function updateProducer(Request $request) {
        $description = $request->query->get('description');

        $producer = $this->getUser()->getProducerProfile();
        $producer->setIntroduction($description);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($producer);
        $entityManager->flush();

        return $this->json([
            'msg' => 'success',
        ], 200);
    }   

}
