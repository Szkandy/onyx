<?php
/** 
 * Copyright (c) 2014 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once('controllers/api/v1_1/recipe_list.php');
require_once('controllers/api/v1_2/recipe_detail.php');

class Onxshop_Controller_Api_v1_2_Recipe_List extends Onxshop_Controller_Api_v1_1_Recipe_List {
    
    /**
     * formatItem
     */
     
    public function formatItem($item) {
        
        return Onxshop_Controller_Api_v1_2_Recipe_Detail::formatItem($item);
        
    }
    
}
