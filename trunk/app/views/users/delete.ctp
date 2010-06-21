<?= $form->create( 'User', array('url' => '/users/delete', 'method' => 'POST')); ?>
<h4>Please confirm your choice by entering your password</h4>
<?= $form->input( 'User.curr_password', array( 'label'=>'Your password', 'type'=>'password' ) ); ?>
    <div class="footer-row">
        <button type="submit">Delete my account now</button>
    </div>
<?= $form->end(); ?>
