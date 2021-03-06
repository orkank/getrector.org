<?php

declare(strict_types=1);

namespace Rector\Website\Research\Controller;

use Rector\Website\Research\Entity\ResearchAnswer;
use Rector\Website\Research\Form\ResearchFormType;
use Rector\Website\Research\Repository\ResearchAnswerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class ResearchController extends AbstractController
{
    private ResearchAnswerRepository $researchAnswerRepository;

    public function __construct(ResearchAnswerRepository $researchAnswerRepository)
    {
        $this->researchAnswerRepository = $researchAnswerRepository;
    }

    /**
     * @Route(path="research/thank-you", name="research_thank_you")
     */
    public function __invoke(Request $request): Response
    {
        $researchForm = $this->createForm(ResearchFormType::class, null, [
            // this is needed for manual render
            'action' => $this->generateUrl('research'),
        ]);
        $researchForm->handleRequest($request);

        if ($researchForm->isSubmitted() && $researchForm->isValid()) {
            return $this->processFormAndRedirectToThankYou($researchForm);
        }

        return $this->render('research/research.twig', [
            'research_form' => $researchForm->createView(),
        ]);
    }

    private function processFormAndRedirectToThankYou(FormInterface $form): RedirectResponse
    {
        /** @var ResearchAnswer $researchAnswer */
        $researchAnswer = $form->getData();

        $this->researchAnswerRepository->save($researchAnswer);

        return $this->redirectToRoute('research_thank_you');
    }
}
