<h3>Login</h3>
<?= $form->create( 'User', array('url' => '/users/login', 'method' => 'POST', 'class'=>'data-form')); ?>
<?= $form->input( 'User.email', array( 'label'=>'Email' ) ); ?>
<?= $form->input( 'User.password', array( 'label'=>'Password', 'type'=>'password' ) ); ?>
    <div class="footer-row">
        <div style="float:left">
            <a href="/users/restorepassword">Restore password</a>&nbsp;|&nbsp;<a href="/users/resend">Re-send activation email</a>
        </div>
        <button type="submit" class="but large blue awesome right">Sign-in</button>
    </div>
<?= $form->end(); ?>