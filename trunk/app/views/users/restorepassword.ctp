<?= $form->create('User', array( 'url' => '/users/restorepassword' )); ?>
<?= $form->input( 'User.email', array( 'label'=>'Your Email<br />', 'value'=>'' ) ); ?>
<div class="footer-row">
    <button type="submit">Send restoring instructions</button>
</div>
<?= $form->end(); ?>
