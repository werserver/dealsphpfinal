<?php
/**
 * Wishlist Page
 */

$config = Config::get();

ob_start();
?>

<div class="container mx-auto px-4 py-12">
    <h1 class="text-4xl font-bold mb-8">รายการโปรด</h1>
    
    <div id="wishlistContainer" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Wishlist items will be loaded by JavaScript -->
    </div>

    <div id="emptyWishlist" class="text-center py-12">
        <p class="text-xl text-gray-600 mb-4">ยังไม่มีสินค้าในรายการโปรด</p>
        <a href="/" class="bg-blue-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-blue-700 transition">
            ไปช้อปปิ้ง
        </a>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const wishlist = JSON.parse(localStorage.getItem('wishlist') || '[]');
    const container = document.getElementById('wishlistContainer');
    const emptyMsg = document.getElementById('emptyWishlist');

    if (wishlist.length === 0) {
        container.style.display = 'none';
        emptyMsg.style.display = 'block';
        return;
    }

    container.style.display = 'grid';
    emptyMsg.style.display = 'none';

    wishlist.forEach(product => {
        const card = document.createElement('div');
        card.className = 'bg-white rounded-lg shadow-sm hover:shadow-lg transition overflow-hidden';
        card.innerHTML = `
            <div class="relative pb-full">
                <img src="${product.product_image || '/assets/images/placeholder.svg'}" 
                     alt="${product.product_name}"
                     class="w-full h-48 object-cover">
            </div>
            <div class="p-4">
                <h3 class="font-bold text-sm mb-2 line-clamp-2">${product.product_name}</h3>
                <div class="flex items-center gap-2 mb-3">
                    <span class="text-yellow-400">★</span>
                    <span class="text-sm font-medium">${product.product_rating}</span>
                </div>
                <div class="mb-3">
                    <div class="text-lg font-bold text-red-600">
                        ฿${product.product_discounted || product.product_price}
                    </div>
                </div>
                <div class="flex gap-2">
                    <a href="${product.cloaked_url || product.product_url}" 
                       target="_blank" rel="noopener noreferrer"
                       class="flex-1 bg-blue-600 text-white py-2 rounded-lg text-sm font-bold hover:bg-blue-700 transition text-center">
                        ดูสินค้า
                    </a>
                    <button onclick="removeFromWishlist('${product.product_id}')" 
                            class="bg-red-600 text-white px-3 py-2 rounded-lg hover:bg-red-700 transition">
                        ❌
                    </button>
                </div>
            </div>
        `;
        container.appendChild(card);
    });
});

function removeFromWishlist(productId) {
    let wishlist = JSON.parse(localStorage.getItem('wishlist') || '[]');
    wishlist = wishlist.filter(p => p.product_id != productId);
    localStorage.setItem('wishlist', JSON.stringify(wishlist));
    location.reload();
}
</script>

<?php
$content = ob_get_clean();
require_once SRC_PATH . '/components/layout.php';
renderLayout($content, 'รายการโปรด');
?>
