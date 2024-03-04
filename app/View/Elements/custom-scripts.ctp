<?php
    $photo = 'https://www.gravatar.com/avatar/00000000000000000000000000000000?s=200&d=mp';
    if(AuthComponent::user('photo')) {
        $photo = '/uploads/' . AuthComponent::user('photo');
        
    }

    $photo = $this->Html->image($photo, [
        'class' => 'avatar',
        'alt' => 'Your Image'
    ]);
?>
<script>
    $(document).ready(function() {
        $('.select2-recipient').select2({
            width: 'resolve',
            theme: "classic"
        });

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

        $(document).on('click', '#reply-btn', (e) => {
            e.preventDefault();
            if($('#MessageContent').val().trim()) {
                reply();
            } else {
                alert('<?= __('Input some messages.') ?>');
            }
        });

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
                        content: $('#MessageContent').val()
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

        const addMessage = (data) => {
            const messageBlock = `
                <div class="message-block m-block-${data.dataId} conversation c-right" data-id="${data.dataId}">
                    <?= $photo ?>          
                    <div class="message-content right-content">
                        <div class="body">
                            ${data.content}                
                        </div>
                        <div class="footer">
                            ${data.created}                   
                        </div>
                    </div>
                    <div class="delete-container delete-container-${data.dataId}">
                        <span class="delete-message-btn" data-id="${data.dataId}">Delete</span>
                    </div>
                </div>
            `;
            $('.inbox').prepend(messageBlock);
        }
    });
</script>