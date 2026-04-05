<?php
require_once dirname(__DIR__) . '/utils/auth_middleware.php';

$auth = auth();
$isLoggedIn = $auth->isAuthenticated();
$userData = $isLoggedIn ? $auth->getCurrentUser() : null;

$userName = $userData['name'] ?? 'Guest';
$userRole = $userData['role'] ?? null;
$userAvatar = $userData['avatar'] ?? '/assets/images/default/avatar.png';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TrustLink - Verified Marketplace for Food & Services in Kenya</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Additional styles for enhanced features */
        .hero {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
            position: relative;
            overflow: hidden;
        }
        .live-activity {
            background: rgba(0,0,0,0.7);
            color: white;
            padding: 8px 0;
            font-size: 0.85rem;
            overflow: hidden;
            white-space: nowrap;
        }
        .ticker {
            display: inline-block;
            animation: ticker 30s linear infinite;
        }
        @keyframes ticker {
            0% { transform: translateX(100%); }
            100% { transform: translateX(-100%); }
        }
        .stats-section {
            background: var(--gray-100);
            padding: 60px 0;
        }
        .stats-grid {
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
            gap: 30px;
            text-align: center;
        }
        .stat-item h3 {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 10px;
        }
        .product-carousel {
            position: relative;
            overflow: hidden;
        }
        .carousel-container {
            display: flex;
            gap: 20px;
            overflow-x: auto;
            scroll-behavior: smooth;
            padding: 10px 0;
            scrollbar-width: thin;
        }
        .carousel-container::-webkit-scrollbar {
            height: 6px;
        }
        .carousel-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: white;
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            cursor: pointer;
            box-shadow: var(--shadow-md);
            z-index: 10;
        }
        .carousel-btn.prev { left: -20px; }
        .carousel-btn.next { right: -20px; }
        @media (max-width: 768px) {
            .carousel-btn { display: none; }
        }
        .skeleton-card {
            background: #e0e0e0;
            border-radius: 12px;
            height: 280px;
            animation: pulse 1.5s infinite;
        }
        @keyframes pulse {
            0% { opacity: 0.6; }
            50% { opacity: 1; }
            100% { opacity: 0.6; }
        }
        .flash-sale {
            background: #ff9800;
            color: white;
            padding: 2px 8px;
            border-radius: 20px;
            font-size: 0.7rem;
            display: inline-block;
        }
        .category-card {
            text-align: center;
            cursor: pointer;
            transition: transform 0.2s;
        }
        .category-card:hover {
            transform: translateY(-5px);
        }
        .category-icon {
            font-size: 3rem;
            margin-bottom: 10px;
        }
        .categories-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }
        .product-rating {
            display: flex;
            align-items: center;
            gap: 4px;
            font-size: 0.75rem;
            color: #f5b642;
        }
        .timer {
            font-weight: bold;
            background: #000;
            color: #fff;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 0.8rem;
        }
    </style>
</head>
<body>

<!-- Live Activity Ticker -->
<div class="live-activity">
    <div class="container ticker" id="liveTicker">
        <!-- Activities will be inserted via JavaScript -->
    </div>
</div>

<nav class="navbar">
    <div class="container">
        <div class="nav-brand">
            <a href="index.php">
                <span class="logo-icon">🌾</span>
                <span class="logo-text">TrustLink</span>
            </a>
        </div>
        <div class="nav-links">
            <a href="products.php" class="nav-link">Browse Products</a>
            <a href="how-it-works.php" class="nav-link">How It Works</a>
            <?php if ($isLoggedIn): ?>
                <div class="dropdown">
                    <button class="dropdown-btn">
                        <img src="<?php echo $userAvatar; ?>" alt="Avatar" class="avatar-small">
                        <span><?php echo htmlspecialchars($userName); ?></span>
                        <span class="dropdown-icon">▼</span>
                    </button>
                    <div class="dropdown-content">
                        <a href="dashboard.php">Dashboard</a>
                        <a href="profile.php">Profile</a>
                        <?php if ($userRole === 'buyer'): ?> <a href="my-orders.php">My Orders</a> <?php endif; ?>
                        <?php if ($userRole === 'farmer'): ?>
                            <a href="received-orders.php">Received Orders</a>
                            <a href="add-product.php">Add Product</a>
                        <?php endif; ?>
                        <?php if ($userRole === 'admin'): ?> <a href="admin.php">Admin Panel</a> <?php endif; ?>
                        <hr>
                        <a href="logout.php">Logout</a>
                    </div>
                </div>
            <?php else: ?>
                <a href="login.php" class="btn btn-outline">Login</a>
                <a href="register.php" class="btn btn-primary">Sign Up</a>
            <?php endif; ?>
        </div>
        <button class="mobile-menu-btn" id="mobileMenuBtn">☰</button>
    </div>
</nav>

<section class="hero">
    <div class="container">
        <div class="hero-content">
            <h1 class="hero-title">Trusted Marketplace for<br><span class="highlight">Kenyan Farmers & Services</span></h1>
            <p class="hero-subtitle">
                Buy fresh, verified produce directly from farmers. Hire trusted service providers with confidence.
                Every seller is verified, every product is traceable.
            </p>
            <div class="hero-buttons">
                <a href="products.php" class="btn btn-primary btn-large">Start Shopping</a>
                <?php if (!$isLoggedIn): ?>
                    <a href="register.php?role=farmer" class="btn btn-outline btn-large">Sell on TrustLink</a>
                <?php else: ?>
                    <a href="dashboard.php" class="btn btn-outline btn-large">My Dashboard</a>
                <?php endif; ?>
            </div>
            <div class="trust-badges">
                <span>✓ Verified Sellers</span>
                <span>✓ Trust Score System</span>
                <span>✓ Direct from Farm</span>
                <span>✓ Fair Prices</span>
            </div>
        </div>
    </div>
</section>

<!-- Animated Stats Section -->
<section class="stats-section">
    <div class="container">
        <div class="stats-grid">
            <div class="stat-item">
                <h3 id="farmersCount">0</h3>
                <p>Verified Farmers</p>
            </div>
            <div class="stat-item">
                <h3 id="ordersCount">0</h3>
                <p>Orders Delivered</p>
            </div>
            <div class="stat-item">
                <h3 id="productsCount">0</h3>
                <p>Products Listed</p>
            </div>
            <div class="stat-item">
                <h3 id="buyersCount">0</h3>
                <p>Happy Buyers</p>
            </div>
        </div>
    </div>
</section>

<!-- Categories Section -->
<section class="categories">
    <div class="container">
        <h2 class="section-title">Shop by Category</h2>
        <div class="categories-grid" id="categoriesGrid">
            <div class="category-card" data-category="vegetables"><div class="category-icon">🥬</div><p>Vegetables</p></div>
            <div class="category-card" data-category="fruits"><div class="category-icon">🍎</div><p>Fruits</p></div>
            <div class="category-card" data-category="dairy"><div class="category-icon">🥛</div><p>Dairy</p></div>
            <div class="category-card" data-category="grains"><div class="category-icon">🌾</div><p>Grains</p></div>
            <div class="category-card" data-category="poultry"><div class="category-icon">🐔</div><p>Poultry</p></div>
            <div class="category-card" data-category="other"><div class="category-icon">📦</div><p>Other</p></div>
        </div>
    </div>
</section>

<!-- Flash Sale Section -->
<section class="flash-sale-section">
    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h2 class="section-title">🔥 Flash Sale</h2>
            <div class="timer" id="flashTimer">Ends in 00:00:00</div>
        </div>
        <div id="flashProducts" class="products-grid">
            <div class="skeleton-card"></div>
            <div class="skeleton-card"></div>
            <div class="skeleton-card"></div>
            <div class="skeleton-card"></div>
        </div>
    </div>
</section>

<!-- Featured Products Carousel -->
<section class="featured-products">
    <div class="container">
        <h2 class="section-title">Fresh from Our Farmers</h2>
        <div class="product-carousel">
            <button class="carousel-btn prev" id="carouselPrev">‹</button>
            <div class="carousel-container" id="featuredCarousel">
                <!-- Skeleton items will be loaded here -->
            </div>
            <button class="carousel-btn next" id="carouselNext">›</button>
        </div>
        <div class="text-center mt-4"><a href="products.php" class="btn btn-outline">View All Products</a></div>
    </div>
</section>

<!-- Personalized Recommendations -->
<section class="recommendations">
    <div class="container">
        <h2 class="section-title"><?php echo $isLoggedIn ? 'Recommended for You' : 'Popular Products'; ?></h2>
        <div id="recommendedProducts" class="products-grid">
            <div class="skeleton-card"></div>
            <div class="skeleton-card"></div>
            <div class="skeleton-card"></div>
            <div class="skeleton-card"></div>
        </div>
    </div>
</section>

<footer class="footer">
    <div class="container">
        <div class="footer-content">
            <div class="footer-section">
                <h3>TrustLink</h3>
                <p>Verified marketplace for food and services in Kenya.</p>
            </div>
            <div class="footer-section">
                <h4>Quick Links</h4>
                <a href="products.php">Browse Products</a>
                <a href="how-it-works.php">How It Works</a>
            </div>
            <div class="footer-section">
                <h4>For Farmers</h4>
                <a href="register.php?role=farmer">Start Selling</a>
            </div>
            <div class="footer-section">
                <h4>Contact</h4>
                <p>📞 0700 000 000</p>
                <p>✉️ support@trustlink.ke</p>
            </div>
        </div>
        <div class="footer-bottom"><p>&copy; 2026 TrustLink. All rights reserved.</p></div>
    </div>
</footer>

<script src="../assets/js/api.js"></script>
<script src="../assets/js/app.js"></script>
<script>
    // Helper: format price
    function formatPrice(price) {
        return new Intl.NumberFormat().format(price);
    }

    // Helper: render stars (simplified)
    function renderStars(rating) {
        const full = Math.floor(rating);
        let stars = '';
        for (let i = 0; i < full; i++) stars += '⭐';
        for (let i = full; i < 5; i++) stars += '☆';
        return stars;
    }

    // Load featured products (carousel)
    async function loadFeaturedProducts() {
        const container = document.getElementById('featuredCarousel');
        container.innerHTML = '<div class="skeleton-card"></div><div class="skeleton-card"></div><div class="skeleton-card"></div><div class="skeleton-card"></div>';
        try {
            const response = await API.get('/products/get_products.php?per_page=8&sort=newest');
            if (response.success && response.data) {
                container.innerHTML = response.data.map(product => `
                    <div class="product-card" style="min-width: 250px;" onclick="location.href='product-detail.php?id=${product.id}'">
                        <img src="${product.primary_image || '../assets/images/default/product-placeholder.jpg'}" alt="${product.name}" class="product-image">
                        <div class="product-info">
                            <h3>${product.name}</h3>
                            <div class="product-price">KES ${formatPrice(product.price)} / ${product.unit_abbr}</div>
                            <div class="product-rating">${renderStars(product.avg_rating || 0)} ${product.avg_rating ? product.avg_rating.toFixed(1) : 'No ratings'}</div>
                        </div>
                    </div>
                `).join('');
            }
        } catch (e) { console.error(e); }
    }

    // Load flash sale products (with discount logic)
    async function loadFlashProducts() {
        const container = document.getElementById('flashProducts');
        container.innerHTML = '<div class="skeleton-card"></div><div class="skeleton-card"></div><div class="skeleton-card"></div><div class="skeleton-card"></div>';
        try {
            const response = await API.get('/products/get_products.php?per_page=4&sort=newest');
            if (response.success && response.data) {
                // For demo, we'll apply a random discount to the first 4 products (in real app, you'd have a flash sale flag)
                container.innerHTML = response.data.slice(0,4).map(product => {
                    const originalPrice = product.price;
                    const discountedPrice = originalPrice * 0.7; // 30% off
                    return `
                        <div class="product-card" onclick="location.href='product-detail.php?id=${product.id}'">
                            <span class="flash-sale">-30%</span>
                            <img src="${product.primary_image || '../assets/images/default/product-placeholder.jpg'}" class="product-image">
                            <div class="product-info">
                                <h3>${product.name}</h3>
                                <div class="product-price">
                                    <span style="text-decoration: line-through; color: gray;">KES ${formatPrice(originalPrice)}</span>
                                    <span style="color: red;"> KES ${formatPrice(discountedPrice)}</span>
                                </div>
                                <div class="product-rating">${renderStars(product.avg_rating || 0)}</div>
                            </div>
                        </div>
                    `;
                }).join('');
            }
        } catch (e) { console.error(e); }
    }

    // Load personalized recommendations
    async function loadRecommendations() {
        const container = document.getElementById('recommendedProducts');
        container.innerHTML = '<div class="skeleton-card"></div><div class="skeleton-card"></div><div class="skeleton-card"></div><div class="skeleton-card"></div>';
        try {
            // For now, fetch popular products (based on rating or sales). Could later use user's history.
            const response = await API.get('/products/get_products.php?per_page=4&sort=trust_desc');
            if (response.success && response.data) {
                container.innerHTML = response.data.map(product => `
                    <div class="product-card" onclick="location.href='product-detail.php?id=${product.id}'">
                        <img src="${product.primary_image || '../assets/images/default/product-placeholder.jpg'}" class="product-image">
                        <div class="product-info">
                            <h3>${product.name}</h3>
                            <div class="product-price">KES ${formatPrice(product.price)} / ${product.unit_abbr}</div>
                            <div class="product-rating">${renderStars(product.avg_rating || 0)} ${product.avg_rating ? product.avg_rating.toFixed(1) : 'New'}</div>
                        </div>
                    </div>
                `).join('');
            }
        } catch (e) { console.error(e); }
    }

    // Animated counters (with Intersection Observer)
    function animateCounter(element, target) {
        let current = 0;
        const step = Math.ceil(target / 50);
        const interval = setInterval(() => {
            current += step;
            if (current >= target) {
                element.innerText = target.toLocaleString();
                clearInterval(interval);
            } else {
                element.innerText = current.toLocaleString();
            }
        }, 30);
    }

    function startCounters() {
        const farmersEl = document.getElementById('farmersCount');
        const ordersEl = document.getElementById('ordersCount');
        const productsEl = document.getElementById('productsCount');
        const buyersEl = document.getElementById('buyersCount');
        // Fetch real counts from API or use dummy for now
        // For demo, set dummy values (replace with API call later)
        animateCounter(farmersEl, 1250);
        animateCounter(ordersEl, 5400);
        animateCounter(productsEl, 3420);
        animateCounter(buyersEl, 2890);
    }

    // Intersection Observer to trigger counters when stats section is visible
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                startCounters();
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.5 });
    observer.observe(document.querySelector('.stats-section'));

    // Carousel scrolling
    const carousel = document.getElementById('featuredCarousel');
    const prevBtn = document.getElementById('carouselPrev');
    const nextBtn = document.getElementById('carouselNext');
    if (carousel && prevBtn && nextBtn) {
        prevBtn.addEventListener('click', () => {
            carousel.scrollBy({ left: -300, behavior: 'smooth' });
        });
        nextBtn.addEventListener('click', () => {
            carousel.scrollBy({ left: 300, behavior: 'smooth' });
        });
    }

    // Flash sale countdown timer (ends at midnight)
    function updateFlashTimer() {
        const now = new Date();
        const end = new Date();
        end.setHours(23, 59, 59, 999);
        const diff = end - now;
        if (diff <= 0) {
            document.getElementById('flashTimer').innerText = 'Sale ended';
            return;
        }
        const hours = Math.floor(diff / 3600000);
        const minutes = Math.floor((diff % 3600000) / 60000);
        const seconds = Math.floor((diff % 60000) / 1000);
        document.getElementById('flashTimer').innerText = `Ends in ${hours.toString().padStart(2,'0')}:${minutes.toString().padStart(2,'0')}:${seconds.toString().padStart(2,'0')}`;
        setTimeout(updateFlashTimer, 1000);
    }
    updateFlashTimer();

    // Live Activity Ticker (random activities)
    const activities = [
        "John from Nairobi just bought Sukuma Wiki",
        "Mary from Mombasa ordered Fresh Mangoes",
        "Peter from Kisumu received his order of Dairy",
        "Alice from Eldoret rated Farmer Peter 5⭐",
        "New farmer registered from Kiambu",
        "Tomatoes from Naivasha now in stock"
    ];
    let tickerContent = "";
    let index = 0;
    function updateTicker() {
        tickerContent = activities.map(a => `&nbsp;&nbsp;• ${a} &nbsp;&nbsp;`).join('');
        document.getElementById('liveTicker').innerHTML = tickerContent;
        // You could also rotate content periodically
        setTimeout(() => {
            // shift first element to end for dynamic effect
            const first = activities.shift();
            activities.push(first);
            updateTicker();
        }, 8000);
    }
    updateTicker();

    // Category click redirect
    document.querySelectorAll('.category-card').forEach(card => {
        card.addEventListener('click', () => {
            const category = card.getAttribute('data-category');
            window.location.href = `products.php?category=${category}`;
        });
    });

    // Load all sections
    loadFeaturedProducts();
    loadFlashProducts();
    loadRecommendations();

    // Mobile menu toggle (basic)
    document.getElementById('mobileMenuBtn')?.addEventListener('click', () => {
        document.querySelector('.nav-links').classList.toggle('active');
    });
</script>
</body>
</html>





















<!-- 
<?php
require_once dirname(__DIR__) . '/utils/auth_middleware.php';

$auth = auth();
$isLoggedIn = $auth->isAuthenticated();
$userData = $isLoggedIn ? $auth->getCurrentUser() : null;

$userName = $userData['name'] ?? 'Guest';
$userRole = $userData['role'] ?? null;
$userAvatar = $userData['avatar'] ?? '/assets/images/default/avatar.png'; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>TrustLink - Verified Marketplace</title>

<link rel="stylesheet" href="../assets/css/style.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">

<style>

/* 🔥 LIVE TICKER */
.ticker {
    background:#111;
    color:#fff;
    overflow:hidden;
    white-space:nowrap;
    padding:8px 0;
}
.ticker-track {
    display:inline-block;
    animation: scroll 25s linear infinite;
}
@keyframes scroll {
    from { transform: translateX(100%); }
    to { transform: translateX(-100%); }
}

/* 🔥 STATS */
.stats {
    display:flex;
    justify-content:space-around;
    padding:40px;
    background:#f7f7f7;
}
.stat h2 {
    font-size:32px;
    color:#28a745;
}

/* 🔥 CATEGORY */
.categories {
    display:grid;
    grid-template-columns: repeat(auto-fit, minmax(150px,1fr));
    gap:20px;
}
.category-card {
    padding:20px;
    background:#fff;
    text-align:center;
    border-radius:10px;
    cursor:pointer;
    transition:0.3s;
}
.category-card:hover {
    transform:translateY(-5px);
}

/* 🔥 CAROUSEL */
.carousel {
    display:flex;
    overflow:hidden;
}
.carousel-track {
    display:flex;
    animation: slide 20s linear infinite;
}
@keyframes slide {
    from { transform:translateX(0); }
    to { transform:translateX(-50%); }
}

/* 🔥 SKELETON */
.skeleton {
    background: linear-gradient(90deg,#eee,#ddd,#eee);
    height:200px;
    border-radius:10px;
    animation: shimmer 1.5s infinite;
}
@keyframes shimmer {
    0%{background-position:-200px 0;}
    100%{background-position:200px 0;}
}

/* 🔥 FLASH SALE */
.flash-sale {
    background:#ff3d00;
    color:#fff;
    padding:20px;
    text-align:center;
}

</style>
</head>

<body>

<!-- 🔥 LIVE ACTIVITY -->
<div class="ticker">
    <div class="ticker-track">
        🔥 John from Nairobi bought Tomatoes • Mary ordered Eggs • Farmer James listed Fresh Milk • 50+ orders today • Trusted by 10,000+ users 🔥
    </div>
</div>

<!-- NAVBAR -->
<nav class="navbar">
<div class="container">

<div class="nav-brand">
<a href="index.php">🌾 TrustLink</a>
</div>

<div class="nav-links">

<?php if ($isLoggedIn): ?>
    <span>Welcome, <?php echo htmlspecialchars($userName); ?></span>

    <img src="<?php echo $userAvatar; ?>" class="avatar-small">

    <a href="dashboard.php">Dashboard</a>
    <a href="logout.php">Logout</a>

<?php else: ?>
    <a href="login.php">Login</a>
    <a href="register.php">Sign Up</a>
<?php endif; ?>

</div>
</div>
</nav>

<!-- HERO -->
<section class="hero">
<div class="container">
<h1>Buy Direct From Farmers</h1>
<p>Fresh • Trusted • Verified</p>

<a href="products.php" class="btn btn-primary">Shop Now</a>
</div>
</section>

<!-- 🔥 STATS -->
<section class="stats">
<div class="stat">
<h2 id="farmers">0</h2>
<p>Farmers</p>
</div>

<div class="stat">
<h2 id="orders">0</h2>
<p>Orders</p>
</div>

<div class="stat">
<h2 id="users">0</h2>
<p>Users</p>
</div>
</section>

<!-- 🔥 FLASH SALE -->
<section class="flash-sale">
<h2>Flash Sale Ends In: <span id="timer"></span></h2>
</section>

<!-- 🔥 CATEGORIES -->
<section class="container">
<h2>Categories</h2>
<div class="categories">
<div class="category-card">🥦 Vegetables</div>
<div class="category-card">🍎 Fruits</div>
<div class="category-card">🥛 Dairy</div>
<div class="category-card">🐓 Poultry</div>
</div>
</section>

<!-- 🔥 CAROUSEL PRODUCTS -->
<section class="container">
<h2>Trending Products</h2>

<div class="carousel">
<div class="carousel-track" id="carouselProducts">

<div class="skeleton"></div>
<div class="skeleton"></div>
<div class="skeleton"></div>

</div>
</div>

</section>

<!-- 🔥 FEATURED -->
<section class="container">
<h2>Featured Products</h2>

<div id="featuredProducts">

<div class="skeleton"></div>
<div class="skeleton"></div>

</div>

</section>

<!-- FOOTER -->
<footer class="footer">
<div class="container">
<p>© 2026 TrustLink</p>
</div>
</footer>

<script src="../assets/js/api.js"></script>

<script>

/* 🔥 COUNTER ANIMATION */
function animateCounter(id, target) {
    let count = 0;
    let el = document.getElementById(id);

    let interval = setInterval(() => {
        count += Math.ceil(target/50);
        if(count >= target){
            count = target;
            clearInterval(interval);
        }
        el.innerText = count.toLocaleString();
    }, 30);
}

animateCounter("farmers", 10000);
animateCounter("orders", 25000);
animateCounter("users", 15000);


/* 🔥 TIMER */
function startTimer() {
    let time = 3600;

    setInterval(() => {
        let m = Math.floor(time / 60);
        let s = time % 60;
        document.getElementById("timer").innerText = m + "m " + s + "s";
        time--;
    }, 1000);
}
startTimer();


/* 🔥 LOAD PRODUCTS */
async function loadProducts() {
    try {
        let res = await API.get('/products/get_products.php?per_page=6');

        let html = res.data.map(p => `
            <div class="product-card">
                <img src="${p.primary_image}">
                <h3>${p.name}</h3>
                <p>KES ${p.price}</p>
            </div>
        `).join('');

        document.getElementById("carouselProducts").innerHTML = html;
        document.getElementById("featuredProducts").innerHTML = html;

    } catch(e) {
        console.error(e);
    }
}

loadProducts();

</script>

</body>
</html> -->