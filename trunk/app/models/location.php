<?php
class Location extends AppModel {
    var $name = 'Location';

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
