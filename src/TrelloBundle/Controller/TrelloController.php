<?php

namespace TrelloBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TrelloController extends Controller
{
    /**
     * @Route("/api/trello", name="trello")
     */
    public function indexAction(Request $request)
    {
        return $this->render(':plugin:trello.html.twig');
    }

    /**
     * @Route("/api/trello/ajax", name="trello_ajax",
     *     options = { "expose" = true })
     */
    public function ajaxAction(Request $request)
    {
        $this->get('trello.plugin')->saveProjectData($this->getUser(), $request->get('boards'));

        return new JsonResponse('ajaxed');
    }
}
