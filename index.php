<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CampusFinder Yogyakarta</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #2c3e50;
            background: linear-gradient(-45deg, #354d44, #537a71, #8B7355, #6B4423, #8B7355, #537a71);
            background-size: 400% 400%;
            animation: gradient 15s ease infinite;
            min-height: 100vh;
            position: relative;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('images/pattern.svg');
            opacity: 0.5;
            z-index: 0;
            pointer-events: none;
        }

        @keyframes gradient {
            0% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
            100% {
                background-position: 0% 50%;
            }
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            text-align: center;
            position: relative;
            z-index: 1;
        }

        .hero {
            padding: 100px 20px;
            position: relative;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at top right, rgba(139, 115, 85, 0.15), transparent);
            pointer-events: none;
        }

        h1 {
            font-size: 3.5em;
            margin-bottom: 20px;
            color: #ffffff;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
            letter-spacing: 2px;
            position: relative;
        }

        .subtitle {
            font-size: 1.5em;
            color: #ffffff;
            margin-bottom: 40px;
            text-shadow: 1px 1px 3px rgba(0,0,0,0.2);
            position: relative;
        }

        .description {
            max-width: 800px;
            margin: 0 auto 40px;
            font-size: 1.1em;
            color: #2c3e50;
            padding: 30px;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1),
                        inset 0 0 0 2px rgba(139, 115, 85, 0.1),
                        0 0 0 1px rgba(255,255,255,0.2);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.3);
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .description:hover {
            box-shadow: 0 12px 40px rgba(0,0,0,0.15),
                        inset 0 0 0 2px rgba(139, 115, 85, 0.2),
                        0 0 0 1px rgba(255,255,255,0.3);
            transform: translateY(-2px);
        }

        .description::after {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, transparent 0%, rgba(139, 115, 85, 0.1) 100%);
            pointer-events: none;
            border-radius: 15px;
        }

        .explore-btn {
            display: inline-block;
            padding: 18px 45px;
            font-size: 1.2em;
            color: white;
            background: linear-gradient(135deg, #354d44, #6B4423);
            text-decoration: none;
            border-radius: 30px;
            transition: all 0.4s ease;
            box-shadow: 0 4px 15px rgba(53, 77, 68, 0.3),
                        inset 0 0 0 1px rgba(255,255,255,0.2);
            position: relative;
            overflow: hidden;
        }

        .explore-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(53, 77, 68, 0.4),
                        inset 0 0 0 2px rgba(255,255,255,0.3);
            background: linear-gradient(135deg, #537a71, #8B7355);
        }

        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin-top: 60px;
            padding: 20px;
            position: relative;
        }

        .feature-card {
            background: rgba(255, 255, 255, 0.95);
            padding: 35px;
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1),
                        inset 0 0 0 1px rgba(139, 115, 85, 0.1),
                        0 0 0 1px rgba(255,255,255,0.2);
            transition: all 0.4s ease;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.3);
            position: relative;
            overflow: hidden;
            text-align: center;
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, 
                rgba(255,255,255,0.1) 0%, 
                rgba(255,255,255,0.05) 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .feature-card:hover::before {
            opacity: 1;
        }

        .feature-card:hover {
            transform: translateY(-5px) scale(1.02);
            box-shadow: 0 12px 40px rgba(0,0,0,0.15),
                        inset 0 0 0 2px rgba(139, 115, 85, 0.2),
                        0 0 0 1px rgba(255,255,255,0.3);
            background: rgba(255, 255, 255, 0.98);
        }

        .feature-card h3 {
            color: #6B4423;
            margin-bottom: 15px;
            font-size: 1.4em;
        }

        .feature-card p {
            color: #8B7355;
            line-height: 1.7;
        }

        .hero-image {
            position: relative;
            width: 100%;
            max-width: 800px;
            margin: 0 auto 40px;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 8px 32px rgba(0,0,0,0.2);
        }

        .hero-image img {
            width: 100%;
            height: auto;
            display: block;
        }

        .feature-icon {
            width: 64px;
            height: 64px;
            margin-bottom: 20px;
            padding: 12px;
            border-radius: 50%;
            background: rgba(53, 77, 68, 0.1);
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .feature-icon svg {
            width: 32px;
            height: 32px;
            fill: #354d44;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="hero">
            <h1>CampusFinder.Yogyakarta</h1>
            <p class="subtitle">Temukan Kampus Impianmu di Kota Pelajar</p>
            
            <div class="hero-image">
                <img src="https://images.unsplash.com/photo-1541339907198-e08756dedf3f?auto=format&fit=crop&w=1200&q=80" alt="Universitas di Yogyakarta">
            </div>
            
            <div class="description">
                <p>Yogyakarta, kota yang dikenal sebagai "Kota Pelajar", adalah rumah bagi puluhan institusi pendidikan tinggi berkualitas. 
                Dengan sejarah pendidikan yang kaya dan budaya yang mendalam, Yogyakarta menawarkan pengalaman belajar yang unik 
                dan berkesan bagi para mahasiswa.</p>
                <br>
                <p>Dari universitas negeri ternama hingga kampus swasta yang inovatif, Yogyakarta menyediakan berbagai pilihan 
                pendidikan tinggi yang dapat memenuhi minat dan passion Anda. Dengan fasilitas modern, tenaga pengajar berkualitas, 
                dan lingkungan belajar yang kondusif, setiap kampus di Yogyakarta siap membantu Anda meraih masa depan yang cerah.</p>
            </div>

            <a href="/pgweb/responsi/map.php" class="explore-btn">Jelajahi Kampus</a>
        </div>

        <div class="features">
            <div class="feature-card">
                <div class="feature-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-book"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                </div>
                <h3>Informasi Lengkap</h3>
                <p>Dapatkan informasi detail tentang setiap kampus, mulai dari lokasi, program studi, hingga fasilitas yang tersedia.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-star"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                </div>
                <h3>Review & Rating</h3>
                <p>Baca review dari mahasiswa dan alumni untuk mendapatkan gambaran nyata tentang kehidupan kampus.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-map"><polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6"></polygon><line x1="8" y1="2" x2="8" y2="18"></line><line x1="16" y1="6" x2="16" y2="22"></line></svg>
                </div>
                <h3>Peta Interaktif</h3>
                <p>Temukan lokasi kampus dengan mudah melalui peta interaktif yang dilengkapi dengan penanda dan informasi.</p>
            </div>
        </div>
    </div>
</body>
</html>