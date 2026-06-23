/**
 * SuaNet Fibra - Main Frontend JavaScript
 * ISP Landing Page
 */

(function () {
    'use strict';

    // ==================== HEADER ====================
    const header = document.getElementById('header');
    const hamburger = document.getElementById('hamburger');
    const mobileMenu = document.getElementById('mobileMenu');

    // Sticky header on scroll
    let lastScroll = 0;
    window.addEventListener('scroll', function () {
        const currentScroll = window.pageYOffset;
        if (currentScroll > 50) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
        lastScroll = currentScroll;
    });

    // Hamburger menu toggle
    if (hamburger && mobileMenu) {
        hamburger.addEventListener('click', function () {
            hamburger.classList.toggle('active');
            mobileMenu.classList.toggle('open');
            document.body.style.overflow = mobileMenu.classList.contains('open') ? 'hidden' : '';
        });

        // Close mobile menu on link click
        mobileMenu.querySelectorAll('.mobile-nav-link').forEach(function (link) {
            link.addEventListener('click', function () {
                hamburger.classList.remove('active');
                mobileMenu.classList.remove('open');
                document.body.style.overflow = '';
            });
        });
    }

    // Smooth scroll for nav links
    document.querySelectorAll('a[href^="#"]').forEach(function (anchor) {
        anchor.addEventListener('click', function (e) {
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;
            const target = document.querySelector(targetId);
            if (target) {
                e.preventDefault();
                const headerHeight = header.offsetHeight;
                const targetPosition = target.getBoundingClientRect().top + window.pageYOffset - headerHeight;
                window.scrollTo({
                    top: targetPosition,
                    behavior: 'smooth'
                });
            }
        });
    });

    // ==================== MODALS ====================
    function openModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeModal(modal) {
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }

    // Close modal on overlay click or close button
    document.querySelectorAll('.modal').forEach(function (modal) {
        modal.querySelector('.modal-overlay').addEventListener('click', function () {
            closeModal(modal);
        });
        modal.querySelector('.modal-close').addEventListener('click', function () {
            closeModal(modal);
        });
    });

    // Close modal on Escape
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.modal.active').forEach(function (modal) {
                closeModal(modal);
            });
        }
    });

    // ==================== PLAN DETAILS MODAL ====================
    document.querySelectorAll('.plan-info-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const planId = parseInt(this.dataset.planId);
            const plan = window.plansData.find(function (p) { return p.id === planId; });
            if (!plan) return;

            const content = document.getElementById('planModalContent');
            let featuresHtml = '';
            if (plan.mobile_data) {
                featuresHtml += '<li><i class="fas fa-mobile-alt"></i> ' + escapeHtml(plan.mobile_data) + ' - ' + escapeHtml(plan.mobile_desc || '') + '</li>';
            }
            if (plan.streaming_name) {
                featuresHtml += '<li><i class="fas fa-play-circle"></i> ' + escapeHtml(plan.streaming_name) + '</li>';
            }
            if (plan.info_details) {
                featuresHtml += '<li><i class="fas fa-check"></i> ' + escapeHtml(plan.info_details) + '</li>';
            }

            content.innerHTML =
                '<div class="plan-modal-header">' +
                    '<h3 class="plan-modal-name">' + escapeHtml(plan.plan_name) + '</h3>' +
                    '<div class="plan-modal-speed">' + escapeHtml(plan.speed) + ' <span>Mega</span></div>' +
                '</div>' +
                (featuresHtml ? '<ul class="plan-modal-features">' + featuresHtml + '</ul>' : '') +
                '<div class="plan-modal-price">' +
                    '<span class="price-big">R$ ' + escapeHtml(plan.price_decimal) + '</span>' +
                    '<span class="price-small">,' + escapeHtml(plan.price_cents) + escapeHtml(plan.price_period) + '</span>' +
                '</div>' +
                (plan.payment_note ? '<p style="color:#666;font-size:0.85rem;margin-bottom:20px;">' + escapeHtml(plan.payment_note) + '</p>' : '') +
                '<a href="' + escapeHtml(plan.cta_link || '#contato') + '" class="btn btn-plan">' + escapeHtml(plan.cta_text || 'Assine já') + '</a>';

            openModal('planModal');
        });
    });

    // ==================== CONTACT FORM ====================
    function handleFormSubmit(form, feedbackId) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            const feedback = document.getElementById(feedbackId);
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;

            // Disable button
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando...';

            // Clear feedback
            if (feedback) {
                feedback.className = 'form-feedback';
                feedback.style.display = 'none';
            }

            // Get form data
            const formData = new FormData(form);

            // Send AJAX request
            fetch('api/contact.php', {
                method: 'POST',
                body: formData
            })
            .then(function (response) { return response.json(); })
            .then(function (data) {
                if (feedback) {
                    feedback.textContent = data.message;
                    feedback.className = 'form-feedback ' + (data.success ? 'success' : 'error');
                    feedback.style.display = 'block';
                }

                if (data.success) {
                    form.reset();
                    // Auto-hide success message after 5 seconds
                    setTimeout(function () {
                        if (feedback) {
                            feedback.style.display = 'none';
                        }
                    }, 5000);
                }
            })
            .catch(function () {
                if (feedback) {
                    feedback.textContent = 'Erro de conexão. Verifique sua internet e tente novamente.';
                    feedback.className = 'form-feedback error';
                    feedback.style.display = 'block';
                }
            })
            .finally(function () {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            });
        });
    }

    // Contact modal form
    const contactForm = document.getElementById('contactForm');
    if (contactForm) {
        handleFormSubmit(contactForm, 'contactFeedback');
    }

    // Callback form
    const callbackForm = document.getElementById('callbackForm');
    if (callbackForm) {
        handleFormSubmit(callbackForm, 'callbackFeedback');
    }

    // ==================== WHATSAPP FLOATING BUTTON ====================
    const whatsappBtn = document.getElementById('whatsappBtn');
    const whatsappMenu = document.getElementById('whatsappMenu');

    if (whatsappBtn && whatsappMenu) {
        whatsappBtn.addEventListener('click', function () {
            whatsappMenu.classList.toggle('active');
        });

        // Close menu when clicking outside
        document.addEventListener('click', function (e) {
            if (!e.target.closest('#whatsappFloat')) {
                whatsappMenu.classList.remove('active');
            }
        });
    }

    // ==================== PHONE MASK ====================
    document.querySelectorAll('input[type="tel"]').forEach(function (input) {
        input.addEventListener('input', function () {
            let value = this.value.replace(/\D/g, '');
            if (value.length > 11) value = value.slice(0, 11);

            if (value.length > 6) {
                value = '(' + value.slice(0, 2) + ') ' + value.slice(2, 7) + '-' + value.slice(7);
            } else if (value.length > 2) {
                value = '(' + value.slice(0, 2) + ') ' + value.slice(2);
            } else if (value.length > 0) {
                value = '(' + value;
            }

            this.value = value;
        });
    });

    // ==================== UTILITY ====================
    function escapeHtml(text) {
        if (!text) return '';
        var div = document.createElement('div');
        div.appendChild(document.createTextNode(text));
        return div.innerHTML;
    }

    // ==================== ANIMATIONS ON SCROLL ====================
    const observerOptions = {
        root: null,
        rootMargin: '0px',
        threshold: 0.1
    };

    const observer = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (entry.isIntersecting) {
                entry.target.classList.add('animated');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    // Observe elements for animation
    document.querySelectorAll('.plan-card, .diff-card, .mesh-card, .why-content').forEach(function (el) {
        el.style.opacity = '0';
        el.style.transform = 'translateY(30px)';
        el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(el);
    });

    // Add animation class styles
    var style = document.createElement('style');
    style.textContent = '.animated { opacity: 1 !important; transform: translateY(0) !important; }';
    document.head.appendChild(style);

})();
