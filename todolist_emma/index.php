<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>My Schedule</title>
  <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;600&display=swap" rel="stylesheet" />
  <style>
    body {
      margin: 0;
      padding: 0;
      font-family: 'Fredoka', sans-serif;
      background-color: #f4f4f4;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .container {
      background: white;
      border: 4px solid #999;
      padding: 50px 60px;
      border-radius: 30px;
      text-align: center;
      box-shadow: 6px 6px 12px rgba(0, 0, 0, 0.1);
      animation: popIn 0.8s ease;
      position: relative;
      max-width: 500px;
      width: 100%;
    }

    h1 {
      font-size: 3rem;
      color: #333;
      margin-bottom: 10px;
    }

    p {
      font-size: 1.3rem;
      color: #666;
      margin-bottom: 25px;
    }

    .btn {
      display: inline-block;
      text-decoration: none;
      background: #e0e0e0;
      color: #222;
      padding: 14px 32px;
      border-radius: 50px;
      font-weight: bold;
      border: 2px solid #aaa;
      transition: all 0.3s ease;
      font-size: 1.1rem;
    }

    .btn:hover {
      background: #ccc;
      transform: scale(1.05);
    }

    @keyframes popIn {
      0% {
        transform: scale(0.7) rotate(-5deg);
        opacity: 0;
      }
      100% {
        transform: scale(1) rotate(0);
        opacity: 1;
      }
    }

    .emoji {
      font-size: 2rem;
      color: #999;
      position: absolute;
      opacity: 0.7;
    }

    .emoji.top-left {
      top: -20px;
      left: -20px;
    }

    .emoji.top-right {
      top: -20px;
      right: -20px;
    }

    .emoji.bottom-left {
      bottom: -20px;
      left: -20px;
    }

    .emoji.bottom-right {
      bottom: -20px;
      right: -20px;
    }
  </style>
</head>
<body>
  <div class="container">
    <span class="emoji top-left">✿</span>
    <span class="emoji top-right">⚙</span>
    <span class="emoji bottom-left">☁</span>
    <span class="emoji bottom-right">★</span>

    <h1>Welcome</h1>
    <p>Come on, Write down your homework</p>
    <a href="tugas.php" class="btn">Let's go</a>
  </div>
</body>
</html>