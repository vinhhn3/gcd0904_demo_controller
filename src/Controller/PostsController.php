<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostsController extends AbstractController
{
    private $_posts = [
            ['description' => 'PHP Basics', 'category' => 'Web Development'],
            ['description' => 'Java Basics', 'category' => 'App Development']
    ];

    /**
     * @Route("/posts", name="posts")
     */
    public function index(): Response
    {
        return $this->render('posts/index.html.twig', [
                'controller_name' => 'PostsController',
        ]);
    }

    /**
     * @Route("/posts/demo", name="demo")
     */
    public function demo()
    {
        $post = new Post();
        $post->setTitle("Post 1");
        $post->setContent("Content 1");
        $post->setAdded("Added 1");
        $articleNumber = 1000;
        return $this->render(
                'posts/demo.html.twig',
                array('post' => $post, 'articleNumber' => $articleNumber
                )
        );
    }

    /**
     * @Route("/posts/create", methods={"GET","POST"}, name="create_get")
     */
    public function create(Request $request)
    {
        $post = new Post();
        $postForm = $this->createForm(PostFormType::class, $post);
        $postForm->handleRequest($request);

        if ($postForm->isSubmitted() && $postForm->isValid()) {
            $data = $postForm->getData();
            $title = $data->getTitle();
            $content = $data->getContent();
            $added = $data->getAdded();

            return $this->render("posts/display.html.twig", [
                    "title" => $title,
                    "content" => $content,
                    "added" => $added
            ]);
        }
        return $this->render('posts/create.html.twig',
                ['postForm' => $postForm->createView()]
        );
    }


    /**
     * @Route("posts/summary", methods={"GET"}, name="summary")
     */
    public function summary()
    {
        return new JsonResponse($this->_posts);
    }

    /**
     * @Route("posts/google", methods={"GET"}, name="google")
     */
    public function google()
    {
        return $this->redirect("https://google.com");
    }

    /**
     * @Route("posts/redirect", methods={"GET"}, name="redirect")
     */
    public function redirectRoute()
    {
        return $this->redirectToRoute("demo");
    }
}
