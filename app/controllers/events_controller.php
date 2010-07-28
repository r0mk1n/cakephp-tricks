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
    var $helpers            = array( 'Time', 'Rss' );

/**
 * access array
 * @see /app/app_controller.php
 */
    var $access = array(
        'index'         => array( 'user', 'admin' ),
        'add'           => array( 'user', 'admin' ),
        'edit'          => array( 'user', 'admin' ),
        'delete'        => array( 'user', 'admin' ),
        'setmode'       => array( 'user', 'admin' ),
        'setcomplete'   => array( 'user', 'admin' ),
        'info'          => array( 'user', 'admin' ),
    );

/**
 * Paginate defaults
 */
    var $paginate = array(
        'limit' => 10,
        'order' => 'Event.exp_date ASC'
    );

/**
 * Index function
 * Showing events list/calendar
 *
 * @see setmode method
 * @return void
 */
    function index() {
        // setting title
        $this->set( 'title_for_layout', 'Events' );
        //setting default view mode (list/calendar)
        $view_mode = 'list';
        //getting stored event mode (if mode exists)
        if ( $this->Session->check( 'Events.mode' ) ) {
            $view_mode = $this->Session->read( 'Events.mode' );
        }
        // setting mode to view
        $this->set( 'view_mode', $view_mode );

        // for calendar mode we need to do additional operations
        if ( $view_mode == 'calendar' ) {
            // setting default month and year for queries
            $month = date( "m" );
            $year = date( "Y" );
            // if user change default values - getting these values
            if ( isset( $this->params['named']['month'] ) && isset( $this->params['named']['year'] ) ) {
                $month = Sanitize::paranoid( $this->params['named']['month'] );
                $year = Sanitize::paranoid( $this->params['named']['year'] );
            }
            // creating dates range
            $start_date = date( "Y-m-d h:i", mktime( 0, 0, 0, $month, 1, $year ) );
            $end_date = date( "Y-m-d ", strtotime( "+1month -1day", strtotime( $start_date ) ) ) . '23:59';

            // making query ...
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
            // just paginate out Event model
            $this->paginate['conditions'][] = array( 'Event.user_id'=>$this->user_id, 'Event.complete' => 'no' );
            $this->data = $this->paginate( 'Event' );
        }
    }

/**
 * This method sorting our events by date and create separate array records for each daty
 *
 * @param  $month
 * @param  $year
 * @return void
 */
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

/**
 * This method finding events for specified day
 *
 * @param  $day
 * @param  $month
 * @param  $year
 * @return array
 */
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

/**
 * Add new event
 * @return void
 */
    function add() {
        // setting title
        $this->set( 'title_for_layout', 'New event' );
        // if filled $data variable - we have filled form with data

        if ( !empty( $this->data ) ) {
            // cleaning data for safe writing into database
            $this->data = Sanitize::clean( $this->data );
            // initializing Event model
            $this->Event->create();
            // setting data
            $this->Event->set( $this->data );
            // setting validation rules
            $this->Event->setValidation( 'add' );
            // validating incoming data
            if ( $this->Event->validates() ) {
                // filing unfilled fields
                $this->data['Event']['user_id'] = $this->user_id;
                // saving data w/o validation ( we validate our data earlier )
                $this->Event->save( $this->data, false );
                // setting operation result
                $this->Session->setFlash( 'New event has been successfully added.', 'default', array(), 'success' );
                // redirecting into events home
                $this->redirect( '/events' );
            } else {
                // setting error result
                $this->Session->setFlash( 'Some errors occur while adding new event.', 'default', array(), 'error' );
            }
        }
    }

/**
 * Edit event method
 *
 * @param  $id          : id of our event
 * @return void
 */
    function edit( $id = null ) {
        // setting title
        $this->set( 'title_for_layout', 'Edit event' );

        // if filled $data variable - processing incoming data
        if ( !empty( $this->data ) ) {
            // cleaning data
            $this->data = Sanitize::clean( $this->data );
            // setting data into model
            $this->Event->set( $this->data );
            // setting validation method
            $this->Event->setValidation( 'add' );
            // checking validation
            if ( $this->Event->validates() ) {
                // saving ....
                $this->Event->save( $this->data );
                // setting success message and redirecting
                $this->Session->setFlash( 'Event has been successfully updated.', 'default', array(), 'success' );
                $this->redirect( '/events' );
            } else {
                $this->Session->setFlash( 'Some errors occure while updatging event, see errors below.', 'default', array(), 'error' );
            }
        } else {
            // $data variable empty - this mean user not filled form, so we getting data from database and seting this data to view
            $this->data = $this->Event->findById( Sanitize::paranoid( $id ) );
            // checking data what we read from database .... is user try to edit him event?
            if ( empty( $this->data ) || $this->data['Event']['user_id'] != $this->user_id ) {
                $this->Session->setFlash( 'Event with such ID not found.', 'default', array(), 'error' );
                $this->redirect( '/events' );
            }
        }
    }

/**
 * Delete event from database
 *
 * @param  $id
 * @return void
 */
    function delete( $id = null ) {
        // turning off autorender
        $this->autoRender = false;
        // clean incoming parameter
        $id = Sanitize::paranoid( $id );

        // if parameter not empty - processing ....
        if ( !empty( $id ) ) {
            // finding event
            $this->data = $this->Event->findById( $id );
            // prevent deleting event owned by other user
            if ( empty( $this->data ) || $this->data['Event']['user_id'] != $this->user_id ) {
                $this->Session->setFlash( 'Event with such ID not found.', 'default', array(), 'error' );
            } else {
                // deleting
                $this->Event->delete( $id );
                $this->Session->setFlash( 'Event has been successfully deleted.', 'default', array(), 'success' );
            }
        }
        // redirecting to events page
        $this->redirect( '/events' );
    }

/**
 * Setting event complete
 * @param  $id
 * @return void
 */
    function setcomplete( $id = null ) {
        // turning off autorender
        $this->autoRender = false;
        // setting AJAX layout
        $this->layout = 'ajax';
        // cleaning ID
        $id = Sanitize::paranoid( $id );
        if ( !empty( $id ) ) {
            // getting event
            $event = $this->Event->findById( $id );
            // check event existing and event's owner
            if ( !empty( $event ) && $event['Event']['user_id'] == $this->user_id ) {
                // update event complete flag
                $this->Event->save( array( 'id' => $id, 'complete' => 'yes' ) );
                // get count of not complete events and return this value
                echo $this->Event->find( 'count', array( 'conditions'=>array( 'Event.user_id'=>$this->user_id, 'Event.complete'=>'no' ) ) );
            }
        }
    }

/**
 * Setting events view mode
 * @param string $view_mode (list|calendar)
 * @return void
 */
    function setmode( $view_mode = 'list' ) {
        // turning off autorender
        $this->autoRender = false;
        // cleaning incoming value
        $view_mode = Sanitize::paranoid( $view_mode );
        // checking ....
        if ( $view_mode == 'list' || $view_mode == 'calendar' ) {
            // saving mode into session
            $this->Session->write( 'Events.mode', $view_mode );
        }
        // redirecting into main event page
        $this->redirect( '/events' );
    }

/**
 * Showing event info
 *
 * @param  $id
 * @return void
 */
    function info( $id = null ) {
        // setting ajax layout
        $this->layout = 'ajax';
        // cleaning incoming ID
        $id = Sanitize::paranoid( $id );
        // reading event by ID
        $this->data = $this->Event->findById( $id );
        if ( !empty( $this->data ) && $this->data['Event']['user_id'] == $this->user_id ) {
            // do nothing here ;)
        } else {
            // setting error message if user trying to access event that owned by another user or trying to access wrong event
            $this->set( 'error', 'Event with with such ID not founjd' );
        }
    }

/**
 * Creating rss stream with uncomplete events
 * @param  $key
 * @return void
 */
    function rss( $key ) {
        // checking request handler ( user trying to get file w/rss extension )
        if( $this->RequestHandler->isRss() ) {
            // cleaning key
            $key = Sanitize::paranoid( $key );
            // checking key
            if ( !empty( $key ) ) {
                // finding user with this key
                $user = $this->User->findByAcCode( $key );
                // if user found - reading events list6
                if ( !empty( $user ) ) {
                    $events = $this->Event->find( 'all', array( 'conditions'=>array( 'Event.user_id'=>$user['User']['id'], 'Event.complete'=>'no' ) ) );
                    $this->set(compact('events'));
                }
            }
        }
    }
}

?>
