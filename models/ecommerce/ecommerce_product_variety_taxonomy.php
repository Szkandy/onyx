<?php

require_once('models/common/common_node_taxonomy.php');

/**
 * class ecommerce_product_variety_taxonomy
 *
 * Copyright (c) 2009-2011 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */
 
class ecommerce_product_variety_taxonomy extends common_node_taxonomy {

    /**
     * NOT NULL REFERENCES ecommerce_product_variety ON UPDATE CASCADE ON DELETE
     * CASCADE
     * @access private
     */
    var $node_id;

    /**
     * create table sql
     */
     
    private function getCreateTableSql() {
    
        $sql = "
CREATE TABLE ecommerce_product_variety_taxonomy ( 
    id serial NOT NULL PRIMARY KEY,
    node_id int NOT NULL REFERENCES ecommerce_product_variety ON UPDATE CASCADE ON DELETE CASCADE,
    taxonomy_tree_id int NOT NULL REFERENCES common_taxonomy_tree ON UPDATE CASCADE ON DELETE CASCADE
);

ALTER TABLE ecommerce_product_variety_taxonomy ADD CONSTRAINT product_variety_node_id_taxonomy_tree_id_key UNIQUE (node_id, taxonomy_tree_id);
        ";
        
        return $sql;
    }
    
}