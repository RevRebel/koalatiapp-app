import { LitElement, html, css } from "lit";
import { unsafeHTML } from "lit/directives/unsafe-html.js";
import { ApiClient } from "../../utils/api";
import stylesReset from "../../native-bear/styles-reset.js";

export class LinkPreviewCard extends LitElement {
	static get styles()
	{
		return [
			stylesReset,
			css`
				a { display: block; line-height: 1; text-decoration: none; background-color: var(--color-white); border: 1px solid var(--color-gray-light); border-radius: 8px; box-shadow: 0 3px 10px rgb(var(--shadow-rgb), .1); transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out,  transform .25s ease-in-out; }
				img { width: 100%; aspect-ratio: 1200/630; object-fit: cover; border-bottom: 1px solid var(--color-gray-light); border-top-right-radius: 8px; border-top-left-radius: 8px; transition: opacity .15s ease-in-out; }
				.text { padding: .6rem .75rem; line-height: 1.3; }
				.site { font-size: .85rem; font-weight: 400; color: var(--color-gray-dark); }
				.title { font-size: .95rem; font-weight: 700; color: var(--color-black); }
				.url { margin: .25rem 0; font-size: .8rem; color: var(--color-blue); }
				.description { margin: .5rem 0; font-size: .8rem; color: var(--color-gray-dark); }

				.placeholder { height: 1em; width: 90%; margin: .3em 0; background-color: #f5f5f5; border-radius: 5px; }

				a:hover { border-color: var(--color-blue-light); box-shadow: 0 4px 12px rgb(var(--shadow-rgb), .15); transform: translateY(-3px); }
				a:hover img { opacity: .75; }

				@media (prefers-color-scheme: dark) {
					.placeholder { background-color: rgba(255, 255, 255, .05); }
				}
			`
		];
	}

	static get properties() {
		return {
			url: {type: String},
			title: {attribute: false},
			description: {attribute: false},
			imageUrl: {attribute: false},
		};
	}

	connectedCallback()
	{
		super.connectedCallback();
		this.fetchMetadata();
	}

	render()
	{
		return html`
			<a href=${this.url} class="link-card" target="_blank">
				<img src=${this.imageUrl || "https://via.placeholder.com/600x315.png?text=Loading..."} alt="" loading="lazy">
				<div class="text">
					<div class="site">${this.siteName || html`<div class="placeholder"></div>`}</div>
					<div class="title">${this.title || html`<div class="placeholder"></div>`}</div>
					<div class="url" aria-hidden="true">${unsafeHTML(this.url.replace(/([-/_])/g, "$1<wbr>"))}</div>
					<div class="description">${this.description || html`<div class="placeholder"></div><div class="placeholder"></div><div class="placeholder"></div>`}</div>
				</div>
			</a>
	  	`;
	}

	fetchMetadata()
	{
		ApiClient.get("api_link_metas", { url: this.url }).then(response => {
			this.url = response.data.url;
			this.siteName = response.data.siteName == response.data.title ? "" : response.data.siteName;
			this.title = response.data.title;
			this.description = response.data.description;
			this.imageUrl = response.data.imageUrl;
		});
	}
}

customElements.define("link-preview-card", LinkPreviewCard);
