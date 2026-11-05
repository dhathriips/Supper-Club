'use strict';
const registration = document.querySelector('#register');
if (registration) {
  const rules = {
    name: /^[A-Za-z][A-Za-z .'-]{1,59}$/,
    email: /^[A-Za-z0-9._%+-]+@example\.test$/i,
    student_code: /^SC-[0-9]{4}$/,
    password: /^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])[!-~]{8,64}$/
  };
  function validate(input) {
    const value = input.name === 'password' ? input.value : input.value.trim();
    const ok = rules[input.name].test(value);
    const hint = document.querySelector('#hint-' + input.name);
    input.setCustomValidity(ok ? '' : hint.textContent);
    input.setAttribute('aria-invalid', String(!ok));
    hint.classList.toggle('invalid', !ok);
  }
  Object.keys(rules).forEach(name => {
    const input = registration.elements[name];
    input.addEventListener('input', () => validate(input));
    input.addEventListener('blur', () => validate(input));
  });
  registration.addEventListener('submit', event => {
    Object.keys(rules).forEach(name => validate(registration.elements[name]));
    if (!registration.reportValidity()) event.preventDefault();
  });
}
const filter = document.querySelector('#dish-filter');
if (filter) filter.addEventListener('input', () => {
  let count = 0;
  for (const dish of document.querySelectorAll('.dish')) {
    dish.hidden = !dish.dataset.search.toLowerCase().includes(filter.value.trim().toLowerCase());
    if (!dish.hidden) count++;
  }
  document.querySelector('#dish-count').textContent = count ? count + (count === 1 ? ' dish' : ' dishes') : 'No matching dishes';
});
const board = document.querySelector('#board');
if (board) {
  board.addEventListener('pointermove', event => {
    document.querySelector('#coords').textContent = 'Client (' + event.clientX + ', ' + event.clientY + ') · Screen (' + event.screenX + ', ' + event.screenY + ')';
  });
  const tickets = [...board.querySelectorAll('.ticket')];
  for (const ticket of tickets) {
    const raise = () => tickets.forEach(other => other.classList.toggle('front', ticket === other));
    ['pointerenter','click','focus'].forEach(name => ticket.addEventListener(name, raise));
  }
  const help = document.querySelector('#help');
  const box = document.querySelector('#help-box');
  function show(open) {
    box.hidden = !open;
    help.setAttribute('aria-expanded', String(open));
    help.textContent = open ? 'Hide help' : '? Show help';
  }
  help.addEventListener('click', () => show(box.hidden));
  document.addEventListener('keydown', e => {
    if (e.key === 'Escape' && !box.hidden) { show(false); help.focus(); }
  });
}
document.querySelectorAll('[data-confirm]').forEach(button =>
  button.addEventListener('click', event => {
    if (!window.confirm(button.dataset.confirm)) event.preventDefault();
  })
);

