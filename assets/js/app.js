/**
 * Main Application JavaScript
 */

// Initialize app
document.addEventListener('DOMContentLoaded', function() {
    initTheme();
    initWishlist();
});

// Theme Management
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

// Wishlist Management
function initWishlist() {
    const wishlist = JSON.parse(localStorage.getItem('wishlist') || '[]');
    updateWishlistUI(wishlist);
}

function addToWishlist(product) {
    let wishlist = JSON.parse(localStorage.getItem('wishlist') || '[]');
    
    // Check if product already in wishlist
    if (wishlist.some(p => p.product_id === product.product_id)) {
        removeFromWishlist(product.product_id);
        return;
    }
    
    wishlist.push(product);
    localStorage.setItem('wishlist', JSON.stringify(wishlist));
    updateWishlistUI(wishlist);
    showNotification('เพิ่มไปยังรายการโปรดแล้ว', 'success');
}

function removeFromWishlist(productId) {
    let wishlist = JSON.parse(localStorage.getItem('wishlist') || '[]');
    wishlist = wishlist.filter(p => p.product_id != productId);
    localStorage.setItem('wishlist', JSON.stringify(wishlist));
    updateWishlistUI(wishlist);
    showNotification('ลบออกจากรายการโปรดแล้ว', 'info');
}

function updateWishlistUI(wishlist) {
    const badge = document.querySelector('.wishlist-badge');
    if (badge) {
        badge.textContent = wishlist.length;
        badge.style.display = wishlist.length > 0 ? 'block' : 'none';
    }
}

function isInWishlist(productId) {
    const wishlist = JSON.parse(localStorage.getItem('wishlist') || '[]');
    return wishlist.some(p => p.product_id === productId);
}

// Notifications
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        bottom: 20px;
        right: 20px;
        padding: 1rem;
        border-radius: 0.5rem;
        color: white;
        z-index: 1000;
        animation: slideIn 0.3s ease;
    `;
    
    if (type === 'success') {
        notification.style.backgroundColor = '#10b981';
    } else if (type === 'error') {
        notification.style.backgroundColor = '#ef4444';
    } else {
        notification.style.backgroundColor = '#3b82f6';
    }
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'fadeOut 0.3s ease';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// API Helpers
async function apiCall(endpoint, options = {}) {
    const method = options.method || 'GET';
    const headers = options.headers || { 'Content-Type': 'application/json' };
    const body = options.body ? JSON.stringify(options.body) : undefined;
    
    try {
        const response = await fetch(endpoint, {
            method,
            headers,
            body
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        return await response.json();
    } catch (error) {
        console.error('API Error:', error);
        showNotification('เกิดข้อผิดพลาด: ' + error.message, 'error');
        throw error;
    }
}

// Utility Functions
function formatPrice(price, currency = 'THB') {
    if (currency === 'THB') {
        return '฿' + new Intl.NumberFormat('th-TH').format(price);
    }
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: currency
    }).format(price);
}

function truncateText(text, length = 100) {
    if (text.length <= length) return text;
    return text.substring(0, length) + '...';
}

function slugify(text) {
    return text
        .toLowerCase()
        .replace(/\s+/g, '-')
        .replace(/[^\w-]/g, '')
        .replace(/-+/g, '-')
        .trim('-');
}

// Export functions for global use
window.addToWishlist = addToWishlist;
window.removeFromWishlist = removeFromWishlist;
window.isInWishlist = isInWishlist;
window.toggleTheme = toggleTheme;
window.apiCall = apiCall;
window.formatPrice = formatPrice;
window.showNotification = showNotification;
