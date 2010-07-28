<?php

class DashboardController extends AppController {
    var $name   = 'Dashboard';
    var $uses   = array( 'User', 'Event', 'Location' );
    var $layout = 'backend';

    var $access     = array(
        'admin_index'   => array( 'admin' )
    );

/**
 * Showing statistics
 * @return void
 */
    function admin_index() {
        $this->set( 'title_for_layout', 'Dashboard' );

        // users
        $this->data['User']['activated']  = $this->User->find( 'count', array( 'conditions'=>array( 'activated'=>'yes' ) ) );
        $this->data['User']['not_activated']  = $this->User->find( 'count', array( 'conditions'=>array( 'activated'=>'no' ) ) );

        // events
        $this->data['Event']['complete']  = $this->Event->find( 'count', array( 'conditions'=>array( 'complete'=>'yes' ) ) );
        $this->data['Event']['not_complete']  = $this->Event->find( 'count', array( 'conditions'=>array( 'complete'=>'no' ) ) );
        $this->data['Event']['expired']  = $this->Event->find( 'count', array( 'conditions'=>array( 'complete'=>'no', 'exp_date < "' . date( 'Y-m-d' )  . '"' ) ) );
        // locations
        $this->data['Location']['all']  = $this->Location->find( 'count' );
    }

}
?>
