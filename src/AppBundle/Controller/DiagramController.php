<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Project;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DiagramController extends Controller
{
    /**
     * @param Request $request
     * @param $id
     *
     * @return Response
     *
     * @Route("/generate/use-case/{id}", name="use-case-generation")
     */
    public function useCaseAction(Request $request, $id)
    {
        $userStories = $this->getDoctrine()->getRepository(Project::class)->find($id)->getUserStories();
        $useCases = $this->get('app.generator.use_case')->generateUseCases($userStories);
        $this->get('app.client.yuml')->fetchUseCaseDiagram($useCases);

        $this->redirectToRoute('project', ['id' => $id]);
    }
}
