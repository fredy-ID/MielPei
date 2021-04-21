<?php

namespace App\Controller;

use Knp\Snappy\Pdf;
use App\Entity\Cart;
use App\Entity\Command;
use App\Form\CommandType;
use App\Repository\CommandRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/command")
 */
class CommandController extends AbstractController
{
    private $invoiceFilesDirectory;

    public function __construct($invoiceFilesDirectory) {
        $this->invoiceFilesDirectory = $invoiceFilesDirectory;
    }

    /**
     * @Route("/user/{id}", name="command_index", methods={"GET"})
     */
    public function index(CommandRepository $commandRepository, $id, SerializerInterface $serializer): Response
    {
        $user = $this->getUser();
        $requestId = intval($id);

        if($user->getId() === $requestId) {

            $commands_data = $commandRepository->findBy(['customer' => $user]);

            $commands = $serializer->serialize(
                $commands_data,
                'json', ['groups' => ['command', 'products']]
                
            );


            return $this->json([
                'commands' => json_decode($commands),
            ]);
        } else {
            return $this->json([
                'commands' => null,
                'user' => $requestId,
                'user_session' => $user->getId(),
            ]);
        }
    }

    /**
     * @Route("/new", name="command_new", methods={"GET"})
     */
    public function new(Pdf $knpSnappyPdf): Response
    {
        $user = $this->getuser();
        $cart = $this->getDoctrine()->getRepository(Cart::class)->findOneBy(['user' => $user->getId()]);
        $cartProducts = $cart->getCartProducts();

        if(count($cartProducts) === 0) {
            return $this->json([
                'response' => 'empty cart',
            ]);
        }

        $fileName = uniqid() . '.pdf';
        $articles_list = [];
        $entityManager = $this->getDoctrine()->getManager();
        $total_price = 0;
        $nb_articles = 0;

        foreach($cartProducts as $article) {
            $product_owner = $article->getProduct()->getOwner();
            $product = $article->getProduct();
            $quantity = $article->getQuantity();

            $command = new Command();
            $command->setSerialNumber(uniqid());
            $command->setCustomer($user);
            $command->setProduct($product);
            $command->setQuantity($quantity);
            $command->setProducer($product_owner);
            $command->setAdress($user->getAdress());
            $command->setSecAdress($user->getSecAdress());
            $command->setPostcode($user->getPostcode());
            $command->setRegion($user->getRegion());
            $command->setCountry($user->getCountry());
            $command->setEtat('En attente');
            $command->setIsActive(true);
            $command->setCreatedAt(new \DateTime("now"));
            $command->setUpdatedAt(new \DateTime("now"));
            $command->setDeliveryDateAt(null);

            $command->setInvoice($fileName);

            $cart->removeCartProduct($article);

            $entityManager->persist($command);
            $entityManager->persist($cart);

            array_push($articles_list, $article);

            $total_price = $total_price + ($product->getPrice() * $quantity);
            $nb_articles = $nb_articles + 1;
        }
        
        $knpSnappyPdf->generateFromHtml(
            $this->renderView('command/invoice/invoice.html.twig', [
                'cartProducts' => $cart->getCartProducts(),
            ]),
            $this->invoiceFilesDirectory . '/public/invoices/'. $fileName
        );

        $entityManager->flush();

        return $this->json([
            'response' => 'success',
            'file' => '/invoices/public/invoices/'. $fileName,
            'montant' => $total_price,
            'nbArticles' => $nb_articles,
            'cartProducts' => $cart->getCartProducts(),
        ]);
        
    }

    /**
     * @Route("/all", name="command_show", methods={"GET"})
     */
    public function customerCommands(Command $command): Response
    {
        return $this->json([
            'commands' => $this->getDoctrine()->getRepository($command)->findBy(['user' => $this->getUser()])
        ]);
    }

    /**
     * @Route("/{id}", name="command_get", methods={"GET"})
     */
    public function show(Command $command): Response
    {
        return $this->render('command/show.html.twig', [
            'command' => $command,
        ]);
    }

    /**
     * @Route("/{commandId}/state/{state}", name="command_edit", methods={"GET","POST"})
     */
    public function edit(CommandRepository $commandRepository, int $commandId, string $state): Response
    {
        $command = $commandRepository->find($commandId);
        $command->setEtat($state);

        if($state === "Envoyé") {
            $command->setIsActive(false);
            $command->setDeliveryDateAt(new \DateTime("now"));
        } else {
            $command->setIsActive(true);
            $command->setDeliveryDateAt(null);
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($command);
        $entityManager->flush();

        return $this->json([
            'message' => "ok",
        ], 200);
    }

    /**
     * @Route("/{id}", name="command_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Command $command): Response
    {
        if ($this->isCsrfTokenValid('delete'.$command->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($command);
            $entityManager->flush();
        }

        return $this->redirectToRoute('command_index');
    }
}
