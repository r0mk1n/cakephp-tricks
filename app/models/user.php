<?php
/* SVN FILE: $Id$ */

/**
 * User model
 * @author		  r0mk1n
 * @version       $Revision$
 * @modifiedby    $LastChangedBy$
 * @lastmodified  $Date$
 */

class User extends AppModel {
    var $name = 'User';

    // validation sets
    var $validationSet = array(
        // registration
        'registration'  => array(
            'password'   => array(
                'minLength' => array (
                    'rule'      => array( 'minLength', '8' ),
                    'required'  => true,
                    'message'   => 'password length need at least 8 characters long'
                ),
                'equalToField' => array (
                    'rule'      => array( 'equalToField', 'repassword' ),
                    'required'  => true,
                    'message'   => 'passwords must be equal'
                ),
            ),
            'repassword'   => array(
                'minLength' => array (
                    'rule'      => array( 'minLength', '8' ),
                    'required'  => true,
                    'message'   => 'password length need at least 8 characters long'
                ),
                'equalToField' => array (
                    'rule'      => array( 'equalToField', 'password' ),
                    'required'  => true,
                    'message'   => 'passwords must be equal'
                ),
            ),
            'email' => array(
                'minLength' => array(
                    'rule'      => array( 'minLength', '1' ),
                    'message'   => 'this field must be filled'
                 ),
                'isEmail' => array(
                    'rule'      => array( 'email', true ),
                    'message'   => 'address must be valid email'
                ),
                'isUnique' => array(
                    'rule'      => 'isUnique',
                    'message'   => 'this email already used by another user'
                ),
            ),
        ),

        // login
        'login' => array(
            'email' => array(
                'minLength' => array(
                    'rule'      => array( 'minLength', '1' ),
                    'message'   => 'this field must be filled'
                 ),
                'isEmail' => array(
                    'rule'      => array( 'email', true ),
                    'message'   => 'address must be valid email'
                ),
            ),
            'password'   => array(
                'minLength' => array (
                    'rule'      => array( 'minLength', '1' ),
                    'required'  => true,
                    'message'   => 'This field cannot be empty'
                ),
            ),
        ),

        // reset password
        'reset_password'    => array(
            'new_password'   => array(
                'minLength' => array (
                    'rule'      => array( 'minLength', '8' ),
                    'required'  => true,
                    'message'   => 'password length need at least 8 characters long'
                ),
                'equalToField' => array (
                    'rule'      => array( 'equalToField', 're_new_password' ),
                    'required'  => true,
                    'message'   => 'passwords must be equal'
                ),
            ),
            're_new_password'   => array(
                'minLength' => array (
                    'rule'      => array( 'minLength', '8' ),
                    'required'  => true,
                    'message'   => 'password length need at least 8 characters long'
                ),
                'equalToField' => array (
                    'rule'      => array( 'equalToField', 'new_password' ),
                    'required'  => true,
                    'message'   => 'passwords must be equal'
                ),
            ),
        ),

        // resend activation email
        'resend_activation' => array(
            'email' => array(
                'minLength' => array(
                    'rule'      => array( 'minLength', '1' ),
                    'message'   => 'this field must be filled'
                 ),
                'isEmail' => array(
                    'rule'      => array( 'email', true ),
                    'message'   => 'address must be valid email'
                ),
            ),
        ),

        // setting new email in profile
        'profile_email'   => array (
            'email' => array(
                'minLength' => array(
                    'rule'      => array( 'minLength', '1' ),
                    'message'   => 'this field must be filled'
                 ),
                'isEmail' => array(
                    'rule'      => array( 'email', true ),
                    'message'   => 'address must be valid email'
                ),
                'uniqueNotMe'   => array(
                    'rule'      => array( 'isUniqueNotMe' ),
                    'message'   => 'you cannot use this email address'

                )
            ),
        ),

        // setting new password in profile
        'profile_password'   => array (
            'curr_password'   => array(
                'minLength' => array (
                    'rule'      => array( 'minLength', '8' ),
                    'required'  => true,
                    'message'   => 'password length need at least 8 characters long'
                ),
            ),
            'new_password'   => array(
                'minLength' => array (
                    'rule'      => array( 'minLength', '8' ),
                    'required'  => true,
                    'message'   => 'password length need at least 8 characters long'
                ),
                'equalToField' => array (
                    'rule'      => array( 'equalToField', 're_new_password' ),
                    'required'  => true,
                    'message'   => 'passwords must be equal'
                ),
            ),
            're_new_password'   => array(
                'minLength' => array (
                    'rule'      => array( 'minLength', '8' ),
                    'required'  => true,
                    'message'   => 'password length need at least 8 characters long'
                ),
                'equalToField' => array (
                    'rule'      => array( 'equalToField', 'new_password' ),
                    'required'  => true,
                    'message'   => 'passwords must be equal'
                ),
            ),
        )

    );

/**
 * Checking is this email not using by another user
 *
 * @param  $email
 * @return bool
 */
    function isUniqueEmail( $email ) {
        return $this->find( 'count', array( 'conditions'=>array( 'email'=>$email ) ) ) ? false : true;
    }

/**
 * Checking is this email exists
 * @param  $email
 * @return bool
 */
    function isEmailExists( $email ) {
        return $this->find( 'count', array( 'conditions'=>array( 'email'=>$email ) ) ) ? true : false;
    }

/**
 * Is this email unique ( exclude curemnt user email )
 * @param  $data
 * @return bool
 */
    function isUniqueNotMe( $data ) {
        $key = array_keys( $data );
        if ( isset( $key[0] ) ) {
            $key = $key[0];
            $result = $this->find( 'count', array( 'conditions'=>array( "User.id <> {$this->data['User']['id']}", "User.{$key}"=>$data[$key] ) ) ) ? false : true;
            return $result;
        }
        return false;
    }

}
?>
