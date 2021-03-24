<?php

namespace App\Controller;

use App\Entity\Cart;
use Knp\Snappy\Pdf;
use App\Entity\Command;
use App\Form\CommandType;
use App\Repository\CommandRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
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
     * @Route("/", name="command_index", methods={"GET"})
     */
    public function index(CommandRepository $commandRepository): Response
    {
        return $this->render('command/index.html.twig', [
            'commands' => $commandRepository->findAll(),
        ]);
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
            $command->setEtat('En attente');
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
                'cartProducts' => $cartProducts,
            ]),
            $this->invoiceFilesDirectory . '/public/invoices/'. $fileName
        );


        $entityManager->flush();

        return $this->json([
            'response' => 'success',
            'file' => '/public/invoices/public/invoices/'. $fileName,
            'montant' => $total_price,
            'nbArticles' => $nb_articles,
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
     * @Route("/{id}/edit", name="command_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Command $command): Response
    {
        $form = $this->createForm(CommandType::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('command_index');
        }

        return $this->render('command/edit.html.twig', [
            'command' => $command,
            'form' => $form->createView(),
        ]);
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
