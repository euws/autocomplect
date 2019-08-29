<?php

namespace Application\Service;

class ImageManager
{
    protected $requestService;

    public function __construct(RequestService $requestService)
    {
        $this->requestService = $requestService;
    }

    public function generateImageSrc(array $auto): array
    {
        if(isset($auto['picture'])){
            $auto['picture'] = $this->correctCharset($auto['picture']);
            $dir = 'public/img/autoImg/'.trim($auto['idauto']).'/avatar/';

            if (!file_exists($dir)) {
                mkdir($dir, 0777, true);
            }

            if(!file_exists($dir.$auto['picture'])){
                $response = $this->requestService
                    ->makeRequest(['picture' => '1'], '['.json_encode($auto['idauto']).']');

                $statusCode = $response->getStatusCode();

                if($statusCode == 200){
                    $body = $response->getBody();
                    $remainingBytes = $body->getContents();
                    $images = $this->saveImages($remainingBytes, $dir);
                    $auto['picture'] = substr($images[0], 7);
                }else{
                    $auto['picture'] = '';
                }
            } else{
                $auto['picture'] = substr($dir, 7).$auto['picture'];
            }
        }

        if (isset($auto['pictureall']) && count($auto['pictureall'])) {
            $dir = 'public/img/autoImg/' . trim($auto['idauto']) . '/';

            if (!file_exists($dir)) {
                mkdir($dir, 0777, true);
            }

            foreach ($auto['pictureall'] as $key => $picture) {

                $picture = $this->correctCharset($picture);

                if (!file_exists($dir . $picture)) {
                    $response = $this->requestService
                        ->makeRequest(['picture' => 'all'], '[' . json_encode($auto['idauto']) . ']');

                    $statusCode = $response->getStatusCode();

                    if ($statusCode == 200) {
                        $body = $response->getBody();
                        $remainingBytes = $body->getContents();
                        $images = $this->saveImages($remainingBytes, $dir);
                        $auto['pictureall'] = array_map(
                            function ($image) {
                                return substr($image, 7);
                            },
                            $images
                        );
                        break;
                    } else {
                        $auto['pictureall'] = [];
                        break;
                    }
                } else {
                    $auto['pictureall'][$key] = substr($dir, 7) . $picture;
                }
            }
        }

        return $auto;
    }

    protected function saveImages($response, $dir): array
    {
        $data = explode( ', ', $response);
        array_pop($data);
        $images = [];

        foreach ($data as $key => $value){
            if ($key % 2 == 0 ){
                $value = $this->correctCharset($value);
                $images[$key] = $dir.$value;
            }else{
                $ifp = fopen( $images[$key-1], 'wb' );
                fwrite($ifp, base64_decode($value));
                fclose($ifp );
                $this->resizeImage($images[$key-1], 200, 150);
            }
        }

        return $images;
    }

    protected function correctCharset($value): string
    {
        $string = mb_convert_encoding($value, 'utf-8', mb_detect_encoding($value));
        $string = trim($string);
        $string = str_replace(' ', '_', $string);
        $string = transliterator_transliterate('Russian-Latin/BGN', $string);
        $string = preg_replace('/[^A-Za-z0-9\- . _]/', '', $string);

        return $string;
    }

    protected function resizeImage($filePath, $w, $h)
    {
        $path_parts = pathinfo($filePath);
        $extension = $path_parts['extension'];

        list($width, $height) = getimagesize($filePath);

        switch ($extension){
            case 'jpg':
                $src = imagecreatefromjpeg($filePath);
                break;
            case 'png':
                $src = imagecreatefrompng($filePath);
                break;
            default:
                return;
        }

        $dst = imagecreatetruecolor($w, $h);
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $w, $h, $width, $height);

        $pos = strrpos($filePath, '.');

        if($pos !== false)
        {
            $filePath = substr_replace($filePath, '_'.$w.'x'.$h.'.', $pos, strlen('.'));
        }

        switch ($extension){
            case 'jpg':
                imagejpeg($dst, $filePath);
                break;
            case 'png':
                imagepng($dst, $filePath);
                break;
            default:
                return;
        }
    }
}