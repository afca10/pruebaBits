<?php

namespace App\Controller;

use Container5t9oVQv\get_VarDumper_Command_ServerDump_LazyService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
USE App\Spotify\ClientSpotify;
class DefaultController extends AbstractController
{
    protected $clientSpotify;

    public function __construct()
    {
        $this->clientSpotify = new ClientSpotify();

    }

    /**
     * Funcion para loguear con cuenta spotify.
     * @Route("/", name="default")
     */
    public function index(): Response
    {
        $token = $this->clientSpotify->login();
        die;
    }

    /**
     * @Route("/artista/{id}", name="artista")
     */
    public function artista($id): Response
    {
        $token = $_COOKIE["tokenSpotify"];
        $albums = $this->clientSpotify->getArtisAlbums($token,$id);
        $artista = $this->clientSpotify->getArtista($token,$id);
        $dataArtista = [];
        $dataArtista[$artista["name"]]["image"] = $artista["images"][2];
        $dataArtista[$artista["name"]]["external_url"] = $artista["external_urls"]["spotify"];
        $dataArtista[$artista["name"]]["name"] = $artista["name"];

        $data = [];
        foreach ($albums["items"] as $item) {
            $track = $this->clientSpotify->getAlbumsTrack($token,$item["id"]);
            $data[$item["name"]]["id"] = $item["id"];
            $data[$item["name"]]["image"] = $item["images"][2];
            $data[$item["name"]]["artists"] = $item["artists"];
            $data[$item["name"]]["name"] = $item["name"];
            $data[$item["name"]]["track"] = $track["items"][0]["name"];
        }

        return $this->render('default/artista.html.twig', [
            'controller_name' => 'DefaultController', 'data' => $data,'artista' => $dataArtista
        ]);
    }

    /**
     * @Route("/lanzamientos", name="lanzamientos")
     */
    public function lanzamientos(): Response
    {
        $token = $_COOKIE["tokenSpotify"];
        if ($token == "undefined"){
            $token = $this->clientSpotify->login();
            exit;
        }
        $release = $this->clientSpotify->getNewRelease($token);
        $data = [];
        foreach ($release["albums"]["items"] as $albums) {
            $data[$albums["name"]]["id"] = $albums["id"];
            $data[$albums["name"]]["image"] = $albums["images"][1];
            $data[$albums["name"]]["artists"] = $albums["artists"];
            $data[$albums["name"]]["name"] = $albums["name"];
        }

        return $this->render('default/lanzamientos.html.twig', [
            'controller_name' => 'DefaultController','data' => $data
        ]);
    }

    /**
     * @Route("/home", name="lanzamientos")
     */
    public function home(): Response
    {
        return $this->render('default/home.html.twig', [
            'controller_name' => 'DefaultController',
        ]);
    }
}
