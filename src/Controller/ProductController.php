<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Encoder\JsonDecode;

/**
 * @Route("/product")
 */
class ProductController extends AbstractController
{
    /**
     * @Route("/", name="product_index", methods={"GET"})
     */
    public function index(ProductRepository $productRepository, SerializerInterface $serializer): Response
    {

        $productsList = $productRepository->findAll();

        $products = $serializer->serialize(
            $productsList,
            'json', ['groups' => ['products' /* if you add "user_detail" here you get circular reference */]]
            
        );

        return $this->json([
            'products' => json_decode($products)
        ]);
    }

    /**
     * @Route("/iventaire", name="product_my_products", methods={"GET"})
     */
    public function myProducts(SerializerInterface $serializer): Response
    {
        
        $user = $this->getUser();

        if($user === null || $user->getProducerProfile() === null) {
            return $this->json([
                "msg" => "Page non trouvée",
            ]);
        }

        $producer = $serializer->serialize(
            $user->getProducerProfile(),
            'json', ['groups' => ['producer' /* if you add "user_detail" here you get circular reference */]]
        );

        $products = $serializer->serialize(
            $user->getProducerProfile()->getProducts(),
            'json', ['groups' => ['products' /* if you add "user_detail" here you get circular reference */]]
        );

        $commands = $serializer->serialize(
            $user->getProducerProfile()->getCommands(),
            'json', ['groups' => ['command', 'products', 'customer' /* if you add "user_detail" here you get circular reference */]]
        );

        return $this->json([
            'products' => json_decode($products),
            'commands' => json_decode($commands),
            'producer' => json_decode($producer),
        ]);
    }

    /**
     * @Route("/new/{name}/{description}/{price}", name="product_new", methods={"POST"})
     */
    public function new(ValidatorInterface $validator, $name, $description, $price): Response
    {

        if(($name == '' || $price < 0) || $name === null) return $this->json([
            'erreur' => 'Valeurs incorrectes',
            'msg' => 'Valeurs incorrectes'
        ]);

        $_AREA_PROD = "La Réunion";

        $product = new Product();

        $product->setName($name);
        $product->setDescription($description);
        $product->setOwner($this->getUser()->getProducerProfile());
        $product->setDescription($description);
        $product->setProductionArea($_AREA_PROD);
        $product->setPrice($price);

        $errors = $validator->validate($product);
        
        if (count($errors) > 0) {
            /*
             * Uses a __toString method on the $errors variable which is a
             * ConstraintViolationList object. This gives us a nice string
             * for debugging.
             */
            $errorsString = (string) $errors;

            return $this->json([
                'erreur' => $errorsString,
                'msg' => "Erreur lors de l'enregistrement du produit"
            ]);
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($product);
        $entityManager->flush();

        return $this->json([
            'succès' => 'Enregistré avec succès',
            'msg' => "Nouveau produit ajouté avec succès"
        ]);
       
    }

    /**
     * @Route("/{id}", name="product_show", methods={"GET"})
     */
    public function show(SerializerInterface $serializer, $id): Response
    {
        $product_object = $this->getDoctrine()->getRepository(Product::class)->find($id);
        
        $product = $serializer->serialize(
            $product_object,
            'json', ['groups' => ['products', 'producer' /* if you add "user_detail" here you get circular reference */]]
        );

        return $this->json([
            'product' => json_decode($product),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="product_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Product $product): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('product_index');
        }

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/delete", name="product_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Product $product): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($product);
            $entityManager->flush();
        }

        return $this->redirectToRoute('product_index');
    }
}
