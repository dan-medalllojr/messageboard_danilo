
<main class="content">
    <div class="container p-0">
        <span class="fs-4">Message Detail</span>
        <div class="py-3 px-4">
            <form id="send-message-form">
                    <textarea type="text" id="content" name="content" class="form-control" placeholder="Type your message"> </textarea>
                  <div class="d-flex justify-content-end mt-2">
                     <button type="submit" class="btn btn-primary">Reply Message</button>
                  </div>
            </form>
         </div>
		<div class="card">
                <div class="px-4 d-md-block">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <input type="text" class="form-control my-3" id="search" placeholder="Search...">
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="position-relative">
                            <div class="chat-messages p-4">
                                    <!-- this will fetch by ajax -->
                            </div>
                           <div class="text-center">
                           <a href="javascript:void()" id="showMore">Show more</a>
                           </div>
                        </div>
                    </div>
		</div>
	</div>
</main> 

<script>
   $(document).ready(function() {
    var offset = 0;
    var limit = 10;
    $('#showMore').on('click', ()=> messageDetail())
    messageDetail(); // Load initial data



    function messageDetail(search) {
        $.ajax({
            type: 'GET',
            url: '/cakephp/messages/messageDetail/<?= h($chatId) ?>',
            data: {
                offset: offset,
                limit: limit,
                search: search || ''
            },
            success: function(data) { 
                var messages = data;
                console.log(messages)
                if(messages.length < 10){
                    $('#showMore').hide();
                }
                // $('.chat-messages').empty(); // Clear previous items
                if(messages.length > 0){
                        messages.forEach(message => {
                            var dbDateCreated = message.created;
                            var dbDateModified = message.modified;
                            var objDateCreated = new Date(dbDateCreated);
                            var objDateModified = new Date(dbDateModified);
                            var messageId = message.id
                            var imagePath = message.user_imagepath && message.user_imagepath !== '' ? message.user_imagepath: 'uploads/no-image.png';
                            var senderId = message.sender_id;

                            // Get today's date
                            const today = new Date();
                            today.setHours(0, 0, 0, 0); // Set time to 00:00:00 for comparison
                            var options;
                                    if(objDateCreated >= today){
                                        // If the date is today, display only the time
                                        options = { hour: 'numeric', minute: 'numeric', hour12: true };
                                    }else{
                                            // If the date is not today, display full date and time
                                        options = { year: 'numeric', month: 'short', day: 'numeric', hour: 'numeric', minute: 'numeric', hour12: true };
                                    }

                                    var messageContent = message.content;

                                if(message.sender_id !== <?= AuthComponent::user('id'); ?>){
                                        $('.chat-messages').append(`
                                            <div class="chat-message-right chat-parent pb-4">
                                                <div>
                                                    <a href="#" class="linkProfile" value="${senderId}">
                                                        <img src="../../${imagePath}" class="rounded-circle mr-1" alt="" width="40" height="40">
                                                    </a>
                                                    <div class="text-muted small text-nowrap mt-2">
                                                    ${objDateCreated.toLocaleString('en-US', options).replace('AM','am').replace('PM','pm')}
                                                    </div>
                                                </div>
                                                <div class="flex-shrink-1 bg-light rounded py-2 px-3 mr-3 message-parent">
                                                     <div class="message-parent">
                                                          <div class="message-parent">
                                                                <div class="message-preview">
                                                                    <div>${limitMessage(messageContent)}</div>
                                                                </div>
                                                            </div>

                                                        </div>

                                                </div>
                                            </div>
                                            
                                        `);
                                    }else{
                                        $('.chat-messages').append(`
                                                <div class="chat-message-left chat-parent pb-4">
                                                    <div class="d-flex justify-content-end">
                                                        <div>
                                                            <img src="../../${imagePath}" class="rounded-circle mr-1" alt="" width="40" height="40">
                                                            <div class="text-muted small text-nowrap mt-2">
                                                                ${objDateCreated.toLocaleString('en-US', options).replace('AM','am').replace('PM','pm')}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="flex-shrink-1 bg-primary text-light rounded py-2 px-3 ml-3 message-parent">
                                                            <div class="message-parent">
                                                          <div class="message-parent">
                                                                <div class="message-preview">
                                                                    <div>${limitMessage(messageContent)}</div>
                                                                </div>
                                                                <a href="javascript:void(0)" class="messageId" value="${messageId}"><i class="far fa-trash-alt text-danger"></i></a>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>
                                        `);
                                    }
                        });
                        offset += limit; // Update offset for the next request
                }else{
                    $('.chat-messages').html('<p class="text-muted text-small">No message search result.</p>')
                }
            }
        });
    }

    //reply
    $(document).on('submit','#send-message-form', function(e){
        e.preventDefault();
             // Get the current URL
             var url = window.location.href;
            // Use a regular expression to extract the ID from the URL
            var id = url.match(/messageDetail\/(\d+)/);
        const message = $('#content').val().trim();
        const chatId = id[1];
            if(message === ''){
                return;
            }
            $.ajax({
                type:'POST',
                url:'/cakephp/messages/replyMessage',
                data:{
                    content: message,
                    chatId: chatId
                },
                success:function(response){
                    $('.chat-messages').empty();
                    offset = 0; //reset offset to zero
                    messageDetail(); // Load new data
                    $('#content').val('');
                }
            })
    });


    let timeout = null;
    const delay = 500; // Delay in milliseconds
    $('#search').on('input', function() {
        clearTimeout(timeout); // Clear the previous timeout
        const search = $(this).val();

        timeout = setTimeout(() => {
            if (search) {
                offset = 0; // Reset offset for new search
                messageDetail(search);
            } else {
                location.reload();
            }
        }, delay);
    });

    function limitMessage(messageText) {
    const limit = 200; // Limit of text
    let truncatedTextContentMessage = messageText.substring(0, limit);
    
    if (messageText.length > limit) {
        truncatedTextContentMessage += `
            <span class="full-message" style="display: none;">${messageText}</span>
            <a href="#" class="text-warning showMorelink">show more</a>
        `;
        return truncatedTextContentMessage;
    } else {
        return messageText;
    }
}

// Toggle Message
$(document).on('click', '.showMorelink', function(e) {
    e.preventDefault(); // Prevent the default link behavior
    const $parent = $(this).closest('.message-parent'); // Get the closest parent
    const $fullMessage = $parent.find('.full-message'); // Find the full message

    // Toggle the message visibility
    if ($fullMessage.is(':visible')) {
        $fullMessage.hide();
        $(this).text('show more'); // Change the link text to "show more"
    } else {
        $fullMessage.show();
        $(this).text('show less'); // Change the link text to "show less"
    }
});

//remove message
$(document).on('click','.messageId',function(){
    const id = $(this).attr('value');
    const $parent = $(this).closest('.chat-parent');
        Swal.fire({
            title: "Are you sure?",
            text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!"
            }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type:'POST',
                    url:'/cakephp/messages/deleteMessage',
                    data:{messageId: id},
                    success:function(response){
                        if(response.success){
                            $parent.remove();
                            Swal.fire({
                                title: "Deleted!",
                                text: "Message has been deleted.",
                                icon: "success"
                                });
                        }else{
                            Swal.fire({
                                title: "Error!",
                                text: "error",
                                icon: "error"
                                });
                        }
                    },
                    error:function(err){
                        console.log(err.message)
                    }
                })
            }
        });
});

//link profile
$(document).on('click', '.linkProfile', function() {
    var senderId = $(this).attr('value');
    var url = '<?= Router::url(['controller' => 'users', 'action' => 'profile']); ?>' + '/' + senderId;
    // Redirect the browser to the profile page
    window.location.href = url;
});





});

</script>