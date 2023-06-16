{extends file="$dir/views/templates/multipleProducts.tpl"}

{block name="products" append}
    {foreach $products as $product}
        {if $product->indexed eq 1}
            <div class="viewedProductsAside__container__product">
                <h2><a href="{$product->link}">{$product->name["1"]} </a></h2>
                <img src="{$product->coverImageUrl}"/>
                {if $configuration.showCategories eq 1} 
                    <h3>{$product->category}</h3>
                {/if}

                {if $configuration.showDescription eq 1} 
                    <div>{$product->description_short["1"] nofilter}</div>
                {/if}

                {if $configuration.showPrice eq 1} 
                    <span>${$product->price}</span>
                {/if}
            </div>
        {/if}
    {foreachelse}
        <span class="viewedProductsAside__noProductsVisited">You haven't visited any product yet</span>
    {/foreach}
{/block}

<!--

    public $id;
    public $name;
    public $description_short;
    public $price;
    public $category;
    public $indexed;
    public $coverImageUrl;
    public $link;
    public $error = false;
-->