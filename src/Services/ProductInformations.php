<?php

require_once("utilities.php");



class ViewedProductsProduct {

    public $id;
    public $name;
    public $description_short;
    public $price;
    public $category;
    public $indexed;
    public $coverImageUrl;
    public $link;
    public $error = false;

    public function __construct(int $id){
        $utilities = new viewedProductsUtilities();

        $product = new Product($id);

        if($product == null){
            $this->error = true;
            return;
        }

        $this->id = $id;
        $this->name = $product->name;
        $this->description_short = $product->description_short;
        $this->price = $product->price;
        $this->indexed = $product->indexed;
        $this->coverImageUrl = $utilities->getImagePath($product);
        $this->link = $product->getLink();

        $categoryNames = array_map(function($category){
            $category = new Category($category); 
            return implode(" ", $category->name);
        }, $product->getCategories());
        $this->category = implode(" ", $categoryNames);

    }

    static public function GetMultipleProducts(array $ids){

        return array_map(function($id){
            return new ViewedProductsProduct($id);
        }, $ids);

    }

}





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