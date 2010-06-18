<?php
    $logged_in_links = array(
        'events'         => '/events',
        'locations'     => '/locations',
        'profile'       => '/users/profile',
        'logout'        => '/users/logout'
    );

    $logged_out_links = array(
        'login'                     => '/users/login',
        'registration'              => '/users/registration'
    );

    $links = ( isset( $User ) && !empty( $User ) ) ? $logged_in_links : $logged_out_links;
?>


<?php
    $i=0;
    foreach ( $links as $title => $url ):
        if ( strpos( $this->here, $url ) !== false ):
?>
        <span class="active"><?= $title ?></span>
<?php   else: ?>
        <a href="<?= $url ?>"><?= $title ?></a>            
<?php
        endif;
        if ( $i++ < count( $links ) -  1  ) {
            echo '&nbsp;';
        }
    endforeach;
?>