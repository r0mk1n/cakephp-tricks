<?php

/* SVN FILE: $Id: users_controller.php 7 2010-06-17 15:30:42Z r0mk1n.work $ */

/**
 * Tasks controller
 *  provide tasks management
 * @author		  r0mk1n
 * @version       $Revision: 7 $
 * @modifiedby    $LastChangedBy: r0mk1n.work $
 * @lastmodified  $Date: 2010-06-17 18:30:42 +0300 (Чт, 17 июн 2010) $
 */

class TasksController extends AppController {
    var $name   = 'Tasks';
    var $uses   = array( 'Task', 'Location', 'Tag', 'TagsToTask' );

    var $access = array(
        'index'     => array( 'user', 'admin' ) 
    );

    function index() {
        $view_mode = 'list';
        if ( $this->Session->check( 'Tasks.mode' ) ) {
            $view_mode = $this->Session->read( 'Tasks.mode' );
        }
        $this->set( 'view_mode', $view_mode );
        $dataset = $this->Task->find( 'all', array( 'conditions'=>array( 'Task.user_id'=>$this->user_id ) )  );
    }

    function add() {
        
    }

    function setmode( $view_mode = 'list' ) {
        $this->autoRender = false;
        $view_mode = Sanitize::paranoid( $view_mode );
        if ( $view_mode == 'list' || $view_mode == 'calendar' ) {
            $this->Session->write( 'Tasks.mode', $view_mode );
        }
        $this->redirect( '/tasks' );
    }

}

?>