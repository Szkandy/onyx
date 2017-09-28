<?php
/** 
 * Copyright (c) 2013 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

require_once 'lib/Zend/Oauth.php';
require_once 'lib/Zend/Service/Twitter.php';


class Onxshop_Controller_Component_Client_Twitter extends Onxshop_Controller {

    /**
     * mainAction
     */
     
    public function mainAction() {
        
        $token = $this->commonAction();
        
        return true;
    }
    
    /**
     * common action
     */
     
    public function commonAction() {
        
        // start auth on request (clicked on login via Twitter button) 
        if ($_POST['twitter_auth']) {
            $this->oAuth();
        }
        
        // endSession() signs users out of client-facing applications.
        if ($this->GET['twitter_logout']) {
            $response   = $this->twitter->account->endSession();
        }
        
        // when comming back from Twitter, oauth_token and oauth_verifier is provided in GET
        if ($this->GET['oauth_token'] && $this->GET['oauth_verifier']) {
            
            //print_r($_SESSION['TWITTER_REQUEST_TOKEN']);
            if (isset($_SESSION['TWITTER_REQUEST_TOKEN'])) $this->oAuthCallback();
            
        }
        
        $token = $this->getAccessToken();
        
        $this->initTwitter($this->getTwitterUsername(), $token);
                        
        return $token;
        
    }
    
    /**
     * verifyUser
     */
     
    public function verifyUser() {
    
        $token = $this->getAccessToken();
        
        $this->initTwitter($this->getTwitterUsername(), $token);
         
        // verify user's credentials with Twitter
        $response = $this->twitter->account->verifyCredentials();


    }
    
    /**
     * initTwitter
     */
     
    public function initTwitter($username, $token) {
        
        $oauth_config = $this->getOAuthConfig();
        
        $this->twitter = new Zend_Service_Twitter(array(
            'oauthOptions' => $oauth_config,
            'username' => $username,
            'accessToken' => $token
        ));
        
        return $token;
        
    }
    
    /**
     * initOAuth
     */
     
    public function initOAuth() {
        
        $oauth_config = $this->getOAuthConfig();
        
        $this->consumer = new Zend_Oauth_Consumer($oauth_config);
        
    }
    
    /**
     * oAuth
     */
    
    public function oAuth() {
        
        $this->initOAuth();
        
        // fetch a request token
        $token = $this->consumer->getRequestToken();
        
        // save request token to session
        // request token is only temporary and it's used to generate access token
        // we'll save access token to persistent storage (database) using setAccessToken
        
        $_SESSION['TWITTER_REQUEST_TOKEN'] = base64_encode(serialize($token));
        
        // close session
        session_write_close();
        
        // redirect the user
        $this->consumer->redirect();

    }
        
    /**
     * oAuthCallback
     */
     
    public function oAuthCallback() {
    
        $this->initOAuth();
        
        $token = $this->consumer->getAccessToken($_GET, unserialize(base64_decode($_SESSION['TWITTER_REQUEST_TOKEN'])));
        
        $this->setAccessToken($token);
        
        // Now that we have an Access Token, we can discard the Request Token
        $_SESSION['TWITTER_REQUEST_TOKEN'] = null;
        
    }
    
    /**
     * getOauthConfig
     */
     
    public function getOAuthConfig() {
        
        // detect http vs https
        if ($_SERVER['HTTPS']) $protocol = 'https';
        else $protocol = 'http';
        
        /**
         * app created on https://dev.twitter.com/apps
         */
         
        $config = array(
            
            'callbackUrl' => "$protocol://{$_SERVER['HTTP_HOST']}/page/8",
            'consumerKey' => ONXSHOP_TWITTER_APP_ID,
            'consumerSecret' => ONXSHOP_TWITTER_APP_SECRET,
            'siteUrl' => 'https://api.twitter.com/oauth'
        );

        return $config;
    
    }
    
    /**
     * setAccessToken
     */
     
    public function setAccessToken($token) {
        
        if (is_numeric($_SESSION['client']['customer']['id']) && $_SESSION['client']['customer']['id'] > 0) {
            $customer_id = $_SESSION['client']['customer']['id'];
        }
        
        if (is_numeric($customer_id)) {
            //TODO: save to client_customer.oauth.twitter 
        }
        
        // Currently saving serialised in session, should be in database if customer id is known
        $_SESSION['TWITTER_ACCESS_TOKEN'] = base64_encode(serialize($token));
        
        return true;
        
    }
    
    /**
     * getAccessToken
     */
     
    public function getAccessToken() {
    
        if (is_numeric($_SESSION['client']['customer']['id']) && $_SESSION['client']['customer']['id'] > 0) {
            $customer_id = $_SESSION['client']['customer']['id'];
        }
        
        if (is_numeric($customer_id)) {
            //TODO: get from client_customer.oauth.twitter 
        }
        
        // Currently saved in session, should be in database if customer id is known
        $token = unserialize(base64_decode($_SESSION['TWITTER_ACCESS_TOKEN']));
        
        return $token;
        
    }
    
    /**
     * debug http request
     */
     
    public function debugLastHttpRequest() {
        
        $httpClient = $this->twitter->getHttpClient();
        msg($httpClient->getLastRequest(), 'error');
    
    }
    
    /**
     * twitterCall
     */
     
    public function twitterCall($function_name) {
        
        try {
                
            $response = $this->twitter->$function_name();
            
            if ($response->isSuccess()) {
                
                return $response->toValue();
                                
            } else {
            
                $errors = $response->getErrors();
                msg($errors, 'error');
                
                return false;
            }
            
        } catch (Zend_Exception $e) {
    
            msg("Twitter: {$e->getMessage()}", 'error', 1);
            $this->debugLastHttpRequest();
            
            return false;
    
        }
        
    }
    
    /**
     * getTwitterUsername
     */
     
    public function getTwitterUsername() {
    
        // username should be taken from client_customer.oauth.twitter
        // TODO: create client_customer.twitter_username field, but what if client will change it?
        return ONXSHOP_TWITTER_USERNAME;
        
    }
    
    /**
     * simple test
     */
     
    public function simpleTest() {
        
        $twitter = new Zend_Service_Twitter(array( 
            'accessToken' => array(
                'token' => ONXSHOP_TWITTER_ACCESS_TOKEN,
                'secret' => ONXSHOP_TWITTER_ACCESS_TOKEN_SECRET,
            ),
            'oauthOptions' => array(
                'consumerKey' => ONXSHOP_TWITTER_APP_ID,
                'consumerSecret' => ONXSHOP_TWITTER_APP_SECRET,
            )
        ));
        
        $options = array( 
            "screen_name"   => ONXSHOP_TWITTER_USERNAME
        );
        $response = $twitter->statuses->userTimeline($options);
        
        return $response;
        
    }
    
    
    /**
     * twitterCallExtend
     */
     
    public function twitterCallExtend($obj, $function_name, $options) {
        
        try {
                
            $response = $this->twitter->$obj->$function_name($options);
            
            if ($response->isSuccess()) {
                
                return $response->toValue();
                                
            } else {
            
                $errors = $response->getErrors();
                msg($errors, 'error');
                
                return false;
            }
            
        } catch (Zend_Exception $e) {
    
            msg("Twitter: {$e->getMessage()}", 'error', 1);
            $this->debugLastHttpRequest();
            
            return false;
    
        }
        
    }
}