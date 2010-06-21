Your account has been created:
	Username: <?= $user_data['User']['email'] ?>

Visit this url to activate your account:
http://<?= $_SERVER['HTTP_HOST']; ?>/users/confirm/<?= $user_data['User']['ac_code']?>

---
Thanks, <?= $_SERVER['HTTP_HOST']?> team.
