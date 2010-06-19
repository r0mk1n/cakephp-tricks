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
    var $uses           = array( 'User' );
    var $components     = array( 'SwiftMailer' );

/**
 * Registration method
 * @param POST
 *
 * @return void
 */

    function registration() {
        $this->set( 'title_for_layout', 'New user registration' );
        if ( !empty( $this->data ) ) {
            $this->data = Sanitize::clean( $this->data );
            $this->User->create();
            $this->User->setValidation( 'registration' );
            $this->User->set( $this->data );
            if ( $this->User->validates() ) {
                $this->data['User']['pass'] = md5( $this->data['User']['password'] );
                $this->data['User']['enabled'] = 'yes';
                $this->data['User']['ac_code'] = md5( date( 'Ymdhisu' ) );
                $result = $this->User->save( $this->data );
                if ( $result ) {
// sending email
                    $this->SwiftMailer->smtpType = Configure::read( 'smtp_type' );
                    $this->SwiftMailer->smtpHost  = Configure::read( 'smtp_host' );
                    $this->SwiftMailer->smtpUsername = Configure::read( 'smtp_user' );
                    $this->SwiftMailer->smtpPassword = Configure::read( 'smtp_password' );
                    $this->SwiftMailer->smtpPort = Configure::read( 'smtp_port' );

                    $this->SwiftMailer->sendAs = 'text';
                    $this->SwiftMailer->from = Configure::read( 'smtp_mail_from_addr' );
                    $this->SwiftMailer->fromName = Configure::read( 'smtp_mail_from_name' );

                    $this->SwiftMailer->to = $this->data['User']['email'];
                    $this->set( 'user_data', $this->data );

                    try {
                        if ( !$this->SwiftMailer->send( 'registration_confirm', "[" . Configure::read('site_name') . "] Please activate your new account") ) {
                            $this->Session->setFlash( 'System can not sending confirmation email. Please check your email address.', 'default', array(), 'error' );
                            $this->User->delete( $this->User->id );
                        } else {
                            $this->Session->setFlash( 'Account successfully created', 'default', array(), 'success' );
                            $this->redirect( '/pages/registration-done' );
                        }
                    } catch(Exception $e) {
                         $this->Session->setFlash( 'System can not sending confirmation email. Please check your email address.', 'default', array(), 'error' );
                         $this->User->delete( $this->User->id );
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
 * Login method
 * @params POST
 * @return void
 */
    function login() {
        $this->set( 'title_for_layout', 'Login' );
        if ( !empty( $this->data ) ) {
            $this->data = Sanitize::clean( $this->data );
            $this->User->setValidation( 'login' );
            $this->User->set( $this->data );
            if ( $this->User->validates() ) {
                $user_info = $this->User->findByEmail( $this->data['User']['email'] );
                if ( !empty( $user_info ) ) {
                    if ( md5( $this->data['User']['password'] ) == $user_info['User']['pass'] ) {
// checking enabled status
                        if ( $user_info['User']['enabled'] == 'no' ) {
                            $this->Session->setFlash( 'Your account has been disabled', 'default', array(), 'error' );
                            return;
                        }
                        if ( $user_info['User']['activated'] == 'no' ) {
                            $this->Session->setFlash( 'Your account is not yet activated. Please check your email box for activation instructions', 'default', array(), 'info' );
                        }
                        $this->Session->write( 'User', $user_info['User'] );
// update modified time

                        $this->User->save( array( 'id' => $user_info['User']['id'], 'modified'=>date( 'Y-m-d h:i' )) );

                        $this->Session->setFlash( 'You are successfully logged in.', 'default', array(), 'success' );
                        if ( $this->Session->check( 'before_login_url' ) ) {
                            $url = $this->Session->read( 'before_login_url' );
                            $this->Session->delete( 'before_login_url' );
                            $this->redirect( $url );
                        } else {
                            $this->redirect( '/events' );
                        }
                    } else {
                        $this->User->invalidate( 'password', 'Wrong password entered.' );
                    }
                } else {
                    $this->User->invalidate( 'email', 'User with entered email address not found.' );
                }
            }
        }

        if ( !empty( $this->User->validationErrors ) ) {
            $this->Session->setFlash( 'Some error occured while process your request. Please check entered data.', 'default', array(), 'error' );
        }
    }

/**
 * Logout user
 * @return void
 */
    function logout() {
        if ( $this->Session->check( 'User' ) ) {
            $this->Session->delete( 'User' );
            $this->Session->setFlash( 'You are successfully logout from your account.', 'default', array(), 'success' );
        } else {
            $this->Session->setFlash( 'You are not logged out from your account.', 'default', array(), 'error' );
        }
        $this->redirect( '/' );
    }

    function restorepassword() {
        $this->set( 'title_for_layout', 'Restoring lost password' );

        if ( !empty( $this->data ) ) {

        }
    }

    function resend() {
         $this->set( 'title_for_layout', 'Re-send activation email' );

         if ( !empty( $this->data ) ) {

         }
    }

    function confirm( $code = null ) {
        $this->autoRender = false;
        $code = Sanitize::paranoid( $code );

        if ( !empty( $code ) ) {


            $this->Session->setFlash( 'Account successfully confirmed.', 'default', array(), 'success' );
            $this->redirect( '/pages/confirmation-done' );
        }
    }

    function profile() {
        $this->set( 'title_for_layout', 'Profile' );

        if ( !empty( $this->data ) ) {

        } else {
            $this->data = $this->User->findById( $this->user_id );
        }
    }

    function info( $user = null ) {
        $user = Sanitize::clean( $user );


    }

}

?>
