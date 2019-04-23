<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Configuration;
use AppBundle\Entity\Project;
use AppBundle\Entity\User;
use AppBundle\Entity\UserStory;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
        $user = $this->getUser();
        $em = $this->get('doctrine.orm.entity_manager');
        $pluginManager = $this->get('app.manager.plugin');
        $configuration = $user->getConfiguration() ?? new Configuration();

        if ($request->getMethod() == Request::METHOD_POST) {
            try {
                if (($plugin = $request->get('type')) === null) {
                    throw new \Exception('Plugin data must be persisted.');
                }

                if (!$request->get('language') || !in_array($request->get('language'), ['en', 'lt'])) {
                    throw new \Exception('A valid language must be set.');
                }

                if (!in_array($plugin, $pluginManager->getAvailablePlugins())) {
                    throw new \Exception('There is no plugin with the name `' . $plugin . '` configured');
                }

                $configuration->setLanguage($request->get('language'));
                $pluginManager->submitConfiguration($request->request->all(), $plugin, $configuration);
                $user->setConfiguration($configuration);

                $em->persist($configuration);
                $em->persist($user);
                $em->flush();

                $this->addFlash('success', 'Configuration added successfully');
            } catch (\Exception $e) {
                $this->addFlash('error', $e->getMessage());

                return $this->render('default/configuration.html.twig', [
                    'manager' => $pluginManager,
                    'configuration' => $configuration,
                ]);
            }
        }


        return $this->render('default/configuration.html.twig', [
            'manager' => $pluginManager,
            'configuration' => $configuration,
        ]);
    }

    /**
     * @Route("/synchronize", name="synchronize")
     */
    public function synchronizeAction(Request $request)
    {
        $configuration = $this->getUser()->getConfiguration();

        try {
            if (!$configuration) {
                throw new \Exception('You must provide configuration if you want to synchronize data.');
            }

            return new RedirectResponse($this->get('app.manager.plugin')->getSynchronizationUrl($configuration));
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());

            return new RedirectResponse($this->get('router')->generate('homepage'));
        }
    }

    /**
     * This action is dedicated to redirecting to a plugin action that is responsible
     * for saving the information back to the project management tool
     *
     * @Route("/backsync/{id}", name="backsync")
     *
     * @param Request $request
     * @param $id Project id
     *
     * @return Response
     */
    public function backsyncAction(Request $request, $id): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $configuration = $user->getConfiguration();

        try {
            if (!$configuration) {
                throw new \Exception('You must provide configuration if you want to synchronize data.');
            }

            return new RedirectResponse($this->get('app.manager.plugin')
                ->getBacksyncUrl($configuration, ['id' => $id]));
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());

            return new RedirectResponse($this->get('router')->generate('homepage'));
        }
    }

    /**
     * @Route("/_configuration_form_data", name="configuration_form_data")
     */
    public function getFinishConfigurationFormDataAction(Request $request)
    {
        return new JsonResponse(
            ['form' => $this->get('app.manager.plugin')->getConfigurationSubForm($request->get('plugin'))]
        );
    }

    /**
     * @Route("/validate/{id}", name="validate_project")
     * @Method("POST")
     *
     * @param Request $request
     * @param $id
     *
     * @return Response
     *
     * @throws \Exception
     */
    public function validationAction(Request $request, $id)
    {
        $response = [];
        $project = $this->getDoctrine()->getRepository(Project::class)->find($id);
        /** @var Configuration $configuration */
        $configuration = $this->getUser()->getConfiguration();
        $generator = $this->get('app.generator.use_case')->getGenerator($configuration->getLanguage());

        if (!$project) {
            return new JsonResponse('Project not found', Response::HTTP_NOT_FOUND);
        } elseif (!$generator) {
            return new JsonResponse('No generator found for validation', Response::HTTP_NOT_FOUND);
        }

        $this->get('app.generator.dictionary')->generateDictionary($project);
        $response['similar_nouns'] = $this->get('app.generator.dictionary')->getSimilarNouns($project);

        /** @var UserStory $story */
        foreach ($project->getUserStories() as $story) {
            $storyDictionary = $project->getDictionaryForStory($story);
            $generator->validateStory($story);

            $response['stories'][] = [
                'id' => $story->getId(),
                'title' => $story->getTitle(),
                'valid' => $generator->isStoryValid($story),
                'dictionary' => $storyDictionary,
            ];

            $this->get('doctrine.orm.entity_manager')->persist($story);
        }

        $this->get('doctrine.orm.entity_manager')->flush();

        return new JsonResponse($response);
    }

    /**
     * @Route("/user_story/{id}", name="user_story_update")
     * @Method("POST")
     *
     * @param Request $request
     * @param $id
     *
     * @return Response
     *
     * @throws \Exception
     */
    public function updateStoryAction(Request $request, $id)
    {
        $userStory = $this->getDoctrine()->getRepository(UserStory::class)->find($id);
        /** @var Configuration $configuration */
        $configuration = $this->getUser()->getConfiguration();
        $generator = $this->get('app.generator.use_case')->getGenerator($configuration->getLanguage());

        if (!$userStory) {
            return new JsonResponse('Story not found', Response::HTTP_NOT_FOUND);
        } elseif (!$generator) {
            return new JsonResponse('No generator found for validation', Response::HTTP_NOT_FOUND);
        }

        $title = $request->request->get('title');

        if ($title && $userStory->getTitle() != $title) {
            $userStory->setTitle($title);
            $userStory->setChanged(true);
            $generator->validateStory($userStory);
        }

        $this->get('doctrine.orm.entity_manager')->persist($userStory);
        $this->get('doctrine.orm.entity_manager')->flush();

        return new JsonResponse(['valid' => $userStory->isValid()]);
    }

    /**
     * @Route("/project/{id}/merge_nouns", name="merge_nouns")
     * @Method("POST")
     *
     * @param Request $request
     * @param $id
     *
     * @return Response
     *
     * @throws \Exception
     */
    public function mergeNounsAction(Request $request, $id)
    {
        $project = $this->getDoctrine()->getRepository(Project::class)->find($id);
        $nouns = $request->get('nouns');

        if (!is_array($nouns) || count($nouns) < 2) {
            return new JsonResponse([], Response::HTTP_BAD_REQUEST);
        }

        /** @var Configuration $configuration */
        $configuration = $this->getUser()->getConfiguration();
        $generator = $this->get('app.generator.use_case')->getGenerator($configuration->getLanguage());

        if (!$project) {
            return new JsonResponse('Project not found', Response::HTTP_NOT_FOUND);
        } elseif (!$generator) {
            return new JsonResponse('No generator found for validation', Response::HTTP_NOT_FOUND);
        }

        $this->get('app.generator.dictionary')->mergeNouns(
            $project,
            $request->request->get('keep'),
            $nouns[0] == $request->request->get('keep') ? $nouns[1] : $nouns[0]
            );

        /** @var UserStory $story */
        foreach ($project->getUserStories() as $story) {
            $this->get('doctrine.orm.entity_manager')->persist($story);
        }

        $this->get('doctrine.orm.entity_manager')->persist($project);
        $this->get('doctrine.orm.entity_manager')->flush();

        return new JsonResponse(['some' => $nouns[0] == $request->request->get('keep') ? $nouns[1] : $nouns[0]]);
    }

    /**
     * @Route("/project/{id}/remove_noun", name="remove_noun")
     * @Method("POST")
     *
     * @param Request $request
     * @param $id
     *
     * @return Response
     *
     * @throws \Exception
     */
    public function removeNounAction(Request $request, $id): Response
    {
        $project = $this->getDoctrine()->getRepository(Project::class)->find($id);
        $noun = $request->request->get('noun');

        if (!$project) {
            return new JsonResponse('Project not found', Response::HTTP_NOT_FOUND);
        } elseif (!$noun) {
            return new JsonResponse('No valid noun provided', Response::HTTP_BAD_REQUEST);
        }

        $project->removeDictionaryNoun($noun);
        $this->get('doctrine.orm.entity_manager')->persist($project);
        $this->get('doctrine.orm.entity_manager')->flush();

        return new JsonResponse('success');
    }
}
