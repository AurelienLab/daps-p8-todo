<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{


    #[Route('/login', name: 'login')]
    public function loginAction(Request $request, AuthenticationUtils $authenticationUtils)
    {

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render(
            'security/login.html.twig', array(
                                          'last_username' => $lastUsername,
                                          'error' => $error,
                                      )
        );
    }


    #[Route('/logout', name: 'logout')]
    public function logoutAction(Security $security)
    {

        // logout the user in on the current firewall
        $response = $security->logout();

        return $this->redirectToRoute('homepage');
    }


}
