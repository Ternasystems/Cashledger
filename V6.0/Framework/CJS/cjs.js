/**
 * =============================================================================
 * CJS (Cashledger JavaScript Library)
 * =============================================================================
 * This file contains the core frontend application libraries for Cashledger.
 */

// Create the main CJS namespace to house our libraries.
const CJS = {};

/**
 * =============================================================================
 * Library 1: The DOM Library
 * =============================================================================
 * Provides a lightweight, fluent, jQuery-like API for DOM manipulation.
 */
class Dom {
    constructor(selector) {
        if (typeof selector === 'string') {
            this.elements = document.querySelectorAll(selector);
        } else if (selector instanceof HTMLElement) {
            this.elements = [selector];
        } else if (selector instanceof NodeList) {
            this.elements = Array.from(selector);
        } else if (selector instanceof Dom) {
            this.elements = selector.elements;
        } else {
            this.elements = [];
        }
    }

    // --- Iteration ---

    each(callback) {
        this.elements.forEach(callback);
        return this;
    }

    // --- Class & Attribute Manipulation ---

    addClass(className) {
        return this.each(el => el.classList.add(className));
    }
    removeClass(className) {
        return this.each(el => el.classList.remove(className));
    }
    toggleClass(className) {
        return this.each(el => el.classList.toggle(className));
    }
    switchClass(classesToRemove, classToAdd) {
        return this.each(el => {
            classesToRemove.split(' ').forEach(cls => {
                if(cls) el.classList.remove(cls);
            });
            el.classList.add(classToAdd);
        });
    }
    attribute(name, value) {
        if (value === undefined) {
            return this.elements.length > 0 ? this.elements[0].getAttribute(name) : null;
        }
        return this.each(el => el.setAttribute(name, value));
    }
    data(key, value) {
        if (value === undefined) {
            return this.elements.length > 0 ? this.elements[0].dataset[key] : undefined;
        }
        return this.each(el => el.dataset[key] = value);
    }

    // --- Event Handling ---

    on(eventName, handler) {
        this.each(el => {
            if (!el._cjs_events) el._cjs_events = {};
            if (!el._cjs_events[eventName]) el._cjs_events[eventName] = [];
            el._cjs_events[eventName].push(handler);
            el.addEventListener(eventName, handler);
        });
        return this;
    }
    off(eventName, handler) {
        return this.each(el => {
            if (!el._cjs_events || !el._cjs_events[eventName]) return;
            const listeners = handler ? [handler] : el._cjs_events[eventName];
            listeners.forEach(listener => el.removeEventListener(eventName, listener));
            if (!handler) {
                delete el._cjs_events[eventName];
            }
        });
    }
    one(eventName, handler) {
        return this.each(el => {
            const onceHandler = (event) => {
                handler(event);
                CJS.$(el).off(eventName, onceHandler);
            };
            CJS.$(el).on(eventName, onceHandler);
        });
    }
    trigger(eventName) {
        const event = new Event(eventName, { bubbles: true, cancelable: true });
        return this.each(el => el.dispatchEvent(event));
    }
    submit(handler) {
        return this.on('submit', handler);
    }
    reset(handler) {
        return this.on('reset', handler);
    }

    // --- Content & HTML Manipulation ---

    html(htmlString) {
        if (htmlString === undefined) {
            return this.elements.length > 0 ? this.elements[0].innerHTML : '';
        }
        return this.each(el => el.innerHTML = htmlString);
    }
    text(textString) {
        if (textString === undefined) {
            return this.elements.length > 0 ? this.elements[0].textContent : '';
        }
        return this.each(el => el.textContent = textString);
    }
    append(content) {
        return this.each(el => {
            if (typeof content === 'string') {
                el.insertAdjacentHTML('beforeend', content);
            } else if (content instanceof HTMLElement) {
                el.appendChild(content);
            } else if (content instanceof Dom) {
                content.each(childEl => el.appendChild(childEl.cloneNode(true)));
            }
        });
    }
    prepend(content) {
        return this.each(el => {
            if (typeof content === 'string') {
                el.insertAdjacentHTML('afterbegin', content);
            } else if (content instanceof HTMLElement) {
                el.insertBefore(content, el.firstChild);
            } else if (content instanceof Dom) {
                [...content.elements].reverse().forEach(childEl => {
                    el.insertBefore(childEl.cloneNode(true), el.firstChild);
                });
            }
        });
    }
    insertBefore(content) {
        return this.each(el => {
            if (typeof content === 'string') {
                el.insertAdjacentHTML('beforebegin', content);
            } else if (content instanceof HTMLElement) {
                el.parentNode.insertBefore(content, el);
            } else if (content instanceof Dom) {
                content.each(childEl => el.parentNode.insertBefore(childEl.cloneNode(true), el));
            }
        });
    }
    insertAfter(content) {
        return this.each(el => {
            if (typeof content === 'string') {
                el.insertAdjacentHTML('afterend', content);
            } else if (content instanceof HTMLElement) {
                el.parentNode.insertBefore(content, el.nextSibling);
            } else if (content instanceof Dom) {
                [...content.elements].reverse().forEach(childEl => {
                    el.parentNode.insertBefore(childEl.cloneNode(true), el.nextSibling);
                });
            }
        });
    }
    remove() {
        return this.each(el => el.parentNode.removeChild(el));
    }
    empty() {
        return this.html('');
    }

    // --- CSS & Visibility ---

    css(property, value) {
        if (typeof property === 'string' && value === undefined) {
            return this.elements.length > 0 ? getComputedStyle(this.elements[0])[property] : null;
        }
        const styles = typeof property === 'object' ? property : { [property]: value };
        return this.each(el => {
            for (const key in styles) {
                el.style[key] = styles[key];
            }
        });
    }
    hide() {
        return this.each(el => el.style.display = 'none');
    }
    show(displayValue = 'block') {
        return this.each(el => el.style.display = displayValue);
    }
    toggle(displayValue = 'block') {
        return this.each(el => {
            el.style.display = (window.getComputedStyle(el).display === 'none') ? displayValue : 'none';
        });
    }

    // --- Forms ---

    val(value) {
        if (value === undefined) {
            return this.elements.length > 0 ? this.elements[0].value : undefined;
        }
        return this.each(el => el.value = value);
    }

    // --- Traversal ---

    find(selector) {
        if (this.elements.length === 0) return new Dom(null);
        const foundElements = [];
        this.each(el => {
            foundElements.push(...el.querySelectorAll(selector));
        });
        return new Dom(foundElements);
    }
    parent() {
        const parents = this.elements.map(el => el.parentElement);
        return new Dom([...new Set(parents)]);
    }
    closest(selector) {
        const closestElements = this.elements.map(el => el.closest(selector)).filter(el => el !== null);
        return new Dom([...new Set(closestElements)]);
    }
    children() {
        const allChildren = [];
        this.each(el => {
            allChildren.push(...el.children);
        });
        return new Dom(allChildren);
    }
    first() {
        return new Dom(this.elements[0]);
    }
    last() {
        return new Dom(this.elements[this.elements.length - 1]);
    }
}
CJS.$ = (selector) => new Dom(selector);
CJS.ready = (callback) => {
    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", callback);
    } else {
        callback();
    }
};

/**
 * =============================================================================
 * Library 2: The AJAX Library
 * =============================================================================
 * Provides a clean, promise-based wrapper around the native fetch API.
 */

class HttpError extends Error {
    constructor(message, status, statusText, body) {
        super(message);
        this.name = 'HttpError';
        this.status = status;
        this.statusText = statusText;
        this.body = body;
    }
}
CJS.HttpError = HttpError;

class Http {
    constructor(options = {}) {
        this.baseUrl = options.baseUrl || '';
        this.defaultHeaders = options.headers || {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        };
    }

    async _request(method, endpoint, body = null, options = {}) {
        const url = this.baseUrl + endpoint;
        const config = {
            method,
            headers: { ...this.defaultHeaders, ...options.headers },
            ...options
        };

        if (body) {
            config.body = JSON.stringify(body);
        }

        try {
            const response = await fetch(url, config);

            if (!response.ok) {
                const responseBody = await response.text();
                throw new HttpError(
                    `HTTP error! Status: ${response.status}`,
                    response.status,
                    response.statusText,
                    responseBody
                );
            }

            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                return await response.json();
            }
            return await response.text();

        } catch (error) {
            // Re-throw HttpErrors, but wrap other errors (e.g., network failures)
            if (error instanceof HttpError) {
                throw error;
            }
            throw new Error(`Network request failed: ${error.message}`);
        }
    }

    get(endpoint, options = {}) {
        return this._request('GET', endpoint, null, options);
    }

    post(endpoint, body, options = {}) {
        return this._request('POST', endpoint, body, options);
    }

    put(endpoint, body, options = {}) {
        return this._request('PUT', endpoint, body, options);
    }

    delete(endpoint, options = {}) {
        return this._request('DELETE', endpoint, null, options);
    }
}
CJS.Http = Http;

// Create a default, pre-configured instance for immediate use.
CJS.http = new CJS.Http({ baseUrl: '/api' });


/**
 * =============================================================================
 * Library 3: The State Management Library
 * =============================================================================
 * A simple, centralized state management solution inspired by Vuex/Redux.
 */
class Store {
    constructor(options) {
        this.state = options.state || {};
        this.mutations = options.mutations || {};
        this.actions = options.actions || {};
        this.subscribers = [];
    }

    subscribe(callback) {
        this.subscribers.push(callback);
        return () => {
            this.subscribers = this.subscribers.filter(sub => sub !== callback);
        };
    }

    _notify() {
        this.subscribers.forEach(callback => callback(this.state));
    }

    dispatch(actionName, payload) {
        if (typeof this.actions[actionName] !== 'function') {
            console.error(`Action "${actionName}" does not exist.`);
            return;
        }
        const context = {
            commit: this.commit.bind(this),
            state: this.state
        };
        return this.actions[actionName](context, payload);
    }

    commit(mutationName, payload) {
        if (typeof this.mutations[mutationName] !== 'function') {
            console.error(`Mutation "${mutationName}" does not exist.`);
            return;
        }
        this.mutations[mutationName](this.state, payload);
        this._notify();
    }
}
CJS.Store = Store;


/**
 * =============================================================================
 * Library 4: The Reactivity Library
 * =============================================================================
 * Connects the Store to the DOM via reactive Components.
 */
class Component {
    /**
     * @param {object} options - The component configuration.
     * @param {CJS.Store} options.store - The CJS store instance.
     * @param {HTMLElement|string} options.element - The root DOM element or selector for the component.
     */
    constructor(options) {
        this.store = options.store;
        this.element = CJS.$(options.element);

        // Subscribe the component's render method to store updates.
        // This is the core of the reactivity.
        if (this.store) {
            this.store.subscribe(this.render.bind(this));
        }

        // Initial render
        this.render(this.store ? this.store.state : {});
    }

    /**
     * The render method to be implemented by child components.
     * It receives the current state and should update the component's element.
     * @param {object} state - The current state from the store.
     */
    render(state) {
        // This method should be overridden by the user's component class.
        // Example: this.element.html(`<h1>${state.title}</h1>`);
        throw new Error('Component must have a "render" method.');
    }
}
CJS.Component = Component;
