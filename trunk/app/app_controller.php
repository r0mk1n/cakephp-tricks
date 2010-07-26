<?php
/**
 * Overrided appController
 * @author r0mk1n
 *
 */

App::import( 'Core', 'Sanitize' );

class AppController extends Controller {
// Common helpers definition
    var $helpers 		= array( 'Html', 'Javascript', 'Ajax', 'Form', 'Text', 'Session' );
// Common components definition
    var $components     = array( 'Session', 'Cookie', 'RequestHandler' );

// default user settings
    var $user_info      = null;
    var $user_id        = null;
    var $user_role      = 'guest';

// default authentication URL
    var $auth_url       = '/users/login';

// pre-defined access array (will be defined in controllers, that extends AppController)
    var $access         = array();

/**
 * Override beforeFilter method
 *  - Loading user-data from session ( if user is logged in )
 *  - checking access ( based on access array )
 * @return void
 */
    function beforeFilter() {
        // loading current user info
        // this value should be write to session in UsersController->login or UsersController->profile
        if ( $this->Session->check( "User" ) ) {
            $this->user_info = $this->Session->read( "User" );
            $this->user_role = $this->user_info['role'];
            $this->user_id = $this->user_info['id'];
        } else {
            $this->user_info   = null;
            $this->user_id     = null;
            $this->user_role   = 'guest';
        }

        // setting user's info for views
        $this->set( 'User', $this->user_info );

        // checking access
        if ( !$this->checkAccess( $this->action, $this->access ) ) {
        	$this->Session->write( 'before_login_url', $this->here );
        	$this->redirect( $this->auth_url );
        }
    }

/**
 * Check access function (ACL - like)
 * based on $controller->access = array( 'method'=>array( 'role1', 'role2' ... 'roleN' ) )
 *
 * @param String $action
 * @param Array $access
 * @return Boolean
 */
    function checkAccess( $action, $access = '' ) {
        $result = true;
        if ( is_array( $access ) && array_key_exists( $action, $access ) ) {
            if ( array_key_exists( 'role', $access[$action] ) ) {
                if ( in_array( $this->user_role, $access[$action]['role'])) {
                    $result =  false;
                }
            } else {
                if ( !in_array( $this->user_role, $access[$action] ) ) {
                     $result = false;
                }
            }
        }
        return $result;
    }
}
