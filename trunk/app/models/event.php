<?php

class Event extends AppModel {
    var $name = 'Event';

    var $belongsTo = array(
        'Location'  => array(
            'className'     => 'Location',
            'foreignKey'    => 'location_id'
        )
    );

    var $validationSet = array(
        'add'   => array(
            'title' => array(
                'minLength' => array (
                    'rule'      => array( 'minLength', '3' ),
                    'required'  => true,
                    'message'   => 'This field cannot be empty.'
                ),
            )
        )
    );
}

?>
