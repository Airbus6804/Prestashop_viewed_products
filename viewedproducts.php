<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

require_once("src/Services/utilities.php");
require_once("src/Controller/ViewedProductsOrm.php");
require_once("src/Services/ProductInformations.php");

class viewedproducts extends Module
{

    private ViewedProductsUtilities $utilities;

    public $tabs = [
        [
            'name' => "Viewed Products2",
            'class_name' => 'Adminviewedproducts',
            'visibile' => true,
            'parent_class_name' => 'ShopParameters'
        ]
    ];

    public $priceName;
    public $descriptionName;
    public $categoriesName;

    

    public function __construct()
    {
        $this->name = 'viewedproducts';
        $this->tab = 'viewed_products';
        $this->version = '1.0.0';
        $this->author = 'Ragonesi Alessio';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = [
            'min' => '1.7.0.0',
            'max' => '8.99.99',
        ];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Viewed Products');
        $this->description = $this->l('Shows a chronology of all the products visited');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');

        if (!Configuration::get('MYMODULE_NAME')) {
            $this->warning = $this->l('No name provided');
        }

        $this->utilities = new viewedProductsUtilities();

        $this->priceName = $this->name . '_PRICE';
        $this->descriptionName = $this->name . '_DESCRIPTION';
        $this->categoriesName = $this->name . '_CATEGORIES';

    }





    public function install()
    {
        Configuration::updateValue($this->priceName, 1);
        Configuration::updateValue($this->descriptionName, 1);
        Configuration::updateValue($this->categoriesName, 1);
        return parent::install() && $this->registerHook('actionFrontControllerSetMedia') && $this->registerHook('displayFooterProduct') && $this->registerHook('displayFooter') && ViewedProductsOrm::createTable();
    }



    public function hookActionFrontControllerSetMedia($params)
    {

        //CSS for viewed products sidebar
        $this->context->controller->registerStylesheet(
            'viewedProducts-style',
            'modules/viewedproducts/views/css/viewedProducts.css',
            [
                'server' => 'local',
                'priority' => 10,
            ]
        );
    }


    public function hookDisplayFooterProduct(array $params)
    {

        //This hook will trigger whenever a user visits a product page
        //and will insert in the visited products table the user id and
        //the product id

        $context = Context::getContext();

        $id = $context->smarty->tpl_vars["product"]->value->id_product;
        $customer = $context->customer->id;

        if (!isset($customer) or $customer == null)
            return;

        ViewedProductsOrm::insertViewedProduct($customer, $id);

    }

    public function hookDisplayFooter(array $params)
    {

        //This hook will always be triggered in the shop page and will show a list
        //of visited products

        global $smarty;

        $context = Context::getContext();
        $customer = $context->customer->id;

        if (!isset($customer) or $customer == null)
            return;
        
        $ids = ViewedProductsOrm::getViewedProductsByCustomerId($customer);

        //format dictionary to list of products id
        $ids = array_map(function ($arr) {
            return $arr["id"];
        }, $ids);

        //get products data
        $productInformations = (array) ViewedProductsProduct::GetMultipleProducts($ids);

        $smarty->assign("dir", __DIR__);
        $smarty->assign('products', $productInformations);
        $smarty->assign('configuration', [
            'showPrice' => Configuration::get($this->priceName),
            'showDescription' => Configuration::get($this->descriptionName),
            'showCategories' => Configuration::get($this->categoriesName),
        ]);

        return parent::display(__FILE__, "/views/templates/singleProduct.tpl");

    }

    public function getContent(){

        if(Tools::isSubmit('submit' . $this->name)){
            $price = (int) Tools::getValue($this->priceName);
            $description = (int) Tools::getValue($this->descriptionName);
            $categories = (int) Tools::getValue($this->categoriesName);


            Configuration::updateValue($this->priceName, $price);
            Configuration::updateValue($this->descriptionName, $description);
            Configuration::updateValue($this->categoriesName, $categories);
        }

        return $this->getForm();

    }

    public function getForm(){

        $helper = new HelperForm();

        $helper->table = $this->table;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&' . http_build_query(['configure' => $this->name]);
        $helper->submit_action = 'submit' . $this->name;

        $helper->default_form_language = (int) Configuration::get('PS_LANG_DEFAULT');

        $helper->fields_value[$this->priceName] = Tools::getValue($this->priceName, Configuration::get($this->priceName));
        $helper->fields_value[$this->categoriesName] = Tools::getValue($this->categoriesName, Configuration::get($this->categoriesName));
        $helper->fields_value[$this->descriptionName] = Tools::getValue($this->descriptionName, Configuration::get($this->descriptionName));
    
        return $helper->generateForm([$this->getConfigurationForm()]);

    }

    public function getConfigurationForm()
    {
        return [
            'form' => [
                'legend' => [
                    'title' => $this->l('Settings'),
                ],
                'input' => [
                    [
                        'type' => 'switch',
                        'label' => $this->l('Price'),
                        'name' => $this->priceName,
                        'size' => 20,
                        'required' => false,
                        'values' => [
                            [
                                'value' => 1,
                                'label' => 'Show'
                            ],
                            [
                                'value' => 0,
                                'label' => 'Hide'
                            ]
                        ]
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Description'),
                        'name' => $this->descriptionName,
                        'size' => 20,
                        'required' => false,
                        'values' => [
                            [
                                'value' => 1,
                                'label' => 'Show'
                            ],
                            [
                                'value' => 0,
                                'label' => 'Hide'
                            ]
                        ]
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Categories'),
                        'name' => $this->categoriesName,
                        'size' => 20,
                        'required' => false,
                        'values' => [
                            [
                                'value' => 1,
                                'label' => 'Show'
                            ],
                            [
                                'value' => 0,
                                'label' => 'Hide'
                            ]
                        ]
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                    'class' => 'btn btn-default pull-right',
                ],
            ]
        ];
    }

    

    public function uninstall()
    {
        return parent::uninstall() && ViewedProductsOrm::deleteTable();
    }


}