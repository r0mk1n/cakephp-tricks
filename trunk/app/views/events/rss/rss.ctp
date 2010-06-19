<?php
    $this->set('documentData', array( 'xmlns:dc' => 'http://purl.org/dc/elements/1.1/' ) );
    $this->set('channelData', array(
        'title' => __( "Uncomplete events", true ),
        'link' => '',
        'description' => __("Uncomplete todo events.", true),
        'language' => 'en-us')
    );

    foreach ( $events as $event ) {
        $eventTime = strtotime( $event['Event']['exp_date'] );

        $eventLink = !empty( $event['Event']['url'] ) ? $event['Event']['url'] : 'http://' . $_SERVER['HTTP_HOST'] ;

        $eventDescription = 'Where: ' . $event['Location']['title'] . ' ' .
                            $event['Location']['address1'] . ' ' .
                            $event['Location']['city'] . '' .
                            $event['Location']['state'] . '' .
                            $event['Location']['zip'] . '<br />' .
                            $event['Event']['description'] . '<br />';

        $eventDescription = preg_replace('=\(.*?\)=is', '', $eventDescription );
        $eventDescription = Sanitize::stripAll( $eventDescription );

        $eventDescription = 'When: ' . $time->nice( $event['Event']['exp_date'] ) . '<br />' . $eventDescription;
        echo $rss->item(array(), array(
            'title' => $event['Event']['title'],
            'link' => $eventLink,
            'guid' => array('url' => $eventLink, 'isPermaLink' => 'true'),
            'description' =>  $eventDescription,
            'dc:creator' => '',
            'pubDate' => $event['Event']['created']));
    }

?>
