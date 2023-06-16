<?php

class ViewedProductsUtilities{

    public function getImagePath(Product $product)
    {
        $image = Image::getCover($product->id);



        // Initialize the link object
        $link = new Link;

        $path = $link->getImageLink($product->link_rewrite[Context::getContext()->language->id], $image['id_image'], 'home_default');

        return str_starts_with($path, "localhost") ? "http://" . $path : $path;
    }

    public function log(string $name, $data)
    {

        $encoded = json_encode($data);

        return "<h1>$name: </h1> <code>$encoded</code>";
    }

    

}

