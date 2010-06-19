<?= $form->create('User', array( 'url' => '/users/resend' )); ?>
<?= $form->input( 'User.email', array( 'label'=>'Your Email<br />', 'value'=>'' ) ); ?>
<div class="footer-row">
    <button type="submit">Re-send my activation email</button>
</div>
<?= $form->end(); ?>
