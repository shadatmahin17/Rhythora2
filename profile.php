<?php
require_once 'includes/header.php';

if (!isLoggedIn()) {
    $_SESSION['error'] = "Please log in to view your profile";
    redirect('login.php');
}

$user_id = $_SESSION['user_id'];
$tab = isset($_GET['tab']) ? $_GET['tab'] : 'playlists';

// Get user data
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $db->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Get stats
$followers = 0; // In a real app, you'd count followers from a separate table
$following = 0; // Same for following
$playlists_count = count(getUserPlaylists($user_id));
?>

    <!-- Sidebar (same as index.php) -->
    <!-- ... -->

    <!-- Main Content -->
    <div class="main-content">
        <div class="content">
            <header>
                <!-- Same header as index.php -->
            </header>

            <!-- Profile Page -->
            <div class="profile-page" id="profilePage">
                <div class="profile-header">
                    <?php if (!empty($user['avatar'])): ?>
                        <img src="<?php echo SITE_URL . '/' . $user['avatar']; ?>" alt="Profile Picture" class="profile-avatar" id="profileAvatar">
                    <?php else: ?>
                        <div class="profile-avatar" id="profileAvatar" style="background-color: #6c5ce7; color: white; display: flex; align-items: center; justify-content: center; font-size: 3rem; font-weight: bold;">
                            <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                        </div>
                    <?php endif; ?>
                    <div class="profile-info">
                        <h1 class="profile-name" id="profileName"><?php echo htmlspecialchars($user['name']); ?></h1>
                        <p class="profile-username" id="profileUsername">@<?php echo htmlspecialchars($user['username']); ?></p>
                        <div class="profile-stats">
                            <div class="stat-item">
                                <div class="stat-value" id="followersCount"><?php echo formatNumber($followers); ?></div>
                                <div class="stat-label">Followers</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-value" id="followingCount"><?php echo formatNumber($following); ?></div>
                                <div class="stat-label">Following</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-value" id="playlistsCount"><?php echo $playlists_count; ?></div>
                                <div class="stat-label">Playlists</div>
                            </div>
                        </div>
                    </div>
                    <div class="profile-actions">
                        <button class="edit-profile-btn" id="editProfileBtn"><i class="fas fa-pencil-alt"></i> Edit Profile</button>
                    </div>
                </div>

                <div class="profile-tabs">
                    <div class="profile-tab <?php echo $tab === 'playlists' ? 'active' : ''; ?>" data-tab="playlists">Playlists</div>
                    <div class="profile-tab <?php echo $tab === 'favorites' ? 'active' : ''; ?>" data-tab="favorites">Favorites</div>
                    <div class="profile-tab <?php echo $tab === 'history' ? 'active' : ''; ?>" data-tab="history">Listening History</div>
                </div>

                <div class="profile-tab-content <?php echo $tab === 'playlists' ? 'active' : ''; ?>" id="playlistsTab">
                    <div class="playlist-grid">
                        <?php 
                        $playlists = getUserPlaylists($user_id);
                        if (empty($playlists)): ?>
                            <div class="empty-state">
                                <div class="empty-icon">
                                    <i class="fas fa-music"></i>
                                </div>
                                <h3 class="empty-title">No playlists yet</h3>
                                <p class="empty-text">Create your first playlist to get started</p>
                                <button class="auth-button signup-btn" id="createPlaylistBtn">
                                    <i class="fas fa-plus"></i> Create Playlist
                                </button>
                            </div>
                        <?php else: ?>
                            <?php foreach ($playlists as $playlist): ?>
                            <div class="playlist-card" onclick="window.location='playlist.php?id=<?php echo $playlist['id']; ?>'">
                                <?php if (!empty($playlist['cover_image'])): ?>
                                    <img src="<?php echo SITE_URL . '/' . $playlist['cover_image']; ?>" alt="<?php echo htmlspecialchars($playlist['title']); ?>" class="playlist-cover">
                                <?php else: ?>
                                    <div class="playlist-cover" style="background-color: var(--primary); display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-music" style="font-size: 2rem; color: white;"></i>
                                    </div>
                                <?php endif; ?>
                                <div class="playlist-info">
                                    <h3 class="playlist-title"><?php echo htmlspecialchars($playlist['title']); ?></h3>
                                    <p class="playlist-count">
                                        <?php 
                                        $song_count = count(getPlaylistSongs($playlist['id']));
                                        echo $song_count . ' song' . ($song_count !== 1 ? 's' : '');
                                        ?>
                                    </p>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="profile-tab-content <?php echo $tab === 'favorites' ? 'active' : ''; ?>" id="favoritesTab">
                    <div class="container">
                        <?php 
                        $favorites = getUserFavorites($user_id);
                        if (empty($favorites)): ?>
                            <div class="empty-state">
                                <div class="empty-icon">
                                    <i class="fas fa-heart"></i>
                                </div>
                                <h3 class="empty-title">No favorites yet</h3>
                                <p class="empty-text">Like songs to add them to your favorites</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($favorites as $song): ?>
                            <div class="card" onclick="playSong(<?php echo $song['id']; ?>, '<?php echo htmlspecialchars($song['title']); ?>', '<?php echo htmlspecialchars($song['artist']); ?>', '<?php echo SITE_URL . '/' . $song['image_path']; ?>', '<?php echo SITE_URL . '/' . $song['audio_path']; ?>')">
                                <img src="<?php echo SITE_URL . '/' . $song['image_path']; ?>" alt="<?php echo htmlspecialchars($song['title']); ?>" class="card-image">
                                <h3 class="card-title"><?php echo htmlspecialchars($song['title']); ?></h3>
                                <p class="card-artist"><?php echo htmlspecialchars($song['artist']); ?></p>
                                <button class="card-button">
                                    <i class="fas fa-play"></i> Play
                                </button>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="profile-tab-content <?php echo $tab === 'history' ? 'active' : ''; ?>" id="historyTab">
                    <div class="container">
                        <?php 
                        $history = getUserHistory($user_id);
                        if (empty($history)): ?>
                            <div class="empty-state">
                                <div class="empty-icon">
                                    <i class="fas fa-history"></i>
                                </div>
                                <h3 class="empty-title">No history yet</h3>
                                <p class="empty-text">Play some songs to see them here</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($history as $song): ?>
                            <div class="card" onclick="playSong(<?php echo $song['id']; ?>, '<?php echo htmlspecialchars($song['title']); ?>', '<?php echo htmlspecialchars($song['artist']); ?>', '<?php echo SITE_URL . '/' . $song['image_path']; ?>', '<?php echo SITE_URL . '/' . $song['audio_path']; ?>')">
                                <img src="<?php echo SITE_URL . '/' . $song['image_path']; ?>" alt="<?php echo htmlspecialchars($song['title']); ?>" class="card-image">
                                <h3 class="card-title"><?php echo htmlspecialchars($song['title']); ?></h3>
                                <p class="card-artist"><?php echo htmlspecialchars($song['artist']); ?></p>
                                <button class="card-button">
                                    <i class="fas fa-play"></i> Play
                                </button>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Audio Controller (same as index.php) -->
            <!-- ... -->
        </div>
    </div>

    <!-- Edit Profile Modal -->
    <div class="edit-profile-modal" id="editProfileModal">
        <div class="edit-profile-form">
            <button class="close-modal" id="closeEditProfileModal">
                <i class="fas fa-times"></i>
            </button>
            <h2>Edit Profile</h2>
            <form id="editProfileForm" action="update_profile.php" method="POST" enctype="multipart/form-data">
                <div class="avatar-upload">
                    <div class="avatar-preview" id="avatarPreview" style="background-image: url('<?php echo !empty($user['avatar']) ? SITE_URL . '/' . $user['avatar'] : ''; ?>')">
                        <?php if (empty($user['avatar'])): ?>
                            <span><?php echo strtoupper(substr($user['name'], 0, 1)); ?></span>
                        <?php endif; ?>
                    </div>
                    <button type="button" class="upload-btn" id="uploadAvatarBtn">
                        <i class="fas fa-camera"></i> Change Avatar
                    </button>
                    <input type="file" id="avatarInput" name="avatar" accept="image/*" style="display: none;">
                </div>
                <div class="form-group">
                    <label for="editName">Full Name</label>
                    <input type="text" id="editName" name="name" required placeholder="Enter your full name" value="<?php echo htmlspecialchars($user['name']); ?>">
                </div>
                <div class="form-group">
                    <label for="editUsername">Username</label>
                    <input type="text" id="editUsername" name="username" required placeholder="Enter your username" value="<?php echo htmlspecialchars($user['username']); ?>">
                </div>
                <div class="form-group">
                    <label for="editBio">Bio</label>
                    <textarea id="editBio" name="bio" rows="3" placeholder="Tell us about yourself"><?php echo htmlspecialchars($user['bio']); ?></textarea>
                </div>
                <button type="submit" class="submit-btn"><i class="fas fa-save"></i> Save Changes</button>
            </form>
        </div>
    </div>

    <script src="<?php echo SITE_URL; ?>/assets/js/script.js"></script>
    <script>
        // Profile tab switching
        document.querySelectorAll('.profile-tab').forEach(tab => {
            tab.addEventListener('click', function() {
                const tabId = this.dataset.tab;
                window.location.href = `profile.php?tab=${tabId}`;
            });
        });

        // Edit profile modal
        document.getElementById('editProfileBtn').addEventListener('click', function() {
            document.getElementById('editProfileModal').classList.add('active');
        });

        document.getElementById('closeEditProfileModal').addEventListener('click', function() {
            document.getElementById('editProfileModal').classList.remove('active');
        });

        // Avatar upload preview
        document.getElementById('uploadAvatarBtn').addEventListener('click', function() {
            document.getElementById('avatarInput').click();
        });

        document.getElementById('avatarInput').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    const preview = document.getElementById('avatarPreview');
                    preview.style.backgroundImage = `url(${event.target.result})`;
                    preview.querySelector('span').style.display = 'none';
                };
                reader.readAsDataURL(file);
            }
        });

        // Create playlist button
        document.getElementById('createPlaylistBtn')?.addEventListener('click', function() {
            const title = prompt('Enter playlist name:');
            if (title && title.trim() !== '') {
                fetch('create_playlist.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `title=${encodeURIComponent(title)}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.reload();
                    } else {
                        alert('Failed to create playlist: ' + (data.message || 'Unknown error'));
                    }
                });
            }
        });
    </script>
</body>
</html>