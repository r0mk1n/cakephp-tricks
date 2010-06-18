<?php

/* SVN FILE: $Id: events_controller.php 7 2010-06-17 15:30:42Z r0mk1n.work $ */

/**
 * Tasks controller
 *  provide events management
 * @author		  r0mk1n
 * @version       $Revision: 7 $
 * @modifiedby    $LastChangedBy: r0mk1n.work $
 * @lastmodified  $Date: 2010-06-17 18:30:42 +0300 (Чт, 17 июн 2010) $
 */

class EventsController extends AppController {
    var $name   = 'Events';
    var $uses   = array( 'Event', 'Location', 'Tag', 'TagsToEvent' );
    var $helpers = array( 'Time' );

    var $access = array(
        'index'     => array( 'user', 'admin' ),
        'add'       => array( 'user', 'admin' ),
        'edit'      => array( 'user', 'admin' ),
        'delete'    => array( 'user', 'admin' ),
        'setmode'   => array( 'user', 'admin' ),
        'complete'  => array( 'user', 'admin' ),
    );

    var $paginate = array(
        'limit' => 10,
        'order' => 'Event.exp_date DESC'
    );

    function index() {
        $this->set( 'title_for_layout', 'Events' );
        $view_mode = 'list';
        if ( $this->Session->check( 'Events.mode' ) ) {
            $view_mode = $this->Session->read( 'Events.mode' );
        }
        $this->set( 'view_mode', $view_mode );
        $this->paginate['conditions'] = array( 'Event.user_id'=>$this->user_id  );
        $this->data = $this->paginate( 'Event' );
    }

    function add() {
        $this->set( 'title_for_layout', 'New event' );
        if ( !empty( $this->data ) ) {
            $this->Event->create();
            $this->Event->set( $this->data );
            $this->Event->setValidation( 'add' );
            if ( $this->Event->validates() ) {
                $this->data['Event']['user_id'] = $this->user_id;
                $this->Event->save( $this->data );

                $this->Session->setFlash( 'New event has been successfully added.', 'default', array(), 'success' );
                $this->redirect( '/events' );
            } else {
                $this->Session->setFlash( 'Some errors occur while adding new event', 'default', array(), 'error' );
            }
        }
    }

    function edit( $id = null ) {
        $this->set( 'title_for_layout', 'Edit event' );
        

    }

    function delete( $id = null ) {

    }

    function setmode( $view_mode = 'list' ) {
        $this->autoRender = false;
        $view_mode = Sanitize::paranoid( $view_mode );
        if ( $view_mode == 'list' || $view_mode == 'calendar' ) {
            $this->Session->write( 'Events.mode', $view_mode );
        }
        $this->redirect( '/events' );
    }

}

?>
