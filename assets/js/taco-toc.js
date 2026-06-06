(function() {
	'use strict';

	document.addEventListener('DOMContentLoaded', function() {
		// 1. Find all TOC containers (supports multiple instances via shortcode or auto-insert)
		const tocContainers = document.querySelectorAll('.tacotoc-wrapper');

		if (tocContainers.length === 0) {
			return;
		}

		// Configuration
		const contentSelector = (typeof tacotoc_config !== 'undefined' && tacotoc_config.selector)
			? tacotoc_config.selector
			: '.entry-content';

		const headingsSelector = (typeof tacotoc_config !== 'undefined' && tacotoc_config.headings)
			? tacotoc_config.headings
			: 'h1, h2, h3';

		const collapsibleSelector = (typeof tacotoc_config !== 'undefined' && tacotoc_config.collapsible)
			? tacotoc_config.collapsible
			: '';

		const collapsibleTags = collapsibleSelector ? collapsibleSelector.split(',').map(t => t.trim().toLowerCase()) : [];

		const container = document.querySelector(contentSelector);

		// Helper to hide all containers if content is missing
		function hideAllContainers() {
			tocContainers.forEach(el => el.style.display = 'none');
		}

		// If the content source is missing, hide skeleton loaders and exit.
		if (!container) {
			hideAllContainers();
			return;
		}

		// Select headings based on user preference
		const headings = container.querySelectorAll(headingsSelector);

		// If no headings found, hide skeleton loaders and exit.
		if (headings.length === 0) {
			hideAllContainers();
			return;
		}

		// Prepare IDs and hierarchy relationships once for the DOM content
		const hierarchyData = [];
		headings.forEach((heading, index) => {
			// Ensure heading has an ID for native anchor jumping (supporting Unicode/non-Latin scripts and accents)
			if (!heading.id) {
				let slug = heading.textContent
					.trim()
					.toLowerCase()
					.replace(/\s+/g, '-') // Replace spaces with hyphens
					.replace(/[^\p{L}\p{N}_-]+/gu, '') // Keep only Unicode letters, numbers, underscores, and hyphens
					.replace(/^-+|-+$/g, ''); // Trim hyphens

				if (!slug) {
					slug = 'heading_' + index;
				}
				heading.id = slug;
			}

			const level = parseInt(heading.tagName.substring(1));
			let parentIndex = -1;

			// Find nearest parent (the closest preceding heading with a numerically lower H-level)
			for (let j = index - 1; j >= 0; j--) {
				if (hierarchyData[j].level < level) {
					parentIndex = j;
					break;
				}
			}

			hierarchyData.push({
				level: level,
				parentIndex: parentIndex,
				hasChildren: false,
				tagName: heading.tagName.toLowerCase()
			});

			if (parentIndex !== -1) {
				hierarchyData[parentIndex].hasChildren = true;
			}
		});

		// Render TOCs for each instance independently to preserve event scoping
		tocContainers.forEach(tocContainer => {
			tocContainer.innerHTML = ''; // Clear skeleton loader

			const tocTitle = document.createElement('div');
			tocTitle.className = 'tacotoc-title';
			tocTitle.innerText = (typeof tacotoc_config !== 'undefined' && tacotoc_config.title)
				? tacotoc_config.title
				: 'Table of Contents';

			tocContainer.appendChild(tocTitle);

			const tocList = document.createElement('ol');
			tocContainer.appendChild(tocList);

			const itemsData = [];

			// Build the list elements
			headings.forEach((heading, index) => {
				const li = document.createElement('li');
				li.className = 'tacotoc-item tacotoc-' + heading.tagName.toLowerCase();

				const link = document.createElement('a');
				link.href = '#' + heading.id;
				link.textContent = heading.textContent;

				li.appendChild(link);
				tocList.appendChild(li);

				itemsData.push({
					li: li,
					parentIndex: hierarchyData[index].parentIndex,
					isOpen: true // default state
				});
			});

			// Setup interactive toggles depending on collapsible status
			hierarchyData.forEach((data, i) => {
				if (data.hasChildren) {
					let isDefaultOpen = true;

					// Determine if first direct child aligns with user's collapsible preference
					const firstChildIndex = hierarchyData.findIndex((c, idx) => idx > i && c.parentIndex === i);
					if (firstChildIndex !== -1 && collapsibleTags.includes(hierarchyData[firstChildIndex].tagName)) {
						isDefaultOpen = false;
					}

					itemsData[i].isOpen = isDefaultOpen;

					const toggleBtn = document.createElement('button');
					toggleBtn.type = 'button';
					toggleBtn.className = 'tacotoc-toggle' + (isDefaultOpen ? ' open' : ' closed');
					toggleBtn.setAttribute('aria-expanded', isDefaultOpen);
					toggleBtn.setAttribute('aria-label', 'Toggle nested headings');

					const icon = document.createElement('span');
					icon.className = 'tacotoc-toggle-icon';
					icon.innerHTML = isDefaultOpen ? '&minus;' : '&plus;';
					toggleBtn.appendChild(icon);

					// Place toggle inside the list item, before the link
					itemsData[i].li.insertBefore(toggleBtn, itemsData[i].li.firstChild);
					itemsData[i].li.classList.add('has-children');

					toggleBtn.addEventListener('click', (e) => {
						e.preventDefault();
						itemsData[i].isOpen = !itemsData[i].isOpen;
						toggleBtn.className = 'tacotoc-toggle' + (itemsData[i].isOpen ? ' open' : ' closed');
						toggleBtn.setAttribute('aria-expanded', itemsData[i].isOpen);
						icon.innerHTML = itemsData[i].isOpen ? '&minus;' : '&plus;';
						renderVisibility();
					});
				} else {
					itemsData[i].li.classList.add('no-children');
				}
			});

			// Evaluates rendering based on chained parental states
			function renderVisibility() {
				itemsData.forEach((item, i) => {
					if (item.parentIndex === -1) {
						item.li.style.display = ''; // Root items always show
					} else {
						let visible = true;
						let curr = item.parentIndex;

						// Walk up the tree and test if all ancestors are opened
						while (curr !== -1) {
							if (!itemsData[curr].isOpen) {
								visible = false;
								break;
							}
							curr = itemsData[curr].parentIndex;
						}

						item.li.style.display = visible ? '' : 'none';
					}
				});
			}

			renderVisibility();
		});

		// Check URL Hash and Scroll if needed
		if (window.location.hash) {
			const hash = window.location.hash.substring(1);
			const targetElement = document.getElementById(hash);

			if (targetElement) {
				setTimeout(() => {
					let offset = 20;
					const adminBar = document.getElementById('wpadminbar');
					if (adminBar) {
						offset += adminBar.offsetHeight;
					}

					const elementPosition = targetElement.getBoundingClientRect().top;
					const offsetPosition = elementPosition + window.scrollY - offset;

					window.scrollTo({
						top: offsetPosition,
						behavior: 'auto'
					});
				}, 0);
			}
		}

		// Scroll Tracking Logic (Active Highlighting)
		function onScroll() {
			let currentId = '';

			headings.forEach(heading => {
				const rect = heading.getBoundingClientRect();
				if (rect.top < 150) {
					currentId = heading.id;
				}
			});

			// If the user has scrolled to the absolute bottom of the page, automatically highlight the last heading
			const scrollPosition = window.innerHeight + window.scrollY;
			const scrollHeight = document.documentElement.scrollHeight;
			if (scrollPosition >= scrollHeight - 10 && headings.length > 0) {
				currentId = headings[headings.length - 1].id;
			}

			const allTocLinks = document.querySelectorAll('.tacotoc-wrapper ol a');

			allTocLinks.forEach(link => {
				const listItem = link.parentElement;
				if (currentId && link.getAttribute('href') === '#' + currentId) {
					listItem.classList.add('active');
				} else {
					listItem.classList.remove('active');
				}
			});
		}

		window.addEventListener('scroll', onScroll);
		onScroll();
	});
})();