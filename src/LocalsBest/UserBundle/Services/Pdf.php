<?php

namespace LocalsBest\UserBundle\Services;

/**
 * The PDF service
 *
 * @author J.Wesley Hulette
 */
class Pdf
{
    /**
     * Generate thumbnails of each page from a PDF
     *
     * @param array $sourcePdfs An array pdf to generatate thumbnails from
     *
     * @return array An array of thumbnail image links
     */
    public function generateThumbnails($sourcePdfs)
    {
        foreach ($sourcePdfs as $pdf) {
            $sourceFolder = dirname($pdf);
            $thumbFolder = $this->createDirectory($sourceFolder, 'thumbs');
            $this->generateThumbnailsFromPdf($pdf, $thumbFolder);
        }
        // Get thumbnails
        $thumbnails = array();
        foreach (glob("$thumbFolder/*.jpg", GLOB_NOSORT) as $thumb) {
            $thumbnails[] = $thumb;
        }
        
        return $thumbnails;
        
    }

    /**
     * Create a new pdf from pages of current pdf
     *
     * @param array $thumbs The thumbnails to which should make up the new pdf
     * @param string $newPdfName The new pdf to create
     *
     * @return string the newly created pdf
     */
    public function createPDF($thumbs, $newPdfName)
    {
        $resArray = [];

        foreach ($thumbs as $page) {
            $sourceFolder = rtrim(dirname($page), '/thumbs');
            $sourceFile = explode('/', $sourceFolder);
            $sourceFileName = last($sourceFile) . '.pdf';
            $sourceFile = implode('/', $sourceFile) . '/' . $sourceFileName;
            $resArray[] =  $sourceFile . ' ' . ($this->getPageNunmberFromThumbnail($page) + 1);
            //$this->splitPdf($page, $pdfDir);
        }

        $result = implode(' ', $resArray);

//        $convert = 'PATH=$PATH:/usr/local/bin;export PATH;/usr/local/bin/convert';
//        $convert = 'PATH=$PATH:/usr/local/bin;export PATH;/usr/local/bin/qpdf';
        $convert  = 'qpdf';
        exec("$convert $sourceFile --pages $result -- $sourceFolder/$newPdfName");

        foreach ($thumbs as $page) {
            unlink($page);
        }

        return $sourceFolder . '/' . $newPdfName;
    }

    /**
     * Generate a thumbnails preview of each page of a pdf
     *
     * @param string $sourcePdf The file to generate the thumbnails from
     * @param string $thumbFolder The folder to store the pdfs
     *
     */
    private function generateThumbnailsFromPdf($sourcePdf, $thumbFolder)
    {
        
        $fileName = basename($sourcePdf);
       
        // Need the path info for the ghostscript delegate
//        $convert = 'PATH=$PATH:/usr/bin;export PATH;/usr/bin/convert';

        // Command for ImageMagic
//        exec("$convert -colorspace RGB +sigmoidal-contrast 11.6933 \
//        -define filter:filter=Sinc -define filter:window=Jinc -define filter:lobes=3 \
//        -resize 200% -quality 100 -sigmoidal-contrast 11.6933 -colorspace sRGB -background white -alpha remove $sourcePdf $thumbFolder/$fileName-thumb_%d.jpg");

        $convert  = 'convert';
        // Command for GraphicMagic
        exec("$convert $sourcePdf +adjoin $thumbFolder/$fileName-thumb_%d.jpg");
    }

    /**
     * Remove the temporary pdfs
     *
     * @param string $dirname
     */
    private function removeTempDir($dirname)
    {
        array_map('unlink', glob("$dirname/*.*"));
        rmdir($dirname);
    }

    /**
     * Merge the pdf pages into one document
     *
     * @param string $sourceFolder The folder to save the new pdf in
     * @param string $pdfDir The location of the pdf pages to merge
     * @param string $newPdfName The name of the new pdf to create
     */
    private function mergePdfFiles($sourceFolder, $pdfDir, $newPdfName)
    {
        $pages = '';
        foreach (glob("$pdfDir/*.pdf", GLOB_NOSORT) as $page) {
            $pages .= $page . ' ';
        }
        $newPdf = "$sourceFolder/$newPdfName";
        exec("/usr/bin/gs -dBATCH -dNOPAUSE -q -sDEVICE=pdfwrite -dPDFSETTINGS=/prepress \
       -sOutputFile=$newPdf $pages");
    }

    /**
     * Create a new pdf for each page
     *
     * @param string $thumbnail
     * @param string $pdfDir The directory to hold the extracted pages
     */
    private function splitPdf($thumbnail, $pdfDir)
    {
        // array $pages The page numbers to extract from the source
        // string $sourcePdf The pdf to extract pages from
        $sourcePdf = $this->getOrignalPdfFromThumbnail($thumbnail);
        $pageNumber = $this->getPageNunmberFromThumbnail($thumbnail);
        $page = $pageNumber + 1; // Pages are not zero based
        $outfile = "$pdfDir/outfile" . mt_rand() . "_p$page.pdf";
        exec("/usr/bin/gs -sDEVICE=pdfwrite -dNOPAUSE -dBATCH -dSAFER \
       -dFirstPage=$page -dLastPage=$page \
       -sOutputFile=$outfile $sourcePdf");
    }

    /**
     * Get the page number from the thumbnail name
     *
     * @param string $thumb The thumbnail file name to parse
     *
     * @return string
     */
    private function getPageNunmberFromThumbnail($thumb)
    {
        $base = explode('thumb_', basename($thumb));
        return rtrim($base[1], '.jpg');
    }

    /**
     * Get the original pdf from the thumbnail name
     *
     * @param string $thumb The thumbnail file name to parse
     *
     * @return string
     */
    private function getOrignalPdfFromThumbnail($thumb)
    {
        $dir = explode('thumbs', dirname($thumb));
        $base = explode('-thumb', basename($thumb));
        return $dir[0] . $base[0];
    }

    /**
     * Create a directory
     *
     * @param string $root The root directory
     * @param string $folder The folder to create
     *
     * @return string
     */
    private function createDirectory($root, $folder)
    {
        $dir = $root . DIRECTORY_SEPARATOR . $folder;
        if (!is_dir($dir)) {
            mkdir($dir);
        }
        return $dir;
    }
}