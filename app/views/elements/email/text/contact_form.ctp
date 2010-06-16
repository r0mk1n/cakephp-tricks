Your message successfully sent:
---------------------------------------------------------------
<?php echo "\n" ?>
ID: #<?= $message_id . "\n" ?>
From: <?= $this->data['Helpdesk']['owner_name'] . ' <' . $this->data['Helpdesk']['owner_email'] . '>' . "\n" ?>
<?php
	$options = array( 'I want to leave feedback', 'I want report bug', 'I want request new feature', 'I want report abuse', 'I will specify subject in message' );
	$subj = isset( $options[$this->data['Helpdesk']['subject']] ) ? $options[$this->data['Helpdesk']['subject']] : 'Other';
?>
Subject: <?= $subj . "\n" ?>
Message: <?= str_replace( '\n', "\n", $this->data['Helpdesk']['message'] ) . "\n";  ?>
---------------------------------------------------------------
Thanks for your participation. We contact you as soon as possible.
<?= Inflector::camelize( $_SERVER['HTTP_HOST'] ) ?> team