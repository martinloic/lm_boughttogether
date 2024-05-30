{if isset($lm_products) && is_array($lm_products) && $lm_products}
  <div id="lm_boughttogether_block"
    class="tabs product-tabs product-sections col-lg-9 mb-3 col-md-12 col-sm-12 clear pt-3">
    <h2>{l s='Product frequently bought with :' mod='lm_boughttogether'}</h2>
    {* {var_dump($lm_products)} *}
    <div class="content p-relative row">
      {* {var_dump($lm_products)} *}
      {foreach from=$lm_products item=lm_product}
        {* {var_dump($lm_product)} *}
        {* {var_dump($lm_product['name'])} *}
        <article class="lm-related-product col-sm-3 col-md-4 col-lg-2 mb-3 mt-3" data-product-id="{$lm_product.id}">
          <a href="{$lm_product.url_link}" title="Lien vers le produit : {$lm_product.name}"
            class="p-relative m-auto h-100">
            <div class="plus-container">
              <img src="https://{$lm_product.image}" alt="{$lm_product.name}" lazyload>
            </div>
            <div class="content">
              <span class="d-block"><b>{$lm_product.name}</b></span>
              <div class="price-container">
                {if $product.labels.tax_long === "TTC"}
                  {if $lm_product.discount != 0}
                    <span>
                      <s>{Tools::displayPrice($lm_product.regular_price_tax)}</s>
                      {* <span class="discount">-{$lm_product.discount}%</span> *}
                    </span>
                  {/if}
                  <span class="price-overview"><b class="product-price-with">{Tools::displayPrice($lm_product.price_tax)}
                      {$product.labels.tax_long}</b></span>
                {else}
                  {if $lm_product.discount != 0}
                    <span>
                      <s>{Tools::displayPrice($lm_product.regular_price_tax_exc)}</s>
                      {* <span class="discount">-{$lm_product.discount}%</span> *}
                    </span>
                  {/if}
                  <span class="price-overview"><b class="product-price-with">{Tools::displayPrice($lm_product.price_tax_exc)}
                      {$product.labels.tax_long}</b></span>
                {/if}
              </div>
            </div>
          </a>
          <form action="{$urls.pages.cart}" method="post" id="add-to-cart-or-refresh">
            <input type="hidden" name="token" value="{$static_token}">
            <input type="hidden" name="id_product" value="{$lm_product.id}" id="product_page_product_id">
            <button class="btn btn-primary add-to-cart" data-button-action="add-to-cart" type="submit">
              {* <i class="material-icons shopping-cart"></i> *}
              {l s='Add to cart' d='Shop.Theme.Actions'}
            </button>
          </form>

        </article>
      {/foreach}
      {* <div class="col-sm-12 col-md-6 col-lg-3 mb-1 mt-1 custom-add">
        <div class="my-auto">
          <p>
            {l s='Total price :' mod='lm_boughttogether'} <b>{Tools::displayPrice($lm_total_price)}</b>
          </p>
          <button class="btn btn-primary custom-add-to-cart" value="{$urls.pages.cart}" spellcheck="false"><i
              class="mdi mdi-cart-arrow-down"></i>
            {l s='Add the %s to the cart' sprintf=[count($lm_products)] mod='lm_boughttogether'}</button>
        </div>
      </div> *}
    </div>
  </div>
{/if}
{* <script type="text/javascript">
  document.addEventListener('DOMContentLoaded', function() {
    // Sélection du bouton "Ajouter au panier"
    var addToCartButton = document.querySelector('.custom-add-to-cart');
    // Écouteur d'événement sur le clic du bouton
    addToCartButton.addEventListener('click', function(event) {
      event.preventDefault(); // Empêcher le comportement par défaut du bouton

      // Tableau des identifiants de produits à ajouter au panier
      var productIdsToAdd = [];

      // Parcours des produits pour extraire leurs identifiants
      var products = document.querySelectorAll('.lm-related-product');
      products.forEach(function(product) {
        var productId = product.getAttribute('data-product-id');
        productIdsToAdd.push(productId);
      });

      // Ajouter chaque produit au panier
      // productIdsToAdd.forEach(function(productId) {
      //   console.log('Adding ' + productId);
      // });
      console.log('Adding products:', productIdsToAdd);
      addToCartCustom(productIdsToAdd);
    });

    function addToCartCustom(productIds) {
      productIds.forEach(function(productId) {
        let xhr = new XMLHttpRequest();
let url = '{$urls.pages.cart}';
        // Vous pouvez ajuster la URL pour inclure l'ID du produit à ajouter
        url += '?id_product=' + productId + '&add=1';
        xhr.open("POST", url, true);
        xhr.onreadystatechange = function() {
          if (this.readyState == 4 && this.status == 200) {
            // var x = document.getElementById("snackbar");
            // x.className = "show";
            // setTimeout(function() { x.className = x.className.replace("show", ""); }, 3000);
          }
        }
        xhr.send();
      });
    }
  });
</script> *}
