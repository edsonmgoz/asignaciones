<?php

namespace EMM\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response; 
use EMM\UserBundle\Entity\Task;
use EMM\UserBundle\Form\TaskType;

class TaskController extends Controller
{
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $dql = "SELECT t FROM EMMUserBundle:Task t ORDER BY t.id DESC";
        $tasks = $em->createQuery($dql);
        
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $tasks,
            $request->query->getInt('page', 1),
            3
        );
        
        return $this->render('EMMUserBundle:Task:index.html.twig', array('pagination' => $pagination));
    }
    
    public function addAction()
    {
        $task = new Task();
        $form = $this->createCreateForm($task);
        
        return $this->render('EMMUserBundle:Task:add.html.twig', array('form' => $form->createView()));
    }
    
    private function createCreateForm(Task $entity)
    {
        $form = $this->createForm(new TaskType(), $entity, array(
            'action' => $this->generateUrl('emm_task_create'),
            'method' => 'POST'
        ));
        
        return $form;
    }
    
    public function createAction(Request $request)
    {
        $task = new Task();
        $form = $this->createCreateForm($task);
        $form->handleRequest($request);
        
        if($form->isValid())
        {
            $task->setStatus(0);
            $em = $this->getDoctrine()->getManager();
            $em->persist($task);
            $em->flush();
            
            $successMessage = $this->get('translator')->trans('The task has been created.');
            $this->addFlash('mensaje', $successMessage);            
            
            return $this->redirectToRoute('emm_task_index');
        }
        
        return $this->render('EMMUserBundle:Task:add.html.twig', array('form' => $form->createView()));
    }
}
