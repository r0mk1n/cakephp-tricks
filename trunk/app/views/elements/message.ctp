<script type="text/javascript">
$(document).ready(function() {
<?php if ( $session->check( 'Message.error' ) ): ?>
<?php $message = $session->read( 'Message.error' ); ?>        
	__showAlertMesage( '<?= $message['message'] ?>', 'fail' );
<?php $session->delete( 'Message.error' ); ?>
<?php endif; ?>
<?php if ( $session->check( 'Message.success' ) ): ?>
<?php $message = $session->read( 'Message.success' ); ?>        
	__showAlertMesage( '<?= $message['message'] ?>', 'success' );
<?php $session->delete( 'Message.success' ); ?>
<?php endif; ?>
<?php if ( $session->check( 'Message.info' ) ): ?>
<?php $message = $session->read( 'Message.info' ); ?>
	__showAlertMesage( '<?= $message['message'] ?>', 'wait' );
<?php $session->delete( 'Message.info' ); ?>
<?php endif; ?>

});
</script>