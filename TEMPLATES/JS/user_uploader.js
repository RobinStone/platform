function upload_user_file_form(call_back=function() {}) {
    const inputFile = document.createElement("input");
    inputFile.type = "file";
    inputFile.accept = "image/*";
    inputFile.onchange = function (event) {
        const file = event.target.files[0];
        if (file.size <= 5 * 1024 * 1024) {
            uploadFile(file, call_back);
        } else {
            say("Разрешено загружать файлы до 5 Мб", 2);
        }
    };
    inputFile.click();
}

function uploadFile(file, call_back) {
    const formData = new FormData();
    formData.append("userfile", file);
    formData.append("com", 'files');
    formData.append("table", 'file');
    formData.append("column", 'none');
    formData.append("id", -1);

    const xhr = new XMLHttpRequest();
    xhr.onload = xhr.onerror = function() {
        if (this.status === 200) {
            let mess = JSON.parse(xhr.response);
            console.dir(mess);
            if(mess.status === 'ok') {
                call_back(mess);
            }
        } else {
            say('Не удалось отправить файл');
        }

        // console.log('ОТВЕТ='+ajax.responseText);
        console.log('===============================');
        let mess = JSON.parse(xhr.response);
        console.dir(mess);
        if(mess.status !== 'ok') {
            error_executing(mess);
        }
        console.log('===============================');
    };

    xhr.open("POST", domain, true);
    xhr.responseType = 'text';
    xhr.send(formData);
}
