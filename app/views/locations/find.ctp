<?php
    $data = array();
    if ( !empty( $this->data ) ) {
        foreach ( $this->data as $key => $row ) {
            $data[] = array( 'id'=> $row['Location']['id'], 'value'=>trim( $row['Location']['title'] ) );
        }
    }
    echo $javascript->object( $data );
?>
