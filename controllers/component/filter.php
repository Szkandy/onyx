<?php
/**
 * Copyright (c) 2008-2011 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onxshop_Controller_Component_Filter extends Onxshop_Controller {
    
    /**
     * main action
     */
     
    public function mainAction() {
    
        if (is_numeric($this->GET['node_id'])) $node_id = $this->GET['node_id'];
        if ($this->GET['template'] == '') $template = 'menu_UL';
        
        $Onxshop_Request = new Onxshop_Request("component/menu&type=taxonomy&level=2&display_all=1&id=$node_id&template=$template");
        
        $this->tpl->assign("FILTER", $Onxshop_Request->getContent());

        return true;
    }
}
