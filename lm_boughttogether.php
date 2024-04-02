<?php

if (!defined('_PS_VERSION_')) {
  exit;
}

use PrestaShop\PrestaShop\Core\Product\ProductExtraContent;
require_once _PS_MODULE_DIR_ . 'lm_boughttogether/classes/LmProductBoughtTogether.php';

class Lm_BoughtTogether extends Module {
  
  const INSTALL_SQL_FILE = '/sql/install.sql';
  const UNINSTALL_SQL_FILE = '/sql/uninstall.sql';

  public function __construct() {
    $this->name = 'lm_boughttogether';
    $this->tab = 'front_office_features';
    $this->version = '1.0.0';
    $this->author = 'Loïc MARTIN';
    $this->need_instance = 0;
    $this->ps_versions_compliancy = [ 'min' => '1.7', 'max' => _PS_VERSION_ ];
    $this->bootstrap = true;
    
    parent::__construct();

    $this->displayName = $this->l('Products Bought Together');
    $this->description = $this->l('Display products bought together');

    $this->confirmUninstall = $this->l('Are you sure you wan\'t to uninstall the module ?');

    if (!Configuration::get('LM_BOUGHTTOGETHER_PAGENAME')) {
        $this->warning = $this->l('No name provided');
    }
  }

  public function install() {
    if (Shop::isFeatureActive()) {
        Shop::setContext(Shop::CONTEXT_ALL);
    }
  
    if (!parent::install() ||
        // !$this->registerHook('displayProductExtraContent') ||
        !$this->registerHook('header') ||
        !$this->registerHook('displayProductBoughtTogether') ||
        !Configuration::updateValue('LM_BOUGHTTOGETHER_PAGENAME', 'Access Key') ||
        !$this->installSQL()
    ) {
        return false;
    }
  
    return true;
  }

  protected function installSQL() {
    // Create database tables from install.sql
    if (!file_exists(dirname(__FILE__) . self::INSTALL_SQL_FILE)) {
      return false;
    }

    if (!$sql = Tools::file_get_contents(dirname(__FILE__) . self::INSTALL_SQL_FILE)) {
      return false;
    }

    $replace = array('PREFIX' => _DB_PREFIX_, 'ENGINE_DEFAULT' => _MYSQL_ENGINE_ );

    $sql = strtr($sql, $replace);
    $sql = preg_split("/;\s*[\r\n]+/", $sql);

    foreach ($sql as &$q) {
      if ($q && count($q) && !Db::getInstance()->Execute(trim($q))) {
        return false;
      }
    }

    // Clean memory
    unset($sql, $q, $replace);
    return true;
  }
  
  public function uninstall() {
    if (!parent::uninstall() ||
        !Configuration::deleteByName('LM_BOUGHTTOGETHER_PAGENAME') ||
        !$this->uninstallSQL()
    ) {
        return false;
    }
    return true;
  }
  
  protected function uninstallSQL() {
    // Create database tables from uninstall.sql
    if (!file_exists(dirname(__FILE__) . self::UNINSTALL_SQL_FILE)) {
        return false;
    }

    if (!$sql = Tools::file_get_contents(dirname(__FILE__) . self::UNINSTALL_SQL_FILE)) {
        return false;
    }

    $replace = array(
        'PREFIX' => _DB_PREFIX_,
        'ENGINE_DEFAULT' => _MYSQL_ENGINE_,
    );

    $sql = strtr($sql, $replace);
    $sql = preg_split("/;\s*[\r\n]+/", $sql);

    foreach ($sql as &$q) {
      if ($q && count($q) && !Db::getInstance()->Execute(trim($q))) {
        return false;
      }
    }

    // Clean memory
    unset($sql, $q, $replace);
    return true;
  }

  public function getContent() {
    if (Tools::isSubmit('btnSubmit')) {
      $pageName = strval(Tools::getValue('LM_BOUGHTTOGETHER_PAGENAME'));
      if (
          !$pageName||
          empty($pageName)
      ) {
          $output .= $this->displayError($this->l('Invalid Configuration value'));
      } else {
          Configuration::updateValue('LM_BOUGHTTOGETHER_PAGENAME', $pageName);
          $output .= $this->displayConfirmation($this->l('Settings updated'));
      }
    }

    return $output.$this->displayForm();
  }

  public function displayForm() {
    // Récupère la langue par défaut
    $defaultLang = (int)Configuration::get('PS_LANG_DEFAULT');
  
    // Initialise les champs du formulaire dans un tableau
    $form = array(
        'form' => array(
            'legend' => array(
                'title' => $this->l('Settings'),
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Access Key Value'),
                    'name' => 'LM_BOUGHTTOGETHER_PAGENAME',
                    'size' => 20,
                    'required' => true
                )
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'name'  => 'btnSubmit'
            )
        ),
    );
  
    $helper = new HelperForm();
  
    // Module, token et currentIndex
    $helper->module = $this;
    $helper->name_controller = $this->name;
    $helper->token = Tools::getAdminTokenLite('AdminModules');
    $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
  
    // Langue
    $helper->default_form_language = $defaultLang;   
  
    // Charge la valeur de LM_BOUGHTTOGETHER_PAGENAME depuis la base
    $helper->fields_value['LM_BOUGHTTOGETHER_PAGENAME'] = Configuration::get('LM_BOUGHTTOGETHER_PAGENAME');
  
    return $helper->generateForm(array($form));
  }

  public function hookDisplayProductExtraContent($params) {

    // $products = array(
    //   "test"
    // );

    // $lm_attribute = array('id' => 'lm_boughttogether_tab', 'class' => 'lm_boughttogether_tab');

    // $productExtraContent = new ProductExtraContent();
    // $productExtraContent->setTitle($this->l('Frequently bought together'));
    // $productExtraContent->setAttr($lm_attribute);

    // $this->context->smarty->assign(array(
    //   'lm_products' => $products,
    //   'lm_page_name' => Configuration::get('LM_BOUGHTTOGETHER_PAGENAME'),
    //   'lm_page_link' => $this->context->link->getModuleLink('lm_boughttogether', 'display')
    // ));
    // $productExtraContent->setContent($this->context->smarty->fetch('module:lm_boughttogether/views/templates/front/lm_boughttogether.tpl'));

    // if(count($products) > 0) {
    //   return array($productExtraContent);
    // }

  }

  public function hookDisplayHeader() {
    $this->context->controller->registerStylesheet(
        'lm_boughttogether',
        $this->_path.'views/assets/css/lm_boughttogether.css',
        ['server' => 'remote', 'position' => 'head', 'priority' => 150]
    );
  }

  public function hookDisplayProductBoughtTogether($params) {
    $lm_products = LmProductBoughtTogether::getLmProductBoughtTogether($params);
    
    $lm_total_price = 0;

    foreach($lm_products as $lm_product) {
      $lm_total_price += $lm_product['price_tax'];
    }

    $this->context->smarty->assign(array(
      'lm_products' => $lm_products,
      'lm_total_price' => $lm_total_price,
    ));
    return $this->fetch('module:'.$this->name.'/views/templates/hook/lm_boughttogether.tpl');
  }
}