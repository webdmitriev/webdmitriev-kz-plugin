document.addEventListener('DOMContentLoaded', function () {
  const wrapper = document.getElementById('contacts-repeatable-wrapper');
  const addBtn = document.getElementById('add-city-btn');

  if (!wrapper || !addBtn) {
    return;
  }

  // Добавление нового города
  addBtn.addEventListener('click', function () {
    const rows = wrapper.querySelectorAll('.city-row');
    const count = rows.length;
    const template = rows[0].cloneNode(true);

    // Очищаем значения полей в клонированной строке
    template.querySelectorAll('input, textarea').forEach(function (input) {
      if (input.name.includes('[id]')) {
        input.value = '';
      } else {
        input.value = '';
      }
      input.name = input.name.replace(/\[\d+\]/, '[' + count + ']');
    });

    wrapper.appendChild(template);
  });

  // Удаление города
  wrapper.addEventListener('click', function (e) {
    if (e.target.classList.contains('remove-city-btn')) {
      const rows = wrapper.querySelectorAll('.city-row');
      if (rows.length > 1) {
        e.target.closest('.city-row').remove();
      } else {
        alert('Должен остаться хотя бы один город!');
      }
    }
  });
});