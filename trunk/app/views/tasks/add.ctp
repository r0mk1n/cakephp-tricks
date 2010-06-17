<h3>New task</h3>
<?= $form->create( 'Task', array('url' => '/tasks/add', 'method' => 'POST')); ?>
<?= $form->input( 'Task.title', array( 'label'=>'Title' ) ); ?>
<?= $form->input( 'Task.description', array( 'label'=>'Description', 'rows'=>3 ) ); ?>
<?= $form->input( 'Task.url', array( 'label'=>'URL' ) ); ?>
<?= $form->input( 'Task.exp_date', array( 'label'=>'Date', 'type'=>'datetime', 'separator'=>' ' ) ); ?>
<?= $form->input( 'Task.location', array( 'label'=>'Location', 'style'=>'float:left;width:70%', 'after'=>'<a href="javascript:void(0)" style="float:left;margin:0px 10px ">add location</a>' ) ); ?>
<br class="clear" />
<div class="footer-row">
    <a href="/tasks">Cancel</a>
    <button type="submit">Add task</button>
</div>
<?= $form->end(); ?>
<script type="text/javascript">
    $( '#dates_dialog' ).dialog( 'option', 'position', 'center' );
    $( '#dates_dialog' ).dialog( 'option', 'title', 'Add new date (dates range)');
    $( '#dates_dialog' ).dialog( 'open' );


	$( '.closeDateDialog' ).click( function() {
		$( '#dates_dialog' ).dialog( 'close' );
	});

    });
</script>