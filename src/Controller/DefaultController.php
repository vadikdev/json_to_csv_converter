<?php

namespace App\Controller;

use App\FileCreator\FileCreator;
use App\Form\ConversionType;
use App\Model\Conversion;
use App\Parser\FileParser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    public function __construct(
        protected FormFactoryInterface $formFactory,
        protected FileCreator $creator,
        protected FileParser $parser
    )
    {}

    #[Route('/')]
    public function index(Request $request)
    {
        $conversion = new Conversion();
        $form = $this->formFactory->create(ConversionType::class, $conversion);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $tmp = sys_get_temp_dir();
            $conversion->getFile()->move($tmp);

            try {
                $data = $this->parser->parse($conversion->getFile());

                // FileCreatorInterface returns BinaryFileResponse
                return $this->creator->create($conversion->getMime(), $data);
            } catch (\Exception $exception) {
                $this->addFlash('danger', $exception->getMessage());
            }
        }

        return $this->render('conversion.html.twig', ['form' => $form->createView()]);
    }
}
