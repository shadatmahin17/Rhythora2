<?php
require_once 'includes/header.php';

$trending_songs = getTrendingSongs();
?>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-logo">
                <img src="<?php echo SITE_URL; ?>/assets/images/logo.png" alt="Rhythora Logo" />
                <h2>Rhythora</h2>
            </div>
            <button class="close-sidebar" id="closeSidebar">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <ul class="sidebar-menu">
            <div class="menu-section">
                <h3 class="menu-title">Discover</h3>
                <li class="sidebar-item">
                    <a href="index.php" class="sidebar-link active" data-page="home">
                        <i class="fas fa-home"></i>
                        <span>Home</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="reels.php" class="sidebar-link" data-page="reels">
                        <i class="fas fa-film"></i>
                        <span>Reels</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="#" class="sidebar-link" data-page="albums">
                        <i class="fas fa-compact-disc"></i>
                        <span>Albums</span>
                    </a>
                </li>
            </div>
            
            <?php if (isLoggedIn()): ?>
            <div class="menu-section">
                <h3 class="menu-title">Your Library</h3>
                <li class="sidebar-item">
                    <a href="profile.php?tab=favorites" class="sidebar-link" data-page="favorites">
                        <i class="fas fa-heart"></i>
                        <span>Favorites</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="profile.php?tab=history" class="sidebar-link" data-page="history">
                        <i class="fas fa-history"></i>
                        <span>Recently Played</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="profile.php?tab=playlists" class="sidebar-link" data-page="playlists">
                        <i class="fas fa-music"></i>
                        <span>Playlists</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="profile.php" class="sidebar-link" data-page="profile">
                        <i class="fas fa-user-circle"></i>
                        <span>Profile</span>
                    </a>
                </li>
            </div>
            <?php endif; ?>
        </ul>
        
        <?php if (isLoggedIn()): ?>
        <div class="sidebar-footer">
            <div class="user-info">
                <div class="user-avatar" id="userAvatar">
                    <?php if (!empty($_SESSION['avatar'])): ?>
                        <img src="<?php echo SITE_URL . '/' . $_SESSION['avatar']; ?>" alt="<?php echo $_SESSION['name']; ?>">
                    <?php else: ?>
                        <span id="avatarInitial"><?php echo strtoupper(substr($_SESSION['name'], 0, 1)); ?></span>
                    <?php endif; ?>
                </div>
                <div class="user-dropdown">
                    <div class="dropdown-menu" id="dropdownMenu">
                        <a href="profile.php" class="dropdown-item" data-page="profile">
                            <i class="fas fa-user"></i>
                            <span>Profile</span>
                        </a>
                        <?php if (isAdmin()): ?>
                        <a href="<?php echo SITE_URL; ?>/admin/" class="dropdown-item">
                            <i class="fas fa-cog"></i>
                            <span>Admin Panel</span>
                        </a>
                        <?php endif; ?>
                        <div class="dropdown-divider"></div>
                        <a href="?logout" class="dropdown-item" id="logoutBtn">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Log Out</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </aside>

    <!-- Main Content -->
    <div class="main-content">
        <div class="content">
            <header>
                <div class="header-left">
                    <button class="menu-toggle" id="menuToggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div class="logo-title">
                        <img src="<?php echo SITE_URL; ?>/assets/images/logo.png" alt="Rhythora Logo" />
                        <h1>Rhythora</h1>
                    </div>
                </div>
                
                <div class="header-right">
                    <button class="theme-toggle" id="toggleThemeBtn">
                        <i class="fas fa-moon" id="themeIcon"></i>
                        <span class="theme-text">Theme</span>
                    </button>
                    
                    <?php if (!isLoggedIn()): ?>
                    <div class="auth-buttons" id="authButtons">
                        <a href="login.php" class="auth-button login-btn" id="loginBtn">Log In</a>
                        <a href="signup.php" class="auth-button signup-btn" id="signupBtn">Sign Up</a>
                    </div>
                    <?php endif; ?>
                </div>
            </header>

            <div class="search-container" id="searchContainer">
                <i class="fas fa-search search-icon"></i>
                <input type="search" class="search-bar" id="searchBar" placeholder="Search songs, artists, or albums...">
            </div>

            <!-- Home Page -->
            <main class="container" id="homePage">
                <div class="section-header">
                    <h2 class="section-title">Trending Now</h2>
                    <a href="#" class="view-all">View All <i class="fas fa-chevron-right"></i></a>
                </div>
                
                <?php foreach ($trending_songs as $song): ?>
                <div class="card" data-id="<?php echo $song['id']; ?>">
                    <?php if ($song['is_trending']): ?>
                    <div class="card-badge">Trending</div>
                    <?php endif; ?>
                    <img src="<?php echo SITE_URL . '/' . $song['image_path']; ?>" alt="<?php echo htmlspecialchars($song['title']); ?>" class="card-image">
                    <h3 class="card-title"><?php echo htmlspecialchars($song['title']); ?></h3>
                    <p class="card-artist"><?php echo htmlspecialchars($song['artist']); ?></p>
                    <button class="card-button" onclick="playSong(<?php echo $song['id']; ?>, '<?php echo htmlspecialchars($song['title']); ?>', '<?php echo htmlspecialchars($song['artist']); ?>', '<?php echo SITE_URL . '/' . $song['image_path']; ?>', '<?php echo SITE_URL . '/' . $song['audio_path']; ?>')">
                        <i class="fas fa-play"></i> Play
                    </button>
                </div>
                <?php endforeach; ?>
            </main>

            <!-- Audio Controller -->
            <div class="audio-controller hidden" id="audioController">
                <div class="song-info">
                    <img src="<?php echo SITE_URL; ?>/assets/images/default-song.png" alt="Album Cover" class="song-cover" id="songCover">
                    <div class="song-details">
                        <div class="song-title" id="currentSong">No song selected</div>
                        <div class="song-artist" id="currentArtist"></div>
                    </div>
                </div>

                <div class="progress-container">
                    <span class="progress-time" id="currentTime">0:00</span>
                    <div class="progress-bar" id="progressBar">
                        <div class="progress-filled" id="progressFilled"></div>
                    </div>
                    <span class="progress-time" id="totalTime">0:00</span>
                </div>

                <div class="controls">
                    <button class="control-button" id="prevBtn" aria-label="Previous">
                        <i class="fas fa-step-backward"></i>
                    </button>
                    <button class="control-button play-button" id="playBtn" aria-label="Play/Pause">
                        <i class="fas fa-play" id="playIcon"></i>
                    </button>
                    <button class="control-button" id="nextBtn" aria-label="Next">
                        <i class="fas fa-step-forward"></i>
                    </button>
                    <button class="control-button" id="repeatBtn" aria-label="Repeat">
                        <i class="fas fa-redo"></i>
                    </button>
                    
                    <div class="equalizer" id="equalizer">
                        <div class="equalizer-bar"></div>
                        <div class="equalizer-bar"></div>
                        <div class="equalizer-bar"></div>
                        <div class="equalizer-bar"></div>
                        <div class="equalizer-bar"></div>
                    </div>
                </div>

                <div class="volume-container">
                    <span class="volume-icon" id="volumeIcon">
                        <i class="fas fa-volume-up"></i>
                    </span>
                    <div class="volume-bar" id="volumeBar">
                        <div class="volume-filled" id="volumeFilled"></div>
                    </div>
                </div>
            </div>

            <footer>
                <div class="footer-content">
                    <div class="footer-links">
                        <a href="#" class="footer-link">About</a>
                        <a href="#" class="footer-link">Contact</a>
                        <a href="#" class="footer-link">Privacy</a>
                        <a href="#" class="footer-link">Terms</a>
                    </div>
                    <div class="social-links">
                        <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-spotify"></i></a>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <script src="<?php echo SITE_URL; ?>/assets/js/script.js"></script>
    <script>
        // Simulate loading
        document.addEventListener('DOMContentLoaded', function() {
            let progress = 0;
            const interval = setInterval(() => {
                progress += Math.random() * 10;
                if (progress >= 100) {
                    progress = 100;
                    clearInterval(interval);
                }
                document.getElementById('loadingProgress').style.width = `${progress}%`;
                
                if (progress === 100) {
                    setTimeout(() => {
                        document.getElementById('loadingScreen').style.opacity = '0';
                        setTimeout(() => {
                            document.getElementById('loadingScreen').style.display = 'none';
                        }, 500);
                    }, 500);
                }
            }, 100);
            
            // Initialize player
            const player = new MusicPlayer();
            window.player = player;
            
            // Search functionality
            document.getElementById('searchBar').addEventListener('input', function() {
                const query = this.value.toLowerCase();
                if (query.length > 2) {
                    fetch(`search.php?q=${encodeURIComponent(query)}`)
                        .then(response => response.json())
                        .then(songs => {
                            const container = document.getElementById('homePage');
                            container.innerHTML = `
                                <div class="section-header">
                                    <h2 class="section-title">Search Results</h2>
                                </div>
                            `;
                            
                            if (songs.length === 0) {
                                container.innerHTML += `
                                    <div class="empty-state">
                                        <div class="empty-icon">
                                            <i class="fas fa-search"></i>
                                        </div>
                                        <h3 class="empty-title">No songs found</h3>
                                        <p class="empty-text">Try a different search term</p>
                                    </div>
                                `;
                                return;
                            }
                            
                            songs.forEach(song => {
                                const card = document.createElement('div');
                                card.className = 'card';
                                card.dataset.id = song.id;
                                card.innerHTML = `
                                    ${song.is_trending ? '<div class="card-badge">Trending</div>' : ''}
                                    <img src="${song.image_path}" alt="${song.title}" class="card-image">
                                    <h3 class="card-title">${song.title}</h3>
                                    <p class="card-artist">${song.artist}</p>
                                    <button class="card-button">
                                        <i class="fas fa-play"></i> Play
                                    </button>
                                `;
                                card.addEventListener('click', () => {
                                    playSong(song.id, song.title, song.artist, song.image_path, song.audio_path);
                                });
                                container.appendChild(card);
                            });
                        });
                } else if (query.length === 0) {
                    // Reload trending songs
                    window.location.reload();
                }
            });
        });
        
        function playSong(id, title, artist, image, audio) {
            const player = window.player;
            player.playSong(id, title, artist, image, audio);
            
            // Add to history if logged in
            <?php if (isLoggedIn()): ?>
            fetch(`add_to_history.php?song_id=${id}`)
                .catch(error => console.error('Error adding to history:', error));
            <?php endif; ?>
        }
    </script>
</body>
</html>