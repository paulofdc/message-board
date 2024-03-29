<?php
    $currentLoggedIn = AuthComponent::user('id');
    $homeUrl = $this->Html->url('/');
?>
<script>
    $(document).ready(function() {
        let defaultPhoto = 'https://www.gravatar.com/avatar/00000000000000000000000000000000?s=200&d=mp',
            initialOffset = parseInt($('#count').val()),
            timeoutId; 

        $('.select2-recipient').select2({
            width: 'resolve',
            theme: "classic",
            templateResult: formatOption
        });

        function formatOption(option) {
            if (!option.id) {
                return option.text;
            }

            let splitData = option.id.split("~USER:");
            var imageUrl = defaultPhoto;
            if(splitData[1]) {
                imageUrl = `<?= $homeUrl ?>${splitData[1].replace(/^\//, '')}`;
            }
            var optionText = option.text;

            var $option = $(
                '<span><img style="width: 20px; height: 20px; margin-right: 5px;" src="' + imageUrl + '" class="select2-option-image" /> ' + optionText + '</span>'
            );

            return $option;
        }

        $(document).on('keyup', '#search', (e) => {
            e.stopPropagation();
            clearTimeout(timeoutId);
            const type = $(e.target).data('type');
            let url = '<?php echo $this->Html->url(['controller' => 'threads', 'action' => 'search']); ?>';

            let payload = {
                searchValue: $('#search').val(),
                searchType: type
            };

            if(type == 'message') {
                payload.thread_id = $('#MessageThreadId').val();
            }

            timeoutId = setTimeout(() => {
                ajaxCall(url, 'POST', payload).then(response => {
                    const result = JSON.parse(response);

                    if($('#search').val()) {
                        $('.inbox-messages').hide();
                        $('#load-btn-container').hide();
                        $('#search-container').show(); 

                        $('.inbox-search').empty();
                        if((result.data).length > 0) {
                            $('.empty-m').hide();
                            result.data.map((v) => {
                                switch(type) {
                                    case 'thread':
                                        let name = v.Owner.name,
                                            photo = v.Owner.photo,
                                            userId = v.Owner.id;
                                        if(v.Owner.id == '<?= $currentLoggedIn ?>') {
                                            name = v.Receiver.name;
                                            photo = v.Receiver.photo;
                                            userId = v.Receiver.id;
                                        }
                                        addThread({
                                                messageOwner: v.Message[0].user_id,
                                                modified: v.Thread.modified,
                                                dataId: v.Thread.id,
                                                content: v.Message[0].content,
                                                name: name,
                                                photo: photo,
                                                userId: userId
                                            },
                                            '.inbox-search'
                                        );
                                        break;
                                    case 'message':
                                        addMessage({
                                                created: v.Message.created,
                                                dataId: v.Message.id,
                                                content: v.Message.content,
                                                photo: v.User.photo,
                                                messageOwner: v.Message.user_id,
                                                isLongText: v.Message.isLongText
                                            }, 
                                            'append', 
                                            v.Message.user_id != '<?= $currentLoggedIn ?>' ? 'left' : 'right',
                                            '.inbox-search'
                                        );
                                        break;
                                }
                            });
                        } else {
                            $('.empty-m').show();
                        }
                    } else {
                        $('#search-container').hide(); 
                        $('.inbox-messages').show();
                        $('#load-btn-container').show();
                    }


                }).catch(error => {
                    console.error(error);
                    alert('There was a problem during getting the message. Please try again.');
                });
            }, 800);
        });

        $(document).on('click', '.thread-image', (e) => {
            e.preventDefault();
            e.stopImmediatePropagation();
            const userId = $(e.target).data('user-id');
            window.open(`<?= $homeUrl ?>users/profile/${userId}`,'_blank')
        });

        $(document).on('click', '#load-more-btn', (e) => {
            e.preventDefault();
            $(e.target).css({'pointer-events': 'none'});
            const type = $(e.target).data('type');
            let url = '<?php echo $this->Html->url(['controller' => 'threads', 'action' => 'showMore']); ?>';
            let childElement = (type == 'thread') ? 'a' : '.message-block';
            let offset = $(`.inbox-messages ${childElement}`).length;
            let payload = {
                currentLatestOldestId: getLatestOldestId(),
                searchType: type,
                offset: offset
            };

            if(type == 'message') {
                payload.thread_id = $('#MessageThreadId').val();
            }
            
            ajaxCall(url, 'POST', payload).then(response => {
                const result = JSON.parse(response);
                if(result.offset >= initialOffset) {
                    $('#load-btn-container').remove();
                }
                result.data.map((v) => {
                    switch(type) {
                        case 'thread':
                            let name = v.Owner.name,
                                photo = v.Owner.photo,
                                userId = v.Owner.id;
                            if(v.Owner.id == '<?= $currentLoggedIn ?>') {
                                name = v.Receiver.name;
                                photo = v.Receiver.photo;
                                userId = v.Receiver.id;
                            }
                            addThread({
                                    messageOwner: v.Message[0].user_id,
                                    modified: v.Thread.modified,
                                    dataId: v.Thread.id,
                                    content: v.Message[0].content,
                                    name: name,
                                    photo: photo,
                                    userId: userId
                                }
                            );
                            break;
                        case 'message':
                            addMessage({
                                    created: v.Message.created,
                                    dataId: v.Message.id,
                                    content: v.Message.content,
                                    photo: v.User.photo,
                                    messageOwner: v.Message.user_id,
                                    isLongText: v.Message.isLongText
                                }, 
                                'append', 
                                v.Message.user_id != '<?= $currentLoggedIn ?>' ? 'left' : 'right'
                            );
                            break;
                    }
                });
                $(e.target).css({'pointer-events': 'all'});
            }).catch(error => {
                $(e.target).css({'pointer-events': 'all'});
                console.error(error);
                alert('There was a problem during getting the message. Please try again.');
            });
        });

        $(document).on('click', '#upload-btn', () => {
            $('#profile-image-upload').click();
        });
        
        $(document).on('click', '.delete-message-btn-thread', (e) => {
            e.preventDefault();
            e.stopImmediatePropagation();
            if(confirm("<?= __('Are you sure you want to delete this?') ?>")){
                const dataId = $(e.target).data('id');
                let url = `<?php echo $this->Html->url(array('controller' => 'threads', 'action' => 'delete')); ?>`;
                ajaxCall(url, 'POST', {
                    id: dataId
                }).then(response => {
                    const data = JSON.parse(response);
                    if(data.isSuccess) {
                        initialOffset -= 1;
                        $(`.t-link-${dataId}`).fadeOut( () => { $(`.t-link-${dataId}`).remove(); });
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

        $(document).on('click', '.message-block, .message-content', (e) => {
            e.stopImmediatePropagation();
            let dataId = $(e.target).data('id');
            if(!dataId) { 
                dataId = $(e.target).parent().data('id');
            }
            $(`.delete-container-${dataId}`).animate({width:'toggle'},350);
        });

        $(document).on('click', '.show-more', (e) => {
            e.stopImmediatePropagation();
            let messageContent = $(e.target).closest('.message-content');
            if (messageContent.length > 0) {
                let dataId = messageContent.data('id');
                let body = messageContent.find('.body');
                if (body.length > 0) {
                    if (body.hasClass('ellipsis')) {
                        body.toggleClass('expand');
                        const showMoreText = $(e.target).text();
                        $(e.target).text(showMoreText == 'Hide' ? 'Show more' : 'Hide');
                    } else {
                        $(`.delete-container-${dataId}`).animate({ width: 'toggle' }, 350);
                    }
                } 
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
                        initialOffset -= 1;
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
                    initialOffset += 1;
                    addMessage({
                        created: data.created,
                        dataId: data.dataId,
                        content: $('#MessageContent').val(),
                        photo: '<?= AuthComponent::user('photo') ?>',
                        messageOwner: data.messageOwner,
                        isLongText: data.isLongText
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

        const addMessage = (data, action = 'prepend', position = 'right', locationClass = '.inbox-messages') => {
            let deleteBtn = `
                <div class="delete-container delete-container-${data.dataId}">
                    <span class="delete-message-btn" data-id="${data.dataId}">Delete</span>
                </div>`;

            if(position == 'left') {
                deleteBtn = '';
            }

            let showMore = isLongText = '';
            if(data.isLongText){
                isLongText = ' ellipsis ';
                showMore = `<div class="show-more">show more</div>`;
            }

            
            const messageBlock = `
                <div class="message-block m-block-${data.dataId} conversation c-${position}" data-id="${data.dataId}">
                    <a href="<?= $homeUrl ?>users/profile/${data.messageOwner}" target="_blank">
                        ${displayPhoto(data.photo)}
                    </a>
                    <div class="message-content ${position}-content" data-id="${data.dataId}">
                        <div class="body${isLongText}">
                            ${nl2br(data.content)}                
                        </div>
                        ${showMore}
                        <div class="footer">
                            ${data.created}                   
                        </div>
                    </div>
                    ${deleteBtn}
                </div>
            `;

            if(action == 'prepend') {
                $(locationClass).prepend(messageBlock);
            } else {
                $(locationClass).append(messageBlock);
            }
        }

        const addThread = (data, locationClass = '.inbox-messages') => {
            const threadBlock = `
                <a class="thread-link t-link-${data.dataId}" href="<?= $homeUrl ?>threads/view/${data.dataId}" data-id="${data.dataId}">
                    <div class="message-block">
                        ${displayPhoto(data.photo, data.userId, 'thread')}
                        <div class="message-content">
                            <div class="header">
                                ${data.name}
                            </div>
                            <div class="body ellipsis">
                                ${nl2br(data.content)}                       
                            </div>
                            <div class="footer">
                                ${data.modified}                         
                            </div>
                        </div>
                        <div class="delete-container-thread delete-thread-container-${data.dataId}">
                            <span class="delete-message-btn-thread fa fa-trash-o" data-id="${data.dataId}"></span>
                        </div>
                    </div>
                </a>`;
            
            $(locationClass).append(threadBlock);
        }

        const displayPhoto = (image, userId = null, type = 'message') => {
            let isThreadBlock = (type != 'message') ? ' thread-image ' : '',
                photo = `<img src="${defaultPhoto}" class="avatar${isThreadBlock}" alt="Your Image" data-user-id="${userId}">`;
            if(image) {
                photo = `<img src="<?= $homeUrl ?>${(image).replace(/^\//, '')}" class="avatar${isThreadBlock}" alt="Your Image" data-user-id="${userId}">`;
            }
            return photo;
        }

        const nl2br = (str, is_xhtml) => {
            if (typeof str === 'undefined' || str === null) {
                return '';
            }
            var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br />' : '<br>';
            return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + breakTag + '$2');
        }
    });
</script>