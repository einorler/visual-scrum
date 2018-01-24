<?php

namespace AppBundle\Controller;

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
        $useCases = $this->get('app.generator.use_case')->generateUseCases($userStories);

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
}
