<?php
/**
 * Global configuration
 * This is configuration shared between development and production environment
 *
 * Copyright (c) 2009-2014 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

/**
 * Server specific configuration (i.e. different on dev&live server)
 */

require_once('deployment.php');

/**
 * Project specific configuration (i.e. shared on dev&live server)
 * See onxshop_dir/conf/global.php for all options
 */
 
//define('ONXSHOP_MAIN_TEMPLATE','node/site/default');

/**
 * Include default global configuration
 */

require_once(ONXSHOP_DIR . "conf/global.php");

