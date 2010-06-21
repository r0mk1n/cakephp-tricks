<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<?php echo $this->Html->charset(); ?>
	<title>
		<?php __('CakePHP:'); ?>
		<?= $title_for_layout; ?>
	</title>
	<?php
		echo $this->Html->meta('icon');
		echo $this->Html->css( array( 'cake.generic', 'cake.additional', 'ui.achtung', 'jquery-ui-1.8.2.custom' ) );
		echo $scripts_for_layout;
	?>
    <?php echo $html->script(array('jquery-1.4.2.min', 'ui.achtung-min', 'jquery-ui-1.8.2.custom.min', 'jquery.forms', 'tricks')); ?>
    <?php if ( isset( $User ) && !empty( $User ) ): ?>
        <link rel="alternate" type="application/rss+xml" title="My events feed" href="/rss/<?= $User['ac_code'] ?>.rss" />
    <?php endif; ?>

</head>
<body>
	<div id="container">
		<div id="header">
            <div id="userpanel"><?= $this->element( 'userpanel' ) ?></div>
			<h1 style="floaat:left"><?php echo $this->Html->link(__('CakePHP: the rapid development php framework', true), 'http://cakephp.org'); ?></h1>

		</div>
		<div id="content">
            <h3><?php echo $title_for_layout; ?></h3>
			<?php echo $content_for_layout; ?>
		</div>
		<div id="footer">
			<?php echo $this->Html->link(
					$this->Html->image('cake.power.gif', array('alt'=> __('CakePHP: the rapid development php framework', true), 'border' => '0')),
					'http://www.cakephp.org/',
					array('target' => '_blank', 'escape' => false)
				);
			?>
		</div>
	</div>
    <?= $this->element( 'message' ); ?>
<?php if ( Configure::read( 'debug' ) ): ?>
	<?= $this->element( 'sql_dump' ); ?>
<?php endif; ?>        
</body>
</html>
