<?php
    $currentLoggedIn = AuthComponent::user('id');
    $photoSrc = $this->Html->url('/');
?>
<script>
    $(document).ready(function() {
        $('.select2-recipient').select2({
            width: 'resolve',
            theme: "classic",
            templateResult: formatOption
        });

        function formatOption(option) {
            console.log('option', option);
            if (!option.id) {
                return option.text;
            }

            let splitData = option.id.split("~USER:");
            var imageUrl = 'https://www.gravatar.com/avatar/00000000000000000000000000000000?s=200&d=mp';
            if(splitData[1]) {
                imageUrl = `<?= $photoSrc ?>${splitData[1].replace(/^\//, '')}`;
            }
            var optionText = option.text;

            var $option = $(
                '<span><img style="width: 20px; height: 20px; margin-right: 5px;" src="' + imageUrl + '" class="select2-option-image" /> ' + optionText + '</span>'
            );

            return $option;
        }

        $(document).on('click', '#upload-btn', () => {
            $('#profile-image-upload').click();
        });
        
        $(document).on('change', '#profile-image-upload', () => {
            var fileName = $(this).val().split('\\').pop();
        });

        $(document).on('click', '.message-block', (e) => {
            e.stopImmediatePropagation();
            const dataId = $(e.target).data('id');
            if(dataId) {
                $(`.delete-container-${dataId}`).animate({width:'toggle'},350);
            }
        });

        $(document).on('click', '.delete-message-btn', (e) => {
            e.stopImmediatePropagation();

            if(confirm("<?= __('Are you sure you want to delete this?') ?>")){
                const dataId = $(e.target).data('id');
                let url = `<?php echo $this->Html->url(array('controller' => 'messages', 'action' => 'delete')); ?>`;
                ajaxCall(url, 'POST', {
                    id: dataId
                }).then(response => {
                    const data = JSON.parse(response);
                    if(data.isSuccess) {
                        $(`.m-block-${dataId}`).fadeOut( () => { $(`.m-block-${dataId}`).remove(); });
                    } else {
                        console.log(response);
                        alert('There was a problem during deletion of message.');
                    }
                }).catch(error => {
                    console.error(error);
                    alert('There was a problem during deletion of message. Please try again.');
                });
            }
        });

        $(document).on('click', '#load-more-btn', (e) => {
            e.preventDefault();
            let url = '<?php echo $this->Html->url(['controller' => 'threads', 'action' => 'loadMoreMessages']); ?>';
            ajaxCall(url, 'POST', {
                thread_id: $('#MessageThreadId').val(),
                currentLatestOldestId: getLatestOldestId(),
            }).then(response => {
                const result = JSON.parse(response);
                
                if(result.hasLastData) {
                    $('#load-btn-container').remove();
                }

                result.data.map((v) => {
                    addMessage({
                            created: v.Message.created,
                            dataId: v.Message.id,
                            content: v.Message.content,
                            photo: v.User.photo
                        }, 
                        'append', 
                        v.Message.user_id != '<?= $currentLoggedIn ?>' ? 'left' : 'right'
                    );
                });

            }).catch(error => {
                console.error(error);
                alert('There was a problem during getting the message. Please try again.');
            });
        });

        $(document).on('click', '#reply-btn', (e) => {
            e.preventDefault();
            if($('#MessageContent').val().trim()) {
                reply();
            } else {
                alert('<?= __('Input some messages.') ?>');
            }
        });

        const getLatestOldestId = () => {
            return lastChild = $('.inbox-messages').children(':last-child').data("id");
        }

        const reply = async () => {
            let url = '<?php echo $this->Html->url(['controller' => 'messages', 'action' => 'add']); ?>';
            ajaxCall(url, 'POST', {
                content: $('#MessageContent').val(),
                thread_id: $('#MessageThreadId').val()
            }).then(response => {
                const data = JSON.parse(response);
                if(data.isSuccess) {
                    addMessage({
                        created: data.created,
                        dataId: data.dataId,
                        content: $('#MessageContent').val(),
                        photo: '<?= AuthComponent::user('photo') ?>'
                    });
                    $('#MessageContent').val('');
                } else {
                    console.log(response);
                    alert('There was a problem during sending the message.');
                }
            }).catch(error => {
                console.error(error);
                alert('There was a problem during sending the message. Please try again.');
            });
        }

        const ajaxCall = async (url, method, data, retries = 1) => {
            if (retries > 3) {
                throw new Error('Maximum retries reached.');
            }

            try {
                const response = await new Promise((resolve, reject) => {
                    $.ajax({
                        type: method,
                        url: url,
                        data: data,
                        success: resolve,
                        error: (xhr, status, error) => {
                            console.error(error);
                            if (retries < 3) {
                                const delay = Math.pow(2, retries) * 1000;
                                console.log(`Retrying request in ${delay}ms (attempt ${retries + 1} of 3)`);
                                setTimeout(() => {
                                    ajaxCall(url, method, data, retries + 1)
                                        .then(resolve)
                                        .catch(reject);
                                }, delay);
                            } else {
                                reject(error);
                            }
                        }
                    });
                });

                return response;
            } catch (error) {
                console.error('Maximum retries reached. Giving up.');
                throw error;
            }
        };

        const addMessage = (data, action = 'prepend', position = 'right') => {
            if(data.hasOwnProperty('user_id')) {

            }

            let deleteBtn = `
                <div class="delete-container delete-container-${data.dataId}">
                    <span class="delete-message-btn" data-id="${data.dataId}">Delete</span>
                </div>`;

            if(position == 'left') {
                deleteBtn = '';
            }

            if(data.photo) {
                photo = `<img src="<?= $photoSrc ?>${(data.photo).replace(/^\//, '')}" class="avatar" alt="Your Image">`;
            } else {
                photo = `<img src="https://www.gravatar.com/avatar/00000000000000000000000000000000?s=200&d=mp" class="avatar" alt="Your Image">`;
            }

            const messageBlock = `
                <div class="message-block m-block-${data.dataId} conversation c-${position}" data-id="${data.dataId}">
                    ${photo}
                    <div class="message-content ${position}-content">
                        <div class="body">
                            ${data.content}                
                        </div>
                        <div class="footer">
                            ${data.created}                   
                        </div>
                    </div>
                    ${deleteBtn}
                </div>
            `;

            if(action == 'prepend') {
                $('.inbox-messages').prepend(messageBlock);
            } else {
                $('.inbox-messages').append(messageBlock);
            }
        }
    });
</script>