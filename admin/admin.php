<?php
session_start();
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: admin_dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Login | K SIXTEEN CAFE</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <style>
    /* VARIABLES */
    :root {
      --cafe-main: #FFD600;
      --cafe-dark: #111111;
      --cafe-bg: #1a1a1a;
      --cafe-card: #2d2d2d;
      --cafe-text: #ffffff;
      --cafe-text-light: #b0b0b0;
      --cafe-shadow: 0 4px 20px rgba(255, 214, 0, 0.15);
      --cafe-border: rgba(255, 214, 0, 0.2);
    }

    /* RESET & BASE STYLES */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: var(--cafe-bg);
      color: var(--cafe-text);
      line-height: 1.6;
    }

    /* NAVIGATION STYLES */
    .navbar {
      background: rgba(17, 17, 17, 0.95);
      backdrop-filter: blur(10px);
      padding: 1rem 0;
      position: fixed;
      top: 0;
      width: 100%;
      z-index: 1000;
      border-bottom: 2px solid var(--cafe-main);
      box-shadow: var(--cafe-shadow);
    }

    .nav-container {
      display: flex;
      justify-content: space-between;
      align-items: center;
      max-width: 1200px;
      margin: 0 auto;
      padding: 0 20px;
    }

    .logo-wrapper {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .logo-image {
      width: 45px;
      height: 45px;
      border-radius: 50%;
      overflow: hidden;
      border: 2px solid var(--cafe-main);
      background: var(--cafe-main);
    }

    .logo-image img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .logo-text {
      font-size: 1.5rem;
      font-weight: 800;
      color: var(--cafe-main);
      text-shadow: 0 2px 10px rgba(255, 214, 0, 0.3);
    }

    .nav-menu {
      display: flex;
      list-style: none;
      gap: 1.5rem;
      align-items: center;
    }

    .nav-link {
      color: var(--cafe-text);
      text-decoration: none;
      font-weight: 600;
      padding: 0.5rem 1rem;
      border-radius: 8px;
      transition: all 0.3s ease;
      font-size: 0.95rem;
    }

    .nav-link:hover {
      color: var(--cafe-main);
      background: rgba(255, 214, 0, 0.1);
    }

    .nav-link.active {
      color: var(--cafe-dark);
      background: var(--cafe-main);
      box-shadow: 0 2px 10px rgba(255, 214, 0, 0.4);
    }

    /* LOGIN STYLES */
    .admin-login-section {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      background: linear-gradient(rgba(0,0,0,0.8), rgba(0,0,0,0.8)), 
                  url('assets/images/logo.jpg') center/cover;
      padding: 2rem;
      margin-top: 0;
    }

    .login-container {
      background: var(--cafe-card);
      padding: 3rem;
      border-radius: 15px;
      border: 2px solid var(--cafe-main);
      box-shadow: var(--cafe-shadow);
      width: 100%;
      max-width: 400px;
      text-align: center;
    }

    .login-logo {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 12px;
      margin-bottom: 2rem;
    }

    .login-logo-image {
      width: 50px;
      height: 50px;
      border-radius: 50%;
      overflow: hidden;
      border: 2px solid var(--cafe-main);
    }

    .login-logo-image img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .login-logo-text {
      font-size: 1.5rem;
      font-weight: 800;
      color: var(--cafe-main);
    }

    .login-title {
      color: var(--cafe-main);
      margin-bottom: 2rem;
      font-size: 1.5rem;
    }

    .form-group {
      margin-bottom: 1.5rem;
      text-align: left;
    }

    .form-group label {
      display: block;
      margin-bottom: 0.5rem;
      color: var(--cafe-main);
      font-weight: 600;
    }

    .form-group input {
      width: 100%;
      padding: 0.75rem 1rem;
      background: rgba(255, 255, 255, 0.1);
      border: 1px solid var(--cafe-border);
      border-radius: 8px;
      color: var(--cafe-text);
      font-size: 1rem;
      transition: all 0.3s ease;
    }

    .form-group input:focus {
      outline: none;
      border-color: var(--cafe-main);
      box-shadow: 0 0 0 3px rgba(255, 214, 0, 0.1);
    }

    .login-btn {
      width: 100%;
      background: var(--cafe-main);
      color: var(--cafe-dark);
      border: none;
      padding: 1rem;
      border-radius: 50px;
      font-weight: 700;
      font-size: 1.1rem;
      cursor: pointer;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.5rem;
    }

    .login-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(255, 214, 0, 0.4);
    }

    .error-message {
      background: rgba(255, 71, 87, 0.1);
      color: #ff4757;
      padding: 0.75rem;
      border-radius: 8px;
      margin-bottom: 1rem;
      text-align: center;
      border: 1px solid rgba(255, 71, 87, 0.3);
    }

    .back-link {
      display: inline-block;
      margin-top: 1.5rem;
      color: var(--cafe-text-light);
      text-decoration: none;
      transition: color 0.3s ease;
    }

    .back-link:hover {
      color: var(--cafe-main);
    }

    /* RESPONSIVE */
    @media (max-width: 768px) {
      .nav-container {
        flex-direction: column;
        gap: 1rem;
      }
      
      .nav-menu {
        gap: 1rem;
      }
      
      .nav-link {
        padding: 0.5rem;
        font-size: 0.9rem;
      }
      
      .login-container {
        padding: 2rem;
        margin: 1rem;
      }
    }
  </style>
</head>
<body>
  <!-- Navigation -->
  <nav class="navbar">
    <div class="nav-container">
      <div class="logo-wrapper">
        <div class="logo-image">
          <img src="../assets/images/logo.jpg" alt="K SIXTEEN CAFE">
        </div>
        <div class="logo-text">K SIXTEEN CAFE</div>
      </div>
      
      <ul class="nav-menu">
        <li><a href="index.php" class="nav-link">Home</a></li>
        <li><a href="menu.php" class="nav-link">Menu</a></li>
        <li><a href="about.php" class="nav-link">About</a></li>
        <li><a href="contact.php" class="nav-link">Contact</a></li>
        <li><a href="Ulasan.php" class="nav-link">Ulasan</a></li>
      </ul>
    </div>
  </nav>

  <!-- Login Section -->
  <section class="admin-login-section">
    <div class="login-container">
      <div class="login-logo">
        <div class="login-logo-image">
          <img src="../assets/images/logo.jpg" alt="K SIXTEEN CAFE">
        </div>
        <div class="login-logo-text">K SIXTEEN CAFE</div>
      </div>

      <h2 class="login-title"><i class="fas fa-lock"></i> Admin Login</h2>
      
      <?php if (isset($_GET['error']) && $_GET['error'] == 1): ?>
        <div class="error-message">
          <i class="fas fa-exclamation-circle"></i> Username atau password salah!
        </div>
      <?php endif; ?>
      
      <form method="POST" action="admin_login.php">
        <div class="form-group">
          <label for="username"><i class="fas fa-user"></i> Username</label>
          <input type="text" id="username" name="username" required placeholder="Masukkan username admin">
        </div>
        
        <div class="form-group">
          <label for="password"><i class="fas fa-key"></i> Password</label>
          <input type="password" id="password" name="password" required placeholder="Masukkan password admin">
        </div>
        
        <button type="submit" class="login-btn">
          <i class="fas fa-sign-in-alt"></i> Login ke Dashboard
        </button>
      </form>

      <a href="index.php" class="back-link">
        <i class="fas fa-arrow-left"></i> Kembali ke Beranda
      </a>
    </div>
  </section>
</body>
</html>