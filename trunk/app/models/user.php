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

    var $validationSet = array(
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
                    'rule'      => array( 'email', false ),
                    'message'   => 'address must be valid email'
                ),
                'isUnique' => array(
                    'rule'      => 'isUnique',
                    'message'   => 'this email already used by another user'
                ),
            ),
        ),
        'login' => array(
            'email' => array(
                'minLength' => array(
                    'rule'      => array( 'minLength', '1' ),
                    'message'   => 'this field must be filled'
                 ),
                'isEmail' => array(
                    'rule'      => array( 'email', false ),
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
        )
    );

    function isUniqueEmail( $email ) {
        return $this->find( 'count', array( 'conditions'=>array( 'email'=>$email ) ) ) ? false : true;
    }

    function isEmailExists( $email ) {
        return $this->find( 'count', array( 'conditions'=>array( 'email'=>$email ) ) ) ? true : false;
    }

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