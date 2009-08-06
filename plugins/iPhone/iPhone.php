<?php
class iPhone extends Joobsbox_Plugin_Base {
  public function init() {
   
  }
  
  public function filter_head_html(){
    $this->_helper->headMeta()->prependName('viewport', 'width = device-width user-scalable = no');
    //<meta name = "viewport" content = "width = device-width user-scalable = no">
    
    $this->_helper->headLink()->appendStylesheet($this->baseUrl . '/plugins/iPhone/css/iphone.css', "only screen and (max-device-width: 480px)");
    /*<link media="only screen and (max-device-width: 480px)" href="<?php echo $this->baseUrl ?>/plugins/iPhone/css/iphone.css" type= "text/css" rel="stylesheet"> */
    
    $this->_helper->headLink()->headLink(array('rel' => 'apple-touch-startup-image',
                                  'href' => $this->baseUrl . '/plugins/iPhone/images/startup.png'),
                                  'PREPEND');
    
    $this->_helper->headScript()->appendFile($this->baseUrl . '/plugins/iPhone/js/iphone.js', 'text/javascript');
    return false;
  }
}
