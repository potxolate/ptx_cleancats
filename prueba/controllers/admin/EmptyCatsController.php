<?php


class EmptyCatsController extends ModuleAdminController {
  public function __construct(){
      parent::__construct();
      // Base
      $this->bootstrap = true; // use Bootstrap CSS
      $this->className = 'EmptyCats'; // PHP class name
      $this->allow_export = true; // allow export in CSV, XLS..
  }
  public function initContent()
    {
        parent::initContent();       

        $filename = '../modules/prueba/empty_cats.txt';
        $message = '';

        if (file_exists($filename)) {
            $message = "The file $filename exists";
        } else {
            $message = "The file $filename does not exist";
        }
				
		if (Tools::getRemoteAddr() === "192.168.1.100"){
            $root_cat = $root_cat = Category::getRootCategory($this->context->cookie->id_lang);
            echo dump($root_cat) ;
        }
		$this->context->smarty->assign(array(
            "message" => $message,
            "filename" => $filename
        ));
       // $this->context->smarty->assign("filename", $filename);
		$template_file = _PS_MODULE_DIR_. 'prueba/views/templates/admin/emptycats.tpl';
        $content = $this->context->smarty->fetch($template_file);
        $this->context->smarty->assign(array(
            'content' =>  $content,
        ));
    }
}