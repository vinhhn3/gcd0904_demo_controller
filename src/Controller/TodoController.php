<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class TodoController extends AbstractController
{
    /**
     * @Route("/todo", name="todo_list")
     */
    public function listAction()
    {
        $todos = $this->getDoctrine()
                ->getRepository('App:Todo')
                ->findAll();
        return $this->render('todo/index.html.twig', [
                'todos' => $todos
        ]);
    }
}
