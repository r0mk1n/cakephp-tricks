<?= $form->create( 'Event', array('url' => '/events/edit', 'method' => 'POST')); ?>
<?= $form->input( 'Event.id', array( 'type'=>'hidden' ) ); ?>
<?= $form->input( 'Event.title', array( 'label'=>'Title' ) ); ?>
<?= $form->input( 'Event.description', array( 'label'=>'Description', 'rows'=>3 ) ); ?>
<?= $form->input( 'Event.url', array( 'label'=>'URL' ) ); ?>
<?= $form->input( 'Event.exp_date', array( 'label'=>'Date', 'type'=>'datetime', 'separator'=>' ' ) ); ?>
<?= $form->input( 'Event.location_id', array( 'type'=>'hidden' ) ); ?>
<?= $form->input( 'Location.title', array( 'label'=>'Location', 'style'=>'float:left;width:70%', 'after'=>'<a href="javascript:void(0)" style="float:left;margin:0px 10px" id="addLocationDialog">add location</a>' ) ); ?>
<br class="clear" />
<div class="footer-row">
    <a href="/events">Cancel</a>
    <button type="submit">Update Event</button>
</div>
<?= $form->end(); ?>
<div id="location_popup"></div>
