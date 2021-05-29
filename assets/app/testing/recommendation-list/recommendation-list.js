import { html, css } from "lit";
import { NbList } from "../../../native-bear";
import fontawesomeImport from "../../../utils/fontawesome-import";

export class RecommendationList extends NbList {
	static get styles()
	{
		return [
			super.styles,
			css`
				.nb--list-header,
				.nb--list-item { grid-template-areas: "title type occurences actions"; grid-template-columns: 1fr 10rem 5.5rem 12rem; }
				.nb--list-item-column[nb-column="type"] { font-size: .85rem; }
				.nb--list-item-column[nb-column="occurences"] { font-size: .85rem; text-align: center; color: var(--color-gray-dark); }

				nb-markdown { display: block; font-weight: 500; text-overflow: ellipsis; overflow: hidden; white-space: nowrap; }
				recommendation-details-link { font-size: .85em; }

				.nb--list-item[pending-completion] { opacity: .35; pointer-events: none; }
				.nb--list-item[pending-completion] .nb--list-item-column { opacity: .5; }
				.nb--list-item[pending-completion] [nb-column="title"] { text-decoration: line-through; }
			`
		];
	}

	static get properties() {
		return {
			...super.properties,
			projectId: {type: Number}
		};
	}

	static get _columns()
	{
		return [
			{
				key: "title",
				label: "recommendation.listing.title",
				render: item => {
					// Remove links from the recommendation title
					const strippedTitle = item.htmlTitle.replace(/\[.+?\]\(.+?\)/g, "");

					return html`
						<nb-markdown barebones>
							<script type="text/markdown">${strippedTitle}</script>
						</nb-markdown>
						<recommendation-details-link recommendationId=${item.sampleId}>
							<i class="fad fa-circle-info"></i>&nbsp;
							${Translator.trans("recommendation.view_more")}
						</recommendation-details-link>
					`;
				},
				placeholder: html`
					<div class="nb--list-item-column-placeholder" style="width: 90%;">&nbsp;</div>
					<div class="nb--list-item-column-placeholder" style="width: min(30ch, 75%); font-size: .65em;">&nbsp;</div>
				`
			},
			{
				key: "type",
				label: "recommendation.listing.type",
				render: (item) => html`<recommendation-type type=${item.type}></recommendation-type>`,
				placeholder: html`
					<div class="nb--list-item-column-placeholder" style="width: 8ch;">&nbsp;</div>
				`
			},
			{
				key: "occurences",
				label: "recommendation.listing.occurences",
				render: (item) => item.count,
				placeholder: html`
					<div class="nb--list-item-column-placeholder" style="width: 5ch; margin: auto;">&nbsp;</div>
				`
			},
			{
				key: "actions",
				label: null,
				render: (item, list) => {
					if (item._pendingCompletion) {
						return html`
							<nb-loading-spinner></nb-loading-spinner>
						`;
					}

					return html`
						<nb-button size="small" color="gray">${Translator.trans("recommendation.ignore")}</nb-button>
						<nb-icon-button size="small" @click=${() => list._markItemAsCompletedCallback(item)}><i class="fas fa-check"></i></nb-icon-button>
					`;
				},
				placeholder: html`
					<div class="nb--list-item-column-placeholder" style="width: 8ch; font-size: 1.75em; margin-left: auto;">&nbsp;</div>
				`
			},
		];
	}

	constructor()
	{
		super();
		this.projectId = null;
	}

	render()
	{
		return [
			fontawesomeImport,
			super.render()
		];
	}

	_renderItem(item)
	{
		const instance = this;

		return html`
			<li class="nb--list-item" ?pending-completion=${!!item._pendingCompletion}>
				${this.constructor._columns.map(column => html`<div class="nb--list-item-column" nb-column=${column.key}>${column.render(item, instance)}</div>`)}
			</li>
		`;
	}

	firstUpdated()
	{
		this.fetchRecommendations();
	}

	fetchRecommendations()
	{
		const url = Routing.generate("api_testing_recommendation_groups", {projectId: this.projectId});

		fetch(url)
			.then(response => response.json())
			.then(response => {
				if (response.status != "ok") {
					throw new Error(`Received invalid response from the API's api_testing_recommendation_groups route: ${JSON.stringify(response)}`);
				}
				this.items = Object.values(response.data);
			});
	}

	_markItemAsCompletedCallback(completedItem)
	{
		completedItem._pendingCompletion = true;
		this.requestUpdate("items");

		const endpointUrl = Routing.generate("api_testing_recommendation_group_complete", {recommendationId: completedItem.sampleId});
		fetch(endpointUrl, { method: "PUT" })
			.then(response => response.json())
			.then(response => {
				if (response.status != "ok") {
					throw new Error(`Received invalid response from the API's api_testing_recommendation_group_complete route: ${JSON.stringify(response)}`);
				}
				this.items = this.items.filter(item => item !== completedItem);
			});
	}
}

customElements.define("recommendation-list", RecommendationList);
