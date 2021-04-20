<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\CartProducts;
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
    // /**
    //  * @Route("/", name="cart_index", methods={"GET"})
    //  */
    // public function index(CartRepository $cartRepository): Response
    // {
    //     return $this->render('cart/index.html.twig', [
    //         'carts' => $cartRepository->findAll(),
    //     ]);
    // }

    /**
     * Add a new product to cart
     * 
     * @Route("/new/product/{id}/quantity/{quantity}", name="cart_new", methods={"GET"})
     */
    public function new($id, $quantity): Response
    {
        $user = $this->getUser();
        $product = $this->getDoctrine()->getRepository(Product::class)->find($id);
        $cart = $this->getDoctrine()->getRepository(Cart::class)->findOneBy(['user' => $user]);

        if(null === $cart) {
            $cart = new Cart();
            $cart->setUser($user);
        }

        $cart_products = $this->getDoctrine()->getRepository(CartProducts::class)->findOneBy(['cart' => $cart, 'product' => $product]);

        if(null === $cart_products) {
            $cart_products = new CartProducts();
            $cart_products->setCart($cart);
            $cart_products->setProduct($product);
            $cart_products->setQuantity($quantity);
        } else {
            $newQuantity = ($cart_products->getQuantity() + $quantity);
            $cart_products->setQuantity($newQuantity);
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($cart);
        $entityManager->persist($cart_products);

        $entityManager->flush();

        return $this->json([
            'response' => 'success',
        ]);
    }

    /**
     * 
     * 
     * @Route("/all/commands", name="cart_all_commands", methods={"GET"})
     */
    public function customerCommands(SerializerInterface $serializer): Response
    {   
        $producer = false;

        if($this->getUser()) {
            $user = $this->getUser();

            // if user has producer profile
            if($user->getProducerProfile()) {
                $producer = true;
            }
        } else {
            $user = null;
        }

        $cart_data = $this->getDoctrine()->getRepository(Cart::class)->findBy(['user' => $this->getUser()]);

        $cart = $serializer->serialize(
            $cart_data,
            'json', ['groups' => ['cart', 'user', 'products', 'cartproducts' ]]
            
        );

        if($user === null) {
            $user_id = $user_lastName = $user_firstName = $user_adress = $user_secAdress = $user_postcode = $user_region = $user_country = $user_phoneNumber = $user_email = $user_isVerified = null;
        } else {
            $user_id = $user->getId();
            $user_lastName = $user->getLastName();
            $user_firstName = $user->getFirstName();
            $user_adress = $user->getAdress();
            $user_secAdress = $user->getSecAdress();
            $user_postcode = $user->getPostcode();
            $user_region = $user->getRegion();
            $user_country = $user->getCountry();
            $user_phoneNumber = $user->getNumber();

            $user_email = $user->getEmail();
            $user_isVerified = $user->isVerified();
        }

        return $this->json([
            'cart' => json_decode($cart),
            'user' => $user_id,
            'user_lastName' => $user_lastName,
            'user_firstName' => $user_firstName,
            'user_adress' => $user_adress,
            'user_secAdress' => $user_secAdress,
            'user_postcode' => $user_postcode,
            'user_region' => $user_region,
            'user_country' => $user_country,
            'user_phoneNumber' => $user_phoneNumber,
            'user_email' => $user_email,
            'producer' => $producer,
            'user_isVerified' => $user_isVerified,
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
        $cart = $this->getDoctrine()->getRepository(Cart::class)->findOneBy(['user' => $this->getUser()]);
        $product = $this->getDoctrine()->getRepository(Product::class)->find($id);

        $cart_products = $this->getDoctrine()->getRepository(CartProducts::class)->findOneBy(['cart' => $cart, 'product' => $product]);
        
        
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($cart_products);
        $entityManager->flush();

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
