<?php


namespace App\Spotify;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;


/**
 * Class ClientSpotify
 * @package App\Spotify
 */
final class ClientSpotify extends HttpClient
{
    protected $baseUrl;
    protected $urlToken;
    protected $ClientID;
    protected $ClientSecret;

    /**
     * ClientSpotify constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->base_url = "https://accounts.spotify.com/api/authorize";
        $this->urlToken = "https://accounts.spotify.com/api/token";
        $this->urlNewRelease = "https://api.spotify.com/v1/browse/new-releases?country=co&limit=12";
        $this->urlArtis = "https://api.spotify.com/v1/artists/";
        $this->urlAlbumsTracks = "https://api.spotify.com/v1/albums/";
        $this->redirectUri = "http%3A%2F%2F127.0.0.1%3A8000%2Fhome";
        $this->ClientID = "902fa3e7f76748ce8983112a108ddb34";
        $this->ClientSecret = "567adf394fa042bc90d3ea48178722f9";
    }

    /**
     * Funciona para realizar la autenticacion con una cuenta de Spotify.
     */
    public function login()
    {
        $scopes = 'user-read-private user-read-email';
        $url = "https://accounts.spotify.com/authorize?response_type=token&client_id=".$this->ClientID."&show_dialog=true&scope=user-read-private user-read-email user-read-playback-state&redirect_uri=http%3A%2F%2F127.0.0.1%3A8000%2Fhome";
        return header("Location: ".$url);
        die;
    }

    public function getAccessToken()
    {
        $result = ['success' => true, 'message' => 'Access token and request token', 'result' => []];
        $url =$this->urlToken;
        $code = $_GET['code'];
        $this->client = new Client();
        /**
         * Se devuelve un code 200 pero no el token, se opta por traer el token devuelvo por la url despues del login.
         */
        try {
            $response = $this->client->request(
                'POST',
                $this->urlToken,
                [
                    'headers'   => [
                        'Authorization'     => 'Basic ' . base64_encode($this->ClientID . ':' . $this->ClientSecret),
                        'Content-Type'      => 'application/x-www-form-urlencoded'
                    ],
                    'form_params'      => [
                        'client_id' => $this->ClientID,
                        'grant_type'        => 'authorization_code',
                        'code' => trim($code),
                        'redirect_uri' => "http://127.0.0.1:8000/lanzamientos"
                    ]
                ]

            );

            if($response->getStatusCode() == 200){
            }else{
                $result['success'] = false;
                $result['message'] = 'Spotify response status is not 200';
            }

            return $response;

        }catch (RequestException $exception) {
            dump( "Solicitud incorrecta" . $exception->getMessage());
            return false;
        }catch (\Exception $e)
        {
            dump( "Error al sincronizar: ".$e->getMessage());
            return false;
        }

    }

    /**
     * Funcion para consultar los nuevos lanzamientos.
     * @param $access_token
     * @return false|mixed|null
     */
    public function getNewRelease($access_token){

        try
        {
            $this->client = new Client();
            $headers = [
                'Authorization' => 'Bearer ' . $access_token,
                'Content-Type' => 'application/json'
            ];
            $response = $this->client->get(

                $this->urlNewRelease,
                [
                    'headers' => $headers,
                ]);
            $rta = json_decode($response->getBody(), true);
            return $rta;
        }catch (RequestException $exception) {
            echo "Solicitud incorrecta " . $exception->getMessage();
            return false;
        }catch (\Exception $e)
        {
            echo "Error al sincronizar: ".$e->getMessage();
            return null;
        }

    }

    /**
     * Funcion para consultar la lista de Álbumes por artista.
     * @param $access_token
     * @param $id
     * @return false|mixed|null
     */
    public function getArtisAlbums($access_token,$id){

        try
        {
            $this->client = new Client();
            $headers = [
                'Authorization' => 'Bearer ' . $access_token,
                'Content-Type' => 'application/json'
            ];
            $response = $this->client->get(

                $this->urlArtis.$id."/albums?limit=10",
                [
                    'headers' => $headers,
                ]);
            $rta = json_decode($response->getBody(), true);
            return $rta;
        }catch (RequestException $exception) {
            echo "Solicitud incorrecta " . $exception->getMessage();
            return false;
        }catch (\Exception $e)
        {
            echo "Error al sincronizar: ".$e->getMessage();
            return null;
        }

    }

    /**
     * Funcion para consultar la lista de canciones de un Album.
     * @param $access_token
     * @param $id
     * @return false|mixed|null
     */
    public function getAlbumsTrack($access_token,$id){

        try
        {
            $this->client = new Client();
            $headers = [
                'Authorization' => 'Bearer ' . $access_token,
                'Content-Type' => 'application/json'
            ];
            $response = $this->client->get(

                $this->urlAlbumsTracks.$id."/tracks?limit=1",
                [
                    'headers' => $headers,
                ]);
            $rta = json_decode($response->getBody(), true);
            return $rta;
        }catch (RequestException $exception) {
            echo "Solicitud incorrecta " . $exception->getMessage();
            return false;
        }catch (\Exception $e)
        {
            echo "Error al sincronizar: ".$e->getMessage();
            return null;
        }

    }

    /**
     * Funcion para consultar la informacion de un artista.
     * @param $access_token
     * @param $id
     * @return false|mixed|null
     */
    public function getArtista($access_token,$id){

        try
        {
            $this->client = new Client();
            $headers = [
                'Authorization' => 'Bearer ' . $access_token,
                'Content-Type' => 'application/json'
            ];
            $response = $this->client->get(

                $this->urlArtis.$id,
                [
                    'headers' => $headers,
                ]);
            $rta = json_decode($response->getBody(), true);
            return $rta;
        }catch (RequestException $exception) {
            echo "Solicitud incorrecta " . $exception->getMessage();
            return false;
        }catch (\Exception $e)
        {
            echo "Error al sincronizar: ".$e->getMessage();
            return null;
        }

    }
}