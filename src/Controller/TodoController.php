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

    /**
     * @Route("/todo/details/{id}", methods={"GET"}, name="todo_details")
     */
    public function detailsAction($id)
    {
        $todo = $this->getDoctrine()
                ->getRepository('App:Todo')
                ->find($id);

        return $this->render('todo/details.html.twig', [
                'todo' => $todo
        ]);
    }

    /**
     * @Route("/todo/delete/{id}", methods={"GET"}, name="todo_delete")
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $todo = $em->getRepository('App:Todo')->find($id);
        $em->remove($todo);
        $em->flush();

        $this->addFlash(
                'error',
                'Todo deleted ...'
        );

        return $this->redirectToRoute('todo_list');
    }
}
