<?php if ( isset( $error ) ): ?>
<?= $error ?>
<?php else: ?>
<ul>
    <li><strong>Title</strong><?= $this->data['Location']['title'] ?></li>
    <li><strong>Address</strong><?= $this->data['Location']['address1'] ?></li>
    <li><strong>City</strong><?= $this->data['Location']['city'] ?></li>
    <li><strong>State</strong><?= $this->data['Location']['state'] ?></li>
    <li><strong>Zip</strong><?= $this->data['Location']['zip'] ?></li>
</ul>
<?php endif; ?>
