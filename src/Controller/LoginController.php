<?php
namespace App\Controller;

use App\Form\LoginFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class LoginController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(Request $request): Response
    {

        $form = $this->createForm(LoginFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $client = new \GuzzleHttp\Client();
            $url = $this->getParameter('1cApiUrl');
            $user = $this->getParameter('1cApiUser');
            $pass = $this->getParameter('1cApiPass');
            $parameters = [
                'auth' => [$user, $pass],
                'email' => $form->get('email')->getData(),
                'pass' => $form->get('password')->getData()
            ];

            $response = $client->request('POST', $url, $parameters);

            var_dump($response);exit;

            return $this->redirectToRoute('app_homepage');
        }

        return $this->render('login/login.html.twig', [
            'loginForm' => $form->createView(),
        ]);
    }

}