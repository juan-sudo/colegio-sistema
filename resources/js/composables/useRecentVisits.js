const STORAGE_KEY = 'recent_visits';

export function getRecentVisits(limit = 5) {
    if (typeof window === 'undefined') return [];
    try {
        const raw = localStorage.getItem(STORAGE_KEY);
        if (!raw) return [];
        const items = JSON.parse(raw);
        return Array.isArray(items) ? items.slice(0, limit) : [];
    } catch (error) {
        console.warn('No se pudieron leer las últimas visitas', error);
        return [];
    }
}

export function pushRecentVisit(visit) {
    if (typeof window === 'undefined' || !visit) return [];
    try {
        const current = getRecentVisits(20);
        const filtered = current.filter(
            (item) => item.route !== visit.route || item.label !== visit.label
        );
        const next = [visit, ...filtered].slice(0, 10);
        localStorage.setItem(STORAGE_KEY, JSON.stringify(next));
        return next;
    } catch (error) {
        console.warn('No se pudo guardar la visita', error);
        return [];
    }
}

export function clearRecentVisits() {
    if (typeof window === 'undefined') return;
    try {
        localStorage.removeItem(STORAGE_KEY);
    } catch (error) {
        console.warn('No se pudo limpiar las últimas visitas', error);
    }
}
