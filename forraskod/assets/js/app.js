document.addEventListener('DOMContentLoaded', () => {
    
    // --- 1. GLOBÁLIS VÁLTOZÓK ÉS INICIALIZÁLÁS ---
    const productGrid = document.getElementById('productGrid');
    const cartBadge = document.getElementById('cartBadge');
    const cartItemsContainer = document.getElementById('cartItemsContainer');
    const checkoutForm = document.getElementById('checkoutForm');
    
    let cart = JSON.parse(localStorage.getItem('rpg_cart')) || [];
    let allProducts = []; 

    updateCartBadge();
    initMobileMenu();

    if (productGrid) {
        loadProducts();
        initCardInteractions(); 
        initFilters();
    }

    if (cartItemsContainer) {
        renderCartPage();
    }

    if (checkoutForm) {
        renderCheckoutPage();
    }
 
    /* =========================================
       2. MOBIL MENÜ LOGIKA
       ========================================= */
    function initMobileMenu() {
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const navLinks = document.getElementById('navLinks');

        if (mobileMenuBtn && navLinks) {
            mobileMenuBtn.addEventListener('click', () => {
                navLinks.classList.toggle('active');
            });
        }
    }

    /* =========================================
       3. TERMÉKEK BETÖLTÉSE ÉS SZŰRÉSE (shop.php)
       ========================================= */
    async function loadProducts() {
        try {
            productGrid.innerHTML = '<p class="loading-msg">Kockák megidézése folyamatban...</p>';
            const response = await fetch('api/endpoints/get_products.php');
            
            if (!response.ok) {
                if (response.status === 404) {
                    productGrid.innerHTML = '<p class="error-msg">Jelenleg nincsenek elérhető termékek a boltban.</p>';
                    return;
                }
                throw new Error(`HTTP hiba! Státusz: ${response.status}`);
            }

            allProducts = await response.json();
            applyFilters();
            
        } catch (error) {
            console.error("Hiba történt a termékek lekérésekor:", error);
            productGrid.innerHTML = '<p class="error-msg">Hiba történt az adatok betöltésekor. Próbáld újra később.</p>';
        }
    }

   function renderProducts(products) {
        productGrid.innerHTML = '';

        if (products.length === 0) {
            productGrid.innerHTML = '<p class="loading-msg">Nincs a feltételeknek vagy a keresésnek megfelelő kocka a kincstárban.</p>';
            return;
        }

        products.forEach(product => {
            const formattedPrice = new Intl.NumberFormat('hu-HU').format(product.ar);
            
            const inStock = product.keszlet > 0;
            const stockDisplay = inStock 
                ? `<p style="color: #27ae60; font-size: 0.85rem; font-weight: bold; margin-bottom: 10px;">Raktáron: ${product.keszlet} db</p>` 
                : `<p style="color: #e74c3c; font-size: 0.85rem; font-weight: bold; margin-bottom: 10px;">Elfogyott</p>`;
                
            const btnHtml = inStock 
                ? `<button class="btn-add-cart" data-id="${product.id}" data-name="${product.nev}" data-price="${product.ar}">
                       <i class="fas fa-cart-plus"></i> Kosárba
                   </button>`
                : `<button class="btn-add-cart" disabled style="background-color: #95a5a6; cursor: not-allowed; transform: none; box-shadow: none;">
                       <i class="fas fa-times"></i> Nincs készleten
                   </button>`;

            // ÚJ: Képek kigyűjtése és Carousel (Lapozó) logika
            const allImages = [product.kep_url];
            if (product.galeria && product.galeria.length > 0) {
                allImages.push(...product.galeria);
            }
            
            // A képtömböt szöveggé alakítjuk, hogy be tudjuk tenni HTML attribútumba
            const imagesJson = JSON.stringify(allImages).replace(/"/g, '&quot;');
            
            // Csak akkor rajzolunk nyilakat, ha van több kép!
            const carouselArrows = allImages.length > 1 ? `
                <button class="carousel-arrow prev" title="Előző kép">&#10094;</button>
                <button class="carousel-arrow next" title="Következő kép">&#10095;</button>
            ` : '';

            const cardHTML = `
                <article class="product-card">
                    <div class="product-image-wrapper">
                        <a href="product_details.php?id=${product.id}" style="text-decoration: none; color: inherit; display: block;">
                            <div class="product-image" style="height: 250px; display: flex; align-items: center; justify-content: center; background: #fff;">
                                <img src="${allImages[0]}" alt="${product.nev}" class="shop-card-img" data-images="${imagesJson}" data-index="0" style="max-height: 100%; object-fit: contain;">
                            </div>
                        </a>
                        ${carouselArrows}
                    </div>
                    <div class="product-info">
                        <span class="product-category">${product.tipus}</span>
                        <a href="product_details.php?id=${product.id}" style="text-decoration: none; color: inherit;">
                            <h3 class="product-title" style="transition: color 0.3s;" onmouseover="this.style.color='var(--accent-color)'" onmouseout="this.style.color='inherit'">${product.nev}</h3>
                        </a>
                        <p class="product-price">${formattedPrice} Ft</p>
                        ${stockDisplay}
                        ${btnHtml}
                    </div>
                </article>
            `;
            productGrid.insertAdjacentHTML('beforeend', cardHTML);
        });
    }

    function initFilters() {
        const filterCheckboxes = document.querySelectorAll('.shop-sidebar input[type="checkbox"]');
        filterCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', applyFilters);
        });

        initColorWheelPopup();

        const searchInput = document.querySelector('.search-box input[name="q"]');
        const searchForm = document.querySelector('.search-box');

        if (searchInput) {
            const urlParams = new URLSearchParams(window.location.search);
            const query = urlParams.get('q');
            if (query) {
                searchInput.value = query; 
            }
            searchInput.addEventListener('input', applyFilters);
        }

        if (searchForm && window.location.pathname.includes('shop.php')) {
            searchForm.addEventListener('submit', (e) => {
                e.preventDefault();
                applyFilters();
            });
        }
    }

    function applyFilters() {
        const checkedTypes = Array.from(document.querySelectorAll('input[data-category="tipus"]:checked')).map(cb => cb.value);
        const checkedColors = Array.from(document.querySelectorAll('input[data-category="szin"]:checked')).map(cb => cb.value);
        
        const searchInput = document.querySelector('.search-box input[name="q"]');
        const searchTerm = searchInput ? searchInput.value.toLowerCase().trim() : '';

        const filteredProducts = allProducts.filter(product => {
            const typeMatch = checkedTypes.length === 0 || checkedTypes.includes(product.tipus);
            const colorMatch = checkedColors.length === 0 || checkedColors.includes(product.szin);
            
            const textMatch = searchTerm === '' || 
                              product.nev.toLowerCase().includes(searchTerm) || 
                              product.tipus.toLowerCase().includes(searchTerm);

            return typeMatch && colorMatch && textMatch;
        });

        renderProducts(filteredProducts);
    }

    function initColorWheelPopup() {
        const popup = document.getElementById('szinPopup');
        const openBtn = document.getElementById('btnOpenSzinPopup');
        const closeBtn = document.getElementById('popupZar');
        const colorCircle = document.getElementById('szinKorPopup');

        if (!popup || !openBtn || !colorCircle) return;

        openBtn.addEventListener('click', () => popup.classList.add('active'));
        closeBtn.addEventListener('click', () => popup.classList.remove('active'));
        
        window.addEventListener('click', (e) => {
            if (e.target === popup) popup.classList.remove('active');
        });

        const colors = [
            { name: 'Piros', hex: '#b30000' },
            { name: 'Kék', hex: '#0d0dce' },
            { name: 'Zöld', hex: '#006600' },
            { name: 'Fekete', hex: '#111111' },
            { name: 'Lila', hex: '#4b0082' },
            { name: 'Fehér', hex: '#fffff0' },
            { name: 'Citromsárga', hex: '#f4f423' },
            { name: 'Narancs', hex: '#ffbc35' },
            { name: 'Rózsaszín', hex: '#d867f1' },
            { name: 'Arany', hex: '#e7c91e' }, 
        ];

        const radius = 100;
        const center = 130;
        const angleStep = (2 * Math.PI) / colors.length;

        colorCircle.innerHTML = ''; 

        colors.forEach((color, i) => {
            const angle = i * angleStep;
            const x = center + radius * Math.cos(angle) - 24;
            const y = center + radius * Math.sin(angle) - 24;

            const dot = document.createElement('div');
            dot.className = 'szinElem';
            dot.style.backgroundColor = color.hex;
            dot.style.left = `${x}px`;
            dot.style.top = `${y}px`;
            dot.title = color.name;
           // Eseményfigyelő hozzáadása a szűréshez
            dot.addEventListener('click', () => {
                const checkbox = document.querySelector(`input[data-category="szin"][value="${color.name}"]`);
                if (checkbox) {
                    checkbox.checked = !checkbox.checked;
                    applyFilters();
                    popup.classList.remove('active');
                }
            });

            colorCircle.appendChild(dot);
        });
    }

    /* =========================================
       4. KÁRTYA INTERAKCIÓK (Kosár és Lapozás)
       ========================================= */
    function initCardInteractions() {
        productGrid.addEventListener('click', (event) => {
            
            // 1. KOSÁR GOMB
            const cartBtn = event.target.closest('.btn-add-cart');
            if (cartBtn) {
                event.preventDefault();
                const productId = cartBtn.getAttribute('data-id');
                const productName = cartBtn.getAttribute('data-name');
                const productPrice = cartBtn.getAttribute('data-price');
                
                addToCart(productId, productName, productPrice);
                
                const originalText = cartBtn.innerHTML;
                cartBtn.innerHTML = '<i class="fas fa-check"></i> Bekerült';
                cartBtn.style.backgroundColor = '#27ae60'; 
                
                setTimeout(() => {
                    cartBtn.innerHTML = originalText;
                    cartBtn.style.backgroundColor = ''; 
                }, 1000);
                return;
            }

            // 2. KÉP LAPOZÓ NYILAK
            const arrowBtn = event.target.closest('.carousel-arrow');
            if (arrowBtn) {
                event.preventDefault(); // Megakadályozzuk, hogy beugorjon a termékoldalra!
                
                const wrapper = arrowBtn.closest('.product-image-wrapper');
                const imgElement = wrapper.querySelector('.shop-card-img');
                const imagesArray = JSON.parse(imgElement.getAttribute('data-images'));
                let currentIndex = parseInt(imgElement.getAttribute('data-index'));

                // Ha balra kattint (prev)
                if (arrowBtn.classList.contains('prev')) {
                    currentIndex = (currentIndex === 0) ? imagesArray.length - 1 : currentIndex - 1;
                } 
                // Ha jobbra kattint (next)
                else {
                    currentIndex = (currentIndex === imagesArray.length - 1) ? 0 : currentIndex + 1;
                }

                // Kép kicserélése a HTML-ben
                imgElement.setAttribute('data-index', currentIndex);
                imgElement.src = imagesArray[currentIndex];
            }
        });
    }

    function addToCart(id, name, price) {
        const existingItem = cart.find(item => item.id === id);
        if (existingItem) {
            existingItem.quantity += 1;
        } else {
            cart.push({
                id: id,
                name: name,
                price: parseInt(price),
                quantity: 1
            });
        }
        localStorage.setItem('rpg_cart', JSON.stringify(cart));
        updateCartBadge();
    }

    function updateCartBadge() {
        if (cartBadge) {
            const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
            cartBadge.textContent = totalItems;
        }
    }

    /* =========================================
       5. KOSÁR OLDAL (cart.php) MEGJELENÍTÉSE
       ========================================= */
    function renderCartPage() {
        if (cart.length === 0) {
            cartItemsContainer.innerHTML = `
                <div class="empty-cart-msg">
                    <h2>A kosarad jelenleg üres.</h2>
                    <p>Ideje beszerezni néhány új kockát a következő kalandhoz!</p>
                    <br><br>
                    <a href="shop.php" class="btn-primary">Tovább a boltba</a>
                </div>
            `;
            cartSummary.style.display = 'none';
            return;
        }

        cartSummary.style.display = 'block';
        cartItemsContainer.innerHTML = '';
        let totalSum = 0;

        cart.forEach((item, index) => {
            const itemTotal = item.price * item.quantity;
            totalSum += itemTotal;
            const formattedPrice = new Intl.NumberFormat('hu-HU').format(item.price);
            const formattedItemTotal = new Intl.NumberFormat('hu-HU').format(itemTotal);

            const itemHTML = `
                <div class="cart-item">
                    <div class="cart-item-info">
                        <span class="cart-item-title">${item.name}</span>
                        <span class="cart-item-price">${formattedPrice} Ft / db</span>
                    </div>
                    <div class="cart-controls">
                        <button class="qty-btn btn-minus" data-index="${index}">-</button>
                        <span class="qty-display">${item.quantity}</span>
                        <button class="qty-btn btn-plus" data-index="${index}">+</button>
                    </div>
                    <div class="cart-item-total">
                        ${formattedItemTotal} Ft
                    </div>
                    <button class="btn-remove" data-index="${index}" aria-label="Törlés">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            `;
            cartItemsContainer.insertAdjacentHTML('beforeend', itemHTML);
        });

        const finalTotalFormatted = new Intl.NumberFormat('hu-HU').format(totalSum) + ' Ft';
        document.getElementById('cartSubtotal').textContent = finalTotalFormatted;
        document.getElementById('cartTotal').textContent = finalTotalFormatted;

        attachCartPageEvents();
    }

    function attachCartPageEvents() {
        document.querySelectorAll('.btn-minus').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const index = e.target.getAttribute('data-index');
                if (cart[index].quantity > 1) {
                    cart[index].quantity -= 1;
                    saveAndRefreshCart();
                }
            });
        });

        document.querySelectorAll('.btn-plus').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const index = e.target.getAttribute('data-index');
                cart[index].quantity += 1;
                saveAndRefreshCart();
            });
        });

        document.querySelectorAll('.btn-remove').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const index = e.target.closest('.btn-remove').getAttribute('data-index');
                cart.splice(index, 1);
                saveAndRefreshCart();
            });
        });
    }

    function saveAndRefreshCart() {
        localStorage.setItem('rpg_cart', JSON.stringify(cart));
        updateCartBadge();
        renderCartPage();
    }

    /* =========================================
       6. PÉNZTÁR OLDAL (checkout.php) LOGIKA
       ========================================= */
    function renderCheckoutPage() {
        if (cart.length === 0) {
            window.location.href = 'shop.php';
            return;
        }

        const checkoutItemsList = document.getElementById('checkoutItemsList');
        const checkoutTotalAmount = document.getElementById('checkoutTotalAmount');
        let totalSum = 0;
        checkoutItemsList.innerHTML = '';

        cart.forEach(item => {
            const itemTotal = item.price * item.quantity;
            totalSum += itemTotal;
            
            const itemHTML = `
                <div class="checkout-item">
                    <div>
                        <div class="checkout-item-name">${item.name}</div>
                        <div class="checkout-item-qty">${item.quantity} db x ${new Intl.NumberFormat('hu-HU').format(item.price)} Ft</div>
                    </div>
                    <div style="font-weight: bold; color: var(--accent-color);">
                        ${new Intl.NumberFormat('hu-HU').format(itemTotal)} Ft
                    </div>
                </div>
            `;
            checkoutItemsList.insertAdjacentHTML('beforeend', itemHTML);
        });

        checkoutTotalAmount.textContent = new Intl.NumberFormat('hu-HU').format(totalSum) + ' Ft';

        checkoutForm.addEventListener('submit', (e) => {
            e.preventDefault(); 
            processOrder();
        });
    }

    async function processOrder() {
        const checkoutContent = document.getElementById('checkoutContent');
        
        const orderData = {
            lastName: document.getElementById('lastName').value,
            firstName: document.getElementById('firstName').value,
            email: document.getElementById('email').value,
            phone: document.getElementById('phone').value,
            city: document.getElementById('city').value,
            zip: document.getElementById('zip').value,
            address: document.getElementById('address').value,
            note: document.getElementById('note').value,
            cart: cart
        };

        try {
            const submitBtn = document.querySelector('.btn-submit-order');
            const originalBtnHtml = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mentés folyamatban...';
            submitBtn.disabled = true;

            const response = await fetch('api/endpoints/save_order.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(orderData)
            });

            if (!response.ok) {
                throw new Error('Hiba történt a mentés során.');
            }

            cart = [];
            localStorage.removeItem('rpg_cart');
            updateCartBadge(); 

            checkoutContent.innerHTML = `
                <div class="success-message-box">
                    <i class="fas fa-check-circle"></i>
                    <h2>Köszönjük a rendelést!</h2>
                    <p>A rendelésedet sikeresen rögzítettük rendszerünkben.</p>
                    <br><br>
                    <a href="index.php" class="btn-primary">Vissza a főoldalra</a>
                </div>
            `;
            
        } catch (error) {
            console.error("Hiba a rendeléskor:", error);
            alert('Sajnos hiba történt a rendelés feldolgozása közben. Kérlek próbáld újra!');
            document.querySelector('.btn-submit-order').innerHTML = '<i class="fas fa-check-circle"></i> Rendelés véglegesítése';
            document.querySelector('.btn-submit-order').disabled = false;
        }
    }
});