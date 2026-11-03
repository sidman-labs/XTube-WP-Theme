/**
 * XTube Advanced Video Player JavaScript v3.0
 * Handles portrait/landscape videos with responsive design
 * 
 * @package XTube
 * @version 3.0.0
 */

window.xtubeVideoPlayer = (function() {
    'use strict';
    
    let config = {};
    let players = {};
    let currentPlayer = null;
    
    /**
     * Initialize the video player
     */
    function init(options) {
        config = Object.assign({
            selector: '.xtube-video-player',
            portraitMode: 'contain',
            enableAnalytics: true,
            autoplayNext: false,
            theme: 'dark'
        }, options);
        
        // Initialize players when DOM is ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initializePlayers);
        } else {
            initializePlayers();
        }
        
        // Handle orientation changes
        window.addEventListener('orientationchange', handleOrientationChange);
        window.addEventListener('resize', debounce(handleResize, 250));
    }
    
    /**
     * Initialize all video players on the page
     */
    function initializePlayers() {
        const videoElements = document.querySelectorAll(config.selector);
        
        videoElements.forEach(function(element) {
            const playerId = element.id || 'video-' + Math.random().toString(36).substr(2, 9);
            element.id = playerId;
            
            // Initialize Video.js if available
            if (typeof videojs !== 'undefined') {
                initializeVideoJSPlayer(element, playerId);
            } else {
                initializeNativePlayer(element, playerId);
            }
            
            // Add event listeners
            addPlayerEventListeners(element, playerId);
        });
    }
    
    /**
     * Initialize Video.js player
     */
    function initializeVideoJSPlayer(element, playerId) {
        try {
            const playerOptions = {
                fluid: false,
                responsive: true,
                controls: true,
                autoplay: false,
                preload: 'metadata',
                playbackRates: [0.5, 1, 1.25, 1.5, 2],
                plugins: {
                    qualitySelector: {
                        default: 'auto'
                    }
                },
                html5: {
                    vhs: {
                        overrideNative: true
                    }
                }
            };
            
            const player = videojs(playerId, playerOptions);
            
            // All videos use the same container - no special portrait handling
            player.ready(function() {
                const playerEl = player.el();
                playerEl.style.width = '100%';
                playerEl.style.height = '100%';
                playerEl.style.position = 'absolute';
                playerEl.style.top = '0';
                playerEl.style.left = '0';
            });
            
            players[playerId] = {
                instance: player,
                type: 'videojs',
                isPortrait: false, // No special handling needed
                videoId: element.dataset.videoId
            };
            
            // Add Video.js specific events
            player.on('play', handlePlay);
            player.on('ended', handleEnded);
            player.on('timeupdate', handleTimeUpdate);
            player.on('qualitychange', handleQualityChange);
            
        } catch (error) {
            console.warn('Video.js initialization failed, falling back to native player:', error);
            initializeNativePlayer(element, playerId);
        }
    }
    
    /**
     * Initialize native HTML5 video player
     */
    function initializeNativePlayer(element, playerId) {
        const isPortrait = element.dataset.orientation === 'portrait';
        
        // Set up native player controls
        element.controls = true;
        element.preload = 'metadata';
        
        // Handle portrait video sizing
        if (isPortrait) {
            setupPortraitVideo(element);
        }
        
        players[playerId] = {
            instance: element,
            type: 'native',
            isPortrait: isPortrait,
            videoId: element.dataset.videoId
        };
        
        // Add native video events
        element.addEventListener('play', handlePlay);
        element.addEventListener('ended', handleEnded);
        element.addEventListener('timeupdate', handleTimeUpdate);
        element.addEventListener('loadedmetadata', handleLoadedMetadata);
    }
    
    /**
     * Setup portrait video container
     */
    function setupPortraitVideo(element) {
        const container = element.closest('.xtube-video-container');
        if (container) {
            container.classList.add('portrait-container');
            
            // Ensure proper aspect ratio
            element.style.aspectRatio = '9/16';
            element.style.objectFit = 'contain';
            element.style.maxHeight = '80vh';
        }
    }
    
    /**
     * Add event listeners to player
     */
    function addPlayerEventListeners(element, playerId) {
        // Like button
        const likeBtn = element.parentElement.querySelector('.like-video');
        if (likeBtn) {
            likeBtn.addEventListener('click', function(e) {
                e.preventDefault();
                handleLike(playerId);
            });
        }
        
        // Share button
        const shareBtn = element.parentElement.querySelector('.share-video');
        if (shareBtn) {
            shareBtn.addEventListener('click', function(e) {
                e.preventDefault();
                handleShare(playerId);
            });
        }
        
        // Download button
        const downloadBtn = element.parentElement.querySelector('.download-video');
        if (downloadBtn) {
            downloadBtn.addEventListener('click', function(e) {
                // Allow default download behavior
            });
        }
    }
    
    /**
     * Handle video play
     */
    function handlePlay(e) {
        const playerId = e.target.id || e.target.closest('.xtube-video-player').id;
        const player = players[playerId];
        
        if (!player) return;
        
        currentPlayer = playerId;
        
        // Pause other players
        Object.keys(players).forEach(function(id) {
            if (id !== playerId) {
                const otherPlayer = players[id];
                if (otherPlayer.type === 'videojs') {
                    otherPlayer.instance.pause();
                } else {
                    otherPlayer.instance.pause();
                }
            }
        });
        
        // Track analytics
        if (config.enableAnalytics && player.videoId) {
            trackVideoAnalytics(player.videoId, 'play');
        }
        
        // Show loading state briefly
        const container = e.target.closest('.xtube-video-container');
        if (container) {
            container.classList.add('loading');
            setTimeout(function() {
                container.classList.remove('loading');
            }, 1000);
        }
    }
    
    /**
     * Handle video end
     */
    function handleEnded(e) {
        const playerId = e.target.id || e.target.closest('.xtube-video-player').id;
        const player = players[playerId];
        
        if (!player) return;
        
        // Track completion
        if (config.enableAnalytics && player.videoId) {
            trackVideoAnalytics(player.videoId, 'complete');
        }
        
        // Autoplay next video if enabled
        if (config.autoplayNext) {
            setTimeout(function() {
                playNextVideo(playerId);
            }, 3000);
        }
    }
    
    /**
     * Handle time update
     */
    function handleTimeUpdate(e) {
        const playerId = e.target.id || e.target.closest('.xtube-video-player').id;
        const player = players[playerId];
        
        if (!player) return;
        
        // Track progress at 25%, 50%, 75%
        const currentTime = e.target.currentTime || (player.type === 'videojs' ? player.instance.currentTime() : 0);
        const duration = e.target.duration || (player.type === 'videojs' ? player.instance.duration() : 0);
        
        if (duration > 0) {
            const progress = (currentTime / duration) * 100;
            const progressKey = 'video_progress_' + player.videoId;
            
            if (progress >= 25 && !localStorage.getItem(progressKey + '_25')) {
                localStorage.setItem(progressKey + '_25', 'true');
                trackVideoAnalytics(player.videoId, 'progress_25');
            } else if (progress >= 50 && !localStorage.getItem(progressKey + '_50')) {
                localStorage.setItem(progressKey + '_50', 'true');
                trackVideoAnalytics(player.videoId, 'progress_50');
            } else if (progress >= 75 && !localStorage.getItem(progressKey + '_75')) {
                localStorage.setItem(progressKey + '_75', 'true');
                trackVideoAnalytics(player.videoId, 'progress_75');
            }
        }
    }
    
    /**
     * Handle quality change
     */
    function handleQualityChange(e) {
        const playerId = e.target.id || e.target.closest('.xtube-video-player').id;
        const player = players[playerId];
        
        if (!player) return;
        
        if (config.enableAnalytics && player.videoId) {
            trackVideoAnalytics(player.videoId, 'quality_change', {
                quality: e.quality || 'auto'
            });
        }
    }
    
    /**
     * Handle loaded metadata
     */
    function handleLoadedMetadata(e) {
        const element = e.target;
        const isPortrait = element.dataset.orientation === 'portrait';
        
        // Adjust player based on video dimensions
        if (isPortrait && element.videoWidth < element.videoHeight) {
            setupPortraitVideo(element);
        }
    }
    
    /**
     * Handle like button click
     */
    function handleLike(playerId) {
        const player = players[playerId];
        if (!player || !player.videoId) return;
        
        const likeBtn = document.querySelector('.like-video[data-video-id="' + player.videoId + '"]');
        const likeCount = likeBtn.querySelector('.like-count');
        
        // Toggle like state
        const isLiked = likeBtn.classList.contains('liked');
        
        if (isLiked) {
            likeBtn.classList.remove('liked');
            if (likeCount) {
                likeCount.textContent = parseInt(likeCount.textContent) - 1;
            }
        } else {
            likeBtn.classList.add('liked');
            if (likeCount) {
                likeCount.textContent = parseInt(likeCount.textContent) + 1;
            }
        }
        
        // Track analytics
        if (config.enableAnalytics) {
            trackVideoAnalytics(player.videoId, isLiked ? 'unlike' : 'like');
        }
    }
    
    /**
     * Handle share button click
     */
    function handleShare(playerId) {
        const player = players[playerId];
        if (!player) return;
        
        const videoUrl = player.instance.dataset.videoUrl || window.location.href;
        
        if (navigator.share) {
            navigator.share({
                title: document.title,
                url: videoUrl
            }).catch(function(error) {
                console.log('Share failed:', error);
                fallbackShare(videoUrl);
            });
        } else {
            fallbackShare(videoUrl);
        }
    }
    
    /**
     * Fallback share method
     */
    function fallbackShare(url) {
        // Copy to clipboard
        if (navigator.clipboard) {
            navigator.clipboard.writeText(url).then(function() {
                showNotification('Link copied to clipboard!');
            });
        } else {
            // Fallback for older browsers
            const textArea = document.createElement('textarea');
            textArea.value = url;
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand('copy');
            document.body.removeChild(textArea);
            showNotification('Link copied to clipboard!');
        }
    }
    
    /**
     * Play next video
     */
    function playNextVideo(currentPlayerId) {
        // Find next video link on page
        const nextVideoLink = document.querySelector('.next-video a, .related-videos .video-item:first-child a');
        if (nextVideoLink) {
            window.location.href = nextVideoLink.href;
        }
    }
    
    /**
     * Track video analytics
     */
    function trackVideoAnalytics(videoId, action, data = {}) {
        if (!config.enableAnalytics || !xtube_video_vars.ajax_url) return;
        
        const analyticsData = Object.assign({
            action: 'xtube_video_analytics',
            video_id: videoId,
            analytics_action: action,
            nonce: xtube_video_vars.nonce
        }, data);
        
        // Send analytics asynchronously
        fetch(xtube_video_vars.ajax_url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams(analyticsData)
        }).catch(function(error) {
            console.log('Analytics tracking failed:', error);
        });
    }
    
    /**
     * Handle orientation change
     */
    function handleOrientationChange() {
        Object.keys(players).forEach(function(playerId) {
            const player = players[playerId];
            if (player.isPortrait) {
                const element = player.type === 'videojs' ? player.instance.el() : player.instance;
                setupPortraitVideo(element);
            }
        });
    }
    
    /**
     * Handle window resize
     */
    function handleResize() {
        Object.keys(players).forEach(function(playerId) {
            const player = players[playerId];
            if (player.isPortrait) {
                const element = player.type === 'videojs' ? player.instance.el() : player.instance;
                setupPortraitVideo(element);
            }
        });
    }
    
    /**
     * Show notification
     */
    function showNotification(message) {
        const notification = document.createElement('div');
        notification.className = 'xtube-notification';
        notification.textContent = message;
        notification.style.cssText = `
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 12px 24px;
            border-radius: 25px;
            z-index: 9999;
            font-size: 14px;
            backdrop-filter: blur(10px);
            animation: slideUp 0.3s ease;
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(function() {
            notification.style.animation = 'slideDown 0.3s ease';
            setTimeout(function() {
                document.body.removeChild(notification);
            }, 300);
        }, 2000);
    }
    
    /**
     * Debounce function
     */
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = function() {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
    
    /**
     * Public API
     */
    return {
        init: init,
        getPlayer: function(playerId) {
            return players[playerId];
        },
        getAllPlayers: function() {
            return players;
        },
        play: function(playerId) {
            const player = players[playerId];
            if (player) {
                if (player.type === 'videojs') {
                    player.instance.play();
                } else {
                    player.instance.play();
                }
            }
        },
        pause: function(playerId) {
            const player = players[playerId];
            if (player) {
                if (player.type === 'videojs') {
                    player.instance.pause();
                } else {
                    player.instance.pause();
                }
            }
        }
    };
})();

// Add CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideUp {
        from { transform: translate(-50%, 100px); opacity: 0; }
        to { transform: translate(-50%, 0); opacity: 1; }
    }
    @keyframes slideDown {
        from { transform: translate(-50%, 0); opacity: 1; }
        to { transform: translate(-50%, 100px); opacity: 0; }
    }
    
    .xtube-notification {
        font-family: 'Inter', 'Poppins', sans-serif;
    }
    
    .like-video.liked {
        background: rgba(229, 9, 20, 0.2) !important;
        border-color: #e50914 !important;
    }
`;
document.head.appendChild(style);
