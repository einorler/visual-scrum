<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Project;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        return $this->render('default/index.html.twig');
    }

    /**
     * @Route("/projects", name="projects")
     */
    public function projectsAction(Request $request)
    {
        return $this->render('default/projects.html.twig');
    }

    /**
     * @Route("/projects/{id}", name="project")
     */
    public function projectAction(Request $request, $id)
    {
        return $this->render('default/project.html.twig', [
            'project' => $this->getDoctrine()->getRepository(Project::class)->find($id)
        ]);
    }

    /**
     * @Route("/configuration", name="configuration")
     */
    public function configurationAction(Request $request)
    {
        return $this->render('default/configuration.html.twig');
    }

    /**
     * @Route("/synchronize", name="synchronize")
     */
    public function synchronizeAction(Request $request)
    {
        return new Response('This is not implemented yet, please go to `/api/trello` manually');
    }
}
