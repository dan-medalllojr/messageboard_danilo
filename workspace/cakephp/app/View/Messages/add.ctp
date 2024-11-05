<main class="content">
    <div class="container p-0 ">
		<div class="d-flex justify-content-between">
        <span class="fs-4">New Message</span>
        </div>
				<div class="col-lg-12 border-right p-2">
                    <select name="" id="recipient" class="form-control my-3 select2">
                        <option value="0" selected disabled>Search for a recipient</option>
                    </select>
                        <textarea name="content" id="content" class="form-control mt-3" rows="10" placeholder="Message"></textarea>
                        <div>
                            <div class="btn btn-primary mt-2" id="send">Send</div>
                        </div>
					<hr class="d-block d-lg-none mt-1 mb-0">
				</div>
	</div>
</main> 

<script>
    $(document).ready(function(){
        $('.select2').select2();

        //getRecipient
        $.ajax({
            type:'POST',
            url:'/cakephp/messages/getRecipient',
            success:function(data){
                const users = data;
                users.forEach(user =>{
                    var id =  user.User.id
                    var name = user.User.name
                    $('#recipient').append(`
                        <option value="${id}">${name}</option>
                    `)
                })
            }
        });




        //send 
        $(document).on('click', '#send', function() {
            const recipientId = $('#recipient option:selected').val();
            const content = $('#content').val().trim();
            if(!content) return;

            $.ajax({
                type:'POST',
                url:'/cakephp/messages/add',
                data:{
                    recipientId:recipientId,
                    content:content
                },
                success:function(res){
                    if(res.success){
                        toastr.success("Message sent.")
                        $('#content').val('')
                        setTimeout(() => {
                            window.location.href = '/cakephp/messages'
                        },2000)
                    }
                }
            })

        })
    })
</script>