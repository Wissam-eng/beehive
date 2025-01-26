@extends('layouts.app')

@section('content')
    @if (session('success'))
        <div
            class="flex items-center p-3.5 rounded text-success bg-success-light dark:bg-success-dark-light text-align-center">
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
        <form class="space-y-5" method="POST" action="{{ route('update_profile') }}" enctype="multipart/form-data">
            @csrf
            @method('POST')



            {{-- <div class="-mt-7 mb-7 -mx-6 rounded-tl rounded-tr h-[215px] overflow-hidden">
                <img src="{{ asset($profile->img) : url('resources/views/assets/images/user-profile.jpeg') }}" alt="image" class="w-full h-full object-cover" />
            </div> --}}

            <input type="hidden" name="id" value="{{ $profile->id }}">

            <div style="display: flex;justify-content: center;"
                class="-mt-7 mb-7 -mx-6 rounded-tl rounded-tr h-[215px] overflow-hidden">
                <img src="{{ $profile->img ? asset($profile->img) : url('resources/views/assets/images/user-profile.jpeg') }}"
                    alt="image" class=" object-cover" />
            </div>






            <div>
                <label for="ctnEmail">name</label>
                <input type="text" name="name" value="{{ $profile->name }}" placeholder="Some Text..."
                    class="form-input" />
            </div>




            <!-- basic -->
            <div x-data="form">
                <input  value="{{ $profile->birth_date }}" name="birth_date" class="form-input" />
            </div>

            <!-- script -->
            <script>
                document.addEventListener("alpine:init", () => {
                    Alpine.data("form", () => ({
                        date1: '2022-07-05',
                        init() {
                            flatpickr(document.getElementById('basic'), {
                                dateFormat: 'Y-m-d',
                                defaultDate: this.date1,
                            })
                        }
                    }));
                });
            </script>

            <div>
                <label for="ctnEmail">mobile</label>
                <input type="text" name="mobile" value="{{ $profile->mobile }}" placeholder="Some Text..."
                    class="form-input" />
            </div>

            <div>
                <label for="ctnEmail">age</label>
                <input type="text" name="age" value="{{ $profile->age }}" placeholder="Some Text..."
                    class="form-input" />
            </div>

            <div>
                <label for="ctnEmail">gender</label>


                    <select name="gender" id="">
                        <option value="male" {{ $profile->gender == 'male' ? 'selected' : ''}}>male</option>
                        <option value="female" {{ $profile->gender == 'female' ? 'selected' : ''}}>female</option>
                    </select>
            </div>








            <div>
                <label for="ctnTextarea">email</label>
                <input type="email" name="email" value="{{ $profile->email }}" placeholder="Some Text..."
                    class="form-input" />
            </div>


            <div>
                <label for="ctnTextarea">password</label>
                <input type="text" name="password" placeholder="if you don't want to change password leave it empty"
                    class="form-input" />
            </div>


            <div>
                <label for="ctnFile">Upload Imag</label>
                <input id="ctnFile" type="file" name="img"
                    class="form-input file:py-2 file:px-4 file:border-0 file:font-semibold p-0 file:bg-primary/90 ltr:file:mr-5 rtl:file:ml-5 file:text-white file:hover:bg-primary" />
            </div>


            <button type="submit" class="btn btn-primary !mt-6">Submit</button>
        </form>
    </div>
@endsection
