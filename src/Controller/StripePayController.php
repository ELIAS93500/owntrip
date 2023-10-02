<?php

namespace App\Controller;

use Stripe\Stripe;
use App\Service\CartService;
use Stripe\Checkout\Session;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StripePayController extends AbstractController
{
    #[Route('/stripe/pay', name: 'app_stripe_pay')]
    public function index(CartService $cs): Response
    {

        $fullCart=$cs->getCartWithData();

        $line_items=[];


        foreach ($fullCart as $item)
        {
            $line_items[]=[
                'price_data'=>[
                    'unit_amount'=>$item['activity']->getPrice(), 'currency'=>'EUR',
                    'product_data'=>['name'=>$item['activity']->getName()
                    ]
                ],
                    'quantity'=>$item['quantity']


            ];
        }
       Stripe::setApiKey('sk_test_51Nwi91ACvwDaBQU7oFqwm84J7wD9HwZqup7X8t9P81Wi8CeINMWBSW1ZMppoHVv0f0lkIpOMMUeM4rlnEZx4TxkD00RWSTc9P2');
        $session=Session::create([
            'success_url'=>'http://127.0.0.1:8000/commande/success',
            'cancel_url'=>'http://127.0.0.1:8000/wishList',
            'payment_method_types'=>['card'],
            'line_items'=>$line_items,
            'mode'=>'payment'



        ]);

        return $this->redirect($session->url, 303);

    }


    #[Route('/commande/{success}', name: 'commande')]
    public function commande(CartService $cartService, $success=null): Response
    {
        if($success){

            $this->addFlash('success', 'Merci pour votre confiance');
            $cartService->destroy();
            return $this->redirectToRoute('app_front');

        }else{
            $this->addFlash('danger', 'Un problème est survenu merci de réitérer votre paiement');
            return $this->redirectToRoute('app_front');

        }
    
    
    
        
    }

}
