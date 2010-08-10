<?php

/* SVN FILE: $Id$ */

/**
 * Users controller
 *  provide users controlling, registration, profile etc
 * @author		  r0mk1n
 * @version       $Revision$
 * @modifiedby    $LastChangedBy$
 * @lastmodified  $Date$
 */

class UsersController extends AppController {
    var $name           = 'Users';
    var $uses           = array( 'User', 'Event', 'Location' );
    var $components     = array( 'SwiftMailer' );
    var $helpers        = array( 'Time' );

/**
 * access array
 * @see /app/app_controller.php
 */
    var $access = array(
        'profile'       => array( 'user', 'admin' ),
        'delete'        => array( 'user', 'admin' ),

        'admin_index'           => array( 'admin' ),
        'admin_update'          => array( 'admin' ),
        'admin_set_filter'      => array( 'admin' ),
        'admin_reset_filter'    => array( 'admin' ),
    );

/**
 * Override beforeFilter method from app_controllelr
 * @return void
 */
    function beforeFilter() {
        // call parent method
        parent::beforeFilter();

        // setuping mailer
        // for settings see /app/config/core.php
        $this->SwiftMailer->smtpType = Configure::read( 'smtp_type' );
        $this->SwiftMailer->smtpHost  = Configure::read( 'smtp_host' );
        $this->SwiftMailer->smtpUsername = Configure::read( 'smtp_user' );
        $this->SwiftMailer->smtpPassword = Configure::read( 'smtp_password' );
        $this->SwiftMailer->smtpPort = Configure::read( 'smtp_port' );

        $this->SwiftMailer->sendAs = 'text';
        $this->SwiftMailer->from = Configure::read( 'smtp_mail_from_addr' );
        $this->SwiftMailer->fromName = Configure::read( 'smtp_mail_from_name' );

        $this->SwiftMailer->replyTo = Configure::read( 'smtp_mail_from_addr' );
    }

/**
 * Registration method
 * @param POST
 *
 * @return void
 */
    function registration() {
        // setting title
        $this->set( 'title_for_layout', 'New user registration' );
        // is incoming data exists ?
        if ( !empty( $this->data ) ) {
            // clean incoming data
            $this->data = Sanitize::clean( $this->data, array( 'encode'=>false ) );
            // initialize User model
            $this->User->create();
            // setting validation scheme
            $this->User->setValidation( 'registration' );
            // setting data
            $this->User->set( $this->data );
            // validating
            if ( $this->User->validates() ) {
                // filling required fields
                $this->data['User']['email'] = strtolower( $this->data['User']['email'] );
                $this->data['User']['pass'] = md5( $this->data['User']['password'] );
                $this->data['User']['enabled'] = 'yes';
                $this->data['User']['ac_code'] = md5( date( 'Ymdhisu' ) );
                // save new user record
                $result = $this->User->save( $this->data, false );

                if ( $result ) {
                    // sending email
                    $this->SwiftMailer->to = $this->data['User']['email'];
                    $this->set( 'user_data', $this->data );

                    try {
                        if ( !$this->SwiftMailer->send( 'registration_confirm', "[" . Configure::read('site_name') . "] Please activate your new account") ) {
                            $this->Session->setFlash( 'System can not sending confirmation email. Please check your email address.', 'default', array(), 'error' );
                            $this->log( "Error sending email" );
                            // delete user record if email fail
                            $this->User->delete( $this->User->id );
                        } else {
                            // redirecting on success
                            $this->Session->setFlash( 'Account successfully created', 'default', array(), 'success' );
                            $this->redirect( '/pages/registration-done' );
                        }
                    } catch(Exception $e) {
                         $this->Session->setFlash( 'System can not sending confirmation email. Please check your email address.', 'default', array(), 'error' );
                         $this->User->delete( $this->User->id );
                         $this->log("Failed to send email: ".$e->getMessage());
                    }
                } else {
                    $this->Session->setFlash( 'Registration failed, please check entered data', 'default', array(), 'error' );
                }
            } else {
                $this->Session->setFlash( 'Registration failed, please check entered data', 'default', array(), 'error' );
            }
        }
    }


/**
 * Login
 * @params POST
 * @return void
 */
    function login() {
        // setting page title
        $this->set( 'title_for_layout', 'Login' );
        // checking incoming data
        if ( !empty( $this->data ) ) {
            // cleaning data
            $this->data = Sanitize::clean( $this->data, array( 'encode'=>false ) );
            // setting validation
            $this->User->setValidation( 'login' );
            // setting incoming data to model
            $this->User->set( $this->data );
            // validate incoming data
            if ( $this->User->validates() ) {
                // finding user by email
                $user_info = $this->User->findByEmail( strtolower( $this->data['User']['email'] ) );
                // if user found ...
                if ( !empty( $user_info ) ) {
                    // checking password hashes
                    if ( md5( $this->data['User']['password'] ) == $user_info['User']['pass'] ) {
                        // checking enabled status
                        if ( $user_info['User']['enabled'] == 'no' ) {
                            $this->Session->setFlash( 'Your account has been disabled', 'default', array(), 'error' );
                            $this->redirect( '/' );
                        }
                        // checking account activation flag
                        if ( $user_info['User']['activated'] == 'no' ) {
                            $this->Session->setFlash( 'Your account is not yet activated. Please check your email box for activation instructions', 'default', array(), 'info' );
                        }
                        // write user info to session ( actually this row is log-in ;) )
                        $this->Session->write( 'User', $user_info['User'] );

                        // update modified time ( we setting this value to determine last user login, it will be used for remove non-active accounts )
                        $this->User->save( array( 'id' => $user_info['User']['id'], 'modified'=>date( 'Y-m-d h:i' )) );
                        // setting message
                        $this->Session->setFlash( 'You are successfully logged in.', 'default', array(), 'success' );
                        // checking url to redirect
                        if ( $this->Session->check( 'before_login_url' ) ) {
                            $url = $this->Session->read( 'before_login_url' );
                            $this->Session->delete( 'before_login_url' );
                            $this->redirect( $url );
                        } else {
                            $this->redirect( '/events' );
                        }
                    } else {
                        // password wrong, just setting error message for 'password' field
                        $this->User->invalidate( 'password', 'Wrong password entered.' );
                    }
                } else {
                    // email not exists, just setting message for 'email' field
                    $this->User->invalidate( 'email', 'User with entered email address not found.' );
                }
            }
        }
        // setting global error message, on one or more errors
        if ( !empty( $this->User->validationErrors ) ) {
            $this->Session->setFlash( 'Some error occured while process your request. Please check entered data.', 'default', array(), 'error' );
        }
    }

/**
 * Logout user
 * @return void
 */
    function logout() {
        // checking if user logged in
        if ( $this->Session->check( 'User' ) ) {
            // delete user info from session
            $this->Session->delete( 'User' );
            $this->Session->setFlash( 'You are successfully logout from your account.', 'default', array(), 'success' );
        } else {
            $this->Session->setFlash( 'You are not logged out from your account.', 'default', array(), 'error' );
        }
        // redirecting to main page
        $this->redirect( '/' );
    }

/**
 * Restore password
 * @return void
 */
    function restorepassword() {
        // setting title
        $this->set( 'title_for_layout', 'Restoring lost password' );

        // checking incoming data
        if ( !empty( $this->data ) ) {
            // cleaning incoming data
            $this->data = Sanitize::clean( $this->data, array( 'encode'=>false ) );
            // use validation set for checking entered email
            $this->User->setValidation( 'resend_activation' );
            // setting data to model
            $this->User->set( $this->data );
            // validate
            if ( $this->User->validates() ) {
                // finding user information
                $this->data = $this->User->findByEmail( strtolower( $this->data['User']['email'] ) );
                // user record found ...
                if ( !empty( $this->data ) ) {

                    // sending email using 3rd party library SwiftMailer
                    // @see /app/classes/components/swift_mailer.php
                    $this->SwiftMailer->to = $this->data['User']['email'];
                    $this->set( 'user_data', $this->data );

                    try {
                        if ( !$this->SwiftMailer->send( 'restore_password', "[" . Configure::read('site_name') . "] You have requested a password change") ) {
                            $this->Session->setFlash( 'System can`t send email. Please try againg later.', 'default', array(), 'error' );
                        } else {
                            $this->Session->setFlash( 'Email with password restoring instructions successfuly sent.', 'default', array(), 'success' );
                        }
                    } catch(Exception $e) {
                         $this->Session->setFlash( 'System can not re-send activation email. Please try againg later.', 'default', array(), 'error' );
                    }
                } else {
                    $this->User->invalidate( 'email', 'User with such email not found. Please enter correct email.' );
                }
            }
        }
        // setting global error message
        if ( !empty( $this->User->validationErrors ) ) {
            $this->Session->setFlash( 'Some error occured while process your request.', 'default', array(), 'error' );
        }
    }

/**
 * reset password
 * @param $key = 32-x digit key
 */
    function reset( $key = null ) {
        // setting title
        $this->set( 'title_for_layout', 'Reset password' );

        // check if incoming data exists
        if ( !empty( $this->data ) ) {
            // cleaning data
            $this->data = Sanitize::clean( $this->data );
            // setting validation scheme
            $this->User->setValidation( 'reset_password' );
            // setting data
            $this->User->set( $this->data );
            // validating ...
            if ( $this->User->validates() ) {
                // searching user by code (key)
                $user_data = $this->User->findByAcCode( $this->data['User']['code'] );

                // if user information exists
                if ( !empty( $user_data ) ) {
                    // saving new password
                    $this->User->save( array( 'id'=>$user_data['User']['id'], 'pass'=>md5( $this->data['User']['new_password'] ) ) );
                    // setting message
                    $this->Session->setFlash( 'New password has been set. Now you can login with your new password.', 'default', array(), 'success' );
                    // redirecting to login page
                    $this->redirect( '/users/login' );
                } else {
                    // setting error message
                    $this->Session->setFlash( 'System unable to find user to change password.', 'default', array(), 'error' );
                    // redirecting to home page
                    $this->redirect( '/' );
                }
            }

            // setting global error message
            if ( !empty( $this->User->validationErrors ) ) {
                $this->Session->setFlash( 'Some error occured while process your request. Please check errors below.', 'default', array(), 'error' );
            }
        } else {
            // cleaning key
            $key = Sanitize::paranoid( $key );
            // if not empty key
            if ( !empty( $key ) ) {
                // finding user
                $user_data = $this->User->findByAcCode( $key );

                if ( !empty( $user_data ) ) {
                    // setting key to $data array (for view)
                    $this->data['User']['code'] = $key;
                } else {
                    $this->Session->setFlash( 'Wrong password restoring link', 'default', array(), 'error' );
                    $this->redirect( '/' );
                }
            } else {
                $this->Session->setFlash( 'Wrong password restoring link.', 'default', array(), 'error' );
                $this->redirect( '/' );
            }
        }
    }

/**
 * Resending activation email
 *
 * @return void
 */
    function resend() {
        // setting title for layout
        $this->set( 'title_for_layout', 'Re-send activation email' );

        // checking incoming data
        if ( !empty( $this->data ) ) {
            // cleaning incoming data
            $this->data = Sanitize::clean( $this->data, array( 'encode'=>false ) );
            // setting validation scheme
            $this->User->setValidation( 'resend_activation' );
            // setting data to model
            $this->User->set( $this->data );

            // validate
            if ( $this->User->validates() ) {
                // finding user
                $this->data = $this->User->findByEmail( strtolower( $this->data['User']['email'] ) );
                // if user found
                if ( !empty( $this->data ) ) {
                    // if user's account activated ...
                    if ( $this->data['User']['activated'] == 'yes' ) {
                        // setting error message
                        $this->Session->setFlash( 'Your account is already activated. Please try to login.', 'default', array(), 'error' );
                        // redirecting to login page
                        $this->redirect( '/users/login' );
                    }

                    // sending activation email

                    $this->SwiftMailer->to = $this->data['User']['email'];
                    $this->set( 'user_data', $this->data );

                    // re-sending activation email
                    try {
                        if ( !$this->SwiftMailer->send( 'registration_confirm', "[" . Configure::read('site_name') . "] Please activate your new account") ) {
                            $this->Session->setFlash( 'System can not re-send activation email. Please try againg later.', 'default', array(), 'error' );
                        } else {
                        $this->Session->setFlash( 'Account successfully created', 'default', array(), 'success' );
                        $this->redirect( '/pages/registration-done' );

                        }
                    } catch(Exception $e) {
                         $this->Session->setFlash( 'System can not re-send activation email. Please try againg later.', 'default', array(), 'error' );
                    }

                } else {
                    $this->User->invalidate( 'email', 'User with such email not found' );
                }
            }
        }

        // setting global error message (if one of fields invalid)
        if ( !empty( $this->User->validationErrors ) ) {
            $this->Session->setFlash( 'Some error occured while process your request. Please check entered data.', 'default', array(), 'error' );
        }
    }

/**
 * Confirm account
 * @param  $key
 * @return void
 */
    function confirm( $key = null ) {
        // turning off auto-rendering
        $this->autoRender = false;
        // clean incoming key
        $key = Sanitize::paranoid( $key );

        // if key is not empty ...
        if ( !empty( $key ) ) {
            // searching for user's info
            $user_info = $this->User->findByAcCode( $key );
            // if user info found ...
            if ( !empty( $user_info ) ) {
                // activating account
                $this->User->save( array( 'id'=>$user_info['User']['id'], 'activated'=>'yes' ) );
                $this->Session->setFlash( 'Account successfully confirmed.', 'default', array(), 'success' );
            } else {
                $this->Session->setFlash( 'User associated with this activation link not found.. Please use "Re0send activation email" link to re-send activation email..', 'default', array(), 'error' );
            }
        } else {
            // setting error message
            $this->Session->setFlash( 'Wrong activation link. Please use "Re0send activation email" link to re-send activation email..', 'default', array(), 'error' );
        }
        // redirecting to login page
        $this->redirect( '/users/login' );
    }

/**
 * Profile
 * @return void
 */
    function profile() {
        // setting title for page
        $this->set( 'title_for_layout', 'Profile' );

        // if incoming data exists ....
        if ( !empty( $this->data ) ) {
            // clean incoming data ...
            $this->data = Sanitize::clean( $this->data, array( 'encode'=>false ) );
            // if user change email ...
            if ( $this->user_info['email'] != $this->data['User']['email'] ) {
                // reseting model
                $this->User->create();
                // setting validation scheme
                $this->User->setValidation( 'profile_email' );
                // setting data to model
                $this->User->set( $this->data );

                // validating
                if ( $this->User->validates() ) {
                    // saving new email
                    $this->User->save( array( 'id'=>$this->user_id, 'email'=>$this->data['User']['email'] ) );
                    // get user data
                    $this->data = $this->User->findById( $this->user_id );
                    // save changed data to session
                    $thus->user_info = $this->User->findById( $this->user_id );
                    $this->Session->write( 'User', $this->user_info );


                    // re-sending activation email to new address
                    $this->SwiftMailer->to = $this->data['User']['email'];
                    $this->set( 'user_data', $this->data );

                    try {
                        if ( !$this->SwiftMailer->send( 'registration_confirm', "[" . Configure::read('site_name') . "] Please please confirm your new email") ) {
                            $this->Session->setFlash( 'System can not re-send activation email. Please try againg later.', 'default', array(), 'error' );
                        }
                    } catch(Exception $e) {
                         $this->Session->setFlash( 'System can not re-send activation email. Please try againg later.', 'default', array(), 'error' );
                    }
                }
            }
            // if user fill password fields - trying to change password
            if ( !empty( $this->data['User']['new_password'] ) ) {
                // checking current password
                if ( md5( $this->data['User']['curr_password'] ) != $this->user_info['pass'] ) {
                    $this->User->invalidate( 'curr_password', 'Invalid password' );
                } else {
                    // reseting model
                    $this->User->create();
                    // setting validation scheme
                    $this->User->setValidation( 'profile_password' );
                    // setting data to model
                    $this->User->set( $this->data );

                    // validating
                    if ( $this->User->validates() ) {
                        // saving new password
                        $this->User->save( array( 'id'=>$this->user_id, 'pass'=>md5( $this->data['User']['new_password'] ) ) );
                        // setting new user's info
                        $thus->user_info = $this->User->findById( $this->user_id );
                        $this->Session->write( 'User', $this->user_info );
                    }
                }
            }
            // setting global message
            if ( !empty( $this->User->validationErrors ) ) {
                $this->Session->setFlash( 'Some error occured while process your request. Please check entered data.', 'default', array(), 'error' );
            } else {
                $this->Session->setFlash( 'Your account information has been successfully updated.', 'default', array(), 'success' );
            }
            $this->redirect( '/users/profile' );
        } else {
            // reading data for view
            $this->data = $this->User->findById( $this->user_id );
        }
    }

/**
 * Delete account
 * @return void
 */
    function delete() {
        // setting title for page
        $this->set( 'title_for_layout', 'Confirm your account deletion' );

        // checking incoming data
        if ( !empty( $this->data ) ) {
            // cleaning incoming data
            $this->data = Sanitize::clean( $this->data, array( 'encode'=>false ) );

            // checking entered and current password ...
            if ( isset( $this->data['User']['curr_password'] ) && md5( $this->data['User']['curr_password'] ) == $this->user_info['pass'] ) {
                // deleting all users data ...
                $this->Location->deleteAll( array( 'Location.user_id' => $this->user_id ) );
                $this->Event->deleteAll( array( 'Event.user_id' => $this->user_id ) );
                $this->User->delete( $this->user_id );
                $this->Session->delete( 'User' );
                // setting message
                $this->Session->setFlash( 'Your account has been successfully removed.', 'default', array(), 'success' );
                // redirecting
                $this->redirect( '/' );
            } else {
                // setting error message
                $this->User->invalidate( 'curr_password', 'Please enter correct password' );
            }
        }
        // setting global error message
        if ( !empty( $this->User->validationErrors ) ) {
            $this->Session->setFlash( 'Some error occured while process your request. Please check entered data.', 'default', array(), 'error' );
        }
    }

/***********************************************************************************************************************************************
 * Backend methods
 */

/**
 * Showing users list (backend)
 * @return void
 */
    function admin_index() {
        // setting 'admin' layout
        $this->layout = 'backend';
        // setting title for page
        $this->set( 'title_for_layout', 'Users management' );

        // reading default filter values
        $filter = $this->__default_filter();
        // checking saved filter values
        if ( $this->Session->check( 'User.filter' ) ) {
            // reading filter values
            $filter = $this->Session->read( 'User.filter' );
        }
        // setting filter ( for view )
        $this->set( 'filter', $filter );

        // setup pagination
        $this->paginate = array(
            'limit'     => 10,
            'order'     => 'User.created DESC'
        );

        // applying filters for 'conditions'
        if ( !empty( $filter['email'] ) ) {
            $this->paginate['conditions'][] = array( "LOWER(User.email) like LOWER('%{$filter['email']}%')" );
        }
        if ( $filter['activated'] != 'all' ) {
            $this->paginate['conditions'][] = array( "User.activated"=>$filter['activated'] );
        }
        if ( $filter['enabled'] != 'all' ) {
            $this->paginate['conditions'][] = array( "User.enabled"=>$filter['enabled'] );
        }
        if ( $filter['role'] != 'all' ) {
            $this->paginate['conditions'][] = array( "User.role"=>$filter['role'] );
        }

        // paginate User model
        $this->data = $this->paginate( 'User' );
    }


 /**
  * Update users list (backend)
  * @return void
  */
    function admin_update() {
        // turn off auto-render
        $this->autoRender = false;

        // Cleaning incoming data
        $this->data = Sanitize::clean( $this->data, array( 'encode'=>false ) );

        // checking incoming data
        if ( !empty( $this->data ) ) {
            // iterating incoming data ...
            foreach( $this->data['User'] as $user_id => $row ) {
                // if row marked for delete ...
                if ( $row['delete'] == 'yes' ) {
                    // deleting user's record with all dependent records from another tables
                    $this->Location->deleteAll( array( 'Location.user_id' => $user_id ) );
                    $this->Event->deleteAll( array( 'Event.user_id' => $user_id ) );
                    $this->User->delete( $user_id );
                } else {
                    // update record
                    $this->User->save( array( 'id'=>$user_id, 'activated'=>$row['activated'], 'enabled'=>$row['enabled'], 'role'=>$row['role'] ) );
                }
            }
        }
        // setting message
        $this->Session->setFlash( 'Records has been updated', 'default', array(), 'success' );
        // redirecting
        $this->redirect( '/admin/users' );
    }

/**
 * Filter setter (backend)
 * @return void
 */

    function admin_set_filter() {
        // turn of auto-render
        $this->autoRender = false;
        // clean incoming data
        $this->data = Sanitize::clean( $this->data );

        if ( !empty( $this->data ) ) {
            // setting filters
            $filters = array(
                'email'         => !empty( $this->data['User']['email'] ) ? $this->data['User']['email'] : '',
                'activated'     => $this->data['User']['activated'],
                'enabled'       => $this->data['User']['enabled'],
                'role'          => $this->data['User']['role'],
            );
            // saving filter data to session
            $this->Session->write( 'User.filter', $filters );
        }
        // redirecting
        $this->redirect( '/admin/users' );
    }

/**
 * Reseting filter (backend)
 * @return void
 */
    function admin_reset_filter(){
        // turn off auto-render
        $this->autoRender = false;

        // write default data to session
        $this->Session->write( 'User.filter', $this->__default_filter() );
        // redirecting ...
        $this->redirect( '/admin/users' );
    }

/**
 * Default filter values setter
 * @return
 */
    function __default_filter() {
        return array(
            'email'         => '',
            'activated'     => 'all',
            'enabled'       => 'all',
            'role'          => 'all',
        );
    }
}

?>
