@extends('Installation::installer.layouts.InstallerMaster')

@section('template_title')
    {{ trans('Manage Supervisor') }}
@endsection

@section('container')
<div class="row justify-content-center">
    <div>
       <p>
           <h5 style="color:red;">Make sure you have installed supervisor and access for sudo</h5>
       </p>
    </div>
    <form id="serviceForm" method="POST">
        @csrf
        <div class="row">
            <div class="form-group col-6">
                <label for="password">Enter Password:</label><span class="text-red">*</span>
                <input type="password" id="password" name="password" required>
            </div>
            <button class="button" type="submit">Submit</button>
            <button class="button" type ="button" id="skipSupervisor">Skip</button>
        </div>
    </form>
    <div id="responseMessage" style="margin-top: 10px;"></div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('serviceForm');
        const responseMessage = document.getElementById('responseMessage');
        const skipButton = document.getElementById('skipSupervisor');
        
        skipButton.addEventListener('click',function(){
            fetch('{{route('SprukoAppInstaller::skip.supervisor.process')}}',{
                method:'GET',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',  // Ensure CSRF token is included for safety
                    'Content-Type':'application/json',
                }
            })
            .then(response => response.json())
            .then(data=>{
                if(data.redirect){
                    window.location.href = data.redirect;
                }
            })
            .catch(error =>{
                console.error('Error:', error);
            });
        })

        form.addEventListener('submit', function(event) {
            event.preventDefault();
    
            const password = document.getElementById('password').value;
            const port = document.getElementById('port').value;
    
            const formData = new FormData();
            formData.append('password', password);
            formData.append('port', port);

            fetch("{{ route('SprukoAppInstaller::manage.supervisor.process') }}", {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type':'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.message == 'Supervisor restarted successfully') {
                    responseMessage.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
                } else {
                    responseMessage.innerHTML = `<div class="alert alert-danger">An error occurred: ${data.error || 'Unknown error'}</div>`;
                }
            })
            .catch(error => {
                responseMessage.innerHTML = `<div class="alert alert-danger">Error: ${error.message}</div>`;
            });
           
        });

    });
</script>
@endsection