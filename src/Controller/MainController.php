<?php
namespace App\Controller;

use GuzzleHttp\Exception\ClientException;
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
        $iduser = $this->session->get('iduser');

        if(!$iduser)
            return $this->redirect('/login');

        $client = new \GuzzleHttp\Client();

        $url = $this->getParameter('1cApiUrl');
        $user = $this->getParameter('1cApiUser');
        $pass = $this->getParameter('1cApiPass');

        $parameters = [
            'auth' => [$user, $pass],
            'Content-type' => 'application/json; charset=utf-8',
            'Accept' => 'application/json',
            'query' => ['uauto' => '1'],
            'body' => '['.json_encode([
                    "iduser" => $iduser
                ]).']'
        ];

        try{
            $response = $client->request('POST', $url, $parameters);
        } catch (ClientException $e){
            return $this->render('pages/home.html.twig', ['message' => 'Ошибка сервера']);
        }

        $statusCode = $response->getStatusCode();

        switch ($statusCode){
            case 200:
                $body = $response->getBody();
                $remainingBytes = $body->getContents();
                $data = json_decode($remainingBytes);
                return $this->render('pages/home.html.twig', ['autos' => $data]);
            case 404:
                return $this->render('pages/home.html.twig', ['message' => 'У пользователя пока нет авто']);
                break;
            default:
                return $this->render('pages/home.html.twig', ['message' => 'Ошибка сервера']);
        }
    }

    /**
     * @Route("/auto/{idauto}", name="app_auto")
     */
    public function autoDetails(string $idauto): Response
    {
        $iduser = $this->session->get('iduser');

        if(!$iduser)
            return $this->redirect('/login');

        $client = new \GuzzleHttp\Client();

        $url = $this->getParameter('1cApiUrl');
        $user = $this->getParameter('1cApiUser');
        $pass = $this->getParameter('1cApiPass');

        $parameters = [
            'auth' => [$user, $pass],
            'Content-type' => 'application/json; charset=utf-8',
            'Accept' => 'application/json',
            'query' => ['posorder' => '1'],
            'body' => '['.json_encode([
                    $idauto
                ]).']'
        ];

        try{
            $response = $client->request('POST', $url, $parameters);
        } catch (ClientException $e){
            return $this->render('pages/home.html.twig', ['message' => 'Ошибка сервера']);
        }

        $statusCode = $response->getStatusCode();

        $body = $response->getBody();
        $remainingBytes = $body->getContents();
        $data = json_decode($remainingBytes);

        //var_dump($data);exit;
        $groupPart = '';
        $dataSorted = [];

        foreach ($data as $detail){

            if ($detail->grouppart != $groupPart){
                $dataSorted[$detail->grouppart] = [];
                $dataSorted[$detail->grouppart]['id'] = uniqid();
                $dataSorted[$detail->grouppart]['details'] = [];
                $groupPart = $detail->grouppart;
            }

            $dataSorted[$groupPart]['details'][] =  $detail;
        }

        //var_dump( $dataSorted);exit;

        return $this->render('pages/auto.html.twig', ['data' => $dataSorted]);
    }
}