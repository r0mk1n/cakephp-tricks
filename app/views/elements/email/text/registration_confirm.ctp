Your account has been created:
	Username: <?= $user_data['User']['email'] ?>


Visit this url to activate your account:
http://<?= $_SERVER['HTTP_HOST']; ?>/confirm/<?= $user_data['User']['code']?><?php if ( !empty( $delayed_url ) ) { echo "/{$delayed_url}"; } ?>

---
Thanks, <?= $_SERVER['HTTP_HOST']?> team.