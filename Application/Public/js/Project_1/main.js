/**
 * SLOT-H - Main JavaScript
 * Single Logical Operating Tool for Hosting
 */

document.addEventListener('DOMContentLoaded', function() {
    'use strict';
    const popups = {
        register: document.getElementById('registerPopup'),
        login: document.getElementById('loginPopup'),
        remind: document.getElementById('remindPopup')
    };
    function openPopup(id) {
        // Закрыть все
        Object.values(popups).forEach(p => {
            if (p) p.classList.remove('active');
        });

        // Открыть нужный
        const popup = document.getElementById(id);
        if (popup) {
            popup.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
    }
    function closeAllPopups() {
        Object.values(popups).forEach(p => {
            if (p) p.classList.remove('active');
        });
        document.body.style.overflow = '';
    }
    const registerBtns = document.querySelectorAll('#openRegisterBtn, #openRegisterFromLogin, #openRegisterBtn2, #openRegisterBtn3');
    registerBtns.forEach(btn => {
        if (btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                openPopup('registerPopup');
            });
        }
    });
    const loginBtns = document.querySelectorAll('#openLoginBtn, #openLoginFromRegister, #openLoginFromRemind');
    loginBtns.forEach(btn => {
        if (btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                openPopup('loginPopup');
            });
        }
    });
    const remindBtns = document.querySelectorAll('#openRemindBtn, #openRemindFromLogin');
    remindBtns.forEach(btn => {
        if (btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                openPopup('remindPopup');
            });
        }
    });
    document.querySelectorAll('.popup-close').forEach(btn => {
        btn.addEventListener('click', closeAllPopups);
    });
    document.querySelectorAll('.popup-overlay').forEach(overlay => {
        overlay.addEventListener('click', function(e) {
            if (e.target === this) {
                closeAllPopups();
            }
        });
    });
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeAllPopups();
        }
    });

    function handleFormSubmit(formId, successMessage) {
        const form = document.getElementById(formId);
        if (!form) return;

        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const inputs = form.querySelectorAll('input[required]');
            let valid = true;

            inputs.forEach(input => {
                if (!input.value.trim()) {
                    input.style.borderColor = 'var(--color-danger)';
                    valid = false;
                } else {
                    input.style.borderColor = '';
                }
            });

            if (formId === 'registerForm') {
                const password = document.getElementById('regPassword');
                const confirm = document.getElementById('regPasswordConfirm');
                if (password && confirm && password.value !== confirm.value) {
                    confirm.style.borderColor = 'var(--color-danger)';
                    alert('❌ Пароли не совпадают!');
                    return;
                }
            }

            if (!valid) {
                alert('⚠️ Пожалуйста, заполните все обязательные поля.');
                return;
            }

            alert(successMessage || '✅ Форма отправлена (заглушка)');
            form.reset();
            closeAllPopups();
        });
    }

    handleFormSubmit('registerForm', '✅ Аккаунт создан! Добро пожаловать в SLOT-H.');

    handleFormSubmit('loginForm', '🔐 Вы успешно вошли в систему.');

    handleFormSubmit('remindForm', '📧 Ссылка для восстановления отправлена на ваш email.');

    const currentPath = window.location.pathname;
    document.querySelectorAll('.main-nav a').forEach(link => {
        const href = link.getAttribute('href');
        if (href === currentPath || (href === '/' && currentPath === '/')) {
            link.classList.add('active');
        }
    });
});
