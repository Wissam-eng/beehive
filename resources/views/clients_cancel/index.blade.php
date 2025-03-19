@extends('layouts.app')

@section('content')
    {{-- @dd($clients) --}}



    <!-- start main content section -->
    <div x-data="sales">
        <ul class="flex space-x-2 rtl:space-x-reverse">
            <li>
                <a href="javascript:;" class="text-primary hover:underline">Dashboard</a>
            </li>
            <li class="before:content-['/'] ltr:before:mr-1 rtl:before:ml-1">
                <span>clients inactive order</span>
            </li>
        </ul>

        <div class="pt-5">


            @if (session('success'))
                <div
                    class="flex items-center p-3.5 rounded text-success bg-success-light dark:bg-success-dark-light text-align-center">
                    {{ session('success') }}

                </div>
            @else
                <div
                    class="flex items-center p-3.5 rounded text-danger bg-danger-light dark:bg-danger-dark-light text-align-center">
                    {{ session('error') }}

                </div>
            @endif


            <div class="grid grid-cols-1 gap-6 ">
                <div class="panel h-full w-full">
                    <div class="mb-5 flex items-center justify-between" style="display: block;text-align: center;">
                        <h5 class="text-lg font-semibold dark:text-white-light">طلبات الغاء العملاء </h5>
                    </div>
                    <div class="table-responsive">
                        <table id="usersTable" class="usersTable">
                            <thead>
                                <tr>
                                    <th class="ltr:rounded-l-md rtl:rounded-r-md">الاسم</th>
                                    <th>الايميل</th>
                                    <th>موبايل_محفظة</th>
                                    <th>الحساب البنكي</th>
                                    <th>الخدمات التي سيتم الغائها</th>
                                    <th>عدد الايام من تاريخ الانشاء</th>
                                    <th>تفاصيل</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($clients_cancel as $user)
                                    <tr class="group text-white-dark hover:text-black dark:hover:text-white-light/90">
                                        <td class="min-w-[150px] text-black dark:text-white">
                                            <div class="flex items-center">
                                                <span class="whitespace-nowrap">{{ $user->client->name }}</span>
                                            </div>
                                        </td>
                                        <td class="text-primary">{{ $user->client->email }}</td>
                                        <td><a href="#">{{ $user->mobile_wallet }}</a></td>

                                        <td>
                                            <span
                                                class="badge bg-primary shadow-md dark:group-hover:bg-transparent">{{ $user->bank_number }}</span>
                                        </td>

                                        <td>
                                            <a href="{{ route('show_details.show', $user->client->id) }}">

                                                <span
                                                    class="badge bg-success shadow-md dark:group-hover:bg-transparent">عرض</span>
                                            </a>
                                        </td>




                                        <td>
                                            <span class="badge bg-primary shadow-md dark:group-hover:bg-transparent">
                                                {{ \Carbon\Carbon::parse($user->client->created_at)->diffInDays(\Carbon\Carbon::now()) }}
                                            </span>
                                        </td>


                                        <td> <a href="{{ route('clients.show', $user->client->id) }}">
                                                <span
                                                    class="badge bg-success shadow-md dark:group-hover:bg-transparent">عرض</span>
                                            </a>
                                        </td>


                                        <td>
                                            <form id="inactive-form-{{ $user->client->id }}"
                                                action="{{ route('inactive_client', ['id' => $user->client->id]) }}"
                                                method="POST" style="display: none;">
                                                @csrf
                                                @method('POST')
                                            </form>
                                            <button type="button" class="btn btn-danger ltr:mr-2 rtl:ml-2"
                                                onclick="confirmAction({{ $user->client->id }}, 'inactive')">
                                                الغاء التنشيط
                                            </button>
                                        </td>


                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                    </div>
                </div>

            </div>
        </div>
    </div>



    <!-- end main content section -->
@endsection
