<form id="UserAddForm" method="post" action="/m/users/register">
<div>ユーザー追加</div>
<input type="hidden" name="_method" value="POST" />

メールアドレス
<input name="data[User][email]" type="text" maxlength="255" value="" /><br />

ニックネーム
<input name="data[User][nick]" type="text" maxlength="255" value="" /><br />

パスワード
<input type="password" name="data[User][password]" value="" /><br />

<input type="submit" value="登録する" />

</form>
