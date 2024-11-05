
<style>
    .chat-parent {
    display: flex;
    align-items: center;
    padding: 5px 0;
    position: relative; /* Ensure we can position the delete button relative to this parent */
}
.chat-parent:hover{
    background-color: #dfe6e9;
}

.deleteChatId {
    margin-right: 10px; /* Add some space between delete button and message */
    display: flex;
    align-items: center;
    justify-content: center;
    border-right: 1px solid #ddd; /* Border between delete button and message */
    padding-right: 10px; /* Add space between border and button */
    opacity: 0; /* Hide the delete button by default */
    pointer-events: none; /* Prevent clicks when hidden */
    transition: opacity 0.3s ease; /* Smooth transition when showing the button */
}

/* Show the delete button when the parent is hovered */
.chat-parent:hover .deleteChatId {
    opacity: 1; /* Make the delete button visible */
    pointer-events: auto; /* Allow clicks on the button */
}

.deleteChatId i {
    font-size: 20px;
    cursor: pointer;
}

.list-group-item {
    display: flex;
    align-items: center;
}

.list-group-item .d-flex {
    display: flex;
    align-items: center;
}

.list-group-item .flex-grow-1 {
    margin-left: 10px;
}

.list-group-item .small {
    font-size: 12px;
}

/* Optional: Add border around the entire message list item */
.list-group-item-action {
    border-left: 1px solid #ddd; /* Border on the left side of the message */
    padding-left: 10px; /* Add space between message content and border */
}
</style>
<main class="content">
    <div class="container p-0 ">
		<div class="d-flex justify-content-between">
        <span class="fs-4">Message List</span>
        <?=
         $this->Html->link(
            'New Message',
            ['controller' => 'Messages', 'action' => '/add'], // Replace with your controller/action
            ['class' => 'btn btn-sm btn-primary', 'escape' => false]
        );
        ?>
        </div>
		<div class="card mt-2">
            <div class="row">
				<div class="col-lg-12 border-right">
					<div class="container messageList">
                    <div class="px-4 d-md-block">
						<div class="d-flex align-items-center">
							<div class="flex-grow-1">
								<input type="text" class="form-control my-3" id="search" placeholder="Search...">
							</div>
						</div>
					</div>
                    <div class="list">
                        <!-- eject result -->
                    </div>
                    <div class="text-center">
                           <a href="javascript:void()" id="showMore">Show more</a>
                           </div>
                    </div>
					<hr class="d-block d-lg-none mt-1 mb-0">
				</div>
                </div>
		</div>
	</div>
</main> 

<script>
 $(document).ready(function() {
    
    var offset = 0;
    var limit = 10;
    $('#showMore').on('click', ()=> messageList())
    messageList(); // Load initial data

    function messageList(search) {
        $.ajax({
            type: 'GET',
            url: '/cakephp/messages/',
            data: {
                offset: offset,
                limit: limit,
                search: search || '' // Send empty string if no search term
            },
            success: function(response) {
                if (response.success) {
                    const data = response.data;
                    if(data.length < 10){
                        $('#showMore').hide();
                    }
                    if (data.length > 0) {
                        let html = '';
                        data.forEach(val => {
                            const chatId = val.chats.id;
                            const name = val.users.user_name;
                            const imagePath = val.users.user_imagepath && val.users.user_imagepath !== '' ? val.users.user_imagepath : 'uploads/no-image.png';
                            const lastMessage = val.last_messages.last_message || ''; // Handle null

                            const limit = 50; // Limit of text
                            let truncatedTextLastMessage = lastMessage.substring(0, limit);
                            if (lastMessage.length > limit) {
                                truncatedTextLastMessage += "...";
                            }
                            html += `
                                    <div class="chat-parent d-flex align-items-center rounded px-2">
                                        <a href="javascript:void(0)" class="deleteChatId" value="${chatId}">
                                            <i class="far fa-trash-alt text-danger"></i>
                                        </a>
                                        
                                        <a href="/cakephp/Messages/messageDetail/${chatId}" class="list-group-item list-group-item-action border-0 flex-grow-1">
                                            <div class="d-flex align-items-start">
                                                <img src="${imagePath}" class="rounded-circle mr-1" alt="" width="40" height="40">
                                                <div class="flex-grow-1 ml-3">
                                                    ${name}
                                                    <div class="small text-muted">${truncatedTextLastMessage}</div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>

                            `;
                        });

                        $('.list').append(html);
                        offset += limit; // Update offset for the next request
                    } else {
                        $('.list').html('<p>No results found.</p>'); // Display message if no results
                    }
                } else {
                    console.error(response.message);
                }
            }
        });
    }

    let timeout = null;
    const delay = 500; // Delay in milliseconds
    $('#search').on('input', function() {
        clearTimeout(timeout); // Clear the previous timeout
        const search = $(this).val();

        timeout = setTimeout(() => {
            if (search) {
                 $('.list').empty(); // Clear previous items
                offset = 0; // Reset offset for new search
                messageList(search);
            } else {
                location.reload();
            }
        }, delay);
    });


    $(document).on('click','.deleteChatId', function(){
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
                    url:'/cakephp/messages/deleteChat',
                    data:{id: id},
                    success:function(response){
                        if(response.success){
                            $parent.remove();
                            Swal.fire({
                                title: "Deleted!",
                                text: "Chat has been deleted.",
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
    })
});


 

</script>