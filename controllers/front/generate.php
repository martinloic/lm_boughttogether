<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

class Lm_BoughtTogetherGenerateModuleFrontController extends ModuleFrontController {
  public function initContent() {
    if(Tools::getValue('key') && Tools::getValue('key') === Configuration::get('LM_BOUGHTTOGETHER_PAGENAME')) {
      parent::initContent();

      $products_relation = $this->generateProductAssociationList();
      $this->clearProductAssociationsTable();
      $this->storeProductAssociations($products_relation);
      echo 'Generation Success';
      
    } else {
      echo 'Wrong token';
    }
    die();
  }

  public function generateProductAssociationList() {
    $query = "SELECT id_order, product_id FROM " . _DB_PREFIX_ . "order_detail WHERE id_order IN (SELECT id_order FROM ". _DB_PREFIX_ . "order_detail GROUP BY id_order HAVING COUNT(*) > 1)";
    $related_products = Db::getInstance()->executeS($query);
    
    $associations = array();
    
    // Parcourir les données pour construire les associations
    foreach($related_products as $item) {
      $id_order = $item['id_order'];
      $product_id = $item['product_id'];

      // Vérifier si la référence existe déjà dans le tableau des associations
      if (!isset($associations[$product_id])) {
        // Si elle n'existe pas, créer une nouvelle entrée
        $associations[$product_id] = array();
      }
      
      // Vérifier si l'ID de commande existe déjà pour cette référence
      if (!isset($associations[$product_id][$id_order])) {
          // Si l'ID de commande n'existe pas, initialiser la fréquence à 1
          $associations[$product_id][$id_order] = 1;
      } else {
          // Sinon, incrémenter la fréquence
          $associations[$product_id][$id_order]++;
      }
    }

    // Tableau pour stocker les références associées les plus fréquentes
    $most_frequent_associations = array();

    // Parcourir les associations pour chaque référence
    foreach ($associations as $reference => $orders) {
      // Calculer le nombre total d'occurrences pour cette référence
      $total_occurrences = array_sum($orders);

      // Trouver l'ID de commande le plus fréquent pour cette référence
      $most_frequent_orders = array_keys($orders, max($orders));

      // Stocker les références associées les plus fréquentes
      $most_frequent_associated_references = [];
      foreach ($most_frequent_orders as $most_frequent_order) {
          foreach ($associations as $associated_reference => $associated_orders) {
              if ($reference !== $associated_reference && isset($associated_orders[$most_frequent_order]) && $associated_orders[$most_frequent_order] === max($orders)) {
                  $most_frequent_associated_references[] = $associated_reference;
              }
          }
      }

      // Supprimer les doublons
      $most_frequent_associated_references = array_unique($most_frequent_associated_references);

      // Filtrer les références associées qui ont moins de 4 occurrences
      $most_frequent_associated_references = array_filter($most_frequent_associated_references, function($associated_reference) use ($associations, $most_frequent_orders) {
          $occurrences = array_sum(array_intersect_key($associations[$associated_reference], array_flip($most_frequent_orders)));
          return $occurrences >= 2;
      });

      // Limiter aux 3 premières références associées les plus fréquentes
      $max_reference_displayed = 5;
      $most_frequent_associated_references = array_slice($most_frequent_associated_references, 0, $max_reference_displayed);

      // Compter le nombre de fois que chaque référence associée apparaît
      $associated_reference_occurrences = [];
      foreach ($most_frequent_associated_references as $associated_reference) {
          $occurrences = array_sum(array_intersect_key($associations[$associated_reference], array_flip($most_frequent_orders)));
          $associated_reference_occurrences[$associated_reference] = $occurrences;
      }

      // Trier les références associées par nombre d'occurrences du plus grand au plus petit
      arsort($associated_reference_occurrences);

      // Ajouter cette association à la liste des associations les plus fréquentes
      $most_frequent_associations[$reference] = array(
          'associated_reference_occurrences' => $associated_reference_occurrences,
          'occurrences' => $total_occurrences
      );
    }

    return $most_frequent_associations;
  }

  public function storeProductAssociations($associations) {
    foreach ($associations as $product_id => $data) {
      foreach($data['associated_reference_occurrences'] as $product_relation_id => $occurrences) {
        $this->storeAssociationInDatabase($product_id, $product_relation_id, $occurrences);
      }
    }
  }

  public function storeAssociationInDatabase($product_id, $product_relation_id, $occurrences) {
    // Insérer les données dans la table product_associations de la base de données
    $sql = "INSERT INTO `". _DB_PREFIX_ . "lm_product_bought_together` (`product_id`, `associated_product_id`, `occurrences`) VALUES ($product_id, $product_relation_id, $occurrences)";

    Db::getInstance()->execute($sql);
  }

  public function clearProductAssociationsTable() {
    // Supprimer toutes les données de la table product_associations
    $sql = "TRUNCATE TABLE ". _DB_PREFIX_ . "lm_product_bought_together";
    Db::getInstance()->execute($sql);
  }
}
