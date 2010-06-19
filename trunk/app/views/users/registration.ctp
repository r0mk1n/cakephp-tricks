<?= $form->create( 'User', array('url' => '/users/registration', 'method' => 'POST')); ?>
<?= $form->input( 'User.email', array( 'label'=>'Email' ) ); ?>
<?= $form->input( 'User.password', array( 'label'=>'Password', 'type'=>'password' ) ); ?>
<?= $form->input( 'User.repassword', array( 'label'=>'Repeat password', 'type'=>'password' ) ); ?>
    <div class="footer-row">
        <button type="submit">Create my account</button>
    </div>
<?= $form->end(); ?>
