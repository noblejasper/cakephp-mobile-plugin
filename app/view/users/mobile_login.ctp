<? if ( $session->check('Message.auth') ) : ?>
<div style="color:#f00"><? $session->flash('auth') ?></div>
<? endif;?>

<div style="text-align:center">
  <form id="UserLoginForm" method="post" action="/m/users/easylogin" utn>
  <input type="submit" value="かんたんログイン">
  </form>
</div>

<form id="UserLoginForm" method="post" action="/m/users/login" utn>
<input name="_method" value="POST" type="hidden">
メールアドレス
<input name="data[User][email]" maxlength="255" value="" type="text" istyle="3" format="*x" /><br />
<?= $form->error('UserEmail') ?>
パスワード
<input name="data[User][password]" value="" type="password" istyle="3" format="*x" /><br />
<?= $form->error('UserPassword') ?>

<input type="checkbox" name="data[User][useuid]" value="1" checked="ckecked" />次回からかんたんログインを使用する。<br />

<input type="submit" value="ログインする">
</form>