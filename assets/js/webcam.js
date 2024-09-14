var avatar = document.getElementById('avatar');
var video = document.getElementById('video');
var canvas = document.getElementById('canvas');

if(typeof canvas !== 'undefined') {
    var replaceAvatarButton = document.getElementById('replaceAvatar');
    var captureButton = document.getElementById('capture');
    var saveButton = document.getElementById('save');
    var context = canvas.getContext('2d');

    try {
        replaceAvatarButton.addEventListener('click', () => {
            avatar.style.display = 'none';
            $(`canvas[id="canvas"]`).html(``);
            video.style.display = 'inline-block';
            captureButton.style.display = 'inline-block';
            navigator.mediaDevices.getUserMedia({ video: true }).then(stream => {
                video.srcObject = stream;
            }).catch(err => {
                console.error("Error accessing webcam: ", err);
            });
        });
        
        captureButton.addEventListener('click', () => {
            context.drawImage(video, 0, 0, canvas.width, canvas.height);
            video.style.display = 'none';
            canvas.style.display = 'inline-block';
            saveButton.style.display = 'inline-block';
            captureButton.style.display = 'none';
        });

        saveButton.addEventListener('click', () => {
            const imageData = canvas.toDataURL('image/png');
            $.ajax({
                url: `${baseUrl}api/save_image`,
                type: 'POST',
                data: {
                    image: imageData,
                    user_id: $.array_stream['user_id']
                },
                success: function(response) {

                },
                error: function(error) {}
            });
        });
    } catch(error) {
        
    }
}