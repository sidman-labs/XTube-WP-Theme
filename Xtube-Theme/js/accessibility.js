/**
 * XTube Accessibility JavaScript v3.0
 * WCAG 2.1 AA compliance enhancements
 * 
 * @package XTube
 * @version 3.0.0
 */

window.xtubeAccessibility = (function() {
    'use strict';
    
    let config = {};
    let focusOutlineVisible = true;
    let keyboardNavigation = true;
    let srAnnouncements = true;
    
    /**
     * Initialize accessibility features
     */
    function init(options) {
        config = Object.assign({
            focus_outline_visible: true,
            keyboard_navigation: true,
            screen_reader_announcements: true
        }, options);
        
        focusOutlineVisible = config.focus_outline_visible;
        keyboardNavigation = config.keyboard_navigation;
        srAnnouncements = config.screen_reader_announcements;
        
        // Initialize features
        setupKeyboardNavigation();
        setupFocusManagement();
        setupScreenReaderSupport();
        setupAriaLiveRegions();
        setupSkipLinks();
        setupVideoAccessibility();
        setupFormAccessibility();
        setupMenuAccessibility();
        setupColorContrast();
        
        // Add event listeners
        document.addEventListener('keydown', handleKeyDown);
        document.addEventListener('mousedown', handleMouseDown);
        document.addEventListener('focusin', handleFocusIn);
    }
    
    /**
     * Setup keyboard navigation
     */
    function setupKeyboardNavigation() {
        if (!keyboardNavigation) return;
        
        // Add keyboard navigation patterns
        const focusableElements = [
            'a[href]',
            'button:not([disabled])',
            'input:not([disabled])',
            'select:not([disabled])',
            'textarea:not([disabled])',
            '[tabindex]:not([tabindex="-1"])',
            '[contenteditable="true"]'
        ].join(',');
        
        // Create keyboard navigation helper
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Tab') {
                // Handle tab trapping for modals
                handleTabTrapping(e);
            }
        });
    }
    
    /**
     * Setup focus management
     */
    function setupFocusManagement() {
        // Hide focus outline for mouse users
        if (!focusOutlineVisible) {
            document.body.classList.add('mouse-user');
        }
        
        // Add focus management for dynamic content
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'childList') {
                    mutation.addedNodes.forEach(function(node) {
                        if (node.nodeType === Node.ELEMENT_NODE) {
                            addFocusManagement(node);
                        }
                    });
                }
            });
        });
        
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    }
    
    /**
     * Setup screen reader support
     */
    function setupScreenReaderSupport() {
        if (!srAnnouncements) return;
        
        // Create screen reader announcement container
        const srContainer = document.createElement('div');
        srContainer.setAttribute('aria-live', 'polite');
        srContainer.setAttribute('aria-atomic', 'true');
        srContainer.className = 'sr-only sr-announcements';
        srContainer.id = 'sr-announcements';
        document.body.appendChild(srContainer);
        
        // Make announcement function globally available
        window.announceToScreenReader = announceToScreenReader;
    }
    
    /**
     * Setup ARIA live regions
     */
    function setupAriaLiveRegions() {
        // Add live regions for dynamic content
        const liveRegions = [
            { selector: '.video-player', type: 'polite', id: 'video-status' },
            { selector: '.search-results', type: 'assertive', id: 'search-results-status' },
            { selector: '.notification-area', type: 'polite', id: 'notifications' }
        ];
        
        liveRegions.forEach(function(region) {
            const element = document.querySelector(region.selector);
            if (element && !document.getElementById(region.id)) {
                const liveRegion = document.createElement('div');
                liveRegion.setAttribute('aria-live', region.type);
                liveRegion.setAttribute('aria-atomic', 'true');
                liveRegion.className = 'sr-only';
                liveRegion.id = region.id;
                element.appendChild(liveRegion);
            }
        });
    }
    
    /**
     * Setup skip links
     */
    function setupSkipLinks() {
        const skipLinks = document.querySelectorAll('.skip-link');
        
        skipLinks.forEach(function(link) {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const targetId = this.getAttribute('href').substring(1);
                const targetElement = document.getElementById(targetId);
                
                if (targetElement) {
                    targetElement.setAttribute('tabindex', '-1');
                    targetElement.focus();
                    
                    // Remove tabindex after focus
                    setTimeout(function() {
                        targetElement.removeAttribute('tabindex');
                    }, 100);
                    
                    // Announce to screen readers
                    announceToScreenReader('Navigated to ' + targetElement.textContent.trim());
                }
            });
        });
    }
    
    /**
     * Setup video accessibility
     */
    function setupVideoAccessibility() {
        const videos = document.querySelectorAll('.xtube-video-player, video');
        
        videos.forEach(function(video) {
            // Add comprehensive keyboard controls
            enhanceVideoKeyboardControls(video);
            
            // Add descriptive labels
            enhanceVideoLabels(video);
            
            // Add captions support
            setupVideoCaptions(video);
        });
    }
    
    /**
     * Setup form accessibility
     */
    function setupFormAccessibility() {
        const forms = document.querySelectorAll('form');
        
        forms.forEach(function(form) {
            enhanceFormAccessibility(form);
        });
        
        // Add validation announcements
        document.addEventListener('invalid', function(e) {
            const field = e.target;
            const message = field.validationMessage || 'This field is required';
            
            announceToScreenReader('Validation error: ' + message);
            
            // Focus the invalid field
            field.focus();
        }, true);
    }
    
    /**
     * Setup menu accessibility
     */
    function setupMenuAccessibility() {
        const menus = document.querySelectorAll('[role="navigation"]');
        
        menus.forEach(function(menu) {
            enhanceMenuAccessibility(menu);
        });
    }
    
    /**
     * Setup color contrast
     */
    function setupColorContrast() {
        // Check for high contrast mode
        if (window.matchMedia('(prefers-contrast: high)').matches) {
            document.body.classList.add('high-contrast');
        }
        
        // Listen for contrast preference changes
        window.matchMedia('(prefers-contrast: high)').addEventListener('change', function(e) {
            document.body.classList.toggle('high-contrast', e.matches);
        });
        
        // Add color contrast toggle for testing
        if (document.querySelector('.contrast-toggle')) {
            document.querySelector('.contrast-toggle').addEventListener('click', toggleHighContrast);
        }
    }
    
    /**
     * Handle keyboard events
     */
    function handleKeyDown(e) {
        // Escape key handling
        if (e.key === 'Escape') {
            handleEscapeKey(e);
        }
        
        // Arrow key navigation for custom components
        if (['ArrowUp', 'ArrowDown', 'ArrowLeft', 'ArrowRight'].includes(e.key)) {
            handleArrowNavigation(e);
        }
        
        // Enter/Space for interactive elements
        if (e.key === 'Enter' || e.key === ' ') {
            handleActivationKeys(e);
        }
    }
    
    /**
     * Handle mouse down (for focus outline)
     */
    function handleMouseDown() {
        if (!focusOutlineVisible) {
            document.body.classList.add('mouse-user');
        }
    }
    
    /**
     * Handle focus in
     */
    function handleFocusIn(e) {
        if (!focusOutlineVisible) {
            document.body.classList.remove('mouse-user');
        }
        
        // Announce focus changes for important elements
        const element = e.target;
        if (element.matches('.video-player, .notification, [role="alert"]')) {
            const label = element.getAttribute('aria-label') || element.textContent.trim();
            if (label) {
                announceToScreenReader('Focused on ' + label);
            }
        }
    }
    
    /**
     * Handle tab trapping
     */
    function handleTabTrapping(e) {
        const modal = document.querySelector('.modal.show, .dialog[open]');
        if (!modal) return;
        
        const focusableElements = modal.querySelectorAll(
            'a[href], button:not([disabled]), input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])'
        );
        
        if (focusableElements.length === 0) return;
        
        const firstElement = focusableElements[0];
        const lastElement = focusableElements[focusableElements.length - 1];
        
        if (e.shiftKey) {
            if (document.activeElement === firstElement) {
                e.preventDefault();
                lastElement.focus();
            }
        } else {
            if (document.activeElement === lastElement) {
                e.preventDefault();
                firstElement.focus();
            }
        }
    }
    
    /**
     * Handle escape key
     */
    function handleEscapeKey(e) {
        // Close modals
        const modal = document.querySelector('.modal.show');
        if (modal) {
            const closeButton = modal.querySelector('.modal-close, [data-dismiss="modal"]');
            if (closeButton) {
                closeButton.click();
            }
        }
        
        // Close dropdowns
        const dropdowns = document.querySelectorAll('.dropdown.show');
        dropdowns.forEach(function(dropdown) {
            dropdown.classList.remove('show');
        });
    }
    
    /**
     * Handle arrow navigation
     */
    function handleArrowNavigation(e) {
        const target = e.target;
        
        // Menu navigation
        if (target.matches('.menu-item a, .dropdown-item')) {
            navigateMenu(e);
        }
        
        // Tab navigation
        if (target.matches('[role="tab"]')) {
            navigateTabs(e);
        }
        
        // Listbox navigation
        if (target.matches('[role="option"]')) {
            navigateListbox(e);
        }
    }
    
    /**
     * Handle activation keys
     */
    function handleActivationKeys(e) {
        const target = e.target;
        
        // Prevent default for space key on non-input elements
        if (e.key === ' ' && !target.matches('input, textarea')) {
            e.preventDefault();
        }
        
        // Activate custom elements
        if (target.matches('[role="button"], [role="tab"], [role="option"]')) {
            target.click();
        }
    }
    
    /**
     * Enhance video keyboard controls
     */
    function enhanceVideoKeyboardControls(video) {
        video.addEventListener('keydown', function(e) {
            switch(e.key) {
                case ' ':
                case 'Spacebar':
                    e.preventDefault();
                    togglePlayPause(video);
                    break;
                case 'ArrowLeft':
                    e.preventDefault();
                    seekVideo(video, -10);
                    break;
                case 'ArrowRight':
                    e.preventDefault();
                    seekVideo(video, 10);
                    break;
                case 'ArrowUp':
                    e.preventDefault();
                    adjustVolume(video, 0.1);
                    break;
                case 'ArrowDown':
                    e.preventDefault();
                    adjustVolume(video, -0.1);
                    break;
                case 'f':
                case 'F':
                    e.preventDefault();
                    toggleFullscreen(video);
                    break;
                case 'c':
                case 'C':
                    e.preventDefault();
                    toggleCaptions(video);
                    break;
            }
        });
    }
    
    /**
     * Enhance video labels
     */
    function enhanceVideoLabels(video) {
        if (!video.getAttribute('aria-label')) {
            const title = video.getAttribute('title') || video.querySelector('.video-title')?.textContent || 'Video';
            video.setAttribute('aria-label', title + ' video player');
        }
        
        // Add status region
        const statusRegion = document.createElement('div');
        statusRegion.setAttribute('aria-live', 'polite');
        statusRegion.setAttribute('aria-atomic', 'true');
        statusRegion.className = 'sr-only video-status';
        video.parentNode.appendChild(statusRegion);
    }
    
    /**
     * Setup video captions
     */
    function setupVideoCaptions(video) {
        // Check for caption tracks
        const tracks = video.textTracks;
        if (tracks.length > 0) {
            // Enable captions by default for accessibility
            for (let i = 0; i < tracks.length; i++) {
                if (tracks[i].kind === 'captions' || tracks[i].kind === 'subtitles') {
                    tracks[i].mode = 'showing';
                    break;
                }
            }
        }
    }
    
    /**
     * Enhance form accessibility
     */
    function enhanceFormAccessibility(form) {
        const inputs = form.querySelectorAll('input, select, textarea');
        
        inputs.forEach(function(input) {
            // Ensure proper labeling
            if (!input.getAttribute('aria-label') && !input.getAttribute('aria-labelledby')) {
                const label = form.querySelector(`label[for="${input.id}"]`);
                if (label) {
                    input.setAttribute('aria-labelledby', label.id || 'label-' + input.id);
                    if (!label.id) {
                        label.id = 'label-' + input.id;
                    }
                }
            }
            
            // Add required field indicators
            if (input.required && !input.getAttribute('aria-required')) {
                input.setAttribute('aria-required', 'true');
            }
            
            // Add validation descriptions
            const errorElement = form.querySelector(`#${input.id}-error`);
            if (errorElement) {
                input.setAttribute('aria-describedby', errorElement.id);
            }
        });
    }
    
    /**
     * Enhance menu accessibility
     */
    function enhanceMenuAccessibility(menu) {
        const menuItems = menu.querySelectorAll('.menu-item');
        
        menuItems.forEach(function(item, index) {
            const link = item.querySelector('a');
            if (link) {
                // Add menu item context
                link.setAttribute('role', 'menuitem');
                link.setAttribute('tabindex', index === 0 ? '0' : '-1');
                
                // Add submenu indicators
                const submenu = item.querySelector('.sub-menu');
                if (submenu) {
                    link.setAttribute('aria-haspopup', 'true');
                    link.setAttribute('aria-expanded', 'false');
                    
                    // Toggle submenu on enter/space
                    link.addEventListener('keydown', function(e) {
                        if (e.key === 'Enter' || e.key === ' ') {
                            e.preventDefault();
                            toggleSubmenu(item);
                        }
                    });
                }
            }
        });
    }
    
    /**
     * Toggle high contrast mode
     */
    function toggleHighContrast() {
        document.body.classList.toggle('high-contrast');
        const isHighContrast = document.body.classList.contains('high-contrast');
        announceToScreenReader('High contrast mode ' + (isHighContrast ? 'enabled' : 'disabled'));
    }
    
    /**
     * Announce to screen reader
     */
    function announceToScreenReader(message) {
        if (!srAnnouncements) return;
        
        const announcement = document.getElementById('sr-announcements');
        if (announcement) {
            announcement.textContent = message;
            
            // Clear after announcement
            setTimeout(function() {
                announcement.textContent = '';
            }, 1000);
        }
    }
    
    /**
     * Video control helpers
     */
    function togglePlayPause(video) {
        if (video.paused) {
            video.play();
            announceToScreenReader('Video playing');
        } else {
            video.pause();
            announceToScreenReader('Video paused');
        }
    }
    
    function seekVideo(video, seconds) {
        video.currentTime = Math.max(0, Math.min(video.duration, video.currentTime + seconds));
        announceToScreenReader('Seeked ' + (seconds > 0 ? 'forward' : 'backward') + ' ' + Math.abs(seconds) + ' seconds');
    }
    
    function adjustVolume(video, change) {
        video.volume = Math.max(0, Math.min(1, video.volume + change));
        announceToScreenReader('Volume ' + Math.round(video.volume * 100) + '%');
    }
    
    function toggleFullscreen(video) {
        if (document.fullscreenElement) {
            document.exitFullscreen();
            announceToScreenReader('Exited fullscreen');
        } else {
            video.requestFullscreen();
            announceToScreenReader('Entered fullscreen');
        }
    }
    
    function toggleCaptions(video) {
        const tracks = video.textTracks;
        let captionsEnabled = false;
        
        for (let i = 0; i < tracks.length; i++) {
            if (tracks[i].kind === 'captions' || tracks[i].kind === 'subtitles') {
                tracks[i].mode = tracks[i].mode === 'showing' ? 'hidden' : 'showing';
                captionsEnabled = tracks[i].mode === 'showing';
            }
        }
        
        announceToScreenReader('Captions ' + (captionsEnabled ? 'enabled' : 'disabled'));
    }
    
    /**
     * Navigation helpers
     */
    function navigateMenu(e) {
        const menuItems = Array.from(e.target.closest('.menu').querySelectorAll('.menu-item a'));
        const currentIndex = menuItems.indexOf(e.target);
        
        let nextIndex;
        if (e.key === 'ArrowDown') {
            nextIndex = (currentIndex + 1) % menuItems.length;
        } else if (e.key === 'ArrowUp') {
            nextIndex = currentIndex === 0 ? menuItems.length - 1 : currentIndex - 1;
        }
        
        if (nextIndex !== undefined) {
            e.preventDefault();
            menuItems[nextIndex].focus();
        }
    }
    
    function navigateTabs(e) {
        const tabs = Array.from(e.target.closest('[role="tablist"]').querySelectorAll('[role="tab"]'));
        const currentIndex = tabs.indexOf(e.target);
        
        let nextIndex;
        if (e.key === 'ArrowRight') {
            nextIndex = (currentIndex + 1) % tabs.length;
        } else if (e.key === 'ArrowLeft') {
            nextIndex = currentIndex === 0 ? tabs.length - 1 : currentIndex - 1;
        }
        
        if (nextIndex !== undefined) {
            e.preventDefault();
            tabs[nextIndex].click();
            tabs[nextIndex].focus();
        }
    }
    
    function navigateListbox(e) {
        const options = Array.from(e.target.closest('[role="listbox"]').querySelectorAll('[role="option"]'));
        const currentIndex = options.indexOf(e.target);
        
        let nextIndex;
        if (e.key === 'ArrowDown') {
            nextIndex = Math.min(currentIndex + 1, options.length - 1);
        } else if (e.key === 'ArrowUp') {
            nextIndex = Math.max(currentIndex - 1, 0);
        }
        
        if (nextIndex !== undefined) {
            e.preventDefault();
            options[nextIndex].focus();
            options[nextIndex].click();
        }
    }
    
    /**
     * Toggle submenu
     */
    function toggleSubmenu(menuItem) {
        const link = menuItem.querySelector('a');
        const submenu = menuItem.querySelector('.sub-menu');
        const isExpanded = link.getAttribute('aria-expanded') === 'true';
        
        link.setAttribute('aria-expanded', !isExpanded);
        menuItem.classList.toggle('sub-menu-open', !isExpanded);
        
        if (!isExpanded) {
            // Focus first submenu item
            const firstSubmenuItem = submenu.querySelector('a');
            if (firstSubmenuItem) {
                firstSubmenuItem.focus();
            }
        }
    }
    
    /**
     * Add focus management to element
     */
    function addFocusManagement(element) {
        // Add focus management logic for dynamically added elements
        const focusableElements = element.querySelectorAll(
            'a[href], button:not([disabled]), input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])'
        );
        
        focusableElements.forEach(function(el) {
            if (!el.hasAttribute('tabindex')) {
                el.setAttribute('tabindex', '0');
            }
        });
    }
    
    /**
     * Public API
     */
    return {
        init: init,
        announce: announceToScreenReader,
        toggleHighContrast: toggleHighContrast,
        focusElement: function(element) {
            if (element) {
                element.focus();
                element.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        },
        trapFocus: function(container) {
            const focusableElements = container.querySelectorAll(
                'a[href], button:not([disabled]), input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])'
            );
            
            if (focusableElements.length > 0) {
                focusableElements[0].focus();
            }
        }
    };
})();

// Auto-initialize if globals are available
if (typeof xtube_a11y_vars !== 'undefined') {
    document.addEventListener('DOMContentLoaded', function() {
        xtubeAccessibility.init(xtube_a11y_vars);
    });
}

// Add screen reader only CSS class
const a11yStyles = document.createElement('style');
a11yStyles.textContent = `
    .sr-only {
        position: absolute !important;
        width: 1px !important;
        height: 1px !important;
        padding: 0 !important;
        margin: -1px !important;
        overflow: hidden !important;
        clip: rect(0, 0, 0, 0) !important;
        white-space: nowrap !important;
        border: 0 !important;
    }
    
    .mouse-user *:focus {
        outline: none !important;
    }
    
    .high-contrast {
        filter: contrast(1.5) !important;
    }
    
    .high-contrast * {
        border-color: currentColor !important;
        box-shadow: none !important;
    }
    
    /* Skip links */
    .skip-link {
        position: absolute !important;
        top: -40px !important;
        left: 6px !important;
        background: #000 !important;
        color: #fff !important;
        padding: 8px !important;
        text-decoration: none !important;
        z-index: 100000 !important;
        border-radius: 4px !important;
    }
    
    .skip-link:focus {
        top: 6px !important;
    }
    
    /* Focus indicators */
    *:focus {
        outline: 2px solid #005fcc !important;
        outline-offset: 2px !important;
    }
    
    /* Video player accessibility */
    .video-status {
        position: absolute;
        left: -10000px;
        width: 1px;
        height: 1px;
        overflow: hidden;
    }
`;
document.head.appendChild(a11yStyles);
