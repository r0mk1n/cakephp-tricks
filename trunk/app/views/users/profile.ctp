<?= $form->create( 'User', array('url' => '/users/profile', 'method' => 'POST')); ?>
<?= $form->input( 'User.email', array( 'label'=>'Email' ) ); ?>
<h4>Please fill password fields only if you want to change your password</h4>
<?= $form->input( 'User.currpassword', array( 'label'=>'Current password', 'type'=>'password' ) ); ?>
<?= $form->input( 'User.new_password', array( 'label'=>'New password', 'type'=>'password' ) ); ?>
<?= $form->input( 'User.re_new_password', array( 'label'=>'Repeat new password', 'type'=>'password' ) ); ?>
    <div class="footer-row">
        <a href="/users/delete" style="float:left;color: #f00" class="delete-account">Delete my account</a>
        <button type="submit">Update my account</button>
    </div>
<?= $form->end(); ?>
