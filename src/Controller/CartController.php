<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Form\CartType;
use App\Entity\Product;
use App\Repository\CartRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/cart")
 */
class CartController extends AbstractController
{
    /**
     * @Route("/", name="cart_index", methods={"GET"})
     */
    public function index(CartRepository $cartRepository): Response
    {
        return $this->render('cart/index.html.twig', [
            'carts' => $cartRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new/product/{id}/quantity/{quantity}", name="cart_new", methods={"GET"})
     */
    public function new($id, $quantity): Response
    {
        $user = $this->getUser();
        $product = $this->getDoctrine()->getRepository(Product::class)->find($id);

        if($this->getDoctrine()->getRepository(Cart::class)->findOneBy(['user' => $user])) {
            $cart = $this->getDoctrine()->getRepository(Cart::class)->findOneBy(['user' => $user]);
            $cart->addProduct($product);
            $cart->setQuantity($quantity);
        } else {
            $cart = new Cart();

            $cart->setUser($user);
            $cart->addProduct($product);
            $cart->setQuantity($quantity);
        }

        

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($cart);
        $entityManager->flush();

        return $this->json([
            'response' => 'success',
        ]);
    }

    /**
     * @Route("/all/commands", name="cart_all_commands", methods={"GET"})
     */
    public function customerCommands(SerializerInterface $serializer): Response
    {
        $cart_data = $this->getDoctrine()->getRepository(Cart::class)->findBy(['user' => $this->getUser()]);

        $cart = $serializer->serialize(
            $cart_data,
            'json', ['groups' => ['cart', 'user', 'products' /* if you add "user_detail" here you get circular reference */]]
            
        );

        return $this->json([
            'cart' => json_decode($cart)
        ]);
    }

    /**
     * @Route("/{id}", name="cart_show", methods={"GET"})
     */
    public function show(Cart $cart): Response
    {
        return $this->render('cart/show.html.twig', [
            'cart' => $cart,
        ]);
    }

    /**
     * @Route("/remove/item/{id}", name="cart_remove_command", methods={"GET"})
     */
    public function removeCommand($id): Response
    {
        $cartItem = $this->getDoctrine()->getRepository(Cart::class)->findOneBy([
            'user' => $this->getUser(),
            'products' => $this->getDoctrine()->getRepository(Product::class)->find($id)
            ]);
        
        $entityManager = $this->getDoctrine()->getManager();
        dump($cartItem);
        $entityManager->remove($cartItem);
        // $entityManager->flush();

        return $this->json([
            'status' => 'success'
        ]);
    }

    /**
     * @Route("/{id}/edit", name="cart_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Cart $cart): Response
    {
        $form = $this->createForm(CartType::class, $cart);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('cart_index');
        }

        return $this->render('cart/edit.html.twig', [
            'cart' => $cart,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="cart_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Cart $cart): Response
    {
        if ($this->isCsrfTokenValid('delete'.$cart->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($cart);
            $entityManager->flush();
        }

        return $this->redirectToRoute('cart_index');
    }
}
