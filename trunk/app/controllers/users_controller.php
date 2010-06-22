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

    var $access = array(
        'profile'       => array( 'user', 'admin' ),
        'delete'        => array( 'user', 'admin' )
    );

    function beforeFilter() {
        parent::beforeFilter();
        $this->SwiftMailer->smtpType = Configure::read( 'smtp_type' );
        $this->SwiftMailer->smtpHost  = Configure::read( 'smtp_host' );
        $this->SwiftMailer->smtpUsername = Configure::read( 'smtp_user' );
        $this->SwiftMailer->smtpPassword = Configure::read( 'smtp_password' );
        $this->SwiftMailer->smtpPort = Configure::read( 'smtp_port' );

        $this->SwiftMailer->sendAs = 'text';
        $this->SwiftMailer->from = Configure::read( 'smtp_mail_from_addr' );
        $this->SwiftMailer->fromName = Configure::read( 'smtp_mail_from_name' );
    }

/**
 * Registration method
 * @param POST
 *
 * @return void
 */

    function registration() {
        $this->set( 'title_for_layout', 'New user registration' );
        if ( !empty( $this->data ) ) {
            $this->data = Sanitize::clean( $this->data, array( 'encode'=>false ) );
            $this->User->create();
            $this->User->setValidation( 'registration' );
            $this->User->set( $this->data );
            if ( $this->User->validates() ) {
                $this->data['User']['email'] = strtolower( $this->data['User']['email'] );
                $this->data['User']['pass'] = md5( $this->data['User']['password'] );
                $this->data['User']['enabled'] = 'yes';
                $this->data['User']['ac_code'] = md5( date( 'Ymdhisu' ) );
                $result = $this->User->save( $this->data );
                if ( $result ) {
// sending email
                    $this->SwiftMailer->to = $this->data['User']['email'];
                    $this->set( 'user_data', $this->data );

                    try {
                        if ( !$this->SwiftMailer->send( 'registration_confirm', "[" . Configure::read('site_name') . "] Please activate your new account") ) {
                            $this->Session->setFlash( 'System can not sending confirmation email. Please check your email address.', 'default', array(), 'error' );
                            $this->log("Error sending email");
                            $this->User->delete( $this->User->id );
                        } else {
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
 * Login method
 * @params POST
 * @return void
 */
    function login() {
        $this->set( 'title_for_layout', 'Login' );
        if ( !empty( $this->data ) ) {
            $this->data = Sanitize::clean( $this->data, array( 'encode'=>false ) );
            $this->User->setValidation( 'login' );
            $this->User->set( $this->data );
            if ( $this->User->validates() ) {
                $user_info = $this->User->findByEmail( strtolower( $this->data['User']['email'] ) );
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
            $this->data = Sanitize::clean( $this->data, array( 'encode'=>false ) );
// use validation set for checking entered email
            $this->User->setValidation( 'resend_activation' );
            $this->User->set( $this->data );

            if ( $this->User->validates() ) {
                $this->data = $this->User->findByEmail( strtolower( $this->data['User']['email'] ) );

                if ( !empty( $this->data ) ) {
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
        if ( !empty( $this->User->validationErrors ) ) {
            $this->Session->setFlash( 'Some error occured while process your request.', 'default', array(), 'error' );
        }
    }

    function reset( $key = null ) {
        $this->set( 'title_for_layout', 'Reset password' );

        if ( !empty( $this->data ) ) {
            $this->data = Sanitize::clean( $this->data );
            $this->User->setValidation( 'reset_password' );
            $this->User->set( $this->data );

            if ( $this->User->validates() ) {
                $user_data = $this->User->findByAcCode( $this->data['User']['code'] );

                if ( !empty( $user_data ) ) {
                    $this->User->save( array( 'id'=>$user_data['User']['id'], 'pass'=>md5( $this->data['User']['new_password'] ) ) );
                    $this->Session->setFlash( 'New password has been set. Now you can login with your new password.', 'default', array(), 'success' );
                    $this->redirect( '/users/login' );
                } else {
                    $this->Session->setFlash( 'System unable to find user to change password.', 'default', array(), 'error' );
                    $this->redirect( '/' );
                }
            }

            if ( !empty( $this->User->validationErrors ) ) {
                $this->Session->setFlash( 'Some error occured while process your request. Please check errors below.', 'default', array(), 'error' );
            }
        } else {
            $key = Sanitize::paranoid( $key );
            if ( !empty( $key ) ) {
                $user_data = $this->User->findByAcCode( $key );

                if ( !empty( $user_data ) ) {
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

    function resend() {
        $this->set( 'title_for_layout', 'Re-send activation email' );

        if ( !empty( $this->data ) ) {
            $this->data = Sanitize::clean( $this->data, array( 'encode'=>false ) );
            $this->User->setValidation( 'resend_activation' );
            $this->User->set( $this->data );

            if ( $this->User->validates() ) {
                $this->data = $this->User->findByEmail( strtolower( $this->data['User']['email'] ) );
                if ( !empty( $this->data ) ) {
                    if ( $this->data['User']['activated'] == 'yes' ) {
                        $this->Session->setFlash( 'Your account is already activated. Please try to login.', 'default', array(), 'error' );
                        $this->redirect( '/users/login' );
                    }
// sending activation email
                    $this->SwiftMailer->to = $this->data['User']['email'];
                    $this->set( 'user_data', $this->data );

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
        if ( !empty( $this->User->validationErrors ) ) {
            $this->Session->setFlash( 'Some error occured while process your request. Please check entered data.', 'default', array(), 'error' );
        }
    }

    function confirm( $key = null ) {
        $this->autoRender = false;
        $key = Sanitize::paranoid( $key );

        if ( !empty( $key ) ) {
            $user_info = $this->User->findByAcCode( $key );
            if ( !empty( $user_info ) ) {
                $this->User->save( array( 'id'=>$user_info['User']['id'], 'activated'=>'yes' ) );
                $this->Session->setFlash( 'Account successfully confirmed.', 'default', array(), 'success' );
            } else {
                $this->Session->setFlash( 'User associated with this activation link not found.. Please use "Re0send activation email" link to re-send activation email..', 'default', array(), 'error' );
            }

        } else {
            $this->Session->setFlash( 'Wrong activation link. Please use "Re0send activation email" link to re-send activation email..', 'default', array(), 'error' );
        }
        $this->redirect( '/users/login' );
    }

    function profile() {
        $this->set( 'title_for_layout', 'Profile' );

        if ( !empty( $this->data ) ) {
            $this->data = Sanitize::clean( $this->data, array( 'encode'=>false ) );
// user change email
            if ( $this->user_info['email'] != $this->data['User']['email'] ) {
                $this->User->create();
                $this->User->setValidation( 'profile_email' );
                $this->User->set( $this->data );

                if ( $this->User->validates() ) {
// setting new email
                    $this->User->save( array( 'id'=>$this->user_id, 'email'=>$this->data['User']['email'] ) );
// get user data
                    $this->data = $this->User->findById( $this->user_id );
// save changed data to session
                    $this->Session->save( 'User', $this->data['User'] );

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
// if user fill password fields - try to change password
            if ( !empty( $this->data['User']['new_password'] ) ) {
// checking current password
                if ( md5( $this->data['User']['curr_password'] ) != $this->user_info['pass'] ) {
                    $this->User->invalidate( 'curr_password', 'Invalid password' );
                } else {
                    $this->User->create();
                    $this->User->setValidation( 'profile_password' );
                    $this->User->set( $this->data );

                    if ( $this->User->validates() ) {
                        $this->User->save( array( 'id'=>$this->user_id, 'pass'=>md5( $this->data['User']['new_password'] ) ) );
                    }
                }
            }
            if ( !empty( $this->User->validationErrors ) ) {
                $this->Session->setFlash( 'Some error occured while process your request. Please check entered data.', 'default', array(), 'error' );
            } else {
                $this->Session->setFlash( 'Your account information has been successfully updated.', 'default', array(), 'success' );
            }
        } else {
            $this->data = $this->User->findById( $this->user_id );
        }
    }

    function delete() {
        $this->set( 'title_for_layout', 'Confirm your account deletion' );

        if ( !empty( $this->data ) ) {
            $this->data = Sanitize::clean( $this->data, array( 'encode'=>false ) );

            if ( isset(  $this->data['User']['curr_password'] ) && md5( $this->data['User']['curr_password'] ) == $this->user_info['pass'] ) {
                $this->Location->deleteAll( array( 'Location.user_id' => $this->user_id ) );
                $this->Event->deleteAll( array( 'Event.user_id' => $this->user_id ) );
                $this->User->delete( $this->user_id );
                $this->Session->delete( 'User' );
                $this->Session->setFlash( 'Your account has been successfully removed.', 'default', array(), 'success' );
                $this->redirect( '/' );
            } else {
                $this->User->invalidate( 'curr_password', 'Please enter correct password' );
            }
        }
        if ( !empty( $this->User->validationErrors ) ) {
            $this->Session->setFlash( 'Some error occured while process your request. Please check entered data.', 'default', array(), 'error' );
        }
    }
}

?>
