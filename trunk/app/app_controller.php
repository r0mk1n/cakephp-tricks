<?php
/**
 * Overrided appController
 * @author r0mk1n
 *
 */

App::import( 'Core', 'Sanitize' );

class AppController extends Controller {
    var $helpers 		= array ( 'Html', 'Javascript', 'Ajax', 'Form', 'Text', 'Session');
    var $components     = array( 'Session', 'RequestHandler', 'Cookie' );

    var $user_info      = null;
    var $user_id        = null;
    var $user_role      = 'guest';

    var $auth_url       = '/users/login';

    var $access         = array();  

    function beforeFilter() {
        if ( $this->Session->check( "User" ) ) {
            $this->user_info = $this->Session->read( "User" );
            $this->user_role = $this->user_info['role'];
            $this->user_id = $this->user_info['id'];
        } else {
            $this->user_info   = null;
            $this->user_id     = null;
            $this->user_role   = 'guest';
        }
        $this->set( 'User', $this->user_info );
        if ( !$this->checkAccess( $this->action, $this->access ) ) {
        	$this->Session->write( 'before_login_url', $this->here );
        	$this->redirect( $this->auth_url );
        }

    }

/**
 * Check access function (ACL - like)
 * based on $controller->access = array( 'view'=>array( 'user', 'admin' ) )
 *
 * @param String $action
 * @param Array $access
 * @return Boolean
 */
    function checkAccess( $action, $access = '' ) {
        if ( is_array( $access ) && array_key_exists( $action, $access ) ) {
            if ( array_key_exists( 'role', $access[$action] ) ) {
                // access based on role
                if ( in_array( $this->user_role, $access[$action]['role'])) {
                    return true;
                } else {
                    return false;
                }
            } else {
                if ( in_array( $this->user_role, $access[$action] ) ) {
                     return true;
                } else {
                     return false;
                }
            }
        }
        // default - no access control,
        return  true;
    }


}
