const cardSelector = '.admin-comments-table-card';
const scrollSelector = '.admin-comments-table-scroll';
let initialized = false;

export function initAdminCommentsTableScroll() {
    if (initialized) {
        return;
    }

    initialized = true;

    const observedElements = new Set();
    let scheduledFrame = null;

    const updateCard = (card) => {
        const scroller = card.querySelector(scrollSelector);
        const leftButton = card.querySelector('[data-comments-table-scroll="left"]');
        const rightButton = card.querySelector('[data-comments-table-scroll="right"]');

        if (!scroller || !leftButton || !rightButton) {
            return;
        }

        const isVisible = scroller.getClientRects().length > 0;
        const hasOverflow = isVisible && scroller.scrollWidth > scroller.clientWidth + 1;

        card.classList.toggle('has-horizontal-overflow', hasOverflow);

        if (!hasOverflow) {
            leftButton.disabled = true;
            rightButton.disabled = true;

            return;
        }

        leftButton.disabled = scroller.scrollLeft <= 1;
        rightButton.disabled = scroller.scrollLeft + scroller.clientWidth >= scroller.scrollWidth - 1;
    };

    const resizeObserver = typeof ResizeObserver === 'undefined'
        ? null
        : new ResizeObserver((entries) => {
            entries.forEach(({ target }) => {
                const card = target.closest(cardSelector);

                if (card) {
                    updateCard(card);
                }
            });
        });

    const refresh = () => {
        observedElements.forEach((element) => {
            if (!element.isConnected) {
                resizeObserver?.unobserve(element);
                observedElements.delete(element);
            }
        });

        document.querySelectorAll(cardSelector).forEach((card) => {
            const scroller = card.querySelector(scrollSelector);
            const table = scroller?.querySelector('table');

            [scroller, table].forEach((element) => {
                if (element && resizeObserver && !observedElements.has(element)) {
                    resizeObserver.observe(element);
                    observedElements.add(element);
                }
            });

            updateCard(card);
        });
    };

    const scheduleRefresh = () => {
        if (scheduledFrame !== null) {
            return;
        }

        scheduledFrame = window.requestAnimationFrame(() => {
            scheduledFrame = null;
            refresh();
        });
    };

    document.addEventListener('click', (event) => {
        const target = event.target;

        if (!(target instanceof Element)) {
            return;
        }

        const button = target.closest('[data-comments-table-scroll]');

        if (!button || button.disabled) {
            return;
        }

        const card = button.closest(cardSelector);
        const scroller = card?.querySelector(scrollSelector);

        if (!scroller) {
            return;
        }

        const direction = button.dataset.commentsTableScroll === 'left' ? -1 : 1;
        const targetLeft = direction < 0 ? 0 : scroller.scrollWidth - scroller.clientWidth;
        const behavior = window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth';

        scroller.scrollTo({ left: targetLeft, behavior });
    });

    document.addEventListener('scroll', (event) => {
        if (event.target instanceof Element && event.target.matches(scrollSelector)) {
            const card = event.target.closest(cardSelector);

            if (card) {
                updateCard(card);
            }
        }
    }, true);

    window.addEventListener('resize', scheduleRefresh);

    const mutationRoot = document.querySelector('main[data-app-content]') || document.body;
    const mutationObserver = new MutationObserver(scheduleRefresh);
    mutationObserver.observe(mutationRoot, { childList: true, subtree: true });

    refresh();
}
