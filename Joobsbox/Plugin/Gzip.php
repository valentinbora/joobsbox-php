<?php
class Joobsbox_Plugin_Gzip extends Zend_Controller_Plugin_Abstract
{

    public function postDispatch(Zend_Controller_Request_Abstract $request)
    {
   
        $content = $this->getResponse()->getBody();

        $content = preg_replace(
            array(
                '/(\x20{2,})/',   // extra-white spaces
                '/\t/',           // tab
                '/\n\r/'          // blank lines
            ),
            array(' ', '', ''),
            $content
        );

        // if the browser does not support gzip, serve the stripped content
        if (@strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') === FALSE) {
            $this->getResponse()->setBody($content);
        }
        else {
            $this->getResponse()->setHeader('Content-Encoding', 'gzip', true);
            $this->getResponse()->setBody(gzencode($content, 9));
        }
    }
}