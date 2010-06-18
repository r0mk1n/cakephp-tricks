<?php

/* SVN FILE: $Id: locations_controller.php 7 2010-06-17 15:30:42Z r0mk1n.work $ */

/**
 * Locations controller
 *  provide locations management
 * @author		  r0mk1n
 * @version       $Revision: 7 $
 * @modifiedby    $LastChangedBy: r0mk1n.work $
 * @lastmodified  $Date: 2010-06-17 18:30:42 +0300 (Чт, 17 июн 2010) $
 */

class LocationsController extends AppController {
    var $name       = 'Locations';
    var $uses       = array( 'Location', 'Event' );
    var $helpers    = array( 'Address' );

    var $paginate   = array(
        'order' => 'Location.created DESC',
        'limit' => 10
    );

    var $access     = array(
        'index'     => array( 'user', 'admin' ),
        'add'       => array( 'user', 'admin' ),
        'edit'      => array( 'user', 'admin' ),
        'delete'    => array( 'user', 'admin' ),
        'find'      => array( 'user', 'admin' ),
    );

    function index() {
        $this->set( 'title_for_layout', 'Locations' );
        $this->paginate['conditions']   = array( 'Location.user_id' => $this->user_id );
        $this->data = $this->paginate( 'Location' );
    }

    function add() {
        $this->set( 'title_for_layout', 'New location' );
        $this->data = Sanitize::clean( $this->data );
        if ( !empty( $this->data ) ) {
            $this->Location->create();
            $this->Location->setValidation( 'add' );
            $this->Location->set( $this->data );

            if ( $this->Location->validates() ) {
                $this->data['Location']['user_id'] = $this->user_id;
                $this->Location->save( $this->data );

                $this->Session->setFlash( 'New location has been successfully created.', 'default', array(), 'success' );
                $this->redirect( '/locations' );
            } else {
                $this->Session->setFlash( 'Some errors occure while adding new location, see errors below', 'default', array(), 'error' );
            }
        }
    }

    function edit( $id = null ) {
        $this->set( 'title_for_layout', 'Edit location' );
        $data = Sanitize::paranoid( $id );

        if ( !empty( $this->data ) ) {
            $this->Location->set( $this->data );
            $this->Location->setValidation( 'add' );

            if ( $this->Location->validates() ) {
                $this->Location->save( $this->data );

                $this->Session->setFlash( 'Location has been successfully updated.', 'default', array(), 'success' );
                $this->redirect( '/locations' );
            } else {
                $this->Session->setFlash( 'Some errors occure while updatging location, see errors below', 'default', array(), 'error' );
            }
        } else {
            $this->data = $this->Location->findById( $id );
            if ( empty( $this->data ) || $this->data['Location']['user_id'] != $this->user_id ) {
                $this->Session->setFlash( 'Location with such ID not found.', 'default', array(), 'error' );
                $this->redirect( '/locations' );
            }
        }
    }

    function delete( $id ) {
        $this->autoRender = false;
        $id = Sanitize::paranoid( $id );

        if ( !empty( $id ) ) {
            $this->data = $this->Location->findById( $id );
// prevent deleting non-exiting location
            if ( empty( $this->data ) || $this->data['Location']['user_id'] != $this->user_id ) {
                $this->Session->setFlash( 'Location with such ID not found.', 'default', array(), 'error' );
            } else {
// checking if location is using in one of not completed items
                $events = $this->Event->find( 'count', array( 'Event.location_id'=>$id, 'Event.complete'=>'no' ) );
                if ( $events ) {
                    $this->Session->setFlash( 'Location with such ID used by one or more not complete events and cannot be delete.', 'default', array(), 'error' );
                } else {
                    $this->Location->delete( $id );
                    $this->Session->setFlash( 'Location has been successfully deleted.', 'default', array(), 'success' );
                }
            }
        }
        $this->redirect( '/locations' );
    }

    function find() {
        $this->layout = 'json';
        if ( isset( $this->params['url']['term'] ) ) {
            $term = Sanitize::clean( $this->params['url']['term'] );
            $this->data = $this->Location->find( 'all', array( 'conditions'=>array( "upper(Location.title) like upper( '%{$term}%' ) " ) ) );
        }
        $this->log( $this->data );
    }
}

?>
