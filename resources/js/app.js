const menuToggle = document.querySelector('[data-menu-toggle]');
const sidebar = document.querySelector('[data-sidebar]');

menuToggle?.addEventListener('click', () => {
	const isOpen = sidebar?.classList.toggle('is-open') ?? false;
	menuToggle.setAttribute('aria-expanded', String(isOpen));
});

document.querySelectorAll('[data-filter-input]').forEach((input) => {
	input.addEventListener('input', () => {
		const list = input.closest('.section-heading')?.nextElementSibling;
		const query = input.value.trim().toLocaleLowerCase();

		list?.querySelectorAll('[data-filter-item]').forEach((item) => {
			item.hidden = !item.dataset.search?.includes(query);
		});
	});
});

document.querySelectorAll('form[data-confirm]').forEach((form) => {
	form.addEventListener('submit', (event) => {
		if (!window.confirm(form.dataset.confirm)) {
			event.preventDefault();
		}
	});
});
