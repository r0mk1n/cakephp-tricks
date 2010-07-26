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

/**
 * default pagination settings
 */
    var $paginate   = array(
        'order' => 'Location.created DESC',
        'limit' => 10
    );

/**
 * access array
 * @see /app/app_controller.php for details
 */
    var $access     = array(
        'index'     => array( 'user', 'admin' ),
        'add'       => array( 'user', 'admin' ),
        'edit'      => array( 'user', 'admin' ),
        'delete'    => array( 'user', 'admin' ),
        'find'      => array( 'user', 'admin' ),
        'info'      => array( 'user', 'admin' ),
    );

/**
 * Locations index
 * @return void
 */
    function index() {
        // setting title
        $this->set( 'title_for_layout', 'Locations' );
        // setting filter ( getting data for current user only )
        $this->paginate['conditions']   = array( 'Location.user_id' => $this->user_id );
        // paginating locations
        $this->data = $this->paginate( 'Location' );
    }

/**
 * Adding new location
 * @return void
 */
    function add() {
        // checking for ajax request
        if ( !empty( $this->params['isAjax'] ) ) {
            $this->layout = 'ajax';
        }
        // setting title
        $this->set( 'title_for_layout', 'New location' );

        // cleaning incoming data
        $this->data = Sanitize::clean( $this->data );

        // if data exists
        if ( !empty( $this->data ) ) {
            // initializing location model
            $this->Location->create();
            // setting validation
            $this->Location->setValidation( 'add' );
            // setting incoming data into model
            $this->Location->set( $this->data );

            // validating
            if ( $this->Location->validates() ) {
                // attaching required fields
                $this->data['Location']['user_id'] = $this->user_id;
                // saving new location
                $this->Location->save( $this->data, false );

                // if this method calling via ajax - setting some variables for view and render another view
                if ( !empty( $this->params['isAjax'] ) ) {
                    $this->set( 'new_location_id', $this->Location->id );
                    $this->set( 'new_location_title', $this->data['Location']['title'] );
                    $this->render('set_location');
                } else {
                    // default message and redirect
                    $this->Session->setFlash( 'New location has been successfully created.', 'default', array(), 'success' );
                    $this->redirect( '/locations' );
                }
            } else {
                if ( empty( $this->params['isAjax'] ) ) {
                    $this->Session->setFlash( 'Some errors occure while adding new location, see errors below', 'default', array(), 'error' );
                }
            }
        }
    }

/**
 * Edit location method
 *
 * @param  $id
 * @return void
 */
    function edit( $id = null ) {
        // setting title
        $this->set( 'title_for_layout', 'Edit location' );

        // cleaning incoming data
        $this->data = Sanitize::clean( $this->data );

        if ( !empty( $this->data ) ) {
            // setting location data
            $this->Location->set( $this->data );
            // setting validation scheme
            $this->Location->setValidation( 'add' );
            // validate ....
            if ( $this->Location->validates() ) {
                // saving
                $this->Location->save( $this->data, false );

                // setting success message and redirecting to locations index
                $this->Session->setFlash( 'Location has been successfully updated.', 'default', array(), 'success' );
                $this->redirect( '/locations' );
            } else {
                $this->Session->setFlash( 'Some errors occure while updatging location, see errors below', 'default', array(), 'error' );
            }
        } else {
            $data = Sanitize::paranoid( $id );
            $this->data = $this->Location->findById( $id );
            if ( empty( $this->data ) || $this->data['Location']['user_id'] != $this->user_id ) {
                $this->Session->setFlash( 'Location with such ID not found.', 'default', array(), 'error' );
                $this->redirect( '/locations' );
            }
        }
    }

/**
 * Delete location method
 *
 * @param  $id
 * @return void
 */
    function delete( $id ) {
        // turning off autorender
        $this->autoRender = false;
        // cleaning incoming data
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

/**
 * Getting location info
 * @param  $id
 * @return void
 */

    function info( $id = null ) {
        // setting layout
        $this->layout = 'ajax';
        // clean incoming parameters
        $id = Sanitize::paranoid( $id );

        if ( !empty( $id ) ) {
            // finding location
            $this->data = $this->Location->findById( $id );
            // checking location and owner
            if ( !empty( $this->data ) && $this->data['Location']['user_id'] == $this->user_id ) {
                // do nothing....
            } else {
                $this->set( 'error', 'Location with such ID not found' );
            }
        }
    }

/**
 * Finding location
 *  this method used in autocomplete field on event add/edit
 * @return void
 */
    function find() {
        // set output layout
        $this->layout = 'json';
        if ( isset( $this->params['url']['term'] ) ) {
            // clean incoming token
            $term = Sanitize::clean( $this->params['url']['term'] );
            // finding location by token
            $this->data = $this->Location->find( 'all', array( 'conditions'=>array( "upper(Location.title) like upper( '%{$term}%' ) " ) ) );
        }
    }
}

?>
