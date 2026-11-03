/**
 * XTube Dark Mode JavaScript v3.0
 * Advanced dark mode functionality with system preference detection
 * 
 * @package XTube
 * @version 3.0.0
 */

window.xtubeDarkMode = (function() {
    'use strict';
    
    let config = {};
    let currentMode = 'light';
    let systemPreference = 'light';
    let toggleButton = null;
    
    /**
     * Initialize dark mode
     */
    function init(options) {
        config = Object.assign({
            defaultMode: 'system',
            showToggle: true,
            rememberPreference: true,
            transitionDuration: '0.3s'
        }, options);
        
        // Detect system preference
        detectSystemPreference();
        
        // Get current mode
        currentMode = getCurrentMode();
        
        // Apply dark mode immediately
        applyMode(currentMode, false);
        
        // Create toggle button if enabled
        if (config.showToggle) {
            createToggleButton();
        }
        
        // Listen for system preference changes
        listenForSystemChanges();
        
        // Listen for custom events
        listenForCustomEvents();
        
        // Remove no-transition class after initial load
        setTimeout(function() {
            document.body.classList.remove('no-transition');
        }, 100);
    }
    
    /**
     * Detect system color scheme preference
     */
    function detectSystemPreference() {
        // Check for modern browser support
        if (window.matchMedia) {
            const darkModeQuery = window.matchMedia('(prefers-color-scheme: dark)');
            systemPreference = darkModeQuery.matches ? 'dark' : 'light';
            
            // Listen for changes
            if (darkModeQuery.addListener) {
                darkModeQuery.addListener(handleSystemChange);
            } else if (darkModeQuery.addEventListener) {
                darkModeQuery.addEventListener('change', handleSystemChange);
            }
        }
        
        // Check HTTP header for system preference (Chrome 89+)
        if (navigator.userAgentData && navigator.userAgentData.brands) {
            const headerPreference = document.querySelector('meta[name="sec-ch-prefers-color-scheme"]');
            if (headerPreference) {
                systemPreference = headerPreference.content === 'dark' ? 'dark' : 'light';
            }
        }
    }
    
    /**
     * Get current mode based on preferences
     */
    function getCurrentMode() {
        // Check for saved preference
        if (config.rememberPreference && getCookie('xtube_dark_mode')) {
            return getCookie('xtube_dark_mode');
        }
        
        // Check for default mode
        if (config.defaultMode === 'system') {
            return systemPreference;
        }
        
        return config.defaultMode;
    }
    
    /**
     * Apply dark/light mode
     */
    function applyMode(mode, save = true) {
        const isDark = mode === 'dark';
        
        // Update body classes
        document.body.classList.toggle('dark-mode', isDark);
        document.body.classList.toggle('light-mode', !isDark);
        
        // Update meta theme-color
        updateThemeColor(isDark);
        
        // Update toggle button
        updateToggleButton(isDark);
        
        // Save preference if enabled
        if (save && config.rememberPreference) {
            setCookie('xtube_dark_mode', mode, 365);
        }
        
        // Trigger custom event
        triggerModeChange(mode);
        
        currentMode = mode;
    }
    
    /**
     * Create toggle button
     */
    function createToggleButton() {
        // Check if button already exists
        if (document.querySelector('.dark-mode-toggle')) {
            return;
        }
        
        const button = document.createElement('button');
        button.className = 'dark-mode-toggle';
        button.setAttribute('aria-label', 'Toggle dark mode');
        button.setAttribute('data-current-mode', currentMode);
        
        const icon = document.createElement('span');
        icon.className = 'icon';
        icon.textContent = currentMode === 'dark' ? '☀️' : '🌙';
        
        const label = document.createElement('span');
        label.className = 'label';
        label.textContent = currentMode === 'dark' ? 'Light Mode' : 'Dark Mode';
        
        button.appendChild(icon);
        button.appendChild(label);
        
        // Add click handler
        button.addEventListener('click', handleToggleClick);
        
        // Add keyboard support
        button.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                handleToggleClick();
            }
        });
        
        // Insert into DOM
        document.body.appendChild(button);
        toggleButton = button;
        
        // Animate entrance
        requestAnimationFrame(function() {
            button.style.transform = 'translateY(0)';
            button.style.opacity = '1';
        });
    }
    
    /**
     * Handle toggle button click
     */
    function handleToggleClick() {
        const newMode = currentMode === 'dark' ? 'light' : 'dark';
        
        // Add animation class
        if (toggleButton) {
            toggleButton.classList.add('toggling');
            setTimeout(function() {
                toggleButton.classList.remove('toggling');
            }, 300);
        }
        
        // Apply new mode
        applyMode(newMode);
        
        // Send AJAX request to save preference
        if (xtube_dark_mode_vars && xtube_dark_mode_vars.ajax_url) {
            sendToggleRequest(newMode);
        }
        
        // Show notification
        showNotification(newMode === 'dark' ? 'Dark mode enabled' : 'Light mode enabled');
    }
    
    /**
     * Update toggle button appearance
     */
    function updateToggleButton(isDark) {
        if (!toggleButton) return;
        
        const icon = toggleButton.querySelector('.icon');
        const label = toggleButton.querySelector('.label');
        
        if (icon) {
            icon.textContent = isDark ? '☀️' : '🌙';
        }
        
        if (label) {
            label.textContent = isDark ? 'Light Mode' : 'Dark Mode';
        }
        
        toggleButton.setAttribute('data-current-mode', isDark ? 'dark' : 'light');
        toggleButton.setAttribute('aria-label', isDark ? 'Switch to light mode' : 'Switch to dark mode');
    }
    
    /**
     * Handle system preference change
     */
    function handleSystemChange(e) {
        const newSystemPreference = e.matches ? 'dark' : 'light';
        
        if (systemPreference !== newSystemPreference) {
            systemPreference = newSystemPreference;
            
            // Only apply system change if user hasn't set explicit preference
            if (config.defaultMode === 'system' && !getCookie('xtube_dark_mode')) {
                applyMode(systemPreference);
                showNotification('System preference changed to ' + systemPreference + ' mode');
            }
        }
    }
    
    /**
     * Listen for system preference changes
     */
    function listenForSystemChanges() {
        // Listen for browser preference changes
        if (window.matchMedia) {
            const darkModeQuery = window.matchMedia('(prefers-color-scheme: dark)');
            
            if (darkModeQuery.addListener) {
                darkModeQuery.addListener(handleSystemChange);
            } else if (darkModeQuery.addEventListener) {
                darkModeQuery.addEventListener('change', handleSystemChange);
            }
        }
    }
    
    /**
     * Listen for custom events
     */
    function listenForCustomEvents() {
        // Listen for manual mode changes
        document.addEventListener('xtube:dark-mode-change', function(e) {
            if (e.detail && e.detail.mode) {
                applyMode(e.detail.mode);
            }
        });
        
        // Listen for toggle requests
        document.addEventListener('xtube:toggle-dark-mode', function() {
            handleToggleClick();
        });
    }
    
    /**
     * Update meta theme-color
     */
    function updateThemeColor(isDark) {
        let themeColor = isDark ? '#141414' : '#e50914';
        
        // Check for custom theme color
        const metaThemeColor = document.querySelector('meta[name="theme-color"]');
        if (metaThemeColor) {
            metaThemeColor.setAttribute('content', themeColor);
        }
    }
    
    /**
     * Send AJAX toggle request
     */
    function sendToggleRequest(mode) {
        if (!xtube_dark_mode_vars || !xtube_dark_mode_vars.ajax_url) {
            return;
        }
        
        const formData = new FormData();
        formData.append('action', 'xtube_toggle_dark_mode');
        formData.append('mode', mode);
        formData.append('nonce', xtube_dark_mode_vars.nonce);
        
        fetch(xtube_dark_mode_vars.ajax_url, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data.message) {
                console.log(data.data.message);
            }
        })
        .catch(error => {
            console.log('Dark mode toggle request failed:', error);
        });
    }
    
    /**
     * Trigger mode change event
     */
    function triggerModeChange(mode) {
        const event = new CustomEvent('xtube:dark-mode-changed', {
            detail: { mode: mode, isDark: mode === 'dark' }
        });
        document.dispatchEvent(event);
    }
    
    /**
     * Show notification
     */
    function showNotification(message) {
        // Check if notifications are enabled
        if (window.xtubeNotifications) {
            window.xtubeNotifications.show(message);
            return;
        }
        
        // Create simple notification
        const notification = document.createElement('div');
        notification.className = 'xtube-dark-mode-notification';
        notification.textContent = message;
        notification.style.cssText = `
            position: fixed;
            top: 80px;
            right: 20px;
            background: var(--xtube-surface-dark, #141414);
            color: var(--xtube-text-dark, #ffffff);
            padding: 12px 20px;
            border-radius: 8px;
            z-index: 10000;
            font-size: 14px;
            font-family: 'Inter', 'Poppins', sans-serif;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transform: translateX(100%);
            transition: transform 0.3s ease;
        `;
        
        document.body.appendChild(notification);
        
        // Animate in
        requestAnimationFrame(function() {
            notification.style.transform = 'translateX(0)';
        });
        
        // Remove after delay
        setTimeout(function() {
            notification.style.transform = 'translateX(100%)';
            setTimeout(function() {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }, 2000);
    }
    
    /**
     * Cookie helpers
     */
    function setCookie(name, value, days) {
        const expires = new Date();
        expires.setTime(expires.getTime() + (days * 24 * 60 * 60 * 1000));
        document.cookie = name + '=' + value + ';expires=' + expires.toUTCString() + ';path=/;SameSite=Lax' + (location.protocol === 'https:' ? ';Secure' : '');
    }
    
    function getCookie(name) {
        const nameEQ = name + '=';
        const ca = document.cookie.split(';');
        
        for (let i = 0; i < ca.length; i++) {
            let c = ca[i];
            while (c.charAt(0) === ' ') {
                c = c.substring(1, c.length);
            }
            if (c.indexOf(nameEQ) === 0) {
                return c.substring(nameEQ.length, c.length);
            }
        }
        return null;
    }
    
    /**
     * Public API
     */
    return {
        init: init,
        toggle: function() {
            handleToggleClick();
        },
        setMode: function(mode) {
            if (mode === 'dark' || mode === 'light') {
                applyMode(mode);
            }
        },
        getMode: function() {
            return currentMode;
        },
        isDark: function() {
            return currentMode === 'dark';
        },
        isLight: function() {
            return currentMode === 'light';
        },
        getSystemPreference: function() {
            return systemPreference;
        },
        refresh: function() {
            detectSystemPreference();
            currentMode = getCurrentMode();
            applyMode(currentMode, false);
        }
    };
})();

// Auto-initialize if globals are available
if (typeof xtube_dark_mode_vars !== 'undefined') {
    document.addEventListener('DOMContentLoaded', function() {
        xtubeDarkMode.init(xtube_dark_mode_vars);
    });
}

// Add CSS for toggle button animation
const darkModeStyles = document.createElement('style');
darkModeStyles.textContent = `
    .dark-mode-toggle {
        transform: translateY(0);
        opacity: 1;
        transition: all 0.3s ease;
    }
    
    .dark-mode-toggle.toggling {
        transform: scale(0.95) translateY(-2px);
    }
    
    .dark-mode-toggle.toggling .icon {
        transform: rotate(360deg);
    }
    
    .no-transition *,
    .no-transition *::before,
    .no-transition *::after {
        transition: none !important;
    }
`;
document.head.appendChild(darkModeStyles);
