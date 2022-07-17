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

    let response = await data.json();

    if(response[0] != false) {

        if(condition != '') document.querySelector('.comments_show').innerText = '';    // Удаление всех записей

        // Размещение записей из БД

        let show = document.querySelector('.comments_show');
        let elements = {
            b: 'login',
            span: 'text',
            p: 'date'
        };

        for(let key in response) if(key != 3) {

            let DIV = document.createElement('div');
            show.appendChild(DIV);
            DIV.classList.add('comment');

            for(let i in elements) {
                let element = document.createElement(i);
                element.textContent = response[key][elements[i]];
                DIV.appendChild(element);
            }
        }

        // Отображение кнопки "Показать ещё"

        let button = document.querySelector('.comments_more');

        if(response.length == 1 || response[response.length - 1] != 'show') button.style.display = "none";
        if(response.length == 4) button.style.display = "block";

        formData = null;
    }
}

// Текстовое поле ввода имени автора для фильтрации

async function filterComments(value) {

    let hints = document.querySelector('.comments_hints');      // Выпадающая подсказка
    let filter = document.getElementById('filter');             // Метка о наличии выбанного имени в поле фильтра

    if(value < 3) {

        if(hints) hints.remove();
        if(filter.classList.contains('hint_choosed')) filter.classList.remove('hint_choosed');

        showComments("true");
    }

    if(value >= 3) {

        if(hints) hints.remove();

        let formData = new FormData();
        formData.append('login', filter.value);
        formData.append('ajaxSettings', 'page:Main:getLogins');

        // Запрос на сервер

        let data = await fetch('/Ajax.php', {
            method: 'POST',
            body: formData
        });

        let response = await data.json();
        let hint = document.querySelector('.comments_filter');

        // Отображение выпадающей подсказки с логинами

        let HINTS = document.createElement('div');
        hint.appendChild(HINTS);
        HINTS.classList.add('comments_hints');

        for(let key in response) {

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