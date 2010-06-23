<div class="cpanel">
    <a href="javascript:void(0)" class="filter">Filter</a>
</div>
<div id="filter" style="display:none">
    <?= $form->create( 'User', array('url' => '/admin/users/set_filter', 'method' => 'POST', 'style'=>'width:100%')); ?>
        <?= $form->input( "User.email", array( 'label'=>'Email', 'value'=>$filter['email'] ) )?>
        <?= $form->input( "User.role", array( 'label'=>'Role', 'options'=>array( 'all'=>'All', 'user'=>'User', 'admin'=>'Admin' ), 'default'=>$filter['role'] ) )?>
        <?= $form->input( "User.activated", array( 'label'=>'Activated', 'options'=>array( 'all'=>'All', 'yes'=>'Yes', 'no'=>'No' ), 'default'=>$filter['activated'] ) )?>
        <?= $form->input( "User.enabled", array( 'label'=>'Enabled', 'options'=>array( 'all'=>'All', 'yes'=>'Yes', 'no'=>'No' ), 'default'=>$filter['enabled'] ) )?>
        <div style="float:right">
            <a href="/admin/users/reset_filter">reset filter</a>
            <button>Set filter</button>
        </div>

        <br style="clear:both"/>
    <?= $form->end(); ?>
</div>
<?= $this->element( 'backend/filtration_detector' ) ?>
<?= $form->create( 'User', array('url' => '/admin/users/update', 'method' => 'POST', 'style'=>'width:100%')); ?>
<?php if ( !empty( $this->data ) ): ?>
    <table width="100%">
        <tr>
            <th width="50%"><?= $paginator->sort( 'Email', 'User.email' ) ?></th>
            <th><?= $paginator->sort( 'Role', 'User.role' ) ?></th>
            <th><?= $paginator->sort( 'Activated', 'User.activated' ) ?></th>
            <th><?= $paginator->sort( 'Enabled', 'User.enabled' ) ?></th>
            <th><?= $paginator->sort( 'Created', 'User.created' ) ?></th>
            <th>Delete</th>
        </tr>

<?php foreach( $this->data as $key => $row ): ?>
    <tr class="row-<?= $key % 2 == 0 ? 'a' : 'b' ?>">
        <td><?= $row['User']['email']?></td>
        <td><?= $form->input( "User.{$row['User']['id']}.role", array( 'label'=>'', 'options'=>array( 'user'=>'User', 'admin'=>'Admin' ), 'default'=>$row['User']['role'] ) )?></td>        
        <td><?= $form->input( "User.{$row['User']['id']}.activated", array( 'label'=>'', 'options'=>array( 'yes'=>'Yes', 'no'=>'No' ), 'default'=>$row['User']['activated'] ) )?></td>
        <td><?= $form->input( "User.{$row['User']['id']}.enabled", array( 'label'=>'', 'options'=>array( 'yes'=>'Yes', 'no'=>'No' ), 'default'=>$row['User']['enabled'] ) )?></td>
        <td><?= $time->niceShort( $row['User']['created'] )?></td>
        <td><?= $form->input( "User.{$row['User']['id']}.delete", array( 'label'=>'', 'type'=>'checkbox', 'value'=>'yes' ) )?></td>
    </tr>
<?php endforeach; ?>
    </table>
<div style="float:right">
    <button type="submit">Update</button>
</div>
<div style="float:left">
    <?= $this->element( 'pagination' ) ?>
</div>
<?= $form->end() ?>
<?php else: ?>
         <div class="cmessage">Users not found.</div>
<?php endif; ?>
