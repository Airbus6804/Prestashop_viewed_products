<?php
if (!defined('_PS_VERSION_')) {
    exit;
}


require_once("src/Services/utilities.php");
require_once("src/Controller/db_orm.php");

//use \viewed_products_utilities;

class viewedproducts extends Module
{

    private viewed_products_utilities $utilities;

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

        $this->utilities = new viewed_products_utilities();

    }





    public function install()
    {

        return parent::install() && $this->registerHook('displayFooterProduct') && $this->registerHook('displayFooter') && viewed_products_orm::createTable();
    }


    //$this->context->customer->email


    //creates a new table to store user chronology



    /*

        customer -> id

        context -> smarty -> tpl_vars -> product -> value -> {
            id_product, 
            name,
            description_short,
            link,
            regular_price,
            title,
            default_image -> medium -> url
            cover -> medium -> url
            add_to_cart_url = http:\/\/localhost\/cart?add=1&id_product=3&id_product_attribute=13&token=0528ea28afd32794d34ce378912358f5
        }

        table -> {
            id_product, 
            on_sale,
            price,

        }

        Product -> {
            id: int,
            name: text,
            description_short: text,
            price,
            active,
            show_price,
            indexed,
            state,
            category
        }

    */


    public function hookDisplayFooterProduct(array $params)
    {


        $context = Context::getContext();

        $id = $context->smarty->tpl_vars["product"]->value->id_product;
        $customer = $context->customer->id;

        viewed_products_orm::insertViewedProduct($customer, $id);

    }

    public function hookDisplayFooter(array $params)
    {
        return "<h1 id='jgjgjgjgjgjg'> hello world </h1>";
    }

    public function uninstall()
    {
        return parent::uninstall() && viewed_products_orm::deleteTable();
    }
}