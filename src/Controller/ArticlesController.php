<?php

namespace App\Controller;

use App\Entity\Article;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticlesController extends AbstractController
{
    // Config serializer
    private $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @Route("/articles", methods={"GET"}, name="rest_api_articles")
     */
    public function articlesAction()
    {
        // Get all articles in Database
        $articles = $this->getDoctrine()->getRepository(Article::class)->findAll();

        // Convert object articles to JSON
        $json = $this->serializer->serialize($articles, 'json');

        return new Response($json,
                Response::HTTP_OK,
                array('content-type' => 'application/json')
        );
    }
}
