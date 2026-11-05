<?php $register=$page==='register'; heading('Club membership',$register?'Join the club':'Welcome back','Use a fictional name, an @example.test email and a made-up password.'); ?>
<form method="post" class="panel formstack auth" <?=$register?'id="register"':''?>><?=act($page)?>
<?php $fields=$register?['name'=>'Demo name','email'=>'Demo email','student_code'=>'Student code','password'=>'Demo password']:['email'=>'Demo email','password'=>'Demo password'];
foreach($fields as $key=>$label): $rule=rules()[$key]; ?><label><?=e($label)?><input name="<?=$key?>" type="<?=$key==='password'?'password':($key==='email'?'email':'text')?>" autocomplete="<?=$key==='password'?($register?'new-password':'current-password'):'off'?>"
value="<?=$key==='password'?'':e(field($_POST,$key))?>" required maxlength="<?= ['name'=>60,'email'=>120,'student_code'=>7,'password'=>64][$key]?>" aria-describedby="hint-<?=$key?>"><?php if($register): ?><small id="hint-<?=$key?>"><?=e($rule[1])?></small><?php else: ?><small id="hint-<?=$key?>"></small><?php endif ?></label><?php endforeach ?>
<button><?=$register?'Create demo account':'Sign in'?> →</button><a href="?p=<?=$register?'login':'register'?>"><?=$register?'Already registered? Sign in':'New here? Create a demo account'?></a></form>

