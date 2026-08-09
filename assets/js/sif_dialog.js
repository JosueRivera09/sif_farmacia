/*
 * Archivo: assets/js/sif_dialog.js
 * Propósito: Sistema global de alertas y confirmaciones personalizadas de SIF Farmacia.
 * Incluye reproducción de sonido sintetizado mediante Web Audio API y modal con diseño fiel al sistema.
 */

(function () {
    // ---------------------------------------------------------
    // 1. Sintetizador de Sonido de Confirmación (Web Audio API)
    // ---------------------------------------------------------
    function playAudioTone(type = 'success') {
        try {
            const AudioCtx = window.AudioContext || window.webkitAudioContext;
            if (!AudioCtx) return;
            const ctx = new AudioCtx();

            if (ctx.state === 'suspended') {
                ctx.resume();
            }

            const now = ctx.currentTime;

            if (type === 'success' || type === 'confirm') {
                // Tono de confirmación / Éxito (Acorde armónico Esmeralda C5 -> E5 -> G5)
                const notes = [523.25, 659.25, 783.99];
                notes.forEach((freq, idx) => {
                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();

                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(freq, now + idx * 0.08);

                    gain.gain.setValueAtTime(0, now + idx * 0.08);
                    gain.gain.linearRampToValueAtTime(0.18, now + idx * 0.08 + 0.02);
                    gain.gain.exponentialRampToValueAtTime(0.001, now + idx * 0.08 + 0.25);

                    osc.connect(gain);
                    gain.connect(ctx.destination);

                    osc.start(now + idx * 0.08);
                    osc.stop(now + idx * 0.08 + 0.25);
                });
            } else if (type === 'error') {
                // Tono suave de advertencia / error (2 tonos descendentes F4 -> C4)
                const notes = [349.23, 261.63];
                notes.forEach((freq, idx) => {
                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();

                    osc.type = 'triangle';
                    osc.frequency.setValueAtTime(freq, now + idx * 0.1);

                    gain.gain.setValueAtTime(0, now + idx * 0.1);
                    gain.gain.linearRampToValueAtTime(0.15, now + idx * 0.1 + 0.02);
                    gain.gain.exponentialRampToValueAtTime(0.001, now + idx * 0.1 + 0.3);

                    osc.connect(gain);
                    gain.connect(ctx.destination);

                    osc.start(now + idx * 0.1);
                    osc.stop(now + idx * 0.1 + 0.3);
                });
            }
        } catch (e) {
            console.warn("Audio Context error:", e);
        }
    }

    // ---------------------------------------------------------
    // 2. Estilos e Inyección del Modal Personalizado SIF
    // ---------------------------------------------------------
    const styleId = 'sif-dialog-styles';
    if (!document.getElementById(styleId)) {
        const style = document.createElement('style');
        style.id = styleId;
        style.textContent = `
            .sif-dialog-overlay {
                position: fixed;
                top: 0; left: 0; right: 0; bottom: 0;
                background: rgba(15, 23, 42, 0.75);
                backdrop-filter: blur(6px);
                z-index: 99999;
                display: flex;
                align-items: center;
                justify-content: center;
                opacity: 0;
                transition: opacity 0.25s ease-in-out;
                padding: 20px;
            }
            .sif-dialog-overlay.active {
                opacity: 1;
            }
            .sif-dialog-box {
                background: #1e293b;
                border: 1px solid rgba(255, 255, 255, 0.1);
                box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.5), 0 8px 10px -6px rgba(0, 0, 0, 0.5);
                border-radius: 16px;
                max-width: 440px;
                width: 100%;
                overflow: hidden;
                transform: scale(0.92);
                transition: transform 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
                color: #f8fafc;
                font-family: system-ui, -apple-system, sans-serif;
            }
            .sif-dialog-overlay.active .sif-dialog-box {
                transform: scale(1);
            }
            .sif-dialog-header {
                padding: 20px 24px 10px 24px;
                display: flex;
                align-items: center;
                gap: 12px;
            }
            .sif-dialog-icon {
                font-size: 32px;
                display: flex;
                align-items: center;
                justify-content: center;
                width: 44px;
                height: 44px;
                border-radius: 12px;
                flex-shrink: 0;
            }
            .sif-dialog-icon.success { background: rgba(16, 185, 129, 0.15); color: #10b981; }
            .sif-dialog-icon.error { background: rgba(239, 68, 68, 0.15); color: #ef4444; }
            .sif-dialog-icon.warning { background: rgba(245, 158, 11, 0.15); color: #f59e0b; }
            .sif-dialog-icon.info { background: rgba(59, 130, 246, 0.15); color: #3b82f6; }
            
            .sif-dialog-title {
                font-size: 17px;
                font-weight: 700;
                margin: 0;
                color: #f8fafc;
            }
            .sif-dialog-body {
                padding: 10px 24px 20px 24px;
                font-size: 14px;
                color: #94a3b8;
                line-height: 1.5;
                white-space: pre-wrap;
                word-break: break-word;
            }
            .sif-dialog-footer {
                padding: 14px 24px;
                background: rgba(15, 23, 42, 0.4);
                border-top: 1px solid rgba(255, 255, 255, 0.05);
                display: flex;
                justify-content: flex-end;
                gap: 10px;
            }
            .sif-dialog-btn {
                padding: 8px 18px;
                border-radius: 8px;
                font-size: 13px;
                font-weight: 600;
                border: none;
                cursor: pointer;
                transition: all 0.2s;
            }
            .sif-dialog-btn-primary {
                background: #10b981;
                color: #ffffff;
            }
            .sif-dialog-btn-primary:hover {
                background: #059669;
            }
            .sif-dialog-btn-secondary {
                background: #334155;
                color: #cbd5e1;
            }
            .sif-dialog-btn-secondary:hover {
                background: #475569;
                color: #f8fafc;
            }
        `;
        document.head.appendChild(style);
    }

    // ---------------------------------------------------------
    // 3. Objeto Principal SIFDialog
    // ---------------------------------------------------------
    window.SIFDialog = {
        show: function (options) {
            return new Promise((resolve) => {
                const message = options.message || options.text || '';
                const type = options.type || 'info'; // success, error, warning, info
                const title = options.title || (type === 'error' ? 'Atención' : (type === 'warning' ? 'Advertencia' : (type === 'success' ? 'Operación Exitosa' : 'Notificación de Sistema')));
                const isConfirm = !!options.confirm;

                // Reproducir sonido
                playAudioTone(type === 'error' ? 'error' : 'success');

                const overlay = document.createElement('div');
                overlay.className = 'sif-dialog-overlay';

                let iconSymbol = 'info';
                if (type === 'success') iconSymbol = 'check_circle';
                else if (type === 'error') iconSymbol = 'cancel';
                else if (type === 'warning') iconSymbol = 'warning';
                else if (isConfirm) iconSymbol = 'help';

                overlay.innerHTML = `
                    <div class="sif-dialog-box">
                        <div class="sif-dialog-header">
                            <div class="sif-dialog-icon ${type}">
                                <span class="material-symbols-outlined">${iconSymbol}</span>
                            </div>
                            <h5 class="sif-dialog-title">${title}</h5>
                        </div>
                        <div class="sif-dialog-body">${message}</div>
                        <div class="sif-dialog-footer">
                            ${isConfirm ? `<button class="sif-dialog-btn sif-dialog-btn-secondary btn-cancel">Cancelar</button>` : ''}
                            <button class="sif-dialog-btn sif-dialog-btn-primary btn-confirm">${isConfirm ? 'Aceptar' : 'Entendido'}</button>
                        </div>
                    </div>
                `;

                document.body.appendChild(overlay);

                // Animar entrada
                requestAnimationFrame(() => {
                    overlay.classList.add('active');
                });

                function closeDialog(result) {
                    overlay.classList.remove('active');
                    setTimeout(() => {
                        if (overlay.parentNode) {
                            overlay.parentNode.removeChild(overlay);
                        }
                        resolve(result);
                    }, 250);
                }

                const btnConfirm = overlay.querySelector('.btn-confirm');
                if (btnConfirm) {
                    btnConfirm.focus();
                    btnConfirm.addEventListener('click', () => closeDialog(true));
                }

                const btnCancel = overlay.querySelector('.btn-cancel');
                if (btnCancel) {
                    btnCancel.addEventListener('click', () => closeDialog(false));
                }

                overlay.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape') {
                        closeDialog(isConfirm ? false : true);
                    } else if (e.key === 'Enter') {
                        closeDialog(true);
                    }
                });
            });
        },

        alert: function (message, title) {
            let type = 'info';
            const msgLower = String(message).toLowerCase();
            if (msgLower.includes('éxito') || msgLower.includes('exitosamente') || msgLower.includes('procesado') || msgLower.includes('correcto') || msgLower.includes('guardado')) {
                type = 'success';
            } else if (msgLower.includes('error') || msgLower.includes('inválido') || msgLower.includes('insuficiente') || msgLower.includes('obligatorio') || msgLower.includes('incorrecto') || msgLower.includes('falló')) {
                type = 'error';
            }
            return window.SIFDialog.show({ message, title, type });
        },

        confirm: function (message, title = 'Confirmación de Operación') {
            return window.SIFDialog.show({ message, title, type: 'warning', confirm: true });
        },

        playSound: function (type = 'success') {
            playAudioTone(type);
        }
    };

    // ---------------------------------------------------------
    // 4. Sobrescribir alert() NATIVO
    // ---------------------------------------------------------
    window.alert = function (message) {
        window.SIFDialog.alert(message);
    };

})();
