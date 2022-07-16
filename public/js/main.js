// Добавление комментария

async function sentComment() {

    let formData = new FormData();

    let form = document.forms[0];
    let text = form.elements.text;
    formData.append('text', text.value);

    formData.append('ajaxSettings', 'page:Main:sentComment');

    // Запрос на сервер

    let data = await fetch('/Ajax.php', {
        method: 'POST',
        body: formData
    });

    let response = await data.json();

    let DIV = document.createElement('div');
    DIV.classList.add('comment');

    let check = document.getElementsByClassName('comment')[0];  // Проверка первый ли это комментарий
    let show = document.querySelector('.comments_show');

    if(check) show.insertBefore(DIV,document.getElementsByClassName('comment')[0]);
    else show.appendChild(DIV);

    let elements = {
        b: 'login',
        span: 'text',
        p: 'date'
    }

    for(let i in elements) {
        let element = document.createElement(i);
        element.textContent = response[elements[i]];
        DIV.appendChild(element);
    }

    text.value = '';
    formData = null;
}

// Кнопка показать ещё + показать для выбранного фильтра (По условию condition)

async function showComments(condition = '') {

    let formData = new FormData();

    let comments = document.querySelectorAll('.comment');

    let from = (condition != '') ? 0 : comments.length;     // С какой записи извлечение
    formData.append('from', from);

    let filter = document.querySelector('.hint_choosed');   // При наличии выбранного варианта в фильтре
    if(filter) formData.append('filter', filter.value);

    formData.append('ajaxSettings', 'page:Main:showComments');

    // Запрос на сервер

    let data = await fetch('/Ajax.php', {
        method: 'POST',
        body: formData
    });

    if(data) {

        let response = await data.json();

        if(response[0] != false) {

            // Удаление не относящихся к фильтру записей

            if(condition != '') {
                comments.forEach((item) => {
                    item.remove()
                });
            }

            // Размещение записей из БД

            for(key in response) {

                let show = document.querySelector('.comments_show');

                if(key != 3) {

                    let DIV = document.createElement('div');
                    show.appendChild(DIV);
                    DIV.classList.add('comment');

                    let P1 = document.createElement('p');
                    P1.textContent = response[key].login;
                    DIV.appendChild(P1);

                    let SPAN = document.createElement('span');
                    SPAN.textContent = response[key].text;
                    DIV.appendChild(SPAN);

                    let P3 = document.createElement('p');
                    P3.textContent = response[key].date;
                    DIV.appendChild(P3);
                }
            }

            // Отображение кнопки "Показать ещё"

            let button = document.querySelector('.comments_more');

            if(response.length == 1 || response[response.length - 1] != 'show') button.style.display = "none";
            if(response.length == 4) button.style.display = "block";

            // Очистка объекта

            formData.delete('from');
            formData.delete('filter');
            formData.delete('buttonStatus');
        }
    }
}

// Текстовое поле ввода имени автора для фильтрации

async function filterComments(value) {

    let formData = new FormData();

    if(value < 3) {

        if(document.querySelector('.comments_hints'))
            document.querySelector('.comments_hints').remove();          // Удаление подсказок
 
         // Удаление метки о наличии выбанного имени в поле фильтра

        if(document.getElementById('filter').classList.contains('hint_choosed')); document.getElementById('filter').classList.remove('hint_choosed');

        showComments("true");
    }

    if(value >= 3) {

        formData.append('login', document.getElementById('filter').value);

        formData.append('ajaxSettings', 'page:Main:getLogins');

        // Запрос на сервер

        let data = await fetch('/Ajax.php', {
            method: 'POST',
            body: formData
        });

        if(data) {

            if(document.querySelector('.comments_hints')) document.querySelector('.comments_hints').remove();

            let hint = document.querySelector('.comments_filter');
            let response = await data.json();
            
            let HINTS = document.createElement('div');
            hint.appendChild(HINTS);
            HINTS.classList.add('comments_hints');

            for(key in response) {

                let DIV = document.createElement('div');
                HINTS.appendChild(DIV);

                DIV.textContent = response[key].login;
                DIV.classList.add('comments_hint');

                let DivOnclick = document.createAttribute("onclick");
                DivOnclick.value = "addHint(this)";
                DIV.setAttributeNode(DivOnclick);
            }
        }
    }
}

// Добавление имени автора из подсказки в текстовое поле фильтра

function addHint(item) {

    let filter = document.getElementById('filter');

    filter.value = item.textContent;
    filter.classList.add('hint_choosed');

    let check = document.querySelector('.comments_hints');
    if(check) check.remove();

    showComments("true");
}

// Кнопка применить для фильтрации авторов

function showFiltered() {

    let input = document.getElementById('filter');
    if(!input.classList.contains('hint_choosed')) input.classList.add('hint_choosed');

    let check = document.querySelector('.comments_hints');
    if(check) check.remove();

    showComments("true");
}