<?= $form->create( 'User', array('url' => '/users/login', 'method' => 'POST')); ?>
<?= $form->input( 'User.email', array( 'label'=>'Email' ) ); ?>
<?= $form->input( 'User.password', array( 'label'=>'Password', 'type'=>'password' ) ); ?>
<div class="footer-row">
    <div style="float:left;margin-top:10px">
        <a href="/users/restorepassword">Restore password</a>&nbsp;|&nbsp;<a href="/users/resend">Re-send activation email</a>
    </div>
    <button type="submit">Sign-in</button>
</div>
<?= $form->end(); ?>
