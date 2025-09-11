/**
 * =============================================================================
 * C2S (Cashledger CSS Library) - Component JavaScript
 * =============================================================================
 * This file provides the JavaScript logic to power interactive C2S components.
 * It is designed to be lightweight and dependency-free.
 */

// We'll create a single global object to house our components
// to avoid polluting the global namespace.
const C2S = {};

/**
 * -----------------------------------------------------------------------------
 * Modal Component
 * -----------------------------------------------------------------------------
 * Manages the behavior of a modal dialog.
 */
C2S.Modal = class {
    /**
     * @param {HTMLElement} modalElement The main modal element.
     */
    constructor(modalElement) {
        if (!modalElement) {
            throw new Error('Modal element not provided.');
        }
        this.modal = modalElement;
        this.closeButton = this.modal.querySelector('[data-c2s-dismiss="modal"]');
        this._boundCloseOnEscape = this.closeOnEscape.bind(this);

        this.init();
    }

    init() {
        // Close when the close button is clicked
        if (this.closeButton) {
            this.closeButton.addEventListener('click', () => this.close());
        }

        // Close when the backdrop is clicked
        this.modal.addEventListener('click', (event) => {
            if (event.target === this.modal) {
                this.close();
            }
        });
    }

    /**
     * Opens the modal dialog.
     */
    open() {
        this.modal.classList.remove('d-none');
        this.modal.classList.add('d-flex');
        document.body.style.overflow = 'hidden'; // Prevent background scrolling
        document.addEventListener('keydown', this._boundCloseOnEscape);
    }

    /**
     * Closes the modal dialog.
     */
    close() {
        this.modal.classList.add('d-none');
        this.modal.classList.remove('d-flex');
        document.body.style.overflow = ''; // Restore scrolling
        document.removeEventListener('keydown', this._boundCloseOnEscape);
    }

    /**
     * Handles the keydown event to close the modal on 'Escape'.
     * @param {KeyboardEvent} event
     */
    closeOnEscape(event) {
        if (event.key === 'Escape') {
            this.close();
        }
    }
};

/**
 * -----------------------------------------------------------------------------
 * Dropdown Component
 * -----------------------------------------------------------------------------
 * Manages the behavior of a dropdown menu.
 */
C2S.Dropdown = class {
    /**
     * @param {HTMLElement} dropdownTrigger The button element that toggles the dropdown.
     */
    constructor(dropdownTrigger) {
        if (!dropdownTrigger) {
            throw new Error('Dropdown trigger element not provided.');
        }
        this.trigger = dropdownTrigger;
        this.menu = document.getElementById(this.trigger.getAttribute('data-c2s-target'));

        if (!this.menu) {
            throw new Error(`Dropdown menu with ID '${this.trigger.getAttribute('data-c2s-target')}' not found.`);
        }

        this.isOpen = false;
        this._boundHandleDocumentClick = this.handleDocumentClick.bind(this);
        this._boundHandleEscapeKey = this.handleEscapeKey.bind(this);

        this.init();
    }

    init() {
        this.trigger.addEventListener('click', (event) => {
            event.preventDefault();
            this.toggle();
        });
    }

    /**
     * Toggles the dropdown menu's visibility.
     */
    toggle() {
        this.isOpen ? this.close() : this.open();
    }

    /**
     * Opens the dropdown menu.
     */
    open() {
        if (this.isOpen) return;
        this.isOpen = true;
        this.menu.classList.remove('d-none');
        this.trigger.setAttribute('aria-expanded', 'true');
        // Listen for clicks outside the dropdown to close it
        setTimeout(() => document.addEventListener('click', this._boundHandleDocumentClick), 0);
        document.addEventListener('keydown', this._boundHandleEscapeKey);
    }

    /**
     * Closes the dropdown menu.
     */
    close() {
        if (!this.isOpen) return;
        this.isOpen = false;
        this.menu.classList.add('d-none');
        this.trigger.setAttribute('aria-expanded', 'false');
        document.removeEventListener('click', this._boundHandleDocumentClick);
        document.removeEventListener('keydown', this._boundHandleEscapeKey);
    }

    /**
     * Closes the dropdown if a click occurs outside of its elements.
     * @param {MouseEvent} event
     */
    handleDocumentClick(event) {
        if (!this.menu.contains(event.target) && !this.trigger.contains(event.target)) {
            this.close();
        }
    }

    /**
     * Closes the dropdown if the Escape key is pressed.
     * @param {KeyboardEvent} event
     */
    handleEscapeKey(event) {
        if (event.key === 'Escape') {
            this.close();
        }
    }
};


/**
 * -----------------------------------------------------------------------------
 * Autocomplete Component
 * -----------------------------------------------------------------------------
 * Creates a search input with a dynamic droplist of results fetched from a server.
 */
C2S.Autocomplete = class {
    /**
     * @param {HTMLInputElement} inputElement The input field to attach to.
     * @param {object} options Configuration options.
     * @param {string} options.source The URL to fetch search results from.
     * @param {function(object): void} options.onSelect Callback for when an item is selected.
     * @param {number} [options.minLength=2] Minimum characters to trigger a search.
     * @param {number} [options.debounce=300] Delay in ms after user stops typing.
     */
    constructor(inputElement, options) {
        this.input = inputElement;
        this.options = {
            minLength: 2,
            debounce: 300,
            ...options
        };
        this.resultsContainer = null;
        this.debounceTimeout = null;
        this.activeIndex = -1;

        this.init();
    }

    init() {
        this.createResultsContainer();

        this.input.addEventListener('input', () => {
            clearTimeout(this.debounceTimeout);
            this.debounceTimeout = setTimeout(() => this.onInput(), this.options.debounce);
        });

        this.input.addEventListener('keydown', (e) => this.onKeydown(e));
        document.addEventListener('click', (e) => this.onClickOutside(e));
    }

    createResultsContainer() {
        this.resultsContainer = document.createElement('div');
        this.resultsContainer.className = 'autocomplete-results card d-none';
        this.input.parentElement.style.position = 'relative';
        this.input.parentElement.appendChild(this.resultsContainer);
    }

    async onInput() {
        const query = this.input.value.trim();
        if (query.length < this.options.minLength) {
            this.hideResults();
            return;
        }

        try {
            this.resultsContainer.innerHTML = '<div class="p-2">Loading...</div>';
            this.showResults();

            const url = new URL(this.options.source, window.location.origin);
            url.searchParams.append('q', query);

            const response = await fetch(url);
            if (!response.ok) throw new Error('Network response was not ok');
            const data = await response.json();

            this.renderResults(data);
        } catch (error) {
            console.error('Autocomplete fetch error:', error);
            this.resultsContainer.innerHTML = '<div class="p-2 text-error">Error fetching results.</div>';
        }
    }

    renderResults(data) {
        this.resultsContainer.innerHTML = '';
        if (data.length === 0) {
            this.resultsContainer.innerHTML = '<div class="p-2">No results found.</div>';
            return;
        }

        data.forEach((item, index) => {
            const itemElement = document.createElement('div');
            itemElement.className = 'autocomplete-item p-2 cursor-pointer';
            // Assuming the server returns objects with 'label' and 'value' properties.
            itemElement.textContent = item.label;
            itemElement.dataset.index = index;
            itemElement.addEventListener('click', () => this.selectItem(item));
            this.resultsContainer.appendChild(itemElement);
        });
        this.activeIndex = -1;
    }

    onKeydown(e) {
        const items = this.resultsContainer.querySelectorAll('.autocomplete-item');
        if (items.length === 0) return;

        switch (e.key) {
            case 'ArrowDown':
                e.preventDefault();
                this.activeIndex = (this.activeIndex + 1) % items.length;
                this.updateActiveItem();
                break;
            case 'ArrowUp':
                e.preventDefault();
                this.activeIndex = (this.activeIndex - 1 + items.length) % items.length;
                this.updateActiveItem();
                break;
            case 'Enter':
                e.preventDefault();
                if (this.activeIndex > -1) {
                    items[this.activeIndex].click();
                }
                break;
            case 'Escape':
                this.hideResults();
                break;
        }
    }

    updateActiveItem() {
        const items = this.resultsContainer.querySelectorAll('.autocomplete-item');
        items.forEach((item, index) => {
            item.classList.toggle('bg-primary-100', index === this.activeIndex);
        });
        items[this.activeIndex]?.scrollIntoView({ block: 'nearest' });
    }

    selectItem(item) {
        if (this.options.onSelect) {
            this.options.onSelect(item);
        }
        this.hideResults();
    }

    showResults() {
        this.resultsContainer.classList.remove('d-none');
    }

    hideResults() {
        this.resultsContainer.classList.add('d-none');
        this.activeIndex = -1;
    }

    onClickOutside(e) {
        if (!this.input.contains(e.target) && !this.resultsContainer.contains(e.target)) {
            this.hideResults();
        }
    }
};

/**
 * -----------------------------------------------------------------------------
 * Tabs Component
 * -----------------------------------------------------------------------------
 * Manages the behavior of a tabbed interface.
 */
C2S.Tabs = class {
    /**
     * @param {HTMLElement} tabsContainer The main container element for the tabs.
     */
    constructor(tabsContainer) {
        if (!tabsContainer) throw new Error('Tabs container element not provided.');
        this.container = tabsContainer;
        this.tabLinks = this.container.querySelectorAll('[data-c2s-toggle="tab"]');
        this.tabPanes = this.container.querySelectorAll('.c2s-tab-pane');
        this.init();
    }

    init() {
        this.tabLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                this.activateTab(link);
            });
        });

        // Activate the first tab by default if none are active
        if (this.container.querySelector('.c2s-tab-link.active') === null && this.tabLinks.length > 0) {
            this.activateTab(this.tabLinks[0]);
        }
    }

    /**
     * Activates a specific tab and its corresponding content pane.
     * @param {HTMLElement} tabLink The tab link element that was clicked.
     */
    activateTab(tabLink) {
        const targetId = tabLink.getAttribute('data-c2s-target');

        // Deactivate all tabs and panes first
        this.tabLinks.forEach(link => link.classList.remove('active'));
        this.tabPanes.forEach(pane => pane.classList.remove('active'));

        // Activate the clicked tab and its corresponding pane
        tabLink.classList.add('active');
        const targetPane = this.container.querySelector(targetId);
        if (targetPane) {
            targetPane.classList.add('active');
        }
    }
};

/**
 * -----------------------------------------------------------------------------
 * Accordion Component
 * -----------------------------------------------------------------------------
 * Manages the behavior of a collapsible accordion.
 */
C2S.Accordion = class {
    /**
     * @param {HTMLElement} accordionElement The main container for the accordion.
     */
    constructor(accordionElement) {
        if (!accordionElement) throw new Error('Accordion element not provided.');
        this.accordion = accordionElement;
        this.items = this.accordion.querySelectorAll('.accordion-item');
        // Check for a data attribute to allow multiple items to be open at once.
        this.allowMultiple = this.accordion.hasAttribute('data-c2s-allow-multiple');
        this.init();
    }

    init() {
        this.items.forEach(item => {
            const button = item.querySelector('.accordion-button');
            if (button) {
                button.addEventListener('click', () => this.toggleItem(item));
            }
        });
    }

    /**
     * Toggles a single accordion item.
     * @param {HTMLElement} itemToToggle The .accordion-item element to toggle.
     */
    toggleItem(itemToToggle) {
        const isActive = itemToToggle.classList.contains('active');

        // If only one item is allowed to be open at a time, close all others first.
        if (!this.allowMultiple && !isActive) {
            this.items.forEach(item => {
                if (item !== itemToToggle) {
                    item.classList.remove('active');
                }
            });
        }

        // Toggle the active class on the clicked item.
        itemToToggle.classList.toggle('active');
    }
};

/**
 * -----------------------------------------------------------------------------
 * Carousel Component
 * -----------------------------------------------------------------------------
 * Manages the behavior of a content slider/carousel.
 */
C2S.Carousel = class {
    /**
     * @param {HTMLElement} carouselElement The main container for the carousel.
     */
    constructor(carouselElement) {
        if (!carouselElement) throw new Error('Carousel element not provided.');

        this.carousel = carouselElement;
        this.track = this.carousel.querySelector('.carousel-track');
        this.slides = Array.from(this.track.children);
        this.nextButton = this.carousel.querySelector('.carousel-button.next');
        this.prevButton = this.carousel.querySelector('.carousel-button.prev');
        this.dotsContainer = this.carousel.querySelector('.carousel-dots');

        this.slideWidth = this.slides[0].getBoundingClientRect().width;
        this.currentIndex = 0;

        this.init();
    }

    init() {
        // Set initial slide positions
        this.slides.forEach((slide, index) => {
            slide.style.left = this.slideWidth * index + 'px';
        });

        this.createDots();
        this.updateSlidePosition();

        this.nextButton.addEventListener('click', () => this.moveToSlide(this.currentIndex + 1));
        this.prevButton.addEventListener('click', () => this.moveToSlide(this.currentIndex - 1));

        if (this.dotsContainer) {
            this.dotsContainer.addEventListener('click', e => {
                const targetDot = e.target.closest('.carousel-dot');
                if (!targetDot) return;

                const targetIndex = this.dots.findIndex(dot => dot === targetDot);
                this.moveToSlide(targetIndex);
            });
        }
    }

    moveToSlide(targetIndex) {
        if (targetIndex < 0) {
            targetIndex = this.slides.length - 1;
        } else if (targetIndex >= this.slides.length) {
            targetIndex = 0;
        }

        this.currentIndex = targetIndex;
        this.updateSlidePosition();
    }

    updateSlidePosition() {
        this.track.style.transform = `translateX(-${this.slideWidth * this.currentIndex}px)`;
        this.updateDots();
    }

    createDots() {
        if (!this.dotsContainer) return;
        this.dotsContainer.innerHTML = '';
        this.dots = this.slides.map((_, index) => {
            const button = document.createElement('button');
            button.classList.add('carousel-dot');
            if (index === this.currentIndex) {
                button.classList.add('active');
            }
            return button;
        });
        this.dotsContainer.append(...this.dots);
    }

    updateDots() {
        if (!this.dots) return;
        this.dots.forEach((dot, index) => {
            dot.classList.toggle('active', index === this.currentIndex);
        });
    }
};

/**
 * -----------------------------------------------------------------------------
 * Progress Bar Component
 * -----------------------------------------------------------------------------
 * Manages the dynamic updating of a progress bar.
 */
C2S.ProgressBar = class {
    /**
     * @param {HTMLElement} progressElement The main container for the progress bar.
     */
    constructor(progressElement) {
        if (!progressElement) throw new Error('Progress bar element not provided.');
        this.progress = progressElement;
        this.bar = this.progress.querySelector('.progress-bar');
        if (!this.bar) throw new Error('The .progress-bar inner element was not found.');
        this.label = this.progress.querySelector('.progress-label');

        this.init();
    }

    init() {
        const initialProgress = parseFloat(this.progress.getAttribute('data-c2s-progress')) || 0;
        this.setProgress(initialProgress);
    }

    /**
     * Sets the progress bar's value.
     * @param {number} percentage The progress value from 0 to 100.
     */
    setProgress(percentage) {
        const sanitizedPercent = Math.max(0, Math.min(100, percentage));
        this.bar.style.width = `${sanitizedPercent}%`;
        if (this.label) {
            this.label.textContent = `${Math.round(sanitizedPercent)}%`;
        }
    }
};

/**
 * -----------------------------------------------------------------------------
 * AJAX Pagination Component
 * -----------------------------------------------------------------------------
 * Handles pagination without a full page reload by fetching content via AJAX.
 */
C2S.Pagination = class {
    /**
     * @param {HTMLElement} paginationElement The container for the pagination links.
     */
    constructor(paginationElement) {
        this.pagination = paginationElement;
        if (!this.pagination) throw new Error('Pagination element not provided.');

        this.containerId = this.pagination.dataset.c2sTargetContainer;
        this.contentContainer = document.getElementById(this.containerId);
        if (!this.contentContainer) throw new Error(`Target container #${this.containerId} not found.`);

        this.links = this.pagination.querySelectorAll('.pagination-link');
        this.init();
    }

    init() {
        this.links.forEach(link => {
            link.addEventListener('click', e => {
                e.preventDefault();
                const url = e.currentTarget.getAttribute('href');
                if (url && !e.currentTarget.parentElement.classList.contains('disabled')) {
                    this.fetchPage(url);
                }
            });
        });
    }

    async fetchPage(url) {
        // Optional: Show a loading state
        this.contentContainer.style.opacity = '0.5';

        try {
            const response = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            if (!response.ok) throw new Error('Network response was not ok.');

            const html = await response.text();
            // In a real app, you might get JSON and render it with a template.
            // For simplicity, we assume the server returns the fully rendered HTML for the content area.
            this.contentContainer.innerHTML = html;

            // Update the browser's URL bar without reloading
            window.history.pushState({}, '', url);

        } catch (error) {
            console.error('Failed to fetch page:', error);
            // Optional: Show an error message in the UI
        } finally {
            this.contentContainer.style.opacity = '1';
        }
    }
};

/**
 * -----------------------------------------------------------------------------
 * Infinite Scroll Component
 * -----------------------------------------------------------------------------
 * Progressively loads more content as the user scrolls down the page.
 */
C2S.InfiniteScroll = class {
    /**
     * @param {HTMLElement} containerElement The container where new content will be appended.
     */
    constructor(containerElement) {
        this.container = containerElement;
        if (!this.container) throw new Error('Infinite scroll container not provided.');

        this.loadMoreUrl = this.container.dataset.c2sLoadMoreUrl;
        if (!this.loadMoreUrl) throw new Error('data-c2s-load-more-url attribute is required.');

        this.page = 2; // Start loading from the second page
        this.isLoading = false;
        this.triggerElement = this.createTriggerElement();

        this.init();
    }

    createTriggerElement() {
        const trigger = document.createElement('div');
        trigger.className = 'infinite-scroll-trigger';
        this.container.insertAdjacentElement('afterend', trigger);
        return trigger;
    }

    init() {
        const observer = new IntersectionObserver(entries => {
            if (entries[0].isIntersecting && !this.isLoading) {
                this.loadMore();
            }
        }, { threshold: 1.0 });

        observer.observe(this.triggerElement);
    }

    async loadMore() {
        this.isLoading = true;
        // Optional: Show a loading spinner
        this.triggerElement.innerHTML = 'Loading...';

        try {
            const url = new URL(this.loadMoreUrl, window.location.origin);
            url.searchParams.set('page', this.page);

            const response = await fetch(url.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            if (!response.ok) throw new Error('Network response was not ok.');

            const html = await response.text();
            if (html.trim() === '') {
                // No more content to load
                this.triggerElement.innerHTML = 'End of results.';
                return;
            }

            this.container.insertAdjacentHTML('beforeend', html);
            this.page++;

        } catch (error) {
            console.error('Failed to load more content:', error);
            this.triggerElement.innerHTML = 'Error loading content.';
        } finally {
            this.isLoading = false;
            if (this.triggerElement.innerHTML === 'Loading...') {
                this.triggerElement.innerHTML = '';
            }
        }
    }
};

/**
 * -----------------------------------------------------------------------------
 * Infinite Scroll Component
 * -----------------------------------------------------------------------------
 * Progressively loads more content as the user scrolls down the page.
 */
C2S.InfiniteScroll = class {
    constructor(triggerElement, options = {}) {
        this.triggerElement = triggerElement;
        this.options = {
            endpoint: window.location.href,
            contentSelector: '#content-area',
            onLoad: (data, container) => { container.innerHTML += data; },
            ...options
        };
        this.isLoading = false;
        this.nextPageUrl = this.triggerElement.dataset.nextPage;
        this.init();
    }

    init() {
        const observer = new IntersectionObserver(entries => {
            if (entries[0].isIntersecting && !this.isLoading && this.nextPageUrl) {
                this.loadMore();
            }
        }, { threshold: 1.0 });

        observer.observe(this.triggerElement);
    }

    async loadMore() {
        this.isLoading = true;
        this.triggerElement.innerHTML = 'Loading...';

        try {
            const response = await fetch(this.nextPageUrl);
            if (!response.ok) throw new Error('Failed to fetch next page.');

            const data = await response.text();
            const contentContainer = document.querySelector(this.options.contentSelector);
            this.options.onLoad(data, contentContainer);

            // In a real app, the server should provide the next page URL,
            // perhaps in a custom header like 'X-Next-Page'.
            // For this example, we'll stop after one load.
            this.nextPageUrl = null;
            this.triggerElement.style.display = 'none';

        } catch (error) {
            console.error('Infinite scroll error:', error);
            this.triggerElement.innerHTML = 'Failed to load.';
        } finally {
            this.isLoading = false;
            if (this.triggerElement.innerHTML === 'Loading...') {
                this.triggerElement.innerHTML = '';
            }
        }
    }
};

/**
 * -----------------------------------------------------------------------------
 * Data-Driven Navigation System
 * -----------------------------------------------------------------------------
 * Initializes a global click handler to make any element with a `data-app`
 * attribute act as a hyperlink, building the URL from structured data attributes.
 */
C2S.initDataLinks = function(baseUrl = '/Applications') {
    document.body.addEventListener('click', function(e) {
        const linkElement = e.target.closest('[data-app]');

        if (linkElement) {
            e.preventDefault();

            const app = linkElement.dataset.app;
            const controller = linkElement.dataset.controller;
            const action = linkElement.dataset.action;
            const activeMenu = linkElement.dataset.activeMenu;

            // Only proceed if all required parts of the URL are present.
            if (app && controller && action) {
                // Construct the URL in a structured way
                const url = `${baseUrl}/${app}/${controller}/${action}`;

                if (activeMenu) {
                    localStorage.setItem('activeMenu', activeMenu);
                }

                window.location.href = url;
            }
        }
    });
};

// --- GLOBAL INITIALIZATION ---
document.addEventListener('DOMContentLoaded', () => {
    // Initialize the data-driven navigation system.
    C2S.initDataLinks();
});