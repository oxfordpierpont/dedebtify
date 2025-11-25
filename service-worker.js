/**
 * DeDebtify Service Worker
 * Handles caching, offline functionality, and push notifications
 *
 * @since      1.0.0
 * @package    Dedebtify
 */

const CACHE_VERSION = 'dedebtify-v1.0.0';
const CACHE_NAME = CACHE_VERSION;

// Assets to cache immediately on install
const PRECACHE_ASSETS = [
    '/debt-dashboard/',
    '/ai-coach/',
    '/wp-content/plugins/dedebtify/assets/css/dedebtify-design-system.css',
    '/wp-content/plugins/dedebtify/assets/css/dedebtify-public.css',
    '/wp-content/plugins/dedebtify/assets/css/dedebtify-enhanced.css',
    '/wp-content/plugins/dedebtify/assets/css/dedebtify-mobile-app.css',
    '/wp-content/plugins/dedebtify/assets/css/dedebtify-ai-coach.css',
    '/wp-content/plugins/dedebtify/assets/js/dedebtify-public.js',
    '/wp-content/plugins/dedebtify/assets/js/dedebtify-calculator.js',
    '/wp-content/plugins/dedebtify/assets/js/dedebtify-managers.js',
    '/wp-content/plugins/dedebtify/assets/js/dedebtify-ai-coach.js',
    '/wp-content/plugins/dedebtify/assets/images/icon-192x192.png',
    '/wp-content/plugins/dedebtify/assets/images/icon-512x512.png'
];

// Routes that should always be fetched from network
const NETWORK_ONLY_ROUTES = [
    '/wp-admin/',
    '/wp-login.php',
    '/wp-json/'
];

// Routes for cache-first strategy
const CACHE_FIRST_ROUTES = [
    '/wp-content/plugins/dedebtify/assets/',
    '/wp-content/themes/'
];

/**
 * Install Event - Cache essential assets
 */
self.addEventListener('install', (event) => {
    console.log('[DeDebtify SW] Installing service worker...');

    event.waitUntil(
        caches.open(CACHE_NAME)
            .then((cache) => {
                console.log('[DeDebtify SW] Caching essential assets');
                return cache.addAll(PRECACHE_ASSETS.map(url => new Request(url, {
                    cache: 'reload'
                })));
            })
            .then(() => {
                console.log('[DeDebtify SW] Installation complete');
                return self.skipWaiting(); // Activate immediately
            })
            .catch((error) => {
                console.error('[DeDebtify SW] Installation failed:', error);
            })
    );
});

/**
 * Activate Event - Clean up old caches
 */
self.addEventListener('activate', (event) => {
    console.log('[DeDebtify SW] Activating service worker...');

    event.waitUntil(
        caches.keys()
            .then((cacheNames) => {
                return Promise.all(
                    cacheNames.map((cacheName) => {
                        if (cacheName !== CACHE_NAME && cacheName.startsWith('dedebtify-')) {
                            console.log('[DeDebtify SW] Deleting old cache:', cacheName);
                            return caches.delete(cacheName);
                        }
                    })
                );
            })
            .then(() => {
                console.log('[DeDebtify SW] Activation complete');
                return self.clients.claim(); // Take control immediately
            })
    );
});

/**
 * Fetch Event - Serve from cache or network based on strategy
 */
self.addEventListener('fetch', (event) => {
    const { request } = event;
    const url = new URL(request.url);

    // Skip non-GET requests
    if (request.method !== 'GET') {
        return;
    }

    // Network-only routes
    if (shouldUseNetworkOnly(url.pathname)) {
        event.respondWith(fetch(request));
        return;
    }

    // Cache-first strategy for static assets
    if (shouldUseCacheFirst(url.pathname)) {
        event.respondWith(cacheFirst(request));
        return;
    }

    // Network-first strategy for dynamic content
    event.respondWith(networkFirst(request));
});

/**
 * Check if route should use network-only strategy
 */
function shouldUseNetworkOnly(pathname) {
    return NETWORK_ONLY_ROUTES.some(route => pathname.startsWith(route));
}

/**
 * Check if route should use cache-first strategy
 */
function shouldUseCacheFirst(pathname) {
    return CACHE_FIRST_ROUTES.some(route => pathname.includes(route));
}

/**
 * Cache-first strategy
 * Try cache first, fallback to network
 */
async function cacheFirst(request) {
    const cache = await caches.open(CACHE_NAME);
    const cachedResponse = await cache.match(request);

    if (cachedResponse) {
        // Return cached version and update in background
        updateCache(request, cache);
        return cachedResponse;
    }

    try {
        const networkResponse = await fetch(request);
        if (networkResponse.ok) {
            cache.put(request, networkResponse.clone());
        }
        return networkResponse;
    } catch (error) {
        console.error('[DeDebtify SW] Cache-first fetch failed:', error);
        return new Response('Offline - Resource not available', {
            status: 503,
            statusText: 'Service Unavailable'
        });
    }
}

/**
 * Network-first strategy
 * Try network first, fallback to cache
 */
async function networkFirst(request) {
    const cache = await caches.open(CACHE_NAME);

    try {
        const networkResponse = await fetch(request);

        if (networkResponse.ok) {
            // Cache successful responses
            cache.put(request, networkResponse.clone());
        }

        return networkResponse;
    } catch (error) {
        console.log('[DeDebtify SW] Network failed, trying cache:', error);

        const cachedResponse = await cache.match(request);

        if (cachedResponse) {
            return cachedResponse;
        }

        // Return offline page for navigation requests
        if (request.mode === 'navigate') {
            const offlinePage = await cache.match('/wp-content/plugins/dedebtify/offline.html');
            if (offlinePage) {
                return offlinePage;
            }
        }

        return new Response('Offline - No cached version available', {
            status: 503,
            statusText: 'Service Unavailable',
            headers: { 'Content-Type': 'text/plain' }
        });
    }
}

/**
 * Update cache in background
 */
async function updateCache(request, cache) {
    try {
        const networkResponse = await fetch(request);
        if (networkResponse.ok) {
            cache.put(request, networkResponse.clone());
        }
    } catch (error) {
        // Silently fail - we already have a cached version
    }
}

/**
 * Push Notification Event
 */
self.addEventListener('push', (event) => {
    console.log('[DeDebtify SW] Push notification received');

    let notificationData = {
        title: 'DeDebtify',
        body: 'You have a new notification',
        icon: '/wp-content/plugins/dedebtify/assets/images/icon-192x192.png',
        badge: '/wp-content/plugins/dedebtify/assets/images/badge-72x72.png',
        vibrate: [200, 100, 200],
        data: {
            url: '/debt-dashboard/'
        }
    };

    if (event.data) {
        try {
            const payload = event.data.json();
            notificationData = { ...notificationData, ...payload };
        } catch (e) {
            notificationData.body = event.data.text();
        }
    }

    event.waitUntil(
        self.registration.showNotification(notificationData.title, {
            body: notificationData.body,
            icon: notificationData.icon,
            badge: notificationData.badge,
            vibrate: notificationData.vibrate,
            data: notificationData.data,
            actions: notificationData.actions || [
                {
                    action: 'open',
                    title: 'View',
                    icon: '/wp-content/plugins/dedebtify/assets/images/action-view.png'
                },
                {
                    action: 'close',
                    title: 'Dismiss',
                    icon: '/wp-content/plugins/dedebtify/assets/images/action-close.png'
                }
            ],
            tag: notificationData.tag || 'dedebtify-notification',
            requireInteraction: notificationData.requireInteraction || false,
            renotify: true
        })
    );
});

/**
 * Notification Click Event
 */
self.addEventListener('notificationclick', (event) => {
    console.log('[DeDebtify SW] Notification clicked');

    event.notification.close();

    if (event.action === 'close') {
        return;
    }

    const urlToOpen = event.notification.data?.url || '/debt-dashboard/';

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true })
            .then((clientList) => {
                // Check if app is already open
                for (let client of clientList) {
                    if (client.url.includes(urlToOpen) && 'focus' in client) {
                        return client.focus();
                    }
                }

                // Open new window
                if (clients.openWindow) {
                    return clients.openWindow(urlToOpen);
                }
            })
    );
});

/**
 * Background Sync Event
 */
self.addEventListener('sync', (event) => {
    console.log('[DeDebtify SW] Background sync:', event.tag);

    if (event.tag === 'sync-snapshot') {
        event.waitUntil(syncSnapshot());
    } else if (event.tag === 'sync-data') {
        event.waitUntil(syncAllData());
    }
});

/**
 * Sync financial snapshot
 */
async function syncSnapshot() {
    try {
        // Get pending snapshot data from IndexedDB or cache
        const cache = await caches.open(CACHE_NAME + '-sync');
        const pendingData = await cache.match('pending-snapshot');

        if (!pendingData) {
            return;
        }

        const snapshotData = await pendingData.json();

        // Send to server
        const response = await fetch('/wp-json/dedebtify/v1/snapshot', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(snapshotData)
        });

        if (response.ok) {
            // Clear pending data
            await cache.delete('pending-snapshot');

            // Show success notification
            self.registration.showNotification('Snapshot Saved', {
                body: 'Your financial snapshot has been synced successfully',
                icon: '/wp-content/plugins/dedebtify/assets/images/icon-192x192.png',
                tag: 'sync-success'
            });
        }
    } catch (error) {
        console.error('[DeDebtify SW] Snapshot sync failed:', error);
    }
}

/**
 * Sync all pending data
 */
async function syncAllData() {
    console.log('[DeDebtify SW] Syncing all pending data...');
    // Implement comprehensive data sync logic
    await syncSnapshot();
}

/**
 * Message Event - Handle messages from client
 */
self.addEventListener('message', (event) => {
    console.log('[DeDebtify SW] Message received:', event.data);

    if (event.data.type === 'SKIP_WAITING') {
        self.skipWaiting();
    } else if (event.data.type === 'CLEAR_CACHE') {
        event.waitUntil(
            caches.keys().then((cacheNames) => {
                return Promise.all(
                    cacheNames.map((cacheName) => {
                        if (cacheName.startsWith('dedebtify-')) {
                            return caches.delete(cacheName);
                        }
                    })
                );
            })
        );
    } else if (event.data.type === 'GET_VERSION') {
        event.ports[0].postMessage({ version: CACHE_VERSION });
    }
});

console.log('[DeDebtify SW] Service worker loaded');
