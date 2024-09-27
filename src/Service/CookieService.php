<?php

namespace App\Service;

use App\Entity\Product;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;

class CookieService
{
    public function cookie_creation(): array
    {
        $cookies['type'] = [
            'trekking' => 0,
            'running' => 0,
            'hiking' => 0,
            'sandals' => 0,
            'heels' => 0,
            'boots' => 0,
            'ankle_boots' => 0,
            'sneakers' => 0,
            'formal' => 0,
            'flip flops' => 0,
            'others' => 0
        ];

        $cookies['brand'] = [
            'Nike' => 0,
            'Adidas' => 0,
            'Puma' => 0,
            'Asics' => 0,
            'Converse' => 0,
            'NewBalance' => 0,
            'Scarpa' => 0,
            'LaSportiva' => 0,
            'Hoka' => 0,
            'Salomon' => 0,
            'others' => 0,
        ];

        $cookies['color'] = [
            'white' => 0,
            'yellow' => 0,
            'orange' => 0,
            'red' => 0,
            'green' => 0,
            'blue' => 0,
            'violet' => 0,
            'pink' => 0,
            'cyan' => 0,
            'gray' => 0,
            'black' => 0,
            'brown' => 0,
        ];

        return $cookies;
    }

    public function cookie_update(Product $product, Request $request, Response $response): Response {

        $typeCookie = $request->cookies->get('type');
        $brandCookie = $request->cookies->get('brand');
        $colorCookie = $request->cookies->get('color');


        $types = json_decode($typeCookie, true);
        $brands = json_decode($brandCookie, true);
        $colors = json_decode($colorCookie, true);

        $types[$product->getType()] ++;
        $brands[$product->getBrand()] ++;
        $colors[$product->getColor()] ++;

        //dump($types, $brands, $colors);

        $typeJSON = json_encode($types);
        $brandJSON = json_encode($brands);
        $colorJSON = json_encode($colors);

        $response->headers->setCookie(new Cookie('type', $typeJSON, strtotime('2200-01-01 00:00:00')));
        $response->headers->setCookie(new Cookie('brand', $brandJSON, strtotime('2200-01-01 00:00:00')));
        $response->headers->setCookie(new Cookie('color', $colorJSON, strtotime('2200-01-01 00:00:00')));


        return $response;
    }
}





?>