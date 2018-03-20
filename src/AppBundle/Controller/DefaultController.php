<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Configuration;
use AppBundle\Entity\Project;
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
     * @Route("/_configuration_form_data", name="configuration_form_data")
     */
    public function getFinishConfigurationFormDataAction(Request $request)
    {
        return new JsonResponse(
            ['form' => $this->get('app.manager.plugin')->getConfigurationSubForm($request->get('plugin'))]
        );
    }
}
