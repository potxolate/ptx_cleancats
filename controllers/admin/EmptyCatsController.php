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

        $filename = '../modules/ptx_cleancats/empty_cats.txt';
        $message = '';

        if (file_exists($filename)) {
            $message = "The file $filename exists";
        } else {
            $message = "The file $filename does not exist";
        }
		
        $template_file = _PS_MODULE_DIR_. 'ptx_cleancats/views/templates/admin/emptycats.tpl';
        $content = $this->context->smarty->fetch($template_file);
        
        $this->context->smarty->assign(array(
            "message" => $message,
            "filename" => $filename,
            'content' =>  $content,
        ));
        
    }
}