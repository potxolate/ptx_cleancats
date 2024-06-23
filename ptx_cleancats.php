<?php

/**
 * 2007-2022 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    PrestaShop SA <contact@prestashop.com>
 *  @copyright 2007-2022 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class Ptx_Cleancats extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'ptx_cleancats';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'potxolate';
        $this->need_instance = 1;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('PTX Clean Categories');
        $this->description = $this->l('Modulo for clean empty categories');

        $this->confirmUninstall = $this->l('');

        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);

        
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        Configuration::updateValue('PRUEBA_LIVE_MODE', false);

        // install Tab to register Controller
        $tab = new Tab();
        $tab->class_name = 'EmptyCats';
        $tab->module = $this->name;
        $tab->id_parent = (int)Tab::getIdFromClassName('DEFAULT');
        $tab->icon = 'settings_applications';
        $languages = Language::getLanguages();
        foreach ($languages as $lang) {
            $tab->name[$lang['id_lang']] = $this->l('Empty Cats');
        }
        $tab->save();

        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('displayBackOfficeHeader') &&
            $this->registerHook('displayAdminAfterHeader') ;  
    }

    public function uninstall()
    {
        Configuration::deleteByName('PRUEBA_LIVE_MODE');

       // include(dirname(__FILE__) . '/sql/uninstall.php');

        return parent::uninstall();
    }

   
    /**
     * Add the CSS & JavaScript files you want to be loaded in the BO.
     */
    public function hookDisplayBackOfficeHeader()
    {
        if (Tools::getValue('configure') == $this->name) {
            $this->context->controller->addJS($this->_path . 'views/js/back.js');
            $this->context->controller->addCSS($this->_path . 'views/css/back.css');
        }
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
        $this->context->controller->addJS($this->_path . '/views/js/front.js');
        $this->context->controller->addCSS($this->_path . '/views/css/front.css');
    }

    public function hookDisplayAdminAfterHeader()
    {
        /* Place your code here. */
        $context = Context::getContext();
        $shops = Shop::getShops($active = true, $id_shop_group = null, $get_as_list_id = false);
        foreach ($shops as $shop){
            $id_shop = $shop['id_shop'];
            $sql = new DbQuery();
            $sql->select('id_category, name');
            $sql->from('category_lang');
            $sql->where('id_shop =' . $id_shop . ' AND  id_category NOT IN (SELECT id_category FROM ps_category_product)');
            $sql->orderBy('id_category');
            $categorias =  Db::getInstance()->executeS($sql);
            echo dump($categorias);
            foreach ($categorias as $categoria) {
                $rcategory = new Category((int)$categoria['id_category'], $context->language->id);
                if ($rcategory->id_parent != 0 && !$rcategory->is_root_category && $categoria['name']==='') {
                    $rcategory->delete();
                    echo "Se ha borrado la categoria ->".$rcategory->id_category.'<br />';
                }
                else{
                    $file = fopen(_PS_MODULE_DIR_.$this->name.'/empty_cats.txt', 'a+');           
                    fwrite($file, $rcategory->id_category.",".$rcategory->name."\r\n");
                    fclose($file);
                    echo "Se ha escrito".$file;
                }
            }
        }


        
        

        
        
    }
}
