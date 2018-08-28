<?php

namespace App\Service;


use App\Entity\Recipe;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\Translation\TranslatorInterface;
use TCPDF;
use Twig\Environment;

class PDFExportService
{

    /**
     * @var EngineInterface|Environment
     */
    protected $templating;

    /**
     * @var \Symfony\Component\Translation\TranslatorInterface
     */
    protected $translator;
    private $webRoot;

    /**
     * MailerService constructor.
     *
     * @param \Swift_Mailer $mailer
     *
     * @param Environment $templating
     * @param \Symfony\Component\Translation\TranslatorInterface $translator
     * @param array $params
     */
    public function __construct(\Swift_Mailer $mailer, Environment $templating, TranslatorInterface $translator, $rootDir)
    {
        $this->mailer = $mailer;
        $this->templating = $templating;
        $this->translator = $translator;
        $this->webRoot = realpath($rootDir . '/../public');
    }

    public function generateRecipePDF(Recipe $recipe)
    {
        $file = realpath($this->webRoot.'/css/app.css');
        $style = file_get_contents($file);

        $content = $this->templating->render('pdf/recipe.html.twig', ['recipe' => $recipe, 'style' => $style]);

        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor($recipe->getAuthor()->getFullName());
        $pdf->SetTitle($recipe->getTitle());
        $pdf->SetSubject('2tasty recipe');
        $pdf->SetKeywords('recipe, '.join(', ', $recipe->getTags()->toArray()));

        $pdf->AddPage();
        $pdf->writeHTML($content, true, false, true, false, '');

        $pdf->Output($recipe->getSlug().'.pdf', 'D');
    }
}
