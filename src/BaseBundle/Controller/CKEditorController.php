<?php

namespace BaseBundle\Controller;

use BaseBundle\Base\BaseController;
use BaseBundle\Tools\Gd;
use BaseBundle\Tools\Math;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;

/**
 * @Route("/ckeditor")
 */
class CKEditorController extends BaseController
{
    /**
     * @Route("/browse", name="ckeditor_browse")
     * @Method({"GET"})
     * @Template("BaseBundle:CKEditor:gallery.html.twig")
     */
    public function browseAction(Request $request)
    {
        $CKEditor = $request->query->get('CKEditor');
        $funcNum  = $request->query->get('CKEditorFuncNum');
        $langCode = $request->query->get('langCode');

        if (!$this->isGranted($this->getParameter('role_file_upload'))) {
            throw $this->createAccessDeniedException();
        }

        if (!$CKEditor || !$funcNum || !$langCode) {
            throw $this->createNotFoundException();
        }

        $dir    = $this->getParameter('kernel.root_dir').'/../web/upload/ckeditor/';
        $images = array_map('basename', glob("{$dir}/*.png"));
        $pager  = $this->getPager($images);

        return [
            'CKEditor' => $CKEditor,
            'funcNum'  => $funcNum,
            'pager'    => $pager,
        ];
    }

    /**
     * Uploads through CKEditor.
     *
     * @see http://stackoverflow.com/a/25181208/731138
     *
     * @Route("/upload", name="ckeditor_upload")
     * @Method({"POST"})
     * @Template("BaseBundle:CKEditor:callback.html.twig")
     */
    public function uploadAction(Request $request)
    {
        if (!$this->isGranted($this->getParameter('role_file_upload'))) {
            throw $this->createAccessDeniedException();
        }

        if ($request->cookies->get('ckCsrfToken') !== $request->request->get('ckCsrfToken')) {
            throw new InvalidCsrfTokenException('Invalid CSRF token');
        }

        $CKEditor = $request->query->get('CKEditor');
        $funcNum  = $request->query->get('CKEditorFuncNum');
        $langCode = $request->query->get('langCode');
        $upload   = $request->files->get('upload');

        if (!$CKEditor || !$funcNum || !$langCode || !$upload) {
            throw $this->createNotFoundException();
        }

        $source = $upload->getPathName();
        $target = $this->getParameter('kernel.root_dir').'/../web/upload/ckeditor/'.date('Ymd-His-').Math::rand(8).'.png';
        $error  = null;
        $img    = Gd::load($source);
        if (!$img) {
            $error = $this->trans('base.ckeditor.invalid_file');
        } else {
            Gd::save($img, $target);
        }

        return [
            'CKEditor' => $CKEditor,
            'funcNum'  => $funcNum,
            'target'   => basename($target),
            'error'    => $error,
        ];
    }

    /**
     * @Route("/remove/{token}/{name}", name="ckeditor_remove")
     * @Method({"GET"})
     * @Template("BaseBundle:CKEditor:gallery.html.twig")
     */
    public function removeAction(Request $request, $token, $name)
    {
        $this->checkCsrfToken('gallery', $token);

        if (!$this->isGranted($this->getParameter('role_file_upload'))) {
            throw $this->createAccessDeniedException();
        }

        $dir  = realpath($this->getParameter('kernel.root_dir').'/../web/upload/ckeditor/');
        $file = $dir.'/'.$name;

        if (is_file($file) && strncmp($dir, realpath($file), strlen($dir)) == 0) {
            unlink($file);
            $this->success($this->trans('base.ckeditor.file_removed'));
        }

        return new RedirectResponse(
           $this->generateUrl('ckeditor_browse', $request->query->all())
        );
    }
}
