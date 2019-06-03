<?php
namespace App\Controller;

use App\Form\LoginFormType;
use GuzzleHttp\Exception\ClientException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBag;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class LoginController extends AbstractController
{
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @Route("/login", name="app_login")
     */
    public function login(Request $request): Response
    {

        if($this->session->get('iduser'))
            return $this->redirect('app_homepage');

        $form = $this->createForm(LoginFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $client = new \GuzzleHttp\Client();

            $url = $this->getParameter('1cApiUrl');
            $user = $this->getParameter('1cApiUser');
            $pass = $this->getParameter('1cApiPass');

            $parameters = [
                'auth' => [$user, $pass],
                'Content-type' => 'application/json; charset=utf-8',
                'Accept' => 'application/json',
                'query' => ['login' => '1'],
                'body' => '['.json_encode([
                    "login" => $form->get('email')->getData(),
                    "pass" => $form->get('password')->getData()
                ]).']'
            ];

            try{
                $response = $client->request('POST', $url, $parameters);
            } catch (ClientException $e){
                $Error = new FormError('Ошибка сервера');
                $form->addError($Error);

                return $this->render('login/login.html.twig', [
                    'loginForm' => $form->createView(),
                ]);
            }

            $statusCode = $response->getStatusCode();

            switch ($statusCode){
                case 200:
                    $body = $response->getBody();
                    $remainingBytes = $body->getContents();
                    $data = json_decode($remainingBytes);
                    $IdUser = $data[0]->iduser;
                    $username = $data[0]->username;
                    $this->session = new Session(new NativeSessionStorage(), new AttributeBag());
                    $this->session->set('iduser', $IdUser);
                    $this->session->set('username', $username);

                    return $this->redirect('/');
                case 404:
                    $Error = new FormError('Пользователь с данным логином или поролем не существует');
                    $form->addError($Error);

                    return $this->render('login/login.html.twig', [
                        'loginForm' => $form->createView(),
                    ]);
                default:
                    $Error = new FormError('Ошибка сервера');
                    $form->addError($Error);

                    return $this->render('login/login.html.twig', [
                        'loginForm' => $form->createView(),
                    ]);
            }
        }

        return $this->render('login/login.html.twig', [
            'loginForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        if($this->session)
            $this->session->invalidate();

        return $this->redirect('/login');
    }
}