<?php

namespace Modules\Free\Controllers;

/**
 * Scope controller for serving static assets.
 *
 * Handles requests for CSS, JavaScript, and font files with proper
 * caching headers and MIME types. This controller serves as the
 * delivery mechanism for all static assets in the system.
 *
 * @package   Modules\Free\Controllers
 * @author    Alexandr Nikitin
 * @version   16.0
 * @copyright (c) 2026
 */
class Scope extends \Application\Assistance\Controller\ApiController
{
    use \Application\Assistance\Controller\TraitClass;

    /**
     * Constructor.
     *
     * @param \Application\Assistance\Request $request Request object
     */
    public function __construct($request)
    {
        parent::__construct($request);
    }

    /**
     * Serve static CSS and JavaScript files.
     *
     * Retrieves the file by hash from the static files table,
     * sets appropriate caching headers, and outputs the file content.
     *
     * @return void
     */
    public function getAction()
    {
        if ($hash = $this->_request->getParams('static')) {
            if ($file = \Application\Helpers\File::getStatic(preg_replace("/(\.*)$/", '', $hash))) {
                $content = file_get_contents(ROOT_PATH . $file);

                header('Content-Type: ' . (preg_match("/css$/", $file) ? 'text/css' : 'application/javascript'));
                header('Cache-Control: public,max-age=3600,s-maxage=900');
                header('ETag: ' . $hash);
                header("Last-Modified: Sun Jan 01 2023 03:00:00 GMT");
                header("Pragma: public");
                header("Expires: " . gmdate("D, d M Y H:i:s", time() + 86400 * 30) . " GMT");

                die($content);
            }
        }
        die();
    }

    /**
     * Serve font files.
     *
     * Serves font files with proper MIME types and caching headers.
     * Supports various font formats: OTF, TTF, EOT, SVG, WOFF, WOFF2.
     *
     * @return void
     */
    public function fontsAction()
    {
        $file = $this->_request->getParams('file');

        if ($file && file_exists(ROOT_PATH . 'Application' . DIRECTORY_SEPARATOR . 'Public' . DIRECTORY_SEPARATOR . 'fonts' . DIRECTORY_SEPARATOR . $file)) {
            $types = [
                'otf'   => 'font/otf',
                'ttf'   => 'font/ttf',
                'eot'   => 'application/vnd.ms-fontobject',
                'svg'   => 'image/svg+xml',
                'woff'  => 'font/woff',
                'woff2' => 'font/woff2'
            ];

            preg_match("/[^\.]{1,}$/", $file, $ext);
            $content = file_get_contents(ROOT_PATH . 'Application' . DIRECTORY_SEPARATOR . 'Public' . DIRECTORY_SEPARATOR . 'fonts' . DIRECTORY_SEPARATOR . $file);

            header('Content-Type: ' . (isset($types[$ext[1]]) ? $types[$ext[1]] : 'text/plain'));
            header('Cache-Control: public,max-age=3600,s-maxage=900');
            header('ETag: ' . md5($file));
            header("Last-Modified: Sun Jan 01 2023 03:00:00 GMT");
            header("Pragma: public");
            header("Expires: " . gmdate("D, d M Y H:i:s", time() + 86400 * 30) . " GMT");

            die($content);
        }
        die();
    }
}