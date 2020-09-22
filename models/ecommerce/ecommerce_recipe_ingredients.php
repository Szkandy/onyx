<?php
/**
 * class ecommerce_recipe_ingredients
 * link products to recipe as ingredients
 *
 * Copyright (c) 2013-2014 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */
 
class ecommerce_recipe_ingredients extends Onxshop_Model {

    /**
     * @access private
     */
    var $id;

    /**
     * @access private
     */
    var $recipe_id;

    /**
     * @access private
     */
    var $product_variety_id;

    /**
     * @access private
     */
    var $quantity;

    /**
     * @access private
     */
    var $units;

    /**
     * @access private
     */
    var $notes;
    

    var $_metaData = array(
        'id'=>array('label' => '', 'validation'=>'int', 'required'=>true), 
        'recipe_id'=>array('label' => '', 'validation'=>'int', 'required'=>true),
        'product_variety_id'=>array('label' => '', 'validation'=>'int', 'required'=>true),
        'quantity'=>array('label' => '', 'validation'=>'int', 'required'=>true),
        'units'=>array('label' => '', 'validation'=>'int', 'required'=>true),
        'notes'=>array('label' => '', 'validation'=>'string', 'required'=>false)
    );

    /**
     * create table sql
     */
     
    private function getCreateTableSql() {
    
        $sql = "
CREATE TABLE ecommerce_recipe_ingredients (
    id serial PRIMARY KEY NOT NULL,
    recipe_id integer REFERENCES ecommerce_recipe(id) ON UPDATE RESTRICT ON DELETE RESTRICT,
    product_variety_id integer NOT NULL REFERENCES ecommerce_product_variety(id) ON UPDATE RESTRICT ON DELETE RESTRICT,
    quantity real,
    units integer REFERENCES common_taxonomy_tree(id) ON UPDATE RESTRICT ON DELETE RESTRICT,
    notes text,
    group_title character varying(255)
);
        ";
        
        return $sql;
    }
    
    /**
     * init configuration
     */
     
    static function initConfiguration() {

        if (array_key_exists('ecommerce_recipe_ingredients', $GLOBALS['onxshop_conf'])) $conf = $GLOBALS['onxshop_conf']['ecommerce_recipe_ingredients'];
        else $conf = array();

        if (!is_numeric($conf['units_taxonomy_tree_id'])) $conf['units_taxonomy_tree_id'] = 74;

        return $conf;
    }

    /**
     * Read list of Units from taxonomy tree
     */
    public function getUnits() {

        $conf = self::initConfiguration();
        $parent = $conf['units_taxonomy_tree_id'];

        if (!is_numeric($parent)) return false;

        $sql = "SELECT common_taxonomy_tree.id, common_taxonomy_label.title
            FROM common_taxonomy_tree
            LEFT JOIN common_taxonomy_label ON common_taxonomy_label.id = common_taxonomy_tree.label_id
            WHERE common_taxonomy_tree.publish = 1 AND common_taxonomy_tree.parent = $parent
            ORDER BY common_taxonomy_tree.priority DESC, common_taxonomy_label.title ASC";

        return $this->executeSql($sql);
    }

    /**
     * Return list of all ingredients for a recipe
     * Each element contains:
     *  - ingredient data
     *  - related product variety data as 'variety' field
     *  - related product data as 'product' field
     *  - unit name as 'unis_name' field
     */
    public function getIngredientsForRecipe($recipe_id) 
    {
        if (!is_numeric($recipe_id)) return false;

        $ingredients = $this->listing("recipe_id = $recipe_id");

        require_once('models/ecommerce/ecommerce_product.php');
        $Product = new ecommerce_product();

        $units_raw = $this->getUnits();
        foreach ($units_raw as $unit) {
            $units[$unit['id']] = $unit['title'];
        }

        foreach ($ingredients as &$ingredient) {

            $ingredient['units_name'] = $units[$ingredient['units']];
            $ingredient['variety'] = $Product->getProductVarietyDetail($ingredient['product_variety_id']);

            if ($ingredient['variety']) {
                $ingredient['product'] = $Product->detail($ingredient['variety']['product_id']);
            }

            $ingredient['name'] = $ingredient['product']['name'];

        }

        $ingredients = php_multisort($ingredients, array(array('key' => 'group_title', 'sort' => 'asc'),
            array('key' => 'name', 'sort' => 'asc')));

        return $ingredients;

    }
    
    /**
     * getIngredientsForRecipeOptimised
     */
     
    public function getIngredientsForRecipeOptimised($recipe_id) {

        if (!is_numeric($recipe_id)) return false;

        $sql = "SELECT ecommerce_recipe_ingredients.quantity, common_taxonomy_label.title AS units, ecommerce_product.name AS title, ecommerce_recipe_ingredients.notes, ecommerce_product_variety.sku AS product_id, ecommerce_recipe_ingredients.group_title
            FROM ecommerce_recipe_ingredients
            LEFT JOIN common_taxonomy_tree ON common_taxonomy_tree.id = ecommerce_recipe_ingredients.units
            LEFT JOIN common_taxonomy_label ON common_taxonomy_label.id = common_taxonomy_tree.label_id
            LEFT JOIN ecommerce_product_variety ON ecommerce_product_variety.id =  ecommerce_recipe_ingredients.product_variety_id
            LEFT JOIN ecommerce_product ON ecommerce_product.id = ecommerce_product_variety.product_id
            WHERE ecommerce_recipe_ingredients.recipe_id = $recipe_id
            ORDER BY ecommerce_recipe_ingredients.id ASC";

        return $this->executeSql($sql);
    }

}