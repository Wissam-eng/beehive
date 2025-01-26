@extends('layouts.app')

@section('content')
    @if (session('success'))
        <div class="flex items-center p-3.5 rounded text-success bg-success-light dark:bg-success-dark-light text-align-center">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="flex items-center p-3.5 rounded text-danger bg-danger-light dark:bg-danger-dark-light text-align-center">
            {{ session('error') }}
        </div>
    @endif

    <div class="container" style="width: 50%;">

        <!-- form controls -->
        <form class="space-y-5" method="POST" action="{{ route('store_user') }}" enctype="multipart/form-data">
            @csrf
            @method('POST')

            <div style="display: flex; justify-content: center;" class="-mt-7 mb-7 -mx-6 rounded-tl rounded-tr h-[215px] overflow-hidden">
                <!-- Default image -->
                <img id="preview-image" src="{{ url('resources/views/assets/images/user-profile.jpeg') }}" alt="image" class="object-cover" />
            </div>

            <div>
                <label for="ctnEmail">Name</label>
                <input type="text" name="name" placeholder="Some Text..." class="form-input" />
            </div>

            <div>
                <label for="ctnTextarea">Email</label>
                <input type="email" name="email" placeholder="Some Text..." class="form-input" />
            </div>

            <div>
                <label for="ctnTextarea">Password</label>
                <input type="text" name="password"  class="form-input" />
            </div>
            <div>
                <label for="ctnTextarea">confirm Password</label>
                <input type="text" name="password_confirmation"  class="form-input" />
            </div>

            <div>
                <label for="ctnFile">Upload Image</label>
                <input id="ctnFile" type="file" name="img" class="form-input file:py-2 file:px-4 file:border-0 file:font-semibold p-0 file:bg-primary/90 ltr:file:mr-5 rtl:file:ml-5 file:text-white file:hover:bg-primary" onchange="previewImage(event)" />
            </div>

            <button type="submit" class="btn btn-primary !mt-6">Submit</button>
        </form>
    </div>

    <script>
        // Function to preview the selected image
        function previewImage(event) {
            var reader = new FileReader();
            reader.onload = function() {
                var output = document.getElementById('preview-image');
                output.src = reader.result;
            };
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
@endsection
