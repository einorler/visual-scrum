<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ClassDiagram;
use AppBundle\Entity\Project;
use AppBundle\Entity\UseCaseDiagram;
use AppBundle\Generator\XmlGenerator;
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
        $em = $this->get('doctrine.orm.entity_manager');
        /** @var Project $project */
        $project = $em->getRepository(Project::class)->find($id);
        $userStories = $project->getUserStories();
        $language = $this->getUser()->getConfiguration()->getLanguage();
        $generator = $this->get('app.generator.use_case')->getGenerator($language);

        if (!$generator) {
            $this->addFlash('error', 'Invalid language set in configuration');

            return $this->redirectToRoute('project', ['id' => $id]);
        }

        $useCases = $generator->generateUseCases($userStories);

        try {
            $filename = $this->get('app.client.yuml')->fetchUseCaseDiagram($useCases);
            $xmlGenerator = new XmlGenerator();
            $xmlFileName = uniqid() . '.xmi';
            $fullName = __DIR__ . '/../../../web/diagrams/' . $xmlFileName;
            file_put_contents($fullName, $xmlGenerator->generateUseCaseXml($useCases));

            $diagram = new UseCaseDiagram();
            $diagram->setProject($project);
            $diagram->setFile($filename);
            $diagram->setTitle($project->getTitle());
            $diagram->setXmiFile($xmlFileName);

            $em->persist($diagram);
            $em->flush($diagram);
            $this->addFlash('success', 'Diagram creation successful!');
        } catch (\Exception $e) {
            $this->addFlash('error', 'There was an error generating the diagram, please try again later');
        }

        return $this->redirectToRoute('project', ['id' => $id]);
    }

    /**
     * @param Request $request
     * @param $id
     *
     * @return Response
     *
     * @Route("/generate/class/{id}", name="class-generation")
     */
    public function classAction(Request $request, $id)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        /** @var Project $project */
        $project = $em->getRepository(Project::class)->find($id);
        $dictionary = $project->getDictionaryNouns();

        if (empty($dictionary)) {
            $this->addFlash(
                'error',
                'You don\'t have any items in your dictionary. Please go through the process of validation first.'
            );

            return $this->redirectToRoute('project', ['id' => $id]);
        }

        try {
            $filename = $this->get('app.client.yuml')->fetchClassDiagram($dictionary);

            $diagram = new ClassDiagram();
            $diagram->setProject($project);
            $diagram->setFile($filename);
            $diagram->setTitle($project->getTitle());
            $diagram->setXmiFile('fake...');

            $em->persist($diagram);
            $em->flush($diagram);
            $this->addFlash('success', 'Diagram creation successful!');
        } catch (\Exception $e) {
            $this->addFlash('error', 'There was an error generating the diagram, please try again later');
        }

        return $this->redirectToRoute('project', ['id' => $id]);
    }
}
