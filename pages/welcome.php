<?php
require __DIR__ . '/../ClassAutoLoad.php';

$name = isset($_GET['name']) ? trim($_GET['name']) : '';
$safeName = $name !== '' ? htmlspecialchars($name, ENT_QUOTES, 'UTF-8') : 'There';

$ObjLayout->header($conf);
?>
<div style="max-width:720px;margin:2rem auto;font-family:Segoe UI, Tahoma, Arial, sans-serif;line-height:1.55;">
  <h2 style="margin-bottom:0.25rem;">Welcome to the Mastering Your Mind course</h2>
  <p style="margin-top:0;">Hi <?php echo $safeName; ?>, your account has been created successfully.</p>

  <p>You can now sign in to access your course materials and get started.</p>

  <div style="margin-top:1.25rem;">
    <a href="login.php" style="display:inline-block;background:#0b5ed7;color:#fff;padding:0.6rem 1rem;border-radius:6px;text-decoration:none;">Sign in now</a>
  </div>

  <p style="margin-top:1.25rem;color:#555;">Didn’t receive your confirmation email? Check your spam/junk folder or try registering again with the correct email address.</p>
</div>
<?php
$ObjLayout->footer($conf);
