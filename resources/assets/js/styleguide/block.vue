<template>
	<article>
		<slot />

		<div class="mt-4">
			<div
				v-observe-visibility="cObserverOptions"
				class="relative bg-gray-100 border"
			>
				<iframe
					v-if="$data.iframeActive"
					ref="iframe"
					class="block w-px min-w-full"
					:src="cUrl"
				/>

				<button
					v-else
					class="flex justify-center items-center w-full h-full p-6 focus:outline-none"
					:style="{ height: `${this.$props.minHeight}px` }"
					@click="loadIframe"
				>
					<icon
						class="block text-5xl text-gray-500"
						:name="cLoadIcon"
					/>
				</button>

				<div class="absolute bottom-0 right-0 mb-1 mr-1">
					<ul class="flex leading-none">
						<li v-if="$props.component">
							<button
								class="p-1 leading-none text-gray-500 hover:text-accent focus:outline-none"
								@click="$data.showCode = !$data.showCode"
							>
								<icon name="code" />
							</button>
						</li>

						<li>
							<a
								class="block p-1 leading-none text-gray-500 hover:text-accent"
								:href="cUrl"
							>
								<icon class="external" name="external" />
							</a>
						</li>
					</ul>
				</div>
			</div>

			<div
				v-if="$props.component"
				v-show="$data.showCode"
				class="overflow-scroll"
			>
				<pre><code
					ref="code"
					class="p-4 text-sm font-mono bg-gray-200 lang-html"
					v-text="cCode"
				/></pre>
			</div>
		</div>
	</article>
</template>

<script>
	import markup from './markup';

	export default {
		props: {
			attributes: {
				type: Array,
				required: true,
			},

			block: {
				type: String,
				required: true,
			},

			component: {
				type: Object,
				default: null,
			},

			minHeight: {
				type: Number,
				default: 200,
			},

			preview: {
				type: String,
				required: true,
			},

			section: {
				type: String,
				required: true,
			},

			autoload: Boolean,
		},

		data() {
			return {
				iframeActive: false,
				showCode: false,
			};
		},

		computed: {
			cCode() {
				const { name, type } = this.$props.component;

				return markup[type](name, this.$props.attributes);
			},

			cObserverOptions() {
				if (this.$props.autoload) {
					return {
						callback: this.visibilityChanged,
						throttle: 500,
					};
				}

				return null;
			},

			cUrl() {
				return `block?section=${this.$props.section}&block=${this.$props.block}&preview=${this.$props.preview}`;
			},

			cLoadIcon() {
				return this.$props.autoload ? 'code' : 'play';
			},
		},

		mounted() {
			if (this.$props.component) {
				window.hljs.highlightBlock(this.$refs.code);
			}
		},

		methods: {
			loadIframe() {
				this.$data.iframeActive = true;

				this.$nextTick(() => window.iFrameResize({
					minHeight: this.$props.minHeight,
				}, this.$refs.iframe));
			},

			visibilityChanged(isVisible) {
				if (this.$data.iframeActive) {
					return;
				}

				if (isVisible) {
					this.loadIframe();
				}
			},
		},
	};
</script>
