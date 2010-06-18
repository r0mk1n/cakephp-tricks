<?php if ( $paginator->numbers() ): ?>
    <div class="paginator">
	    <div style='float:left;margin:10px;'><?php echo $paginator->prev('<< Previous ', array('url' => $paginator->params['pass']), null, array('class' => 'disabled')); ?></div>
		<div style='float:left;margin:10px;font-weight:bold;'><?php echo $paginator->numbers( array("separator"=>'&nbsp;', 'url' => $paginator->params['pass']) ); ?></div>
		<div style='float:left;margin:10px;'><?php echo $paginator->next(' Next >>', array('url' => $paginator->params['pass']), null, array('class' => 'disabled')); ?></div>
		<div style='float:left;margin:10px;white-space:nowrap;'>Page <?php echo $paginator->counter(); ?></div>
	</div>
<?php endif; ?>
