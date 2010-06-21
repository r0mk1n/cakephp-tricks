<?= $form->create( 'User', array('url' => '/users/reset', 'method' => 'POST')); ?>
<?= $form->input( 'User.code', array( 'type'=>'hidden' ) ) ?>
<?= $form->input( 'User.new_password', array( 'label'=>'New password', 'type'=>'password' ) ); ?>
<?= $form->input( 'User.re_new_password', array( 'label'=>'Repeat new password', 'type'=>'password' ) ); ?>
    <div class="footer-row">
        <button type="submit">Set new password</button>
    </div>
<?= $form->end(); ?>
