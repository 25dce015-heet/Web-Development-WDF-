<?php
require_once __DIR__.'/../config/db.php';$msg='';$err='';
if($_SERVER['REQUEST_METHOD']==='POST'){$name=trim($_POST['name']??'');$email=filter_var(trim($_POST['email']??''),FILTER_VALIDATE_EMAIL);$pass=$_POST['password']??'';
 if(!$name||!$email||strlen($pass)<8){$err='Enter a name, valid email and password of at least 8 characters.';}else{
  $check=$conn->prepare('SELECT id FROM users WHERE email=?');$check->bind_param('s',$email);$check->execute();$check->store_result();
  if($check->num_rows){$err='That email is already registered.';}else{$hash=password_hash($pass,PASSWORD_DEFAULT);$stmt=$conn->prepare("INSERT INTO users(name,email,password_hash,role) VALUES(?,?,?,'student')");$stmt->bind_param('sss',$name,$email,$hash);$stmt->execute();$msg='Registration complete. Password was hashed before storage.';}
 }
}
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Practical 9 — Register</title><link rel="stylesheet" href="../assets/style.css"></head><body><header><h1>Practical 9 — Secure Registration</h1><a href="../index.php">← All practicals</a></header><main><?php if($msg):?><p class="notice"><?=htmlspecialchars($msg)?></p><?php endif;?><?php if($err):?><p class="notice error"><?=htmlspecialchars($err)?></p><?php endif;?><section class="card"><form method="post"><label>Name</label><input name="name" required maxlength="100"><label>Email</label><input type="email" name="email" required maxlength="190"><label>Password</label><input type="password" name="password" required minlength="8"><button>Create account</button></form></section><p>Register a sample user first, then use Practical 10 to log in. Do not use a real password.</p></main></body></html>