<?php

/**
 * Class LmProductBoughtTogether
 */

class LmProductBoughtTogether extends ObjectModel {

  public static function getLmProductBoughtTogether($params) {
    $query = "SELECT * FROM " . _DB_PREFIX_ . "lm_product_bought_together WHERE product_id = ".$params['id_product']." ORDER BY occurrences DESC";
    $data_query = Db::getInstance()->executeS($query);
    

    if($data_query) {
      $associated_product_ids = array();
      foreach ($data_query as $data) {
        $associated_product_ids[] = $data['associated_product_id'];
      }
      $associated_product_ids_str = implode(',', $associated_product_ids);

      $query_related_products = "SELECT * FROM " . _DB_PREFIX_ . "product WHERE id_product IN ($associated_product_ids_str) AND active = 1 ORDER BY FIELD(id_product,$associated_product_ids_str)";
      $related_products = Db::getInstance()->executeS($query_related_products);

      $products_data = array();
      $id_lang = (int) Configuration::get('PS_LANG_DEFAULT');

      foreach($related_products as $related_product) {
        $product = new Product($related_product['id_product'], false, $id_lang);
        if (Validate::isLoadedObject($product)) {
          $link = new Link();
          $image = Product::getCover((int)$product->id);
          $image_url = $link->getImageLink($product->link_rewrite, $image['id_image'], 'home_default');
          $link_url = $link->getProductLink($product);
          
          $price_tax = $product->getPrice(true, null, 2, null, false, true);
          $price_tax_exc = $product->getPrice(false, null, 2, null, false, true);

          $regular_price_tax = $product->getPrice(true, null, 2, null, false, false);
          $regular_price_tax_exc = $product->getPrice(false, null, 2, null, false, false);

          $discount = (($regular_price_tax_exc - $price_tax_exc)*100)/$regular_price_tax_exc;

          $product_data = array(
            'id' => $product->id,
            'name' => $product->name,
            'price_tax' => $price_tax,
            'regular_price_tax' => $regular_price_tax,
            'price_tax_exc' => $price_tax_exc,
            'regular_price_tax_exc' => $regular_price_tax_exc,
            'image' => $image_url,
            'url_link' => $link_url,
            'discount' => round($discount, 1),
            // Add other data here...
          );

          $products_data[] = $product_data;
        }
      }

      return $products_data;
    } else {
      return null;
    }
  }
}
