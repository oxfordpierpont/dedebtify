/**
 * DeDebtify PWA Handler
 *
 * Handles service worker registration, install prompts, and push notifications
 *
 * @since      1.0.0
 * @package    Dedebtify
 */

(function($) {
    'use strict';

    let deferredPrompt;
    let swRegistration;

    $(document).ready(function() {
        if ('serviceWorker' in navigator) {
            initPWA();
        }
    });

    /**
     * Initialize PWA
     */
    function initPWA() {
        registerServiceWorker();
        setupInstallPrompt();
        checkNotificationPermission();
        setupConnectionMonitoring();
    }

    /**
     * Register Service Worker
     */
    function registerServiceWorker() {
        navigator.serviceWorker.register(dedebtifyPWA.serviceWorkerUrl, {
            scope: '/'
        })
        .then(function(registration) {
            console.log('[DeDebtify PWA] Service Worker registered:', registration.scope);
            swRegistration = registration;

            // Check for updates every hour
            setInterval(function() {
                registration.update();
            }, 60 * 60 * 1000);

            // Handle service worker updates
            registration.addEventListener('updatefound', function() {
                const newWorker = registration.installing;

                newWorker.addEventListener('statechange', function() {
                    if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                        showUpdateNotification();
                    }
                });
            });

            // Setup push notifications if enabled
            if (dedebtifyPWA.pushEnabled && registration.pushManager) {
                setupPushNotifications(registration);
            }
        })
        .catch(function(error) {
            console.error('[DeDebtify PWA] Service Worker registration failed:', error);
        });
    }

    /**
     * Setup install prompt
     */
    function setupInstallPrompt() {
        window.addEventListener('beforeinstallprompt', function(e) {
            console.log('[DeDebtify PWA] Install prompt available');

            // Prevent default prompt
            e.preventDefault();
            deferredPrompt = e;

            // Show custom install button
            showInstallButton();
        });

        // Handle successful installation
        window.addEventListener('appinstalled', function() {
            console.log('[DeDebtify PWA] App installed successfully');
            deferredPrompt = null;
            hideInstallButton();

            // Track installation
            if (typeof gtag !== 'undefined') {
                gtag('event', 'app_installed', {
                    event_category: 'pwa',
                    event_label: 'DeDebtify PWA'
                });
            }
        });
    }

    /**
     * Show install button
     */
    function showInstallButton() {
        // Create install banner if it doesn't exist
        if ($('#dd-pwa-install-banner').length === 0) {
            const banner = `
                <div id="dd-pwa-install-banner" class="dd-pwa-banner">
                    <div class="dd-pwa-banner-content">
                        <div class="dd-pwa-banner-icon">
                            <span class="dashicons dashicons-download"></span>
                        </div>
                        <div class="dd-pwa-banner-text">
                            <strong>Install DeDebtify</strong>
                            <p>Access your finances faster with our app</p>
                        </div>
                        <div class="dd-pwa-banner-actions">
                            <button id="dd-pwa-install-btn" class="dedebtify-btn dedebtify-btn-primary">
                                Install
                            </button>
                            <button id="dd-pwa-dismiss-btn" class="dedebtify-btn dedebtify-btn-secondary">
                                Later
                            </button>
                        </div>
                    </div>
                </div>
            `;

            $('body').append(banner);

            // Animate in
            setTimeout(function() {
                $('#dd-pwa-install-banner').addClass('show');
            }, 1000);

            // Handle install button click
            $('#dd-pwa-install-btn').on('click', function() {
                installApp();
            });

            // Handle dismiss button click
            $('#dd-pwa-dismiss-btn').on('click', function() {
                hideInstallButton();
                // Remember dismissal for 7 days
                localStorage.setItem('dd_pwa_install_dismissed', Date.now());
            });
        }

        // Check if user dismissed recently
        const dismissed = localStorage.getItem('dd_pwa_install_dismissed');
        if (dismissed) {
            const daysSinceDismissed = (Date.now() - parseInt(dismissed)) / (1000 * 60 * 60 * 24);
            if (daysSinceDismissed < 7) {
                return; // Don't show if dismissed within last 7 days
            }
        }

        $('#dd-pwa-install-banner').addClass('show');
    }

    /**
     * Hide install button
     */
    function hideInstallButton() {
        $('#dd-pwa-install-banner').removeClass('show');
        setTimeout(function() {
            $('#dd-pwa-install-banner').remove();
        }, 300);
    }

    /**
     * Install app
     */
    function installApp() {
        if (!deferredPrompt) {
            return;
        }

        // Show install prompt
        deferredPrompt.prompt();

        // Wait for user choice
        deferredPrompt.userChoice.then(function(choiceResult) {
            if (choiceResult.outcome === 'accepted') {
                console.log('[DeDebtify PWA] User accepted install');
            } else {
                console.log('[DeDebtify PWA] User dismissed install');
                localStorage.setItem('dd_pwa_install_dismissed', Date.now());
            }

            deferredPrompt = null;
            hideInstallButton();
        });
    }

    /**
     * Setup push notifications
     */
    function setupPushNotifications(registration) {
        // Check if notifications are supported
        if (!('Notification' in window) || !('PushManager' in window)) {
            console.log('[DeDebtify PWA] Push notifications not supported');
            return;
        }

        // Check current permission
        if (Notification.permission === 'granted') {
            subscribeToPush(registration);
        } else if (Notification.permission !== 'denied') {
            // Permission not yet requested
            showNotificationPrompt();
        }
    }

    /**
     * Check notification permission
     */
    function checkNotificationPermission() {
        if (!('Notification' in window)) {
            return;
        }

        // Update UI based on permission
        updateNotificationStatus(Notification.permission);
    }

    /**
     * Show notification permission prompt
     */
    function showNotificationPrompt() {
        // Create notification prompt
        if ($('#dd-pwa-notification-prompt').length === 0 && !localStorage.getItem('dd_notification_dismissed')) {
            const prompt = `
                <div id="dd-pwa-notification-prompt" class="dd-pwa-notification-prompt">
                    <div class="dd-notification-prompt-content">
                        <span class="dashicons dashicons-bell"></span>
                        <div class="dd-notification-prompt-text">
                            <strong>Stay Updated</strong>
                            <p>Get notified about bill due dates and financial goals</p>
                        </div>
                        <button id="dd-enable-notifications-btn" class="dedebtify-btn dedebtify-btn-small">
                            Enable
                        </button>
                        <button id="dd-dismiss-notifications-btn" class="dd-btn-icon">
                            <span class="dashicons dashicons-no-alt"></span>
                        </button>
                    </div>
                </div>
            `;

            $('.dedebtify-dashboard').prepend(prompt);

            // Handle enable button
            $('#dd-enable-notifications-btn').on('click', function() {
                requestNotificationPermission();
            });

            // Handle dismiss button
            $('#dd-dismiss-notifications-btn').on('click', function() {
                $('#dd-pwa-notification-prompt').fadeOut(function() {
                    $(this).remove();
                });
                localStorage.setItem('dd_notification_dismissed', Date.now());
            });
        }
    }

    /**
     * Request notification permission
     */
    function requestNotificationPermission() {
        Notification.requestPermission().then(function(permission) {
            console.log('[DeDebtify PWA] Notification permission:', permission);

            updateNotificationStatus(permission);

            if (permission === 'granted') {
                $('#dd-pwa-notification-prompt').fadeOut(function() {
                    $(this).remove();
                });

                // Subscribe to push
                if (swRegistration && swRegistration.pushManager) {
                    subscribeToPush(swRegistration);
                }

                // Show success message
                showToast('Notifications enabled! You\'ll receive updates about your finances.', 'success');
            }
        });
    }

    /**
     * Subscribe to push notifications
     */
    function subscribeToPush(registration) {
        const vapidPublicKey = dedebtifyPWA.vapidPublicKey;

        if (!vapidPublicKey) {
            console.log('[DeDebtify PWA] VAPID public key not configured');
            return;
        }

        registration.pushManager.subscribe({
            userVisibleOnly: true,
            applicationServerKey: urlBase64ToUint8Array(vapidPublicKey)
        })
        .then(function(subscription) {
            console.log('[DeDebtify PWA] Push subscription:', subscription);

            // Send subscription to server
            return fetch(dedebtifyPWA.ajaxUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'dedebtify_save_push_subscription',
                    subscription: subscription,
                    nonce: dedebtifyPWA.nonce
                })
            });
        })
        .then(function(response) {
            if (response.ok) {
                console.log('[DeDebtify PWA] Subscription saved to server');
            }
        })
        .catch(function(error) {
            console.error('[DeDebtify PWA] Push subscription failed:', error);
        });
    }

    /**
     * Update notification status in settings
     */
    function updateNotificationStatus(permission) {
        const $statusIndicator = $('.dd-notification-status');

        if ($statusIndicator.length) {
            if (permission === 'granted') {
                $statusIndicator.html('<span class="dashicons dashicons-yes-alt" style="color: #00a32a;"></span> Enabled');
            } else if (permission === 'denied') {
                $statusIndicator.html('<span class="dashicons dashicons-dismiss" style="color: #d63638;"></span> Blocked');
            } else {
                $statusIndicator.html('<span class="dashicons dashicons-warning" style="color: #dba617;"></span> Not Enabled');
            }
        }
    }

    /**
     * Setup connection monitoring
     */
    function setupConnectionMonitoring() {
        window.addEventListener('online', function() {
            console.log('[DeDebtify PWA] Connection restored');
            showToast('You\'re back online! Syncing data...', 'success');

            // Trigger background sync if available
            if (swRegistration && swRegistration.sync) {
                swRegistration.sync.register('sync-data').catch(function(error) {
                    console.error('[DeDebtify PWA] Sync registration failed:', error);
                });
            }
        });

        window.addEventListener('offline', function() {
            console.log('[DeDebtify PWA] Connection lost');
            showToast('You\'re offline. Changes will sync when you\'re back online.', 'warning');
        });
    }

    /**
     * Show update notification
     */
    function showUpdateNotification() {
        const updateBanner = `
            <div id="dd-pwa-update-banner" class="dd-pwa-update-banner">
                <div class="dd-update-banner-content">
                    <span class="dashicons dashicons-update"></span>
                    <span>A new version is available!</span>
                    <button id="dd-pwa-update-btn" class="dedebtify-btn dedebtify-btn-small">
                        Update Now
                    </button>
                </div>
            </div>
        `;

        $('body').append(updateBanner);

        $('#dd-pwa-update-btn').on('click', function() {
            if (swRegistration && swRegistration.waiting) {
                swRegistration.waiting.postMessage({ type: 'SKIP_WAITING' });
            }

            window.location.reload();
        });
    }

    /**
     * Show toast notification
     */
    function showToast(message, type = 'info') {
        const toastClass = 'dd-toast-' + type;
        const toast = `
            <div class="dd-toast ${toastClass}">
                ${message}
            </div>
        `;

        $('body').append(toast);

        const $toast = $('.dd-toast').last();
        setTimeout(function() {
            $toast.addClass('show');
        }, 100);

        setTimeout(function() {
            $toast.removeClass('show');
            setTimeout(function() {
                $toast.remove();
            }, 300);
        }, 4000);
    }

    /**
     * Convert VAPID key
     */
    function urlBase64ToUint8Array(base64String) {
        const padding = '='.repeat((4 - base64String.length % 4) % 4);
        const base64 = (base64String + padding)
            .replace(/\-/g, '+')
            .replace(/_/g, '/');

        const rawData = window.atob(base64);
        const outputArray = new Uint8Array(rawData.length);

        for (let i = 0; i < rawData.length; ++i) {
            outputArray[i] = rawData.charCodeAt(i);
        }

        return outputArray;
    }

    /**
     * Create snapshot with offline support
     */
    window.createSnapshotOffline = function(data) {
        if (!navigator.onLine && swRegistration && swRegistration.sync) {
            // Save to cache for later sync
            caches.open('dedebtify-v1.0.0-sync').then(function(cache) {
                cache.put('pending-snapshot', new Response(JSON.stringify(data)));
            });

            // Register background sync
            swRegistration.sync.register('sync-snapshot');

            showToast('Snapshot saved. Will sync when online.', 'info');
            return Promise.resolve();
        }

        // Normal online behavior
        return $.ajax({
            url: dedebtify.restUrl + 'snapshot',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(data),
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-WP-Nonce', dedebtify.restNonce);
            }
        });
    };

    /**
     * Expose PWA functions globally
     */
    window.dedebtifyPWA = {
        installApp: installApp,
        requestNotifications: requestNotificationPermission,
        isInstalled: function() {
            return window.matchMedia('(display-mode: standalone)').matches ||
                   window.navigator.standalone === true;
        },
        isOnline: function() {
            return navigator.onLine;
        }
    };

})(jQuery);
