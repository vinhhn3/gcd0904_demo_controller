<?php

namespace App\Controller;

use App\Entity\Todo;
use App\Form\TodoType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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

    /**
     * @Route("/todo/create", name="todo_create", methods={"GET", "POST"})
     */
    public function createAction(Request $request)
    {
        $todo = new Todo();
        $form = $this->createForm(TodoType::class, $todo);

        if ($this->saveChanges($form, $request, $todo)) {
            $this->addFlash(
                    'notice',
                    'Todo addedd ...'
            );
            return $this->redirectToRoute('todo_list');
        }

        return $this->render('todo/create.html.twig', [
                'form' => $form->createView()
        ]);
    }

    public function saveChanges($form, $request, $todo)
    {
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $todo->setName($request->request->get('todo')['name']);
            $todo->setCategory($request->request->get('todo')['category']);
            $todo->setDescription($request->request->get('todo')['description']);
            $todo->setPriority($request->request->get('todo')['priority']);
            $todo->setDueDate(\DateTime::createFromFormat('Y-m-d', $request->request->get('todo')['due_date']));
            $em = $this->getDoctrine()->getManager();
            $em->persist($todo);
            $em->flush();

            return true;
        }

        return false;
    }


}
