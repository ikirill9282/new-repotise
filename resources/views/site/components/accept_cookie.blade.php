<section class="file_cookie" id="cookie-banner" style="display: none;">
  <div class="container !mx-auto">
      <div class="about_block">
          <h3>We use cookies to enhance your experience. See our <a href="{{ url('/policies/cookie-policy') }}">Cookie Policy</a> for more details.</h3>
          <div class="right_block">
              <a href="javascript:void(0)" class="oll_products" id="accept-all-cookies">Accept All</a>
              <a href="javascript:void(0)" class="cookie_management" id="manage-cookies-btn">Manage Cookies</a>
          </div>
      </div>
  </div>
</section>

<!-- Cookie Management Modal -->
<div id="cookie-management-modal" class="cookie-modal" style="display: none;">
  <div class="cookie-modal-overlay"></div>
  <div class="cookie-modal-content">
    <div class="cookie-modal-header">
      <h2>Cookie Preferences</h2>
      <button class="cookie-modal-close" id="close-cookie-modal">&times;</button>
    </div>
    <div class="cookie-modal-body">
      <p class="cookie-modal-description">We use cookies to enhance your browsing experience, serve personalized content, and analyze our traffic. By clicking "Accept All", you consent to our use of cookies. You can manage your preferences below.</p>
      
      <div class="cookie-category">
        <div class="cookie-category-header">
          <div>
            <h3>Essential Cookies</h3>
            <p>These cookies are necessary for the website to function and cannot be switched off.</p>
          </div>
          <label class="cookie-toggle">
            <input type="checkbox" id="essential-cookies" checked disabled>
            <span class="cookie-slider"></span>
          </label>
        </div>
      </div>

      <div class="cookie-category">
        <div class="cookie-category-header">
          <div>
            <h3>Analytics Cookies</h3>
            <p>These cookies help us understand how visitors interact with our website.</p>
          </div>
          <label class="cookie-toggle">
            <input type="checkbox" id="analytics-cookies">
            <span class="cookie-slider"></span>
          </label>
        </div>
      </div>

      <div class="cookie-category">
        <div class="cookie-category-header">
          <div>
            <h3>Marketing Cookies</h3>
            <p>These cookies are used to deliver personalized advertisements.</p>
          </div>
          <label class="cookie-toggle">
            <input type="checkbox" id="marketing-cookies">
            <span class="cookie-slider"></span>
          </label>
        </div>
      </div>
    </div>
    <div class="cookie-modal-footer">
      <button class="cookie-btn-secondary" id="reject-all-cookies">Reject All</button>
      <button class="cookie-btn-primary" id="save-cookie-preferences">Save Preferences</button>
    </div>
  </div>
</div>