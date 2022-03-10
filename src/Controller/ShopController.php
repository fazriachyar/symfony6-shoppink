<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use Doctrine\ORM\EntityManagerInterface;

use App\Repository\ShopRepository;
use App\Entity\Shop;
use App\Form\AddToCartType;
use App\Form\ShopType;
use App\Manager\CartManager;

#[Route('/shop', name: 'shop')]
class ShopController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(ShopRepository $shoprepository): Response
    {
        $shops = $shoprepository->findAll();

        return $this->render('shop/index.html.twig', [
            'shops' => $shops
        ]);
    }

    #[Route('/create', name:'create')]
    public function create(Request $request, EntityManagerInterface $em)
    {
        $shop = new Shop();
        $form = $this->createForm(ShopType::class, $shop);
        $form->handleRequest($request);

        if($form->isSubmitted()){
            $em->persist($shop);
            $em->flush();
            return $this->redirect($this->generateUrl(route:'shopindex'));
        }

        return $this->render('/shop/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/product/{id}', name:'product.detail')]
    public function detail(Shop $shop, Request $request, CartManager $cartManager)
    {
        $form = $this->createForm(AddToCartType::class);
        $form->handleRequest($request);

        if($form->isSubmitted()){
            $item = $form->getData();
            $item->setProduct($shop);

            $cart = $cartManager->getCurrentCart();
            $cart 
                ->addItem($item)
                ->setUpdatedAt(new \DateTime());
            
            $cartManager->save($cart);

            return $this->redirectToRoute('shopproduct.detail', ['id' => $shop->getId()]);
        }

        return $this->render('shop/detail.html.twig', [
            'shop' => $shop,
            'form' => $form->createView()
        ]);
    }


    
}
