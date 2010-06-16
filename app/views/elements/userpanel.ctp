<?php if ( isset( $User ) && !empty( $User ) ): ?>
<a href="/users/profile">profile</a>&nbsp;|&nbsp;<a href="/users/logout">logout</a>
<?php else: ?>
<a href="/users/login">login</a>&nbsp;|&nbsp;<a href="/users/registration">registration</a>
<?php endif; ?>