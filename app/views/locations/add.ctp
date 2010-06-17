<h3>New location</h3>
<?= $form->create( 'Location', array('url' => '/locations/add', 'method' => 'POST')); ?>
<?= $form->input( 'Location.title', array( 'label'=>'Title' ) ); ?>
<?= $form->input( 'Location.address1', array( 'label'=>'Address', 'rows'=>3 ) ); ?>
<?= $form->input( 'Location.city', array( 'label'=>'City' ) ); ?>
<?= $form->input( 'Location.state', array( 'label'=>'State' ) ); ?>
<?= $form->input( 'Location.zip', array( 'label'=>'Zip' ) ); ?>
<div class="footer-row">
    <a href="/locations">cancel</a>
    <button type="submit">Add location</button>
</div>    
<?= $form->end() ?>