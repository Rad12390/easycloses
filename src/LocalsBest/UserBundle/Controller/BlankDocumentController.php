<?php

namespace LocalsBest\UserBundle\Controller;

use LocalsBest\CommonBundle\Controller\SuperController as Controller;
use LocalsBest\UserBundle\Entity\BlankDocument;
use LocalsBest\UserBundle\Form\BlankDocumentType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use ZipArchive;

class BlankDocumentController extends Controller
{
    /**
     * Display Blank Docs Summary Page
     *
     * @return Response
     */
    public function indexAction()
    {
        if (!$this->get('localsbest.checker')->forAddon('blank documents', $this->getUser())) {
            $this->addFlash('danger', 'Sorry you are not authorized to use this feature. If you feel this is in error please open a support ticket');
            return $this->redirectToRoute('locals_best_user_homepage');
        }

        $em = $this->getDoctrine()->getManager();

        $role = $this->getUser()->getRole()->getRole();

        // Get Blank Docs for Current Business
        $blankDocs = $em->getRepository('LocalsBestUserBundle:BlankDocument')
            ->findByBusinessAndRole($this->getBusiness(),$role);

        return $this->render('@LocalsBestUser/blank_document/index.html.twig', [
            'blankDocs' => $blankDocs,
        ]);
    }


    public function pdfAction($id)
    {
        if (!$this->get('localsbest.checker')->forAddon('blank documents', $this->getUser())) {
            $this->addFlash('danger', 'Sorry you are not authorized to use this feature. If you feel this is in error please open a support ticket');
            return $this->redirectToRoute('locals_best_user_homepage');
        }

        $em = $this->getDoctrine()->getManager();

        // Get current Business
        $business = $this->getBusiness();

        // Get Blank Doc
        $blankDoc = $em->getRepository('LocalsBestUserBundle:BlankDocument')->findOneBy([
            'id' => $id,
            'business' =>$business
        ]);
       
        return $this->render(
            '@LocalsBestUser/blank_document/pdf_form.html.twig',
            [
                'file' => $blankDoc,
                'bucket' => 'blank_docs',
            ]
        );
    }

    /**
     * Preview for Blank Doc
     *
     * @param $id
     * @return Response
     */
    public function showAction($id)
    {
        if (!$this->get('localsbest.checker')->forAddon('blank documents', $this->getUser())) {
            $this->addFlash('danger', 'Sorry you are not authorized to use this feature. If you feel this is in error please open a support ticket');
            return $this->redirectToRoute('locals_best_user_homepage');
        }

        $em = $this->getDoctrine()->getManager();

        // Get current Business
        $business = $this->getBusiness();

        // Get Blank Doc
        $blankDoc = $em->getRepository('LocalsBestUserBundle:BlankDocument')->findOneBy([
            'id' => $id,
            'business' =>$business
        ]);

        return $this->render(
            '@LocalsBestUser/blank_document/show.html.twig',
            [
                'file' => $blankDoc,
                'bucket' => 'file', 
            ]
        );
    }

    /**
     * Create new Blank Doc
     *
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function createAction(Request $request)
    {
        if (!$this->get('localsbest.checker')->forAddon('blank documents', $this->getUser())) {
            $this->addFlash('danger', 'Sorry you are not authorized to use this feature. If you feel this is in error please open a support ticket');
            return $this->redirectToRoute('locals_best_user_homepage');
        }

        // Get current User
        $user = $this->getUser();

        // Check User Role
        if ($user->getRole()->getLevel() > 5) {
            throw $this->createAccessDeniedException();
        }

        $em = $this->getDoctrine()->getManager();
        // Create new Blank Doc object
        $blankDoc = new BlankDocument();
        // Get current Business
        $business = $this->getBusiness();
        // Find Doc Type using Doc Rules
        $documentsRaw = $em->getRepository('LocalsBestUserBundle:DocRule')->findForNRD($business);

        $documents = [];
        // Get doc type names
        foreach ($documentsRaw as $item) {
            if ($item['documentName'] == '') {
                continue;
            }
            $documents[$item['documentName']] = $item['documentName'];
        }
        // If user choose General Blank Doc Type
        $titleValue = '';

        if(
            isset($request->request->all()['localsbest_userbundle_blankdocument'])
            && isset($request->request->all()['localsbest_userbundle_blankdocument']['title'])
        ) {
            $titleValue = $request->request->all()['localsbest_userbundle_blankdocument']['title'];
        }

        $form = $this->createForm(BlankDocumentType::class, $blankDoc, [
            'documents' => $documents,
            'business' => $business,
            'titleValue' => $titleValue,
        ]);

        if($request->isMethod('POST')) {
            $form->handleRequest($request);

            if($form->isValid()) {
                $blankDoc->setBusiness($business);
                // Save new Blank Doc
                $em->persist($blankDoc);
                // Update DB
                $em->flush();

                return $this->redirectToRoute('blank_docs_index');
            }
        }

        return $this->render('@LocalsBestUser/blank_document/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Edit Blank Doc / Freeze for NOW
     *
     * @param Request $request
     * @param $id
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, $id)
    {
        if (!$this->get('localsbest.checker')->forAddon('blank documents', $this->getUser())) {
            $this->addFlash('danger', 'Sorry you are not authorized to use this feature. If you feel this is in error please open a support ticket');
            return $this->redirectToRoute('locals_best_user_homepage');
        }

        $user = $this->getUser();

        if($user->getRole()->getLevel() > 5) {
            throw $this->createAccessDeniedException();
        }

        $em = $this->getDoctrine()->getManager();

        $blankDoc = $em->getRepository('LocalsBestUserBundle:BlankDocument')->find($id);

        $business = $this->getBusiness();

        $documentsRaw = $em->getRepository('LocalsBestUserBundle:DocRule')->findForNRD($business);

        $documents = [];

        foreach ($documentsRaw as $item) {
            if($item['documentName'] == '') {
                continue;
            }
            $documents[$item['documentName']] = $item['documentName'];
        }

        $form = $this->createForm(BlankDocumentType::class, $blankDoc, ['documents' => $documents]);

        if($request->isMethod('POST')) {
            $form->handleRequest($request);

            if($form->isValid()) {
                $em->flush();

                return $this->redirectToRoute('blank_docs_index');
            }
        }

        return $this->render('@LocalsBestUser/blank_document/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Download Blank Doc File
     *
     * @param $id
     */
    public function downloadAction($id)
    {
        if (!$this->get('localsbest.checker')->forAddon('blank documents', $this->getUser())) {
            $this->addFlash('danger', 'Sorry you are not authorized to use this feature. If you feel this is in error please open a support ticket');
            return $this->redirectToRoute('locals_best_user_homepage');
        }

        $em = $this->getDoctrine()->getManager();
        // Get current Business
        $business = $this->getBusiness();
        // Get Blank Doc object
        $blankDoc = $em->getRepository('LocalsBestUserBundle:BlankDocument')->findOneBy([
            'id' => $id,
            'business' =>$business
        ]);

        // Send File content to User
        $sdk = $this->get('aws_sdk');
        $s3  = $sdk->createS3();

        $obj = $s3->getObject(array(
            'Bucket' => $this->getParameter('transactions_bucket'),
            'Key' => $blankDoc->getFileName()
        ));

        if (ob_get_level()) {
            ob_end_clean();
        }
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . $blankDoc->getFileName());
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');

        echo $obj['Body'];
        exit;
    }

    /**
     * Display Modal Window for Transaction View Page
     *
     * @param Request $request
     * @return Response
     */
    public function transactionViewAction(Request $request)
    {
        // Check Request for AJAX
        if ($request->isXmlHttpRequest()) {
            if (!$this->get('localsbest.checker')->forAddon('blank documents', $this->getUser())) {
                $this->addFlash('danger', 'Sorry you are not authorized to use this feature. If you feel this is in error please open a support ticket');
                return $this->redirectToRoute('locals_best_user_homepage');
            }

            $em = $this->getDoctrine()->getManager();
            // Get current Business
            $business = $this->getBusiness();

            $role = $this->getUser()->getRole()->getRole();
            // Get ID's of selected Doc Types
            $documentTypes = $request->query->get('documents');
            // Get Type (Listing or Closing)
            $type = $request->query->get('type');
            // Get Doc Types objects
            $docs = $em
                ->getRepository('LocalsBestUserBundle:DocumentType')
                ->findBy(['id' => $documentTypes])
            ;

            $docTypeNames = [];
            // Get names of Doc Types
            foreach ($docs as $item) {
                $docTypeNames[$item->getId()] = $item->getName();
            }
            // Get Blank Docs by Business, Type and Doc Types Names
            $blankDocs = $em
                ->getRepository('LocalsBestUserBundle:BlankDocument')
                ->findTransactionDocsByBusinessTypeAndRole($business, $role, $docTypeNames, $type)
            ;

            $blankDocsNames = [];
            // Create array with Blank Doc Name and ID
            foreach ($blankDocs as $item) {
                $blankDocsNames[$item->getId()] = $item->getTitle();
            }
            // Render view
            return $this->render(
                '@LocalsBestUser/blank_document/transaction-view.html.twig',
                [
                    'docTypes' => $docTypeNames,
                    'blankDocs' => $blankDocsNames,
                ]
            );
        }
        die('403');
    }

    /**
     * Download several files as ZIP file.
     *
     * @param Request $request
     */
    public function downloadZipAction(Request $request)
    {
        if (!$this->get('localsbest.checker')->forAddon('blank documents', $this->getUser())) {
            $this->addFlash('danger', 'Sorry you are not authorized to use this feature. If you feel this is in error please open a support ticket');
            return $this->redirectToRoute('locals_best_user_homepage');
        }

        $em = $this->getDoctrine()->getManager();
        // Get available Blank Docs for download
        $blankDocs = $request->request->get('documents');
        // Get Blank Docs objects
        $docs = $em->getRepository('LocalsBestUserBundle:BlankDocument')->findBy([
            'id' => $blankDocs,
        ]);

        // Create new ZIP object
        $zip = new ZipArchive();
        $zipName = 'Blank-Documents-'
            . str_replace(' ', '-', $this->getUser()->getFullName()) . '-'
            . date("m-d-Y", time())
            . ".zip"
        ;
        // Create ZIP file
        $zip->open($zipName,  ZipArchive::CREATE);

        $sdk = $this->get('aws_sdk');
        $s3  = $sdk->createS3();

        foreach ($docs as $doc) {
            $obj = $s3->getObject(array(
                'Bucket' => $this->getParameter('transactions_bucket'),
                'Key' => $doc->getFileName()
            ));

            if (ob_get_level()) {
                ob_end_clean();
            }
            // Add file to ZIP
            $zip->addFromString(basename($doc->getFileName()),  $obj['Body']);
        }
        $zip->close();

        // Send ZIP file to User
        header('Content-Type', 'application/zip');
        header('Content-disposition: attachment; filename="' . $zipName . '"');
        header('Content-Length: ' . filesize($zipName));
        readfile($zipName);

        // Delete ZIP file from server
        unlink($zipName);
        exit;
    }

    /**
     * Delete Blank Doc
     *
     * @param $id
     * @return RedirectResponse
     */
    public function deleteAction($id)
    {
        if (!$this->get('localsbest.checker')->forAddon('blank documents', $this->getUser())) {
            $this->addFlash('danger', 'Sorry you are not authorized to use this feature. If you feel this is in error please open a support ticket');
            return $this->redirectToRoute('locals_best_user_homepage');
        }

        // Get current User
        $user = $this->getUser();

        // Check User Role
        if ($user->getRole()->getLevel() > 5) {
            throw $this->createAccessDeniedException();
        }
        $em = $this->getDoctrine()->getManager();

        // Get Blank Doc object
        $blankDoc = $em->getRepository('LocalsBestUserBundle:BlankDocument')->find($id);
        // delete Blank Doc
        $em->remove($blankDoc);
        // update DB
        $em->flush();
        // redirect User to Blank Docs Summary Page
        return $this->redirectToRoute('blank_docs_index');
    }

    
}
