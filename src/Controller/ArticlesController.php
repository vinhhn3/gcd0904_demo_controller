<?php

namespace App\Controller;

use App\Entity\Article;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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

    /**
     * @Route("/articles/create", methods={"POST"}, name="rest_api_article_create")
     */
    public function createAction(Request $request)
    {
        try {
            // Get data in Request from client
            $article = new Article();
            $data = json_decode($request->getContent(), true);
            $article->setTitle($data['title']);
            $article->setContent($data['content']);
            $article->setDate(\DateTime::createFromFormat('Y-m-d', $data['date']));

            // Try to insert new article to Database
            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();

            // Return OK if created successfully
            return new Response(null, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            // Return BAD_REQUEST if created unsuccessfully
            return new Response(null, Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/articles/delete/{id}", methods={"DELETE"}, name="rest_api_delete")
     */
    public function deleteAction($id)
    {
        try {
            // Get id from Request and try to delete
            $article = $this->getDoctrine()->getRepository(Article::class)->find($id);
            if ($article == null) {
                $statusCode = Response::HTTP_NOT_FOUND;
            } else {
                $em = $this->getDoctrine()->getManager();
                $em->remove($article);
                $em->flush();

                $statusCode = Response::HTTP_NO_CONTENT;
            }

            return new Response(null, $statusCode);
        } catch (\Exception $e) {
            // Return BAD_REQUEST if something went wrong
            return new Response(null, Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/articles/edit/{id}", methods={"PUT"}, name="rest_api_edit")
     */
    public function editAction(Request $request, $id)
    {
        // Find if article with id exsited
        $article = $this->getDoctrine()->getRepository(Article::class)->find($id);
        if ($article == null) {
            $statusCode = Response::HTTP_NOT_FOUND;
        } else {
            $data = json_decode($request->getContent(), true);
            $article->setTitle($data['title']);
            $article->setContent($data['content']);
            $article->setDate(\DateTime::createFromFormat('Y-m-d', $data['date']));

            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();

            $statusCode = Response::HTTP_NO_CONTENT;
        }
        return new Response(null, $statusCode);
    }
}
