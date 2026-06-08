// ...existing code...

/**
 * Set up navigation interaction effects
 */
function setupNavigation() {
    const navLinks = document.querySelectorAll('.nav-link');
    
    navLinks.forEach(function(link) {
        // Make sure all transitions are properly applied
        link.addEventListener('mouseover', function() {
            link.classList.add('hover');
        });

        link.addEventListener('mouseout', function() {
            link.classList.remove('hover');
        });

        link.addEventListener('click', function(event) {
            event.preventDefault();
            navLinks.forEach(function(navLink) {
                navLink.classList.remove('active');
            });
            link.classList.add('active');
        });
    });
}

/**
 * Create floating particles in the background
 * @param {HTMLElement} container - The container element for particles
 */
function createParticles(container) {
    const particleCount = 30;
    const documentFragment = document.createDocumentFragment();
    
    for (let i = 0; i < particleCount; i++) {
        const particle = document.createElement('div');
        particle.className = 'particle';
        
        // Generate random properties for natural movement
        const randomLeft = Math.floor(Math.random() * 100);
        const randomOpacity = (Math.random() * 0.5 + 0.1).toFixed(2);
        const randomDuration = Math.floor(Math.random() * 10 + 10);
        const randomDelay = Math.floor(Math.random() * 5);
        
        // Apply styles in a way compatible with older browsers
        particle.style.left = randomLeft + '%';
        particle.style.opacity = randomOpacity;
        
        // Apply animation with proper prefixes
        applyAnimation(
            particle, 
            'moveUpDown', 
            randomDuration + 's', 
            'linear', 
            'infinite', 
            randomDelay + 's'
        );
        
        documentFragment.appendChild(particle);
    }
    
    // Add all particles at once for better performance
    container.appendChild(documentFragment);
}

// Wait for the DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    try {
        // Get background animation container
        const bgAnimation = document.getElementById('bg-animation');
        if (!bgAnimation) {
            console.warn('Background animation container not found');
            return;
        }
        
        // Create particle elements with browser compatibility checks
        createParticles(bgAnimation);
        
        // Setup navigation effects
        setupNavigation();
        
        // For older browsers, add a class to enable fallback styles
        if (!supportsModernFeatures()) {
            document.body.classList.add('legacy-browser');
        }
    } catch (error) {
        console.error('Error initializing animations:', error);
        // Fallback to simpler experience if animations fail
        document.body.classList.add('no-animation');
    }
});

// ...existing code...