<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback - Nasgor K5</title>
    <link rel="stylesheet" href="css/feedback.css">
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;500;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>
<body>

    <div class="bg-decoration"></div>

    <div class="paper-container">
        
        <nav>
            <a href="index.html" class="logo">K5.FEEDBACK</a>
            <div class="nav-links">
                <a href="index.html">Home</a>
                <a href="menu.html">Menu</a>
                <a href="#" class="active">Feedback</a>
            </div>
        </nav>

        <header class="page-header">
            <span class="subtitle">YOUR VOICE MATTERS</span>
            <h1>Cerita Rasa.</h1>
            <p>Bagikan pengalaman kulinermu bersama kami.</p>
        </header>

        <div class="feedback-layout">
            
            <section class="form-section">
                <div class="form-card">
                    <h3>Tulis Ulasan</h3>
                    <form action="submit_feedback.php" method="POST">
                        
                        <div class="input-group">
                            <label>Nama Lengkap</label>
                            <input type="text" name="nama" placeholder="Cth: Nauval Fitra" required>
                        </div>

                        <div class="input-group">
                            <label>Rating Kepuasan</label>
                            <div class="star-rating">
                                <input type="radio" id="star5" name="rating" value="5" /><label for="star5" title="Sempurna"></label>
                                <input type="radio" id="star4" name="rating" value="4" /><label for="star4" title="Bagus"></label>
                                <input type="radio" id="star3" name="rating" value="3" /><label for="star3" title="Biasa"></label>
                                <input type="radio" id="star2" name="rating" value="2" /><label for="star2" title="Kurang"></label>
                                <input type="radio" id="star1" name="rating" value="1" /><label for="star1" title="Buruk"></label>
                            </div>
                        </div>

                        <div class="input-group">
                            <label>Komentar</label>
                            <textarea name="pesan" rows="4" placeholder="Apa yang Anda suka dari masakan kami?" required></textarea>
                        </div>

                        <button type="submit" class="btn-submit">Kirim Ulasan</button>
                    </form>
                </div>
            </section>

            <section class="reviews-list">
                <h3>Apa Kata Mereka?</h3>
                
                <div class="review-card">
                    <div class="review-header">
                        <div class="user-info">
                            <div class="avatar">Z</div>
                            <div>
                                <h4>Zeneo Aiman</h4>
                                <span class="date">2 Jam yang lalu</span>
                            </div>
                        </div>
                        <div class="stars-display">
                            <i class="ph-fill ph-star"></i>
                            <i class="ph-fill ph-star"></i>
                            <i class="ph-fill ph-star"></i>
                            <i class="ph-fill ph-star"></i>
                            <i class="ph-fill ph-star"></i>
                        </div>
                    </div>
                    <p class="review-text">"Nasgor Kambingnya juara! Dagingnya empuk banget dan gak bau prengus. Rempahnya kerasa banget."</p>
                </div>

                <div class="review-card">
                    <div class="review-header">
                        <div class="user-info">
                            <div class="avatar">I</div>
                            <div>
                                <h4>Ivon Fadilah</h4>
                                <span class="date">Kemarin</span>
                            </div>
                        </div>
                        <div class="stars-display">
                            <i class="ph-fill ph-star"></i>
                            <i class="ph-fill ph-star"></i>
                            <i class="ph-fill ph-star"></i>
                            <i class="ph-fill ph-star"></i>
                            <i class="ph ph-star"></i>
                        </div>
                    </div>
                    <p class="review-text">"Pelayanan cepat dan ramah. Suka banget sama Es Teh Solonya, wangi melatinya autentik."</p>
                </div>

                <div class="review-card">
                    <div class="review-header">
                        <div class="user-info">
                            <div class="avatar">B</div>
                            <div>
                                <h4>Bagus Setiawan</h4>
                                <span class="date">3 Hari lalu</span>
                            </div>
                        </div>
                        <div class="stars-display">
                            <i class="ph-fill ph-star"></i>
                            <i class="ph-fill ph-star"></i>
                            <i class="ph-fill ph-star"></i>
                            <i class="ph-fill ph-star"></i>
                            <i class="ph-fill ph-star"></i>
                        </div>
                    </div>
                    <p class="review-text">"Nasgor Spesialnya porsi kuli tapi rasa bintang lima. Worth it banget!"</p>
                </div>

            </section>
        </div>
        
        <footer>
            <p>© 2024 Kelompok 5.</p>
        </footer>
    </div>

</body>
</html>