<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

interface PluginControllerInterface
{
    /**
     * @param Request $request
     */
    public function indexAction(Request $request);

    /**
     * Responsible for saving the user stories of the project back to trello
     *
     * @param Request $request
     * @param int     $id project id
     */
    public function backsyncAction(Request $request, $id);
}
