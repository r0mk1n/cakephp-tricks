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
<?php if ( isset( $User ) && !empty( $User ) && $User['role'] == 'admin' ): ?>
    <a href="/admin/dashboard" style="font-weight:bold;color: #ff0">backend</a>&nbsp;
<?php endif; ?>
<?php
    $i=0;
    foreach ( $links as $title => $url ):
        if ( strpos( $this->here, $url ) !== false  ):
?>
        <span class="active"><?= $title ?></span>
<?php   else: ?>
        <a href="<?= $url ?>"><?= $title ?></a>
<?php
        endif;
        echo '&nbsp;';
    endforeach;
?>

<?php if ( isset( $User ) && !empty( $User ) ): ?>
    <a href="/rss/<?= $User['ac_code'] ?>.rss"><img src="/img/rss.png" align="top"/></a>
<?php endif; ?>
