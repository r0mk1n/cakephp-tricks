<?php

/* SVN FILE: $Id: events_controller.php 7 2010-06-17 15:30:42Z r0mk1n.work $ */

/**
 * Events controller
 *  provide events management
 * @author		  r0mk1n
 * @version       $Revision: 7 $
 * @modifiedby    $LastChangedBy: r0mk1n.work $
 * @lastmodified  $Date: 2010-06-17 18:30:42 +0300 (Чт, 17 июн 2010) $
 */

class EventsController extends AppController {
    var $name               = 'Events';
    var $uses               = array( 'Event', 'Location', 'User'  );
    var $components         = array( 'RequestHandler' );
    var $helpers            = array( 'Time', 'Rss' );

    var $access = array(
        'index'         => array( 'user', 'admin' ),
        'add'           => array( 'user', 'admin' ),
        'edit'          => array( 'user', 'admin' ),
        'delete'        => array( 'user', 'admin' ),
        'setmode'       => array( 'user', 'admin' ),
        'setcomplete'   => array( 'user', 'admin' ),
        'info'          => array( 'user', 'admin' ),
    );

    var $paginate = array(
        'limit' => 10,
        'order' => 'Event.exp_date ASC'
    );

    function index() {
        $this->set( 'title_for_layout', 'Events' );
        $view_mode = 'list';
        if ( $this->Session->check( 'Events.mode' ) ) {
            $view_mode = $this->Session->read( 'Events.mode' );
        }
        $this->set( 'view_mode', $view_mode );

        if ( $view_mode == 'calendar' ) {
            $month = date( "m" );
            $year = date( "Y" );
            if ( isset( $this->params['named']['month'] ) && isset( $this->params['named']['year'] ) ) {
                $month = Sanitize::paranoid( $this->params['named']['month'] );
                $year = Sanitize::paranoid( $this->params['named']['year'] );
            }
            $start_date = date( "Y-m-d h:i", mktime( 0, 0, 0, $month, 1, $year ) );
            $end_date = date( "Y-m-d ", strtotime( "+1month -1day", strtotime( $start_date ) ) ) . '23:59';

            $this->data = $this->Event->find(
                'all',
                array(
                    'conditions' => array(
                        'Event.user_id'=>$this->user_id,
                        'Event.complete' => 'no',
                        "Event.exp_date between '{$start_date}' AND '{$end_date}'"
                    ),
                    'order'     => array(
                        'Event.exp_date ASC'
                    )
                )
            );
// sorting events by date
            $this->__sortEvents( $month, $year );
            $this->set( 'month', $month );
            $this->set( 'year', $year );
        } else {
            $this->paginate['conditions'][] = array( 'Event.user_id'=>$this->user_id, 'Event.complete' => 'no' );
            $this->data = $this->paginate( 'Event' );
        }
    }

    function __sortEvents( $month, $year ) {
        $result = array();
        if ( !empty( $this->data ) ) {
            $first_of_month = gmmktime( 0, 0, 0, $month, 1, $year );
            $days_in_month = gmdate( 't', $first_of_month );
            for ( $day = 1; $day <= $days_in_month; $day++ ) {
                $result[$day] = $this->__findRecordsForDay( $day, $month, $year );
            }
        }
        if ( !empty( $result ) ) {
            $this->data = $result;
        }
    }

    function __findRecordsForDay( $day, $month, $year ) {
        $result = array();
        foreach ( $this->data as $key => $row ) {
            $dt = strtotime( $row['Event']['exp_date'] );
            if ( $dt >= mktime( 0, 0, 0, $month, $day, $year ) && $dt < mktime( 23, 59, 99, $month, $day, $year ) ) {
                $result[] = $row;
            }
        }
        return $result;
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

        if ( !empty( $this->data ) ) {
            $this->data = Sanitize::clean( $this->data );
            $this->Event->set( $this->data );
            $this->Event->setValidation( 'add' );
            if ( $this->Event->validates() ) {
                $this->Event->save( $this->data );

                $this->Session->setFlash( 'Event has been successfully updated.', 'default', array(), 'success' );
                $this->redirect( '/events' );
            } else {
                $this->Session->setFlash( 'Some errors occure while updatging event, see errors below.', 'default', array(), 'error' );
            }
        } else {
            $data = Sanitize::paranoid( $id );
            $this->data = $this->Event->findById( $id );
            if ( empty( $this->data ) || $this->data['Event']['user_id'] != $this->user_id ) {
                $this->Session->setFlash( 'Event with such ID not found.', 'default', array(), 'error' );
                $this->redirect( '/events' );
            }
        }
    }

    function delete( $id = null ) {
        $this->autoRender = false;
        $id = Sanitize::paranoid( $id );

        if ( !empty( $id ) ) {
            $this->data = $this->Event->findById( $id );
// prevent deleting non-exiting location
            if ( empty( $this->data ) || $this->data['Event']['user_id'] != $this->user_id ) {
                $this->Session->setFlash( 'Event with such ID not found.', 'default', array(), 'error' );
            } else {
                $this->Event->delete( $id );
                $this->Session->setFlash( 'Event has been successfully deleted.', 'default', array(), 'success' );
            }
        }
        $this->redirect( '/events' );
    }

    function setcomplete( $id = null ) {
        $this->autoRender = false;
        $this->layout = 'ajax';
        $id = Sanitize::paranoid( $id );
        if ( !empty( $id ) ) {
            $event = $this->Event->findById( $id );
            if ( !empty( $event ) && $event['Event']['user_id'] == $this->user_id ) {
                $this->Event->save( array( 'id' => $id, 'complete' => 'yes' ) );
// get count of not complete events
                echo $this->Event->find( 'count', array( 'conditions'=>array( 'Event.user_id'=>$this->user_id, 'Event.complete'=>'no' ) ) );
            }
        }
    }

    function setmode( $view_mode = 'list' ) {
        $this->autoRender = false;
        $view_mode = Sanitize::paranoid( $view_mode );
        if ( $view_mode == 'list' || $view_mode == 'calendar' ) {
            $this->Session->write( 'Events.mode', $view_mode );
        }
        $this->redirect( '/events' );
    }

    function info( $id = null ) {
        $this->layout = 'ajax';
        $id = Sanitize::paranoid( $id );

        $this->data = $this->Event->findById( $id );
        if ( !empty( $this->data ) && $this->data['Event']['user_id'] == $this->user_id ) {
// do nothing here ;)
        } else {
            $this->set( 'error', 'Event with with such ID not founjd' );
        }
    }

    function rss( $key ) {
        if( $this->RequestHandler->isRss() ) {
            $key = Sanitize::paranoid( $key );
            if ( !empty( $key ) ) {
                $user = $this->User->findByAcCode( $key );
                if ( !empty( $user ) ) {
                    $events = $this->Event->find( 'all', array( 'conditions'=>array( 'Event.user_id'=>$user['User']['id'], 'Event.complete'=>'no' ) ) );
                    $this->set(compact('events'));
                }
            }
        }
    }
}

?>
