<?php

namespace App\Controller;

use Knp\Snappy\Pdf;
use App\Entity\Command;
use App\Entity\Producer;
use App\Entity\Product;
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
     * @Route("/new/product/{id}", name="command_new", methods={"GET"})
     */
    public function new($id, Pdf $knpSnappyPdf): Response
    {
        $user = $this->getuser();
        $product = $this->getDoctrine()->getRepository(Product::class)->find($id);
        $product_owner = $product->getOwner();

        $fileName = uniqid() . '.pdf';

        $command = new Command();
        $command->setSerialNumber(uniqid());
        $command->setCustomer($user);
        $command->setProduct($product);
        $command->setProducer($product_owner);
        $command->setEtat('En attente');
        $command->setCreatedAt(new \DateTime("now"));
        $command->setUpdatedAt(new \DateTime("now"));
        $command->setDeliveryDateAt(null);

        $knpSnappyPdf->generateFromHtml(
            $this->renderView('command/invoice/invoice.html.twig'),
            $this->invoiceFilesDirectory . '/public/invoices/'. $fileName
        );

        $command->setInvoice($fileName);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($command);
        $entityManager->flush();

        return $this->json([
            'response' => 'success',
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
     * @Route("/{id}", name="command_show", methods={"GET"})
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
