/**
 * Cookie Manager
 * Manages cookie consent banner and preferences
 */

const CookieManager = {
  // Cookie names
  COOKIE_CONSENT_KEY: 'cookie_consent',
  COOKIE_PREFERENCES_KEY: 'cookie_preferences',
  
  // Cookie categories
  CATEGORIES: {
    essential: 'essential',
    analytics: 'analytics',
    marketing: 'marketing'
  },

  /**
   * Initialize cookie manager
   */
  init() {
    this.checkConsent();
    this.attachEventListeners();
  },

  /**
   * Check if user has already given consent
   */
  checkConsent() {
    const consent = this.getConsent();
    
    if (!consent) {
      // Show banner if no consent given
      this.showBanner();
    } else {
      // Hide banner if consent already given
      this.hideBanner();
    }
  },

  /**
   * Get consent from localStorage
   */
  getConsent() {
    try {
      return localStorage.getItem(this.COOKIE_CONSENT_KEY);
    } catch (e) {
      return null;
    }
  },

  /**
   * Get cookie preferences from localStorage
   */
  getPreferences() {
    try {
      const prefs = localStorage.getItem(this.COOKIE_PREFERENCES_KEY);
      return prefs ? JSON.parse(prefs) : null;
    } catch (e) {
      return null;
    }
  },

  /**
   * Save consent to localStorage
   */
  saveConsent(preferences = null) {
    try {
      localStorage.setItem(this.COOKIE_CONSENT_KEY, 'true');
      
      if (preferences) {
        localStorage.setItem(this.COOKIE_PREFERENCES_KEY, JSON.stringify(preferences));
      }
      
      this.hideBanner();
      this.hideModal();
    } catch (e) {
      console.error('Error saving cookie consent:', e);
    }
  },

  /**
   * Show cookie banner
   */
  showBanner() {
    const banner = document.getElementById('cookie-banner');
    if (banner) {
      banner.style.display = 'block';
    }
  },

  /**
   * Hide cookie banner
   */
  hideBanner() {
    const banner = document.getElementById('cookie-banner');
    if (banner) {
      banner.style.display = 'none';
    }
  },

  /**
   * Show cookie management modal
   */
  showModal() {
    const modal = document.getElementById('cookie-management-modal');
    if (modal) {
      modal.style.display = 'block';
      document.body.style.overflow = 'hidden';
      
      // Load current preferences
      this.loadPreferences();
    }
  },

  /**
   * Hide cookie management modal
   */
  hideModal() {
    const modal = document.getElementById('cookie-management-modal');
    if (modal) {
      modal.style.display = 'none';
      document.body.style.overflow = '';
    }
  },

  /**
   * Load preferences into modal checkboxes
   */
  loadPreferences() {
    const preferences = this.getPreferences();
    
    if (preferences) {
      // Load saved preferences
      document.getElementById('analytics-cookies').checked = preferences.analytics || false;
      document.getElementById('marketing-cookies').checked = preferences.marketing || false;
    } else {
      // Default: all accepted
      document.getElementById('analytics-cookies').checked = true;
      document.getElementById('marketing-cookies').checked = true;
    }
    
    // Essential cookies are always checked and disabled
    document.getElementById('essential-cookies').checked = true;
  },

  /**
   * Accept all cookies
   */
  acceptAll() {
    const preferences = {
      essential: true,
      analytics: true,
      marketing: true
    };
    
    this.saveConsent(preferences);
  },

  /**
   * Reject all non-essential cookies
   */
  rejectAll() {
    const preferences = {
      essential: true,
      analytics: false,
      marketing: false
    };
    
    // Update checkboxes
    document.getElementById('analytics-cookies').checked = false;
    document.getElementById('marketing-cookies').checked = false;
    
    this.saveConsent(preferences);
  },

  /**
   * Save current preferences from modal
   */
  savePreferences() {
    const preferences = {
      essential: true, // Always true
      analytics: document.getElementById('analytics-cookies').checked,
      marketing: document.getElementById('marketing-cookies').checked
    };
    
    this.saveConsent(preferences);
  },

  /**
   * Attach event listeners
   */
  attachEventListeners() {
    // Accept All button
    const acceptAllBtn = document.getElementById('accept-all-cookies');
    if (acceptAllBtn) {
      acceptAllBtn.addEventListener('click', (e) => {
        e.preventDefault();
        this.acceptAll();
      });
    }

    // Manage Cookies button
    const manageBtn = document.getElementById('manage-cookies-btn');
    if (manageBtn) {
      manageBtn.addEventListener('click', (e) => {
        e.preventDefault();
        this.showModal();
      });
    }

    // Close modal button
    const closeModalBtn = document.getElementById('close-cookie-modal');
    if (closeModalBtn) {
      closeModalBtn.addEventListener('click', () => {
        this.hideModal();
      });
    }

    // Save preferences button
    const saveBtn = document.getElementById('save-cookie-preferences');
    if (saveBtn) {
      saveBtn.addEventListener('click', () => {
        this.savePreferences();
      });
    }

    // Reject all button
    const rejectBtn = document.getElementById('reject-all-cookies');
    if (rejectBtn) {
      rejectBtn.addEventListener('click', () => {
        this.rejectAll();
      });
    }

    // Close modal on overlay click
    const modal = document.getElementById('cookie-management-modal');
    if (modal) {
      const overlay = modal.querySelector('.cookie-modal-overlay');
      if (overlay) {
        overlay.addEventListener('click', () => {
          this.hideModal();
        });
      }
    }

    // Close modal on Escape key
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') {
        const modal = document.getElementById('cookie-management-modal');
        if (modal && modal.style.display === 'block') {
          this.hideModal();
        }
      }
    });
  },

  /**
   * Check if a specific cookie category is allowed
   */
  isCategoryAllowed(category) {
    const preferences = this.getPreferences();
    
    if (!preferences) {
      return false; // No consent given
    }
    
    // Essential cookies are always allowed
    if (category === this.CATEGORIES.essential) {
      return true;
    }
    
    return preferences[category] === true;
  }
};

// Initialize when DOM is ready
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', () => {
    CookieManager.init();
  });
} else {
  CookieManager.init();
}

// Export for global access
window.CookieManager = CookieManager;

