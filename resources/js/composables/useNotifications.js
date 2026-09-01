const NOTIFICATIONS_KEY = 'app_notifications';

export function getStoredNotifications() {
    if (typeof window === 'undefined') return [];
    try {
        const raw = localStorage.getItem(NOTIFICATIONS_KEY);
        if (!raw) return [];
        return JSON.parse(raw) || [];
    } catch (error) {
        console.warn('No se pudieron leer las notificaciones', error);
        return [];
    }
}

export function setStoredNotifications(items) {
    if (typeof window === 'undefined') return;
    try {
        localStorage.setItem(NOTIFICATIONS_KEY, JSON.stringify(items));
    } catch (error) {
        console.warn('No se pudieron guardar las notificaciones', error);
    }
}

export function markNotificationRead(id) {
    const items = getStoredNotifications();
    const next = items.map((item) => (item.id === id ? { ...item, read: true } : item));
    setStoredNotifications(next);
    return next;
}

export function markAllNotificationsRead() {
    const items = getStoredNotifications();
    const next = items.map((item) => ({ ...item, read: true }));
    setStoredNotifications(next);
    return next;
}

export function clearNotifications() {
    if (typeof window === 'undefined') return;
    localStorage.removeItem(NOTIFICATIONS_KEY);
}
