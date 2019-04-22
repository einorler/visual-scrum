<?php

namespace TrelloBundle\Controller;

use AppBundle\Controller\PluginControllerInterface;
use AppBundle\Entity\Project;
use AppBundle\Entity\UserStory;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TrelloController extends Controller implements PluginControllerInterface
{
    /**
     * @Route("/api/trello", name="trello")
     */
    public function indexAction(Request $request)
    {
        return $this->render(':plugin/trello:trello.html.twig', [
            'app_key' => $this->getParameter('trello_app_key')
        ]);
    }

    /**
     * Responsible for saving the user stories of the project back to trello
     *
     * @Route("/api/trello/backsync/{id}", name="trello_backsync")
     *
     * @param Request $request
     * @param int     $id project id
     *
     * @return Response
     */
    public function backsyncAction(Request $request, $id)
    {
        $cards = [];
        $project = $this->getDoctrine()->getRepository(Project::class)->find($id);

        if (!$project) {
            $this->addFlash("error", "Project with id $id was not found!");

            return new RedirectResponse('/');
        }

        /** @var UserStory $story */
        foreach ($project->getUserStories() as $story) {
            $cards[] = ['id' => $story->getDistId(), 'name' => $story->getTitle()];
        }

        return $this->render(':plugin/trello:trello_backsync.html.twig', [
            'app_key' => $this->getParameter('trello_app_key'),
            'project' => $project,
            'cards' => json_encode($cards),
        ]);
    }

    /**
     * @Route("/api/trello/ajax", name="trello_ajax",
     *     options = { "expose" = true })
     */
    public function ajaxAction(Request $request)
    {
        $this->get('trello.plugin')->saveProjectData($this->getUser(), $request->request->get('boards'));

        return new JsonResponse('Success!');
    }
}
