<?php

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