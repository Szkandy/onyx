<?php
/** 
 * Copyright (c) 2006-2014 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

require_once('controllers/bo/node/content/default.php');

class Onxshop_Controller_Bo_Node_Content_Component extends Onxshop_Controller_Bo_Node_Content_Default {
    
    /**
     * pre action
     */
     
    function pre() {
        
        parent::pre();
        
        $_POST['node']['component']['template'] = trim($_POST['node']['component']['template']);
        $_POST['node']['component']['controller'] = trim($_POST['node']['component']['controller']);
        $_POST['node']['component']['parameter'] = trim($_POST['node']['component']['parameter']);
    }
}
