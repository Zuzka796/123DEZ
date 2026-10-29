<?php session_start(); ?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <!-- ВОТ ЭТА СТРОКА ГОВОРИТ ТЕЛЕФОНУ НЕ ОТДАЛЯТЬ САЙТ: -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>123DEZ — ДЕЗ-СЛУЖБА | ИП Строкун Оксана Андреевна</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css?v=6">
</head>
<body>

<!-- ВЕРХНЯЯ ШАПКА -->
<header class="top-nav">
    <div class="nav-wrapper">
        <!-- Логотип с щитом и бейджем -->
        <div class="brand-group" onclick="showView('catalog')">
            <div class="brand-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    <path d="m9 12 2 2 4-4"/>
                </svg>
            </div>
            <div class="brand-info">
                <div class="brand-title-row">
                    <span class="brand-name">123DEZ</span>
                    <span class="brand-tag">ДЕЗ-СЛУЖБА</span>
                </div>
                <span class="brand-sub">ИП Строкун Оксана Андреевна</span>
            </div>
        </div>

        <!-- Центральная навигация -->
        <nav class="nav-links">
            <button class="nav-btn active" id="btn-catalog" onclick="showView('catalog')">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
                Каталог услуг
            </button>
            <button class="nav-btn" id="btn-cart" onclick="showView('cart')">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="2" width="16" height="20" rx="2"/><line x1="8" y1="6" x2="16" y2="6"/><line x1="16" y1="14" x2="16" y2="18"/><path d="M16 10h.01"/><path d="M12 10h.01"/><path d="M8 10h.01"/><path d="M12 14h.01"/><path d="M8 14h.01"/><path d="M12 18h.01"/><path d="M8 18h.01"/></svg>
                Корзина и калькулятор
                <span class="cart-pill" id="cart-counter">0</span>
            </button>
            <button class="nav-btn" id="btn-orders" onclick="showView('orders')">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><rect x="8" y="2" width="8" height="4" rx="1" ry="1"/></svg>
                Мои заявки
            </button>
            <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin'): ?>
    <button class="nav-btn" id="btn-admin" onclick="showView('admin')">
        <svg viewBox="0 0 24 24" fill="none" ...> ... </svg>
        Админ-панель
    </button>
<?php endif; ?>
        </nav>

        <!-- Блок авторизации и контактов -->
        <div class="header-right-zone">
            <!-- Блок пользователя (Вход / Регистрация или Профиль) -->
            <div id="auth-header-block"></div>

            <!-- Телефон диспетчера -->
            <div class="phone-block">
                <span class="phone-caption">Круглосуточный диспетчер:</span>
                <a href="tel:+79384869931" class="phone-number">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                    +7 (938) 486-99-31
                </a>
            </div>
        </div>
    </div>
</header>

<main class="page-body">
    <!-- СЕКЦИЯ 1: КАТАЛОГ -->
    <section id="view-catalog" class="view-panel active">
        <!-- Главный темно-изумрудный баннер -->
        <div class="hero-banner">
            <div class="hero-bg-watermark">
                <svg viewBox="0 0 200 240" fill="none">
                    <path d="M100 220C100 220 180 170 180 90V40L100 10L20 40V90C20 170 100 220 100 220Z" fill="white" fill-opacity="0.04"/>
                </svg>
            </div>

            <div class="hero-top-badge">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                Официальный договор • Сертифицированные препараты
            </div>

            <h1 class="hero-heading">Уничтожение клопов,<br>тараканов и грызунов за 2<br>выезда</h1>

            <p class="hero-desc">
                Служба санитарной обработки <strong>123DEZ</strong> (ИП Строкун О.А.). Безопасные препараты IV класса опасности без резкого запаха. Гарантия результата по договору.
            </p>

            <div class="hero-actions">
                <div class="hero-pill-info">
                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
                    Выезд мастера за 45 минут
                </div>
                <div class="hero-pill-info">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                    Акт и гарантия до 1 года
                </div>
                <a href="tel:+79384869931" class="hero-pill-phone">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                    +7 (938) 486-99-31
                </a>
            </div>
        </div>

        <!-- Заголовок каталога и фильтры -->
        <div class="catalog-header-bar">
            <div>
                <h2 class="section-title">Прайс-лист услуг 123DEZ</h2>
                <div class="section-subtitle">Выберите услугу для быстрого добавления в корзину и точного расчета</div>
            </div>

            <div class="filter-switch-group">
                <button class="filter-switch active" onclick="applyFilter('all', this)">Все (6)</button>
                <button class="filter-switch" onclick="applyFilter('дезинсекция', this)">🦗 Дезинсекция</button>
                <button class="filter-switch" onclick="applyFilter('дератизация', this)">🐀 Дератизация</button>
            </div>
        </div>

        <!-- Сетка карточек -->
        <div class="cards-grid" id="services-grid"></div>
    </section>

    <!-- СЕКЦИЯ 2: КОРЗИНА И КАЛЬКУЛЯТОР -->
    <section id="view-cart" class="view-panel">
        <div class="card-box">
            <div class="box-top">
                <h2>Корзина и точный калькулятор площади/комнат</h2>
                <p>Изменяйте количество комнат или соток ползунком — итоговая сумма пересчитывается на лету.</p>
            </div>

            <div id="cart-rows-box"></div>

            <div id="cart-order-form-box" class="order-form-block">
                <div class="grand-total-row">
                    <span>Итоговая расчетная сумма:</span>
                    <strong id="cart-total-sum">0 ₽</strong>
                </div>

                <form onsubmit="placeOrder(event)" class="clean-form">
                    <h3>Данные для выезда специалиста 123DEZ</h3>
                    <div class="form-row-2">
                        <div class="field">
                            <label>Ваше имя</label>
                            <input type="text" id="cust-name" placeholder="Иван Петров" required>
                        </div>
                        <div class="field">
                            <label>Контактный телефон</label>
                            <input type="tel" id="cust-phone" placeholder="+7 (___) ___-__-__"required>
                        </div>
                    </div>
                    <div class="field">
                        <label>Адрес объекта (улица, номер дома, квартира/офис)</label>
                        <input type="text" id="cust-address" placeholder="г. Краснодар, ул. Красная, 102, кв. 14" required>
                    </div>
                    <button type="submit" class="submit-btn">Оформить выезд мастера с гарантией</button>
                </form>
            </div>
        </div>
    </section>

    <!-- СЕКЦИЯ 3: МОИ ЗАЯВКИ -->
 
    <section id="view-orders" class="view-panel">
        <div class="card-box">
            <div class="box-top">
                <h2>История ваших обращений в 123DEZ</h2>
                <p>Статусы заявок обновляются диспетчером в режиме реального времени.</p>
            </div>
            <div class="table-scroll">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>№ Заявки</th>
                            <th>Состав услуг</th>
                            <th>Адрес обработки</th>
                            <th>Стоимость</th>
                            <th>Статус выполнения</th>
                        </tr>
                    </thead>
                    <tbody id="user-orders-rows"></tbody>
                </table>
            </div>
        </div>
    </section>
   
    
    <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin'): ?>
  <section id="view-admin" class="view-panel">
        <div class="card-box">
            <div class="box-top admin-header">
                <div>
                    <h2>Панель диспетчера — 123DEZ</h2>
                    <p>Администратор: <strong>ИП Строкун Оксана Андреевна</strong> | Тел.: +7 (938) 486-99-31</p>
                </div>
                <div class="admin-badge-lock">РЕЖИМ УПРАВЛЕНИЯ</div>
            </div>

            <!-- Карточка статистики пользователей -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 15px; margin: 20px 0 25px 0;">
                <div style="background: #ffffff; border: 1.5px solid #e2e8f0; border-radius: 12px; padding: 16px 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <div style="font-size: 12px; color: #64748b; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">Клиентов в базе</div>
                        <div style="display: flex; align-items: baseline; gap: 8px; margin-top: 4px;">
                            <span id="admin-users-count" style="font-size: 30px; font-weight: 800; color: #059669;">0</span>
                            <span style="font-size: 13px; color: #94a3b8;">зарегистрировано</span>
                        </div>
                    </div>
                    <div style="width: 44px; height: 44px; border-radius: 10px; background: #ecfdf5; color: #059669; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                        👥
                    </div>
                </div>
            </div>

            <h3 class="admin-subheading">Журнал поступающих заказов</h3>
            <div class="table-scroll">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Клиент / Телефон</th>
                            <th>Услуга (объем)</th>
                            <th>Адрес</th>
                            <th>Сумма</th>
                            <th>Действия / Статус</th>
                        </tr>
                    </thead>
                    <tbody id="admin-orders-rows"></tbody>
                </table>
            </div>

            <h3 class="admin-subheading" style="margin-top: 35px;">Редактирование цен в каталоге</h3>
            <div class="table-scroll">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Категория</th>
                            <th>Название услуги</th>
                            <th>Базовая ставка (₽)</th>
                            <th>Доплата за доп. ед. (₽)</th>
                        </tr>
                    </thead>
                    <tbody id="admin-prices-rows"></tbody>
                </table>
            </div>
        </div>
    </section>
    <?php endif; ?>
</main>

<!-- МОДАЛЬНОЕ ОКНО АВТОРИЗАЦИИ И РЕГИСТРАЦИИ -->
<div class="modal-overlay hidden" id="auth-modal">
    <div class="modal-card">
        <button class="modal-close" onclick="closeAuthModal()">✕</button>
        <div class="auth-tabs">
            <button class="auth-tab-btn active" id="tab-login-btn" onclick="setAuthTab('login')">Вход в аккаунт</button>
            <button class="auth-tab-btn" id="tab-reg-btn" onclick="setAuthTab('register')">Регистрация</button>
        </div>

        <!-- ФОРМА ВХОДА -->
        <form id="form-login" class="auth-form" onsubmit="handleLogin(event)">
            <div class="field">
                <label>Email или Телефон</label>
                <input type="text" id="login-email" placeholder="client@mail.ru (или admin)" required>
            </div>
            <div class="field">
                <label>Пароль</label>
                <input type="password" id="login-password" placeholder="••••••••" required>
            </div>
            <!-- Полезная подсказка для клиентов -->
            <div class="auth-hint">
                🔒 Для входа используйте ваш номер телефона или Email, указанные при регистрации.
            </div>

            <!-- Капча от ботов -->
            <div class="field captcha-field" style="margin-top: 12px;">
                <label style="font-size: 13px; color: #475569; display: flex; justify-content: space-between;">
                    <span>Подвердите что вы человек:</span>
                    <span id="login-captcha-task" style="font-weight: 700; color: #0f172a; background: #e2e8f0; padding: 2px 8px; border-radius: 4px;">-- + -- = ?</span>
                </label>
                <div style="display: flex; gap: 8px; margin-top: 5px;">
                    <input type="number" id="login-captcha-answer" placeholder="Ответ" required style="width: 100%; padding: 10px; border: 1.5px solid #cbd5e1; border-radius: 8px;">
                    <button type="button" onclick="generateCaptcha()" title="Обновить пример" style="background: #f1f5f9; border: 1px solid #cbd5e1; border-radius: 8px; padding: 0 12px; cursor: pointer; font-size: 16px;">🔄</button>
                </div>
            </div>
            <button type="submit" class="submit-btn" style="margin-top: 10px;">Войти в 123DEZ</button>
        </form>

        <!-- ФОРМА РЕГИСТРАЦИИ -->
        <form id="form-register" class="auth-form hidden" onsubmit="handleRegister(event)">
            <div class="field">
                <label>ФИО или Имя</label>
                <input type="text" id="reg-name" placeholder="Иван Петров" required>
            </div>
            <div class="field">
                <label>Email</label>
                <input type="email" id="reg-email" placeholder="ivan@mail.ru" required>
            </div>
            <div class="field">
                <label>Телефон</label>
                <input type="tel" id="reg-phone" placeholder="+7 (938) 486-99-31" required>
            </div>
            <div class="field">
                <label>Придумайте пароль (от 6 символов)</label>
                <input type="password" id="reg-password" minlength="6" placeholder="••••••••" required>
            </div>
            <button type="submit" class="submit-btn" style="margin-top: 10px;">Зарегистрироваться</button>
        </form>
    </div>
</div>

<!-- Окно предупреждения о Cookie -->
<div id="cookie-overlay" class="cookie-overlay hidden">
    <div class="cookie-modal">
        <div class="cookie-icon">🍪</div>
        <h3 id="cookie-title">Мы используем файлы Cookie</h3>
        <p id="cookie-text">Этот сайт использует файлы cookie и локальное хранилище для сохранения вашей корзины и корректной работы личного кабинета. Продолжая, вы даете согласие на обработку данных.</p>
        <div class="cookie-btns" id="cookie-btns">
            <button class="cookie-btn-yes" onclick="acceptCookies()">Понятно, принимаю</button>
            <button class="cookie-btn-no" onclick="declineCookies()">Нет</button>
        </div>
    </div>
</div>

<!-- ПОДВАЛ САЙТА 123DEZ -->
<footer class="site-footer">
    <div class="footer-container">
        <!-- Колонка 1: О компании -->
        <div class="footer-col">
            <div class="footer-logo">
                <span class="footer-logo-badge">123DEZ</span>
                <span class="footer-logo-text">ДЕЗ-СЛУЖБА</span>
            </div>
            <p class="footer-desc">
                Профессиональное уничтожение насекомых, грызунов и обработка участков в Краснодаре и крае. Официальная гарантия по договору и безопасные препараты.
            </p>
            <p class="footer-ip">ИП Строкун Оксана Андреевна</p>
        </div>

        <!-- Колонка 2: Быстрая навигация -->
        <div class="footer-col">
            <h4 class="footer-title">Навигация</h4>
            <ul class="footer-links">
                <li><a href="#" onclick="showView('catalog'); return false;">Каталог услуг</a></li>
                <li><a href="#" onclick="showView('cart'); return false;">Корзина и калькулятор</a></li>
                <li><a href="#" onclick="showView('orders'); return false;">Мои заявки</a></li>
            </ul>
        </div>

        <!-- Колонка 3: Услуги дезинсекции -->
        <div class="footer-col">
            <h4 class="footer-title">Популярные услуги</h4>
            <ul class="footer-links">
                <li><span>Уничтожение клопов и тараканов</span></li>
                <li><span>Дератизация (крысы и мыши)</span></li>
                <li><span>Акарицидная обработка участков</span></li>
                <li><span>Дезинфекция помещений</span></li>
            </ul>
        </div>

        <!-- Колонка 4: Контакты и диспетчер -->
        <div class="footer-col">
            <h4 class="footer-title">Контакты</h4>
            <p class="footer-contact-item" style="color: #94a3b8; font-size: 13.5px; margin-bottom: 6px;">
                Круглосуточный диспетчер:
            </p>
            <a href="tel:+79384869931" class="footer-phone">+7 (938) 486-99-31</a>
            <p class="footer-contact-item" style="margin-top: 10px; font-size: 12.5px; color: #94a3b8;">
                Работаем ежедневно. Выезд мастера в день обращения.
            </p>
        </div>
    </div>

    <!-- Нижняя полоса с копирайтом -->
    <div class="footer-bottom">
        <div class="footer-bottom-container">
            <p>© 2026 123DEZ — Служба дезинфекции, дезинсекции и дератизации. Все права защищены.</p>
            <p>Сайт разработан для комфортного заказа услуг.</p>
        </div>
    </div>
</footer>

<script src="script.js"></script>
</body>
</html>