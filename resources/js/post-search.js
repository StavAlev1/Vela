// This function builds the "state + behavior" object that Alpine will use.
// It takes two starting values from Blade:
//   - initialQuery: whatever was typed in the search box before (e.g. from the URL ?q=...)
//   - indexUrl: the base URL for the posts page (route('posts.index'))
export default function postSearch(initialQuery, indexUrl) {

    // Alpine expects x-data to receive an OBJECT — so we return one here.
    // Everything inside this object is either a piece of state (a variable)
    // or a method (a function) that Alpine can access via x-model, x-show, @click, etc.
    return {

        // "q" holds whatever the user has typed into the search input.
        // x-model="q" in the Blade file keeps this in sync with the <input> automatically.
        q: initialQuery,

        // "loading" tracks whether a fetch request is currently in progress.
        // x-show="loading" in the Blade file uses this to show/hide the "Searching…" text.
        loading: false,

        // Alpine automatically calls init() once, as soon as this component loads.
        // We use it to set up a "watcher" — code that runs whenever "q" changes.
        init() {
            // $watch('q', callback) tells Alpine: "every time q changes, run this function"
            // In our case, whenever the user types something new, re-run search().
            this.$watch('q', () => this.search());
        },

        // Runs a fresh search based on whatever is currently in "q".
        search() {
            // Show the "Searching…" indicator while we wait for the server.
            this.loading = true;

            // Build a URL object starting from the base posts page URL.
            // window.location.origin is just "http://127.0.0.1:8000" (or wherever the app is running).
            const url = new URL(indexUrl, window.location.origin);

            // If there's actually something typed in the search box,
            // add it to the URL as a query string parameter: ?q=whatever
            if (this.q) {
                url.searchParams.set('q', this.q);
            }

            // Now that we have the correct URL (with or without ?q=...),
            // hand off to fetchAndSwap() to actually go get the new results.
            this.fetchAndSwap(url);
        },

        // Runs when a pagination link (e.g. "Next Page") is clicked.
        // "event" is the click event automatically passed in by Alpine's @click="paginate($event)"
        paginate(event) {
            // The user might click directly on the <a> tag, or on something inside it
            // (like an icon). closest('a') walks up the DOM to find the actual link element.
            const link = event.target.closest('a');

            // Safety check: if for some reason there's no actual link
            // (e.g. they clicked empty space in the container), do nothing.
            if (!link) return;

            // Stop the browser's default behavior, which would normally be
            // "follow this link and reload the whole page." We don't want that —
            // we want to fetch it in the background instead.
            event.preventDefault();

            // Fetch whatever URL that pagination link was pointing to,
            // using the same swap logic as a normal search.
            this.fetchAndSwap(link.href);
        },

        // Shared logic used by both search() and paginate():
        // go fetch a given URL from the server, then swap the results into the page.
        fetchAndSwap(url) {
            // Show "Searching…" while the request is in flight.
            this.loading = true;

            // fetch() sends a request to the given URL, in the background,
            // WITHOUT reloading the page. It returns a "Promise" — basically
            // a placeholder that resolves once the server actually responds.
            fetch(url, {
                // This custom header tells our Laravel controller "this is an AJAX request",
                // so it knows to return just the results partial instead of the full page.
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
                // Once the response comes back, read its body as plain text.
                // (Our controller returns rendered HTML, not JSON, so .text() is correct here.)
                .then(response => response.text())

                // Once we actually have that HTML text in hand...
                .then(html => {
                    // Find the <div id="posts-results"> container in the page,
                    // and replace everything inside it with the new HTML we just fetched.
                    document.getElementById('posts-results').innerHTML = html;

                    // Update the browser's address bar to reflect the new URL
                    // (so refreshing the page or sharing the link still works),
                    // WITHOUT actually triggering a page reload.
                    window.history.pushState({}, '', url);

                    // Hide the "Searching…" indicator now that we're done.
                    this.loading = false;
                });
        },
    };
}