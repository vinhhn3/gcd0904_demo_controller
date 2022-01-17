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

    /**
     * @Route("/articles/{id}", methods={"GET"}, name="rest_api_article")
     */
    public function articleAction($id)
    {
        // Get a single article from Database
        $article = $this->getDoctrine()->getRepository(Article::class)->find($id);

        // Article not found
        if ($article == null) {
            return new Response(json_encode(array('error' => 'article not found')),
                    Response::HTTP_NOT_FOUND,
                    array('content-type' => 'application/json')
            );

        }

        // Article found
        $json = $this->serializer->serialize($article, 'json');
        return new Response(
                $json,
                Response::HTTP_OK,
                array('content-type' => 'application/json')
        );
    }
}
