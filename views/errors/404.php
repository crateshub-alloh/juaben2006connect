<!DOCTYPE html>
<html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>404 – Page Not Found</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Poppins:wght@700;800&display=swap" rel="stylesheet">
<style>
  *{box-sizing:border-box;margin:0;padding:0}body{font-family:'Inter',sans-serif;background:#f3f4f6;display:grid;place-items:center;min-height:100vh;text-align:center;padding:24px}
  h1{font-family:'Poppins',sans-serif;font-size:6rem;font-weight:800;color:#1a3c6e;line-height:1}
  h2{font-size:1.5rem;color:#374151;margin:12px 0}p{color:#6b7280;margin-bottom:28px}
  .btn{display:inline-flex;align-items:center;gap:8px;padding:12px 28px;border-radius:9999px;background:#1a3c6e;color:#fff;font-weight:600;text-decoration:none}
  .btn:hover{background:#1f4d8c}
</style>
</head><body>
<div>
  <h1>404</h1>
  <h2>Page Not Found</h2>
  <p>The page you're looking for doesn't exist or has been moved.</p>
  <a href="<?= defined('APP_URL') ? APP_URL : '/' ?>/" class="btn">← Go Home</a>
</div>
</body></html>
