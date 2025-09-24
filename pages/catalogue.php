<?php
require __DIR__ . '/../ClassAutoLoad.php';

// Check if user is logged in
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$courses = [
    [
        'title' => 'Mastering Your Mind',
        'tagline' => 'Train focus, resilience, and mental models to thrive.',
        'level' => 'Beginner–Intermediate',
        'duration' => '4 weeks',
        'price' => 'Free',
        'slug' => 'mastering-your-mind',
    ],
    [
        'title' => 'Critical Thinking Essentials',
        'tagline' => 'Improve judgment, avoid biases, and make better decisions.',
        'level' => 'All levels',
        'duration' => '3 weeks',
        'price' => 'KSh 1,500',
        'slug' => 'critical-thinking-essentials',
    ],
    [
        'title' => 'Time Mastery',
        'tagline' => 'Prioritize, plan, and execute with clarity and confidence.',
        'level' => 'Beginner',
        'duration' => '2 weeks',
        'price' => 'KSh 750',
        'slug' => 'time-mastery',
    ],
    [
        'title' => 'Emotional Intelligence',
        'tagline' => 'Navigate emotions, build empathy, and lead effectively.',
        'level' => 'Intermediate',
        'duration' => '4 weeks',
        'price' => 'KSh 2,900',
        'slug' => 'emotional-intelligence',
    ],
    [
        'title' => 'Productivity Systems',
        'tagline' => 'Build sustainable workflows using proven frameworks.',
        'level' => 'All levels',
        'duration' => '3 weeks',
        'price' => 'KSh 1,200',
        'slug' => 'productivity-systems',
    ],
];

$ObjLayout->header($conf);
?>

<div style="max-width:1024px;margin:2rem auto;padding:0 1rem;font-family:Segoe UI, Tahoma, Arial, sans-serif;">
  <h2 style="margin:0 0 1rem 0;">Course Catalogue</h2>
  <p style="margin:0 0 1.25rem 0;color:#555;">Explore a selection of courses designed to strengthen mindset, thinking, and performance.</p>

  <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:16px;">
    <?php foreach ($courses as $c): ?>
      <div style="border:1px solid #ddd;border-radius:8px;padding:14px;background:#fff;">
        <h3 style="margin:0 0 6px 0;font-size:1.1rem;"><?php echo htmlspecialchars($c['title'], ENT_QUOTES, 'UTF-8'); ?></h3>
        <p style="margin:0 0 10px 0;color:#444;"><?php echo htmlspecialchars($c['tagline'], ENT_QUOTES, 'UTF-8'); ?></p>
        <p style="margin:0 0 6px 0;font-size:0.95rem;color:#666;">Level: <?php echo htmlspecialchars($c['level'], ENT_QUOTES, 'UTF-8'); ?></p>
        <p style="margin:0 0 6px 0;font-size:0.95rem;color:#666;">Duration: <?php echo htmlspecialchars($c['duration'], ENT_QUOTES, 'UTF-8'); ?></p>
        <p style="margin:0 0 12px 0;font-weight:600;color:#111;">Price: <?php echo htmlspecialchars($c['price'], ENT_QUOTES, 'UTF-8'); ?></p>
        <div>
          <a href="signup.php<?php echo '?course=' . urlencode($c['slug']); ?>" style="display:inline-block;background:#0b5ed7;color:#fff;padding:0.5rem 0.85rem;border-radius:6px;text-decoration:none;">Enroll</a>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<?php $ObjLayout->footer($conf); ?>
