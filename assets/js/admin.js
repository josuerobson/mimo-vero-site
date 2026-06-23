/**
 * SuaNet Fibra - Admin Panel JavaScript
 */

(function () {
    'use strict';

    // ==================== SIDEBAR TOGGLE ====================
    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');

    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function () {
            sidebar.classList.toggle('open');
        });

        // Close sidebar on overlay click (mobile)
        document.addEventListener('click', function (e) {
            if (window.innerWidth <= 1024 && sidebar.classList.contains('open')) {
                if (!e.target.closest('.sidebar') && !e.target.closest('.sidebar-toggle')) {
                    sidebar.classList.remove('open');
                }
            }
        });
    }

    // ==================== SAVE SETTINGS (INLINE) ====================
    document.querySelectorAll('.btn-save-setting').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var key = this.dataset.key;
            var input = document.querySelector('[data-key="' + key + '"]');
            var statusEl = document.getElementById('status-' + key);

            if (!input) return;

            var value = input.value;

            // Disable button during save
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

            // Clear status
            if (statusEl) {
                statusEl.textContent = '';
                statusEl.className = 'save-status';
            }

            var formData = new FormData();
            formData.append('key', key);
            formData.append('value', value);

            fetch('api/save_setting.php', {
                method: 'POST',
                body: formData
            })
            .then(function (response) { return response.json(); })
            .then(function (data) {
                if (statusEl) {
                    statusEl.textContent = data.message || (data.success ? 'Salvo!' : 'Erro');
                    statusEl.className = 'save-status ' + (data.success ? 'success' : 'error');
                }

                // Auto-hide status after 3 seconds
                setTimeout(function () {
                    if (statusEl) {
                        statusEl.textContent = '';
                        statusEl.className = 'save-status';
                    }
                }, 3000);
            })
            .catch(function () {
                if (statusEl) {
                    statusEl.textContent = 'Erro de conexão';
                    statusEl.className = 'save-status error';
                }
            })
            .finally(function () {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-save"></i>';
            });
        });
    });

    // ==================== COLOR INPUT SYNC ====================
    document.querySelectorAll('.setting-color').forEach(function (colorInput) {
        var textInput = document.querySelector('[data-for="' + colorInput.id + '"]');
        if (!textInput) return;

        colorInput.addEventListener('input', function () {
            textInput.value = this.value;
        });

        textInput.addEventListener('input', function () {
            if (/^#[0-9A-Fa-f]{6}$/.test(this.value)) {
                colorInput.value = this.value;
            }
        });
    });

    // ==================== TOGGLE ACTIVE STATUS ====================
    document.querySelectorAll('.toggle-active').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var table = this.dataset.table;
            var id = this.dataset.id;
            var button = this;

            var formData = new FormData();
            formData.append('table', table);
            formData.append('id', id);

            fetch('api/toggle_active.php', {
                method: 'POST',
                body: formData
            })
            .then(function (response) { return response.json(); })
            .then(function (data) {
                if (data.success) {
                    var badge = button.querySelector('.status-badge');
                    if (badge) {
                        if (data.active) {
                            badge.className = 'status-badge status-active';
                            badge.textContent = 'Ativo';
                            button.closest('tr').classList.remove('inactive-row');
                        } else {
                            badge.className = 'status-badge status-inactive';
                            badge.textContent = 'Inativo';
                            button.closest('tr').classList.add('inactive-row');
                        }
                    }
                } else {
                    alert(data.message || 'Erro ao alterar status');
                }
            })
            .catch(function () {
                alert('Erro de conexão');
            });
        });
    });

    // ==================== DELETE ITEM ====================
    document.querySelectorAll('.delete-item').forEach(function (btn) {
        btn.addEventListener('click', function () {
            if (!confirm('Tem certeza que deseja excluir este item?')) return;

            var table = this.dataset.table;
            var id = this.dataset.id;
            var row = this.closest('tr');

            var formData = new FormData();
            formData.append('table', table);
            formData.append('id', id);

            fetch('api/delete_item.php', {
                method: 'POST',
                body: formData
            })
            .then(function (response) { return response.json(); })
            .then(function (data) {
                if (data.success) {
                    if (row) {
                        row.style.transition = 'opacity 0.3s, transform 0.3s';
                        row.style.opacity = '0';
                        row.style.transform = 'translateX(20px)';
                        setTimeout(function () {
                            row.remove();
                            // Check if table is empty
                            var tbody = document.querySelector('.table tbody');
                            if (tbody && tbody.children.length === 0) {
                                var card = tbody.closest('.card');
                                if (card) {
                                    card.querySelector('.card-body').innerHTML =
                                        '<p class="empty-state">Nenhum item cadastrado.</p>';
                                }
                            }
                        }, 300);
                    }
                } else {
                    alert(data.message || 'Erro ao excluir');
                }
            })
            .catch(function () {
                alert('Erro de conexão');
            });
        });
    });

    // ==================== ICON PICKER ====================
    var iconInput = document.getElementById('iconInput');
    var iconPreview = document.getElementById('iconPreview');

    if (iconInput) {
        iconInput.addEventListener('input', function () {
            updateIconPreview(this.value);
        });
    }

    document.querySelectorAll('.icon-suggestion').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var icon = this.dataset.icon;
            if (iconInput) {
                iconInput.value = icon;
                updateIconPreview(icon);
            }

            // Update selected state
            document.querySelectorAll('.icon-suggestion').forEach(function (b) {
                b.classList.remove('selected');
            });
            this.classList.add('selected');
        });
    });

    function updateIconPreview(iconClass) {
        if (iconPreview) {
            if (iconClass) {
                iconPreview.innerHTML = '<i class="fas ' + escapeHtml(iconClass) + '"></i>';
            } else {
                iconPreview.innerHTML = '';
            }
        }
    }

    // ==================== EXPAND MESSAGE ====================
    document.querySelectorAll('.expand-msg').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var cell = this.closest('.msg-cell');
            var preview = cell.querySelector('.msg-preview');
            var full = cell.querySelector('.msg-full');

            if (full.style.display === 'none') {
                preview.style.display = 'none';
                full.style.display = 'inline';
                this.textContent = 'ver menos';
            } else {
                preview.style.display = 'block';
                full.style.display = 'none';
                this.textContent = 'ver mais';
            }
        });
    });

    // ==================== ENTER KEY ON SETTINGS ====================
    document.querySelectorAll('.setting-input').forEach(function (input) {
        input.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                var key = this.dataset.key;
                var saveBtn = document.querySelector('.btn-save-setting[data-key="' + key + '"]');
                if (saveBtn) {
                    saveBtn.click();
                }
            }
        });
    });

    // ==================== UTILITY ====================
    function escapeHtml(text) {
        if (!text) return '';
        var div = document.createElement('div');
        div.appendChild(document.createTextNode(text));
        return div.innerHTML;
    }

    // ==================== AUTO-HIDE ALERTS ====================
    document.querySelectorAll('.alert-success').forEach(function (alert) {
        setTimeout(function () {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(function () {
                alert.remove();
            }, 500);
        }, 4000);
    });

})();
