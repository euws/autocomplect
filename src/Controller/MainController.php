<?php
namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class MainController extends AbstractController
{

    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @Route("/", name="app_homepage")
     */
    public function index(): Response
    {
        $foo = $this->session->get('foo');

        if(!$foo)
            return $this->redirectToRoute('app_login');

        return $this->render('pages/home.html.twig');
    }

}