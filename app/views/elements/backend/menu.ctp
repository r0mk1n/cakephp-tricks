<?php
    $links = array(
        'dashboard'           => '/admin/dashboard',
        'users management'    => '/admin/users',
        'view frontend'       => '/',
        'logout'              => '/users/logout'
    );

?>
<?php
    $i=0;
    foreach ( $links as $title => $url ):
        if ( ( strlen( $url ) > 1 && strpos( $this->here, $url ) !== false ) || ( strlen( $this->here ) == 1 && $this->here == $url ) ):
?>
        <span class="active"><?= $title ?></span>
<?php   else: ?>
        <a href="<?= $url ?>"><?= $title ?></a>
<?php
        endif;
        echo '&nbsp;';
    endforeach;
?>
