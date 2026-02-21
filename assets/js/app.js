/**
 * Main Application JavaScript
 * Delasof2026 PHP
 */

// Initialize app when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    console.log('App initialized');
    initTheme();
    initWishlist();
    initMobileMenu();
});

// ==================== Theme Management ====================

function initTheme() {
    const theme = localStorage.getItem('theme') || 'light';
    applyTheme(theme);
}

function applyTheme(theme) {
    if (theme === 'dark') {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }
    localStorage.setItem('theme', theme);
}

function toggleTheme() {
    const current = localStorage.getItem('theme') || 'light';
    const newTheme = current === 'light' ? 'dark' : 'light';
    applyTheme(newTheme);
}

// ==================== Wishlist Management ====================

function initWishlist() {
    const wishlist = JSON.parse(localStorage.getItem('wishlist') || '[]');
    updateWishlistUI(wishlist);
}

function addToWishlist(productId, productName) {
    let wishlist = JSON.parse(localStorage.getItem('wishlist') || '[]');
    
    // Check if product already in wishlist
    if (wishlist.some(p => p.id === productId)) {
        removeFromWishlist(productId);
        return;
    }
    
    wishlist.push({
        id: productId,
        name: productName,
        addedAt: new Date().toISOString()
    });
    
    localStorage.setItem('wishlist', JSON.stringify(wishlist));
    updateWishlistUI(wishlist);
    showNotification('เพิ่มไปยังรายการโปรดแล้ว', 'success');
}

function removeFromWishlist(productId) {
    let wishlist = JSON.parse(localStorage.getItem('wishlist') || '[]');
    wishlist = wishlist.filter(p => p.id !== productId);
    localStorage.setItem('wishlist', JSON.stringify(wishlist));
    updateWishlistUI(wishlist);
    showNotification('ลบออกจากรายการโปรดแล้ว', 'info');
}

function updateWishlistUI(wishlist) {
    const wishlistCount = document.getElementById('wishlist-count');
    if (wishlistCount) {
        wishlistCount.textContent = wishlist.length;
        wishlistCount.style.display = wishlist.length > 0 ? 'inline' : 'none';
    }
}

function isInWishlist(productId) {
    const wishlist = JSON.parse(localStorage.getItem('wishlist') || '[]');
    return wishlist.some(p => p.id === productId);
}

// ==================== Notifications ====================

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    
    const styles = {
        position: 'fixed',
        top: '20px',
        right: '20px',
        padding: '15px 20px',
        borderRadius: '8px',
        color: 'white',
        zIndex: '9999',
        animation: 'slideIn 0.3s ease-in-out',
        maxWidth: '300px'
    };
    
    const typeStyles = {
        success: { backgroundColor: '#10b981' },
        error: { backgroundColor: '#ef4444' },
        warning: { backgroundColor: '#f59e0b' },
        info: { backgroundColor: '#3b82f6' }
    };
    
    Object.assign(notification.style, styles, typeStyles[type] || typeStyles.info);
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'fadeIn 0.3s ease-in-out reverse';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// ==================== Mobile Menu ====================

function initMobileMenu() {
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    if (mobileMenuBtn) {
        mobileMenuBtn.addEventListener('click', toggleMobileMenu);
    }
}

function toggleMobileMenu() {
    const nav = document.querySelector('nav');
    if (nav) {
        nav.classList.toggle('hidden');
    }
}

// ==================== Search & Filter ====================

function handleSearch() {
    const keyword = document.getElementById('searchInput')?.value;
    if (keyword) {
        window.location.href = '/?keyword=' + encodeURIComponent(keyword);
    }
}

function handleSort(sortValue) {
    const keyword = new URLSearchParams(window.location.search).get('keyword') || '';
    window.location.href = '/?keyword=' + encodeURIComponent(keyword) + '&sort=' + encodeURIComponent(sortValue);
}

// ==================== Form Handling ====================

function handleFormSubmit(event) {
    event.preventDefault();
    const form = event.target;
    const formData = new FormData(form);
    
    // Show loading state
    const submitBtn = form.querySelector('button[type="submit"]');
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.textContent = 'กำลังประมวลผล...';
    }
    
    // Submit form
    form.submit();
}

// ==================== Utility Functions ====================

function formatPrice(price) {
    return new Intl.NumberFormat('th-TH', {
        style: 'currency',
        currency: 'THB'
    }).format(price);
}

function truncateText(text, length) {
    if (text.length > length) {
        return text.substring(0, length) + '...';
    }
    return text;
}

function getQueryParam(param) {
    const searchParams = new URLSearchParams(window.location.search);
    return searchParams.get(param);
}

// ==================== Event Listeners ====================

// Search input enter key
document.addEventListener('keypress', function(e) {
    if (e.target.id === 'searchInput' && e.key === 'Enter') {
        handleSearch();
    }
});

// Sort select change
document.addEventListener('change', function(e) {
    if (e.target.id === 'sortSelect') {
        handleSort(e.target.value);
    }
});

// ==================== Console Logging ====================

console.log('Delasof2026 PHP - App loaded successfully');
