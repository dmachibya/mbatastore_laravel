<?php

namespace App\Services;

use App\Models\PdfTemplate;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class PdfGenerator
{
    private $pdfFolderDirectory;
    private $generated_file_name;

    public function __construct()
    {
        $this->pdfFolderDirectory = 'pdf_templates';
    }

    /**
     * Generate a PDF from a Blade template.
     *
     * @param  object|array  $data
     * @param  \App\Models\PdfTemplate  $pdfTemplate
     * @param  mixed  $paperSize
     * @param  string  $action  Supported values are 'download', 'stream', and 'save'.
     * @param  string  $file_path  The path to save the generated PDF file. If empty, the PDF is sent to the browser.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \InvalidArgumentException
     */
    public function generatePdfFromTemplate(object|array $data, PdfTemplate $pdfTemplate, $paperSize = 'a4', string $action, $file_path = null)
    {
        try {
            $pdf = Pdf::loadView($pdfTemplate->blade_template_name, compact('data'));
        } catch (\Exception $e) {
            $defaultPdfTemplate = PdfTemplate::where('is_default', true)->where('type', $pdfTemplate->type)->first();
            $pdf = Pdf::loadView($defaultPdfTemplate->blade_template_name, compact('data'));
        }

        $paperSize = is_array($paperSize)
            ? $this->paperSize(...$paperSize)
            : $paperSize;

        $pdf->setPaper($paperSize, 'portrait');

        $file_name = $this->generated_file_name ?? $this->getNameOfDownloadedPdf($pdfTemplate->name);

        return match ($action) {
            'download' => $pdf->download($file_name),
            'stream' => $pdf->stream($file_name),
            'save' => $pdf->save($file_name),
            default => throw new \InvalidArgumentException("Invalid action: {$action}"),
        };
    }

    /**
     * Returns the paper size in points, given a width and height in any unit.
     *
     * @param  float  $width
     * @param  float  $height
     * @param  string  $scale  The unit of measurement for the width and height. Supported values are 'mm', 'cm', 'inch'.
     * @return array
     */
    private function paperSize(float $width, float $height, string $scale = 'inch'): array
    {
        $pointsPerUnit = $this->getPointsPerUnit($scale);

        $paperWidth = $width * $pointsPerUnit;
        $paperHeight = $height * $pointsPerUnit;

        return [0, 0, $paperWidth, $paperHeight];
    }

    /**
     * Returns the number of points per unit for the given unit of measurement.
     * In DomPDF package every inch equals to 72 points in screen size.
     *
     * @param  string  $unit  The unit of measurement. Supported values are 'mm', 'cm', 'inch'.
     * @return float
     */
    private function getPointsPerUnit(string $unit): float
    {
        return match ($unit) {
            'mm' => 72 / 25.4,
            'cm' => 72 / 2.54,
            'inch' => 72,
            default => 72,
        };
    }

    /**
     * Return the name of the generated PDF file. The name is the given file name
     * with '.pdf' appended.
     *
     * @param string $fileName The name of the generated PDF file.
     * @return string The name of the generated PDF file.
     */
    public function getNameOfDownloadedPdf($fileName)
    {
        return "{$fileName}.pdf";
    }

    /**
     * Set the name of the generated PDF file.
     *
     * @param string $fileName
     * @return $this
     */
    public function setGeneratedFileName($fileName)
    {
        $this->generated_file_name = Str::endsWith($fileName, '.pdf') ? $fileName : "{$fileName}.pdf";

        return $this;
    }
}
