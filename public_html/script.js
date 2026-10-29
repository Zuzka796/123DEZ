// =========================================================
// БАЗОВЫЕ НАСТРОЙКИ И ИКОНКИ
// =========================================================
const icons = {
    bed: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 4v16"/><path d="M2 8h18a2 2 0 0 1 2 2v10"/><path d="M2 17h20"/><path d="M6 8v9"/></svg>`,
    bug: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="8" height="14" x="8" y="6" rx="4"/><path d="m19 7-3 2"/><path d="m5 7 3 2"/><path d="m19 19-3-2"/><path d="m5 19 3-2"/><path d="M20 13h-4"/><path d="M4 13h4"/><path d="m10 4 1 2"/><path d="m14 4-1 2"/></svg>`,
    tree: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 19V5"/><path d="M5 12l7-7 7 7"/><path d="M5 17l7-7 7 7"/></svg>`,
    ant: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="6" r="3"/><path d="M12 9v8"/><path d="M8 12h8"/><path d="M9 19l3-2 3 2"/><path d="M6 8l3 2"/><path d="M18 8l-3 2"/></svg>`,
    mouse: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2C8 2 5 5 5 9v6c0 4 3 7 7 7s7-3 7-7V9c0-4-3-7-7-7z"/><path d="M12 6v5"/></svg>`,
    warehouse: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21h18"/><path d="M5 21V7l7-4 7 4v14"/><path d="M9 10a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v11H9z"/></svg>`
};

let services = [];
let cart = JSON.parse(localStorage.getItem('123dez_cart')) || [];
let orders = [];
let activeFilter = 'all';
let currentUser = null;
let captchaResult = 0;

// =========================================================
// КАТАЛОГ И КОРЗИНА
// =========================================================
function saveCartLocal() {
    localStorage.setItem('123dez_cart', JSON.stringify(cart));
}

function applyFilter(cat, btn) {
    activeFilter = cat;
    document.querySelectorAll('.filter-switch').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    renderCards();
}

function renderCards() {
    const grid = document.getElementById('services-grid');
    if (!grid) return;
    grid.innerHTML = '';

    const list = services.filter(s => activeFilter === 'all' || s.category === activeFilter);
    list.forEach(s => {
        grid.innerHTML += `
            <div class="card-item">
                <div>
                    <div class="card-top-row">
                        <span class="badge-cat ${s.category}">${s.category}</span>
                        <span class="warranty-text">Гарантия 1 год</span>
                    </div>
                    <div class="card-title-row">
                        <div class="icon-square">${s.icon}</div>
                        <h3 class="card-title">${s.title}</h3>
                    </div>
                    <p class="card-desc">${s.desc}</p>
                </div>
                <div>
                    <div class="card-price-row">
                        <div class="card-main-price">от ${s.basePrice.toLocaleString()} ₽</div>
                        <div class="card-sub-price">(+${s.unitPrice} ₽ за кажд. доп. ${s.unitName})</div>
                    </div>
                    <button class="card-order-btn" onclick="addToCart(${s.id})">+ Заказать услугу</button>
                </div>
            </div>
        `;
    });
}

function addToCart(id) {
    const exists = cart.find(i => i.id === id);
    if (exists) exists.units += 1;
    else cart.push({ id, units: 1 });
    
    saveCartLocal();
    updateCartUI();
    showView('cart');
}

function removeFromCart(idx) {
    cart.splice(idx, 1);
    saveCartLocal();
    updateCartUI();
}

function updateCartUI() {
    const badge = document.getElementById('cart-counter');
    if (badge) badge.textContent = cart.reduce((acc, i) => acc + i.units, 0);

    const container = document.getElementById('cart-rows-box');
    const formBox = document.getElementById('cart-order-form-box');
    if (!container || !formBox) return;

    if (cart.length === 0) {
        container.innerHTML = '<p style="color: #64748b; padding: 25px 0;">Корзина пока пуста. Выберите услугу в каталоге.</p>';
        formBox.style.display = 'none';
        return;
    }

    formBox.style.display = 'block';
    container.innerHTML = '';
    let total = 0;

    cart.forEach((item, idx) => {
        const s = services.find(srv => srv.id === item.id);
        if (!s) return;
        const cost = s.basePrice + (item.units - 1) * s.unitPrice;
        total += cost;

        container.innerHTML += `
            <div class="cart-row">
                <div style="flex: 1; min-width: 240px;">
                    <strong style="font-size: 16px;">${s.title}</strong>
                    <div style="font-size: 13px; color: #64748b; margin-top: 3px;">
                        Базовая ставка: ${s.basePrice} ₽ | Доп. ${s.unitName}: +${s.unitPrice} ₽
                    </div>
                </div>
                <div class="calc-box">
                    <span style="font-size: 13px;">Объем (${s.unitName}): <strong id="unit-text-${idx}">${item.units}</strong></span>
                    <input type="range" min="1" max="15" value="${item.units}" oninput="changeUnitsLive(${idx}, this.value)">
                </div>
                <div class="row-sum" id="sum-text-${idx}">${cost.toLocaleString()} ₽</div>
                <button class="del-btn" onclick="removeFromCart(${idx})">✕</button>
            </div>
        `;
    });

    const sumEl = document.getElementById('cart-total-sum');
    if (sumEl) sumEl.textContent = `${total.toLocaleString()} ₽`;
}

function changeUnitsLive(idx, val) {
    cart[idx].units = parseInt(val) || 1;
    const s = services.find(srv => srv.id === cart[idx].id);
    if (!s) return;

    const cost = s.basePrice + (cart[idx].units - 1) * s.unitPrice;
    
    const unitEl = document.getElementById(`unit-text-${idx}`);
    const sumEl = document.getElementById(`sum-text-${idx}`);
    if (unitEl) unitEl.textContent = cart[idx].units;
    if (sumEl) sumEl.textContent = cost.toLocaleString() + ' ₽';
    
    let total = 0;
    cart.forEach(item => {
        const srv = services.find(x => x.id === item.id);
        if (srv) total += srv.basePrice + (item.units - 1) * srv.unitPrice;
    });
    
    const totalEl = document.getElementById('cart-total-sum');
    const badge = document.getElementById('cart-counter');
    
    if (totalEl) totalEl.textContent = `${total.toLocaleString()} ₽`;
    if (badge) badge.textContent = cart.reduce((acc, i) => acc + i.units, 0);
    
    saveCartLocal();
}

// =========================================================
// ВЗАИМОДЕЙСТВИЕ С БАЗОЙ ДАННЫХ (УСЛУГИ И ЗАКАЗЫ)
// =========================================================
async function loadServicesFromDB() {
    try {
        const res = await fetch('api/services.php?t=' + Date.now());
        const data = await res.json();
        
        if (data.success && data.services) {
            const iconMap = {
                1: icons.bed, 2: icons.bug, 3: icons.tree,
                4: icons.ant, 5: icons.mouse, 6: icons.warehouse
            };

            services = data.services.map(s => ({
                id: parseInt(s.id),
                category: s.category,
                title: s.title,
                desc: s.description || '',
                basePrice: parseFloat(s.base_price),
                unitPrice: parseFloat(s.price_per_unit),
                unitName: s.unit_name || 'комн.',
                icon: iconMap[s.id] || icons.bed
            }));

            renderCards();
            updateCartUI();
            renderPricesTable(); // Отрисовка таблицы цен для админа
        }
    } catch (e) {
        console.error('Ошибка загрузки цен из БД:', e);
    }
}

function renderPricesTable() {
    const pricesBody = document.getElementById('admin-prices-rows');
    if (!pricesBody) return;
    pricesBody.innerHTML = '';
    services.forEach((s, idx) => {
        pricesBody.innerHTML += `
            <tr>
                <td><span class="badge-cat ${s.category}">${s.category}</span></td>
                <td><input type="text" value="${s.title}" onchange="changeService(${idx}, 'title', this.value)" style="padding: 6px; border: 1px solid #cbd5e1; border-radius: 6px; width: 100%;"></td>
                <td><input type="number" value="${s.basePrice}" onchange="changeService(${idx}, 'basePrice', this.value)" style="padding: 6px; border: 1px solid #cbd5e1; border-radius: 6px; width: 100px;"></td>
                <td><input type="number" value="${s.unitPrice}" onchange="changeService(${idx}, 'unitPrice', this.value)" style="padding: 6px; border: 1px solid #cbd5e1; border-radius: 6px; width: 100px;"></td>
            </tr>
        `;
    });
}

async function changeService(idx, field, val) {
    if (field === 'title') {
        services[idx].title = val;
    } else {
        services[idx][field] = parseFloat(val) || 0;
    }

    renderCards();
    updateCartUI();
    const targetService = services[idx];

    try {
        await fetch('api/services.php', {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                id: targetService.id,
                title: targetService.title,
                basePrice: targetService.basePrice,
                unitPrice: targetService.unitPrice
            })
        });
    } catch (e) {
        console.error('Ошибка сохранения цены в базу MySQL');
    }
}

async function placeOrder(e) {
    e.preventDefault();
    const name = document.getElementById('cust-name').value.trim();
    const phone = document.getElementById('cust-phone').value.trim();
    const address = document.getElementById('cust-address').value.trim();

    let total = 0;
    let list = [];
    cart.forEach(item => {
        const s = services.find(srv => srv.id === item.id);
        if (!s) return;
        total += s.basePrice + (item.units - 1) * s.unitPrice;
        list.push(`${s.title} (${item.units} ${s.unitName})`);
    });

    const payload = {
        name: name, phone: phone, address: address,
        items: list.join(', '), total: total
    };

    try {
        const response = await fetch('api/orders.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });
        const res = await response.json();

        if (res.success) {
            cart = [];
            localStorage.removeItem('123dez_cart');
            updateCartUI();
            alert(`Заявка №${res.order_id} успешно сохранена в базе MySQL! Диспетчер свяжется с вами по номеру ${phone}.`);
            await loadOrdersFromDB();
            showView('orders');
        } else {
            alert('Ошибка при оформлении: ' + res.error);
        }
    } catch (err) {
        alert('Ошибка связи с сервером XAMPP. Проверьте, что Apache и MySQL запущены.');
    }
}

async function loadOrdersFromDB() {
    try {
        const res = await fetch('api/orders.php?t=' + Date.now());
        const data = await res.json();
        if (data.success && data.orders) {
            orders = data.orders.map(o => ({
                id: o.id,
                client: o.client_name,
                phone: o.client_phone,
                items: o.items_desc,
                address: o.address,
                total: parseFloat(o.total_price),
                status: o.status
            }));
            renderTables();
        }
    } catch (e) {
        console.error('Ошибка загрузки данных из БД:', e);
    }
}

function renderTables() {
    const userBody = document.getElementById('user-orders-rows');
    const adminBody = document.getElementById('admin-orders-rows');

    if (userBody) userBody.innerHTML = '';
    if (adminBody) adminBody.innerHTML = '';

    orders.forEach((o) => {
       if (userBody) {
            let statusText = (o.status && o.status.trim() !== '') ? o.status : 'новый';
            let bg = '#dbeafe';
            let color = '#1d4ed8';
            
            if (statusText === 'новый') { bg = '#dbeafe'; color = '#1d4ed8'; }
            else if (statusText === 'выезд мастера') { bg = '#fef3c7'; color = '#b45309'; }
            else if (statusText === 'выполнен') { bg = '#dcfce7'; color = '#15803d'; }
            else if (statusText === 'отменен') { bg = '#fee2e2'; color = '#b91c1c'; }

            userBody.innerHTML += `
                <tr>
                    <td><strong>#${o.id}</strong></td>
                    <td>${o.items}</td>
                    <td>${o.address}</td>
                    <td><strong>${o.total.toLocaleString()} ₽</strong></td>
                    <td>
                        <span style="display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 13px; font-weight: 600; background: ${bg}; color: ${color};">
                            ${statusText}
                        </span>
                    </td>
                </tr>
            `;
        }
        if (adminBody) {
            adminBody.innerHTML += `
            <tr>
                <td>#${o.id}</td>
                <td><strong>${o.client}</strong><br><small style="color: #64748b;">${o.phone}</small></td>
                <td>${o.items}</td>
                <td>${o.address}</td>
                <td><strong>${o.total.toLocaleString()} ₽</strong></td>
                <td>
                    <div style="display: flex; gap: 8px; align-items: center;">
                        <select onchange="updateStatus(${o.id}, this.value)" style="padding: 6px; border-radius: 6px; border: 1px solid #cbd5e1;">
                            <option value="новый" ${o.status === 'новый' ? 'selected' : ''}>Новый</option>
                            <option value="выезд мастера" ${o.status === 'выезд мастера' ? 'selected' : ''}>Выезд мастера</option>
                            <option value="выполнен" ${o.status === 'выполнен' ? 'selected' : ''}>Выполнен</option>
                            <option value="отменен" ${o.status === 'отменен' ? 'selected' : ''}>Отменен</option>
                        </select>
                        <button type="button" onclick="deleteOrder(${o.id})" title="Удалить заявку" style="background: #fee2e2; color: #ef4444; border: 1px solid #fecaca; border-radius: 6px; padding: 6px 10px; cursor: pointer; font-size: 14px;">
                            🗑️
                        </button>
                    </div>
                </td>
            </tr>
            `;
        }
    });
}

async function updateStatus(orderId, val) {
    try {
        await fetch('api/orders.php', {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: orderId, status: val })
        });
        await loadOrdersFromDB();
    } catch (e) {
        alert('Не удалось обновить статус заказа');
    }
}

async function deleteOrder(orderId) {
    if (!confirm(`Вы действительно хотите безвозвратно удалить заявку #${orderId}?`)) return;
    try {
        const res = await fetch('api/orders.php', {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: orderId })
        });
        const data = await res.json();
        if (data.success) {
            alert(`Заявка #${orderId} успешно удалена.`);
            await loadOrdersFromDB();
        } else {
            alert('Ошибка при удалении: ' + data.error);
        }
    } catch (e) {
        alert('Не удалось связаться с сервером для удаления');
    }
}

// =========================================================
// НАВИГАЦИЯ И АВТОРИЗАЦИЯ
// =========================================================
function showView(name) {
    document.querySelectorAll('.view-panel').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.nav-btn').forEach(b => b.classList.remove('active'));
    const panel = document.getElementById('view-' + name);
    const btn = document.getElementById('btn-' + name);
    if (panel) panel.classList.add('active');
    if (btn) btn.classList.add('active');
    // ---> СОХРАНЯЕМ ТЕКУЩУЮ ВКЛАДКУ В ПАМЯТЬ БРАУЗЕРА <---
    localStorage.setItem('123dez_active_view', name);
}

async function initAuth() {
    try {
        const res = await fetch('api/auth.php?action=check');
        const data = await res.json();
        if (data.user) {
            currentUser = data.user;
        } else {
            const saved = localStorage.getItem('123dez_user');
            if (saved) currentUser = JSON.parse(saved);
        }
    } catch (e) {
        const saved = localStorage.getItem('123dez_user');
        if (saved) currentUser = JSON.parse(saved);
    }
    renderAuthHeader();
}

function renderAuthHeader() {
    const container = document.getElementById('auth-header-block');
    const adminBtn = document.getElementById('btn-admin');
    if (!container) return;

    if (!currentUser) {
        container.innerHTML = `
            <div class="auth-header-btns">
                <button class="auth-link-btn" onclick="openAuthModal('login')">Войти</button>
                <button class="auth-link-btn primary" onclick="openAuthModal('register')">Регистрация</button>
            </div>
        `;
        if (adminBtn) adminBtn.classList.add('hidden');
    } else {
        const isAdm = currentUser.role === 'admin';
        container.innerHTML = `
            <div class="user-profile-badge">
                <span class="user-name-label">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    ${currentUser.name}
                    <span class="user-role-tag ${isAdm ? 'role-tag-admin' : 'role-tag-user'}">${isAdm ? 'Админ' : 'Клиент'}</span>
                </span>
                <button class="logout-icon-btn" onclick="handleLogout()" title="Выйти из аккаунта">Выход</button>
            </div>
        `;
        if (adminBtn) {
            if (isAdm) adminBtn.classList.remove('hidden');
            else adminBtn.classList.add('hidden');
        }
    }
}

async function loadUsersCount() {
    const counterEl = document.getElementById('admin-users-count');
    if (!counterEl) return;
    try {
        const res = await fetch('api/auth.php?action=count_users');
        const data = await res.json();
        if (data.success) {
            counterEl.textContent = data.total_users;
        }
    } catch (e) { }
}

function openAuthModal(tab = 'login') {
    const modal = document.getElementById('auth-modal');
    if (!modal) return;
    setAuthTab(tab);
    generateCaptcha();
    modal.classList.remove('hidden');
}

function closeAuthModal() {
    const modal = document.getElementById('auth-modal');
    if (modal) modal.classList.add('hidden');
}

function setAuthTab(tab) {
    const loginForm = document.getElementById('form-login');
    const regForm = document.getElementById('form-register');
    const loginTab = document.getElementById('tab-login-btn');
    const regTab = document.getElementById('tab-reg-btn');

    if (tab === 'login') {
        if(loginForm) loginForm.classList.remove('hidden');
        if(regForm) regForm.classList.add('hidden');
        if(loginTab) loginTab.classList.add('active');
        if(regTab) regTab.classList.remove('active');
    } else {
        if(loginForm) loginForm.classList.add('hidden');
        if(regForm) regForm.classList.remove('hidden');
        if(loginTab) loginTab.classList.remove('active');
        if(regTab) regTab.classList.add('active');
    }
}

function generateCaptcha() {
    const num1 = Math.floor(Math.random() * 9) + 1;
    const num2 = Math.floor(Math.random() * 9) + 1;
    captchaResult = num1 + num2;
    const label = document.getElementById('login-captcha-task');
    if (label) label.textContent = `${num1} + ${num2} = ?`;
    const input = document.getElementById('login-captcha-answer');
    if (input) input.value = '';
}

async function handleLogin(e) {
    e.preventDefault();
    const userAnswer = parseInt(document.getElementById('login-captcha-answer').value, 10);
    if (userAnswer !== captchaResult) {
        alert('Капча введена неверно! Попробуйте снова.');
        generateCaptcha();
        return;
    }

    const login = document.getElementById('login-email').value.trim();
    const pass = document.getElementById('login-password').value.trim();

    try {
        const res = await fetch('api/auth.php?action=login', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ login: login, password: pass })
        });
        const data = await res.json();

        if (data.success) {
            currentUser = data.user;
            localStorage.setItem('123dez_user', JSON.stringify(currentUser));
            renderAuthHeader();
            closeAuthModal();
            loadOrdersFromDB(); 

            if (currentUser.role === 'admin') {
                alert('Успешный вход в роли Администратора (ИП Строкун О.А.)!');
                loadUsersCount();
                showView('admin');
            } else {
                alert(`Добро пожаловать, ${currentUser.name}!`);
                showView('orders');
            }
        } else {
            alert('Ошибка входа: ' + data.error);
        }
    } catch (err) {
        alert('Ошибка авторизации. Проверьте сервер.');
    }
}

async function handleRegister(e) {
    e.preventDefault();
    const name = document.getElementById('reg-name').value.trim();
    const email = document.getElementById('reg-email').value.trim();
    const phone = document.getElementById('reg-phone').value.trim();
    const pass = document.getElementById('reg-password').value.trim();

    try {
        const res = await fetch('api/auth.php?action=register', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ name: name, email: email, phone: phone, password: pass })
        });
        const data = await res.json();

        if (data.success) {
            document.getElementById('form-register').reset();
            setAuthTab('login');
            generateCaptcha();
            const loginInput = document.getElementById('login-email');
            if (loginInput) loginInput.value = phone || email;
            alert(`Регистрация прошла успешно! Теперь введите пароль и решите пример с капчей для входа.`);
        } else {
            alert('Ошибка регистрации: ' + data.error);
        }
    } catch (err) {
        alert('Ошибка при связи с базой данных.');
    }
}

async function handleLogout() {
    try { await fetch('api/auth.php?action=logout'); } catch (e) { }
    currentUser = null;
    localStorage.removeItem('123dez_user');
    renderAuthHeader();
    showView('catalog');
}

// =========================================================
// СИСТЕМА COOKIE И ИНИЦИАЛИЗАЦИЯ
// =========================================================
function checkCookies() {
    const consent = localStorage.getItem('123dez_cookie');
    if (!consent) {
        const overlay = document.getElementById('cookie-overlay');
        if (overlay) {
            overlay.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
    }
}

function acceptCookies() {
    localStorage.setItem('123dez_cookie', 'accepted');
    const overlay = document.getElementById('cookie-overlay');
    if (overlay) overlay.classList.add('hidden');
    document.body.style.overflow = '';
}

function declineCookies() {
    document.getElementById('cookie-title').textContent = 'Сайт заблокирован 🔒';
    document.getElementById('cookie-title').style.color = '#dc2626';
    document.getElementById('cookie-text').innerHTML = 'К сожалению, без использования файлов cookie и локальной памяти мы не можем гарантировать работу корзины и оформление заявок.<br><br><b>Дальнейшее использование сайта невозможно.</b>';
    const btns = document.getElementById('cookie-btns');
    if (btns) btns.innerHTML = `<button class="cookie-btn-yes" onclick="location.reload()" style="width: 100%;">Я передумал, разрешить Cookie</button>`;
}

// ЗАПУСК ПРИ ЗАГРУЗКЕ СТРАНИЦЫ
window.onload = function () {
    checkCookies();
    initAuth();
    loadServicesFromDB(); // Эта функция теперь сама запускает отрисовку корзины и карточек, чтобы они не были пустыми!
    loadOrdersFromDB();
    loadUsersCount();
    
    // ---> ВОССТАНАВЛИВАЕМ ОТКРЫТУЮ ВКЛАДКУ ПРИ ОБНОВЛЕНИИ <---
    const savedView = localStorage.getItem('123dez_active_view');
    if (savedView) {
        showView(savedView);
    } else {
        showView('catalog'); // Если ничего не было сохранено, открываем каталог
    }
};