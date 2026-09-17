/**
 * SLOT-H - Index Page JavaScript
 */

document.addEventListener('DOMContentLoaded', function() {
    'use strict';
    const animatedElements = document.querySelectorAll(
        '.about-card, .feature-card, .audience-card, .license-item'
    );

    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        });

        animatedElements.forEach(function(el) {
            el.style.opacity = '0';
            el.style.transform = 'translateY(20px)';
            el.style.transition = 'all 0.6s cubic-bezier(0.34, 1.56, 0.64, 1)';
            observer.observe(el);
        });
    }
    const terminalLines = document.querySelectorAll('.terminal-line');
    if (terminalLines.length) {
        // Убеждаемся, что все строки видны (анимация уже применена через CSS)
        // Можно добавить эффект печатания для первой строки
        const firstLine = terminalLines[0];
        if (firstLine) {
            const text = firstLine.textContent;
            firstLine.textContent = '';
            let charIndex = 0;

            const typeInterval = setInterval(function() {
                if (charIndex < text.length) {
                    firstLine.textContent += text[charIndex];
                    charIndex++;
                } else {
                    clearInterval(typeInterval);
                    firstLine.style.opacity = '1';
                }
            }, 30);
        }
    }
    const codeSnippet = document.querySelector('.code-snippet');
    if (codeSnippet) {
        codeSnippet.addEventListener('mouseenter', function() {
            this.style.borderColor = 'var(--color-accent)';
            this.style.transition = 'border-color 0.3s ease';
        });

        codeSnippet.addEventListener('mouseleave', function() {
            this.style.borderColor = 'var(--color-border)';
        });
    }
    const statNumbers = document.querySelectorAll('.stat-number');
    if ('IntersectionObserver' in window) {
        const statObserver = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    const el = entry.target;
                    const text = el.textContent;

                    // Только для чисел (не для ∞)
                    if (!isNaN(parseFloat(text)) && text !== '∞') {
                        const target = parseInt(text);
                        let current = 0;
                        const increment = target / 40;

                        const counter = setInterval(function() {
                            current += increment;
                            if (current >= target) {
                                current = target;
                                clearInterval(counter);
                            }
                            el.textContent = Math.round(current) + (text.includes('+') ? '+' : '');
                        }, 30);
                    }
                }
            });
        }, {
            threshold: 0.5
        });
        statNumbers.forEach(function(el) {
            statObserver.observe(el);
        });
    }
    const terminalBody = document.querySelector('.terminal-body');
    if (terminalBody) {
        terminalBody.addEventListener('click', function() {
            // Добавляем новую строку в терминал при клике (для демо)
            const newLine = document.createElement('div');
            newLine.className = 'terminal-line';
            newLine.style.opacity = '1';
            newLine.innerHTML = '<span class="prompt">$</span> <span style="color: #fbbf24;">Команда выполнена (демо)</span>';
            this.insertBefore(newLine, this.lastElementChild);

            // Прокручиваем вниз
            setTimeout(function() {
                newLine.scrollIntoView({ behavior: 'smooth', block: 'end' });
            }, 100);
        });
    }
    document.querySelectorAll('a[href^="#"]').forEach(function(anchor) {
        anchor.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (href === '#') return;

            const target = document.querySelector(href);
            if (target) {
                e.preventDefault();
                const headerOffset = 80;
                const elementPosition = target.getBoundingClientRect().top;
                const offsetPosition = elementPosition + window.pageYOffset - headerOffset;

                window.scrollTo({
                    top: offsetPosition,
                    behavior: 'smooth'
                });
            }
        });
    });
});