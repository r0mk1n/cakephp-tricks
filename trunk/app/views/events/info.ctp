<?php if ( isset( $error ) ): ?>
<?= $error ?>
<?php else: ?>
<ul>
    <li><strong>Title</strong><?= $this->data['Event']['title'] ?></li>
    <li><strong>URL</strong><a href="<?= $this->data['Event']['url'] ?>" target="_blank""><?= $this->data['Event']['url'] ?></a></li>
    <li><strong>Exp</strong><?= $time->niceShort( $this->data['Event']['exp_date'] ) ?></li>
    <li><strong>Description</strong><?= $this->data['Event']['description'] ?></li>
    <li><strong>Location</strong><?= $this->data['Location']['address1'] ?></li>
    <li><?= $this->data['Location']['city'] ?></li>
    <li><?= $this->data['Location']['state'] ?></li>
    <li><?= $this->data['Location']['zip'] ?></li>
</ul>
<?php endif; ?>
