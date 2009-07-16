<?php
class iPhone extends Joobsbox_Plugin_Base {
  public function init() {
   
  }
  public function filter_head_html(){
    $meta = '<meta name = "viewport" content = "width = device-width user-scalable = no">';
    $meta .= '<link rel="apple-touch-startup-image" href="/plugins/iPhone/images/startup.png">';
   // $this->view->headScript()->appendFile($this->view->baseUrl() . 'default/public_html/js/prototype.js');
    $this->_helper->headLink()->appendStylesheet($this->baseUrl . 'plugins/iPhone/css/iphone.css');  
    /*<link media="only screen and (max-device-width: 480px)" href="<?php echo $this->baseUrl ?>/plugins/iPhone/css/iphone.css" type= "text/css" rel="stylesheet">
    */return array($meta);
  }
}