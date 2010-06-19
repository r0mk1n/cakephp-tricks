<?= $form->create( 'Location', array('url' => '/locations/add', 'method' => 'POST', 'id'=>'addLocationForm')); ?>
<?= $form->input( 'Location.title', array( 'label'=>'Title' ) ); ?>
<?= $form->input( 'Location.address1', array( 'label'=>'Address', 'rows'=>3 ) ); ?>
<?= $form->input( 'Location.city', array( 'label'=>'City' ) ); ?>
<?= $form->input( 'Location.state', array( 'label'=>'State', 'options'=>$address->us_states ) ); ?>
<?= $form->input( 'Location.zip', array( 'label'=>'Zip' ) ); ?>
<div class="footer-row">
    <a href="/locations" id="cancelAddLocation">cancel</a>
    <button type="submit" id="processAddLocation">Add location</button>
</div>
<?= $form->end() ?>
