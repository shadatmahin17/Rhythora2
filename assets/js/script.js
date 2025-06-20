class MusicPlayer {
    constructor() {
        this.audio = new Audio();
        this.currentIndex = 0;
        this.repeat = false;
        this.shuffle = false;
        this.volume = 0.7;
        this.isPlaying = false;
        this.currentSongId = null;
        
        this.initElements();
        this.bindEvents();
        this.initVolume();
    }
    
    initElements() {
        this.elements = {
            audioController: document.getElementById('audioController'),
            currentTime: document.getElementById('currentTime'),
            totalTime: document.getElementById('totalTime'),
            progressBar: document.getElementById('progressBar'),
            progressFilled: document.getElementById('progressFilled'),
            currentSong: document.getElementById('currentSong'),
            currentArtist: document.getElementById('currentArtist'),
            songCover: document.getElementById('songCover'),
            playBtn: document.getElementById('playBtn'),
            playIcon: document.getElementById('playIcon'),
            prevBtn: document.getElementById('prevBtn'),
            nextBtn: document.getElementById('nextBtn'),
            repeatBtn: document.getElementById('repeatBtn'),
            volumeBar: document.getElementById('volumeBar'),
            volumeFilled: document.getElementById('volumeFilled'),
            volumeIcon: document.getElementById('volumeIcon'),
            equalizer: document.getElementById('equalizer'),
            themeToggle: document.getElementById('toggleThemeBtn'),
            themeIcon: document.getElementById('themeIcon'),
            menuToggle: document.getElementById('menuToggle'),
            closeSidebar: document.getElementById('closeSidebar'),
            sidebar: document.getElementById('sidebar'),
            mobileOverlay: document.getElementById('mobileOverlay')
        };
    }
    
    bindEvents() {
        // Audio events
        this.audio.addEventListener('timeupdate', () => this.updateProgress());
        this.audio.addEventListener('loadedmetadata', () => this.updateDuration());
        this.audio.addEventListener('play', () => this.onPlay());
        this.audio.addEventListener('pause', () => this.onPause());
        this.audio.addEventListener('ended', () => this.onSongEnd());
        this.audio.addEventListener('volumechange', () => this.updateVolumeUI());
        
        // UI events
        this.elements.playBtn.addEventListener('click', () => this.togglePlay());
        this.elements.repeatBtn.addEventListener('click', () => this.toggleRepeat());
        
        // Progress bar
        this.elements.progressBar.addEventListener('click', (e) => {
            const rect = this.elements.progressBar.getBoundingClientRect();
            const percent = (e.clientX - rect.left) / rect.width;
            this.audio.currentTime = percent * this.audio.duration;
        });
        
        // Volume control
        this.elements.volumeBar.addEventListener('click', (e) => {
            const rect = this.elements.volumeBar.getBoundingClientRect();
            const percent = (e.clientX - rect.left) / rect.width;
            this.volume = Math.max(0, Math.min(1, percent));
            this.audio.volume = this.volume;
            this.updateVolumeUI();
        });
        
        // Theme toggle
        this.elements.themeToggle.addEventListener('click', () => this.toggleTheme());
        
        // Sidebar toggle
        this.elements.menuToggle.addEventListener('click', () => {
            this.toggleSidebar(true);
        });
        
        this.elements.closeSidebar.addEventListener('click', () => {
            this.toggleSidebar(false);
        });
        
        // Mobile overlay click to close sidebar
        this.elements.mobileOverlay.addEventListener('click', () => {
            this.toggleSidebar(false);
        });
    }
    
    toggleSidebar(open) {
        if (open) {
            this.elements.sidebar.classList.add('active');
            this.elements.mobileOverlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        } else {
            this.elements.sidebar.classList.remove('active');
            this.elements.mobileOverlay.classList.remove('active');
            document.body.style.overflow = '';
        }
    }
    
    initVolume() {
        this.audio.volume = this.volume;
        this.updateVolumeUI();
    }
    
    playSong(id, title, artist, image, audio) {
        this.currentSongId = id;
        this.elements.currentSong.textContent = title;
        this.elements.currentArtist.textContent = artist;
        this.elements.songCover.src = image;
        
        // Pause current audio first
        this.audio.pause();
        
        // Reset audio element
        this.audio = new Audio(audio);
        this.audio.volume = this.volume;
        
        // Add event listeners to the new audio instance
        this.bindEvents();
        
        // Show player
        this.elements.audioController.classList.remove('hidden');
        
        // Play after user interaction
        const playPromise = this.audio.play();
        
        if (playPromise !== undefined) {
            playPromise.catch(error => {
                console.error("Playback failed:", error);
                this.onPause();
            });
        }
    }
    
    togglePlay() {
        if (this.audio.paused) {
            if (!this.audio.src && this.currentSongId) {
                // In a real app, you'd fetch the song data again
                console.log("No audio source available");
            } else {
                this.audio.play().catch(error => {
                    console.error("Playback failed:", error);
                });
            }
        } else {
            this.audio.pause();
        }
    }
    
    onPlay() {
        this.isPlaying = true;
        this.elements.playIcon.classList.remove('fa-play');
        this.elements.playIcon.classList.add('fa-pause');
        this.elements.equalizer.style.display = 'flex';
        this.elements.songCover.classList.add('now-playing');
    }
    
    onPause() {
        this.isPlaying = false;
        this.elements.playIcon.classList.remove('fa-pause');
        this.elements.playIcon.classList.add('fa-play');
        this.elements.equalizer.style.display = 'none';
        this.elements.songCover.classList.remove('now-playing');
    }
    
    onSongEnd() {
        if (this.repeat) {
            this.audio.currentTime = 0;
            this.audio.play();
        } else {
            // In a real app, you'd play the next song
            console.log("Song ended");
        }
    }
    
    toggleRepeat() {
        this.repeat = !this.repeat;
        this.elements.repeatBtn.style.background = this.repeat ? "var(--accent)" : '';
        this.showToast(this.repeat ? 'Repeat: On' : 'Repeat: Off');
    }
    
    updateProgress() {
        if (this.audio.duration) {
            const percent = (this.audio.currentTime / this.audio.duration) * 100;
            this.elements.progressFilled.style.width = `${percent}%`;
            this.elements.currentTime.textContent = this.formatTime(this.audio.currentTime);
        }
    }
    
    updateDuration() {
        this.elements.totalTime.textContent = this.formatTime(this.audio.duration);
    }
    
    updateVolumeUI() {
        const percent = this.audio.volume * 100;
        this.elements.volumeFilled.style.width = `${percent}%`;
        
        // Update volume icon based on volume level
        const volumeIcon = this.elements.volumeIcon.querySelector('i');
        volumeIcon.classList.remove('fa-volume-up', 'fa-volume-down', 'fa-volume-off', 'fa-volume-mute');
        
        if (this.audio.volume === 0) {
            volumeIcon.classList.add('fa-volume-mute');
        } else if (this.audio.volume < 0.5) {
            volumeIcon.classList.add('fa-volume-down');
        } else {
            volumeIcon.classList.add('fa-volume-up');
        }
    }
    
    formatTime(seconds) {
        const mins = Math.floor(seconds / 60);
        const secs = Math.floor(seconds % 60);
        return `${mins}:${secs < 10 ? '0' : ''}${secs}`;
    }
    
    showToast(message) {
        const toast = document.createElement('div');
        toast.className = 'toast-notification';
        toast.innerHTML = `<i class="fas fa-check-circle"></i> ${message}`;
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.classList.add('show');
        }, 10);
        
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => {
                document.body.removeChild(toast);
            }, 300);
        }, 3000);
    }
    
    toggleTheme() {
        document.body.classList.toggle('dark');
        
        if (document.body.classList.contains('dark')) {
            localStorage.setItem('rhythora-theme', 'dark');
            this.elements.themeIcon.classList.remove('fa-moon');
            this.elements.themeIcon.classList.add('fa-sun');
        } else {
            localStorage.setItem('rhythora-theme', 'light');
            this.elements.themeIcon.classList.remove('fa-sun');
            this.elements.themeIcon.classList.add('fa-moon');
        }
    }
}

// Initialize theme
function initTheme() {
    const savedTheme = localStorage.getItem('rhythora-theme') || 'light';
    if (savedTheme === 'dark') {
        document.body.classList.add('dark');
        document.getElementById('themeIcon').classList.remove('fa-moon');
        document.getElementById('themeIcon').classList.add('fa-sun');
    } else {
        document.body.classList.remove('dark');
        document.getElementById('themeIcon').classList.remove('fa-sun');
        document.getElementById('themeIcon').classList.add('fa-moon');
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initTheme();
    
    // Initialize player
    window.player = new MusicPlayer();
    
    // Admin sidebar toggle
    const adminMenuToggle = document.getElementById('adminMenuToggle');
    const adminNav = document.querySelector('.admin_nav');
    const adminContent = document.querySelector('.admin-content');
    
    if (adminMenuToggle && adminNav) {
        adminMenuToggle.addEventListener('click', function() {
            adminNav.classList.toggle('active');
            adminContent.classList.toggle('active');
        });
    }
});