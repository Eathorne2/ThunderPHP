(function(){
    const input = document.getElementById('tcLogoInput');
    const pathInput = document.getElementById('tcLogoPath');
    const progressWrap = document.getElementById('tcUploadProgressWrap');
    const progressBar = document.getElementById('tcUploadProgress');
    const status = document.getElementById('tcUploadStatus');
    const preview = document.getElementById('tcLogoPreview');

    if(!input) return;

    function csrfParts(){
        const html = window.THUNDER_CONFIG_CSRF || '';
        const div = document.createElement('div');
        div.innerHTML = html;
        const csrfInput = div.querySelector('input');
        if(!csrfInput) return null;
        return { name: csrfInput.name, value: csrfInput.value };
    }

    input.addEventListener('change', function(){
        if(!input.files || !input.files[0]) return;

        const file = input.files[0];
        const data = new FormData();
        data.append('logo', file);

        const csrf = csrfParts();
        if(csrf) data.append(csrf.name, csrf.value);

        const xhr = new XMLHttpRequest();
        xhr.open('POST', input.dataset.uploadUrl, true);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

        progressWrap.style.display = 'block';
        progressBar.style.width = '0%';
        status.textContent = 'Uploading logo...';

        xhr.upload.addEventListener('progress', function(e){
            if(e.lengthComputable){
                const percent = Math.round((e.loaded / e.total) * 100);
                progressBar.style.width = percent + '%';
            }
        });

        xhr.onload = function(){
            let res = null;
            try{ res = JSON.parse(xhr.responseText); }catch(e){}

            if(xhr.status >= 200 && xhr.status < 300 && res && res.success){
                status.textContent = res.message || 'Logo uploaded.';
                pathInput.value = res.logo;

                if(preview && preview.tagName === 'IMG'){
                    preview.src = res.logo + '?v=' + Date.now();
                }else if(preview){
                    preview.outerHTML = '<img id="tcLogoPreview" src="' + res.logo + '?v=' + Date.now() + '" alt="Application Logo">';
                }
                progressBar.style.width = '100%';
            }else{
                status.textContent = (res && res.message) ? res.message : 'Upload failed.';
            }
        };

        xhr.onerror = function(){
            status.textContent = 'Upload failed because of a network error.';
        };

        xhr.send(data);
    });
})();
