 
  <title>404 - Page Not Found</title>
  <style>
    .section-404 * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      font-family: Arial, sans-serif;
    }

    .section-404 {
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      background: linear-gradient(135deg, #0f172a, #1e293b, #334155);
      color: white;
      overflow: hidden;
    }

    .section-404 .container {
      text-align: center;
      padding: 40px;
      border-radius: 20px;
      background: rgba(255, 255, 255, 0.08);
      backdrop-filter: blur(12px);
      box-shadow: 0 8px 30px rgba(0, 0, 0, 0.35);
      animation: float 3s ease-in-out infinite;
      max-width: 500px;
      width: 90%;
    }

    .section-404 h1 {
      font-size: 6rem;
      margin-bottom: 10px;
      color: #38bdf8;
      text-shadow: 0 0 20px rgba(56, 189, 248, 0.6);
    }

    .section-404 h2 {
      font-size: 1.8rem;
      margin-bottom: 15px;
    }

    .section-404 p {
      font-size: 1rem;
      color: #cbd5e1;
      margin-bottom: 25px;
      line-height: 1.6;
    }

    .section-404 a {
      display: inline-block;
      text-decoration: none;
      background: #38bdf8;
      color: #0f172a;
      padding: 12px 24px;
      border-radius: 999px;
      font-weight: bold;
      transition: 0.3s ease;
    }

    .section-404 a:hover {
      background: #0ea5e9;
      transform: scale(1.05);
    }

    .section-404 .bg-circle {
      position: absolute;
      border-radius: 50%;
      background: rgba(56, 189, 248, 0.12);
      filter: blur(50px);
      z-index: 0;
    }

    .section-404 .circle1 {
      width: 220px;
      height: 220px;
      top: 10%;
      left: 10%;
    }

    .section-404 .circle2 {
      width: 280px;
      height: 280px;
      bottom: 10%;
      right: 10%;
    }

    @keyframes float {
      0%, 100% {
        transform: translateY(0);
      }
      50% {
        transform: translateY(-10px);
      }
    }

    .section-404 .container {
      position: relative;
      z-index: 1;
    }
  </style>
 

	<section class="section-404">
	  <div class="bg-circle circle1"></div>
	  <div class="bg-circle circle2"></div>

	  <div class="container">
	    <h1>404</h1>
	    <h2>Page Not Found</h2>
	    <p>Oops. The page you’re looking for doesn’t exist, was moved, or never existed in the first place.</p>
	    <a href="<?=ROOT?>">Go Home</a>
	  </div>
	</section>
 