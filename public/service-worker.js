self.addEventListener('push', event => {
    const payload = event.data ? event.data.json() : {};
    const title = payload.title || 'Notification';
    const options = {
        body: payload.body,
        icon: payload.icon || '/images/icons/icon-192x192.png',
        data: payload.data || {},
        actions: payload.actions || [{ action: 'open_app', title: 'Mở' }],
    };
    event.waitUntil(self.registration.showNotification(title, options));
});

self.addEventListener('notificationclick', event => {
    event.notification.close();
    const targetUrl = (event.notification.data && event.notification.data.url) || '/';
    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then(windowClients => {
            const client = windowClients.find(wc => wc.url.includes(self.registration.scope));
            if (client) return client.focus();
            return clients.openWindow(targetUrl);
        })
    );
});
