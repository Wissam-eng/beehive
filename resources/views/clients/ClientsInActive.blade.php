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
                <span>clients InActive</span>
            </li>
        </ul>

        <div class="pt-5">


            <div class="grid grid-cols-1 gap-6 ">
                <div class="panel h-full w-full">
                    <div class="mb-5 flex items-center justify-between" style="display: block;text-align: center;">
                        <h5 class="text-lg font-semibold dark:text-white-light">العملاء الغير نشطون</h5>
                    </div>
                    <div class="table-responsive">
                        <table id="usersTable">
                            <thead>
                                <tr>
                                    <th class="ltr:rounded-l-md rtl:rounded-r-md">الاسم</th>
                                    <th>الايميل</th>
                                    <th>الموبايل</th>
                                    <th>عدد الايام من تاريخ الانشاء</th>
                                    <th>الحالة</th>
                                    <th>مرتج الاموال</th>
                                    <th>الخدمات</th>
                                    <th>تفاصيل</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($clients as $user)
                                    <tr class="group text-white-dark hover:text-black dark:hover:text-white-light/90">
                                        <td class="min-w-[150px] text-black dark:text-white">
                                            <div class="flex items-center">
                                                <span class="whitespace-nowrap">{{ $user->name }}</span>
                                            </div>
                                        </td>
                                        <td class="text-primary">{{ $user->email }}</td>
                                        <td><a href="#">{{ $user->mobile }}</a></td>



                                        <td>
                                            <span class="badge bg-primary shadow-md dark:group-hover:bg-transparent">
                                                {{ \Carbon\Carbon::parse($user->created_at)->diffInDays(\Carbon\Carbon::now()) }}
                                            </span>
                                        </td>



                                        <td>
                                            <span
                                                class="badge bg-primary shadow-md dark:group-hover:bg-transparent">{{ $user->status }}
                                            </span>
                                        </td>

                                        <td>
                                            <span
                                                class="badge bg-primary shadow-md dark:group-hover:bg-transparent">{{ $user->refund }}
                                            </span>
                                        </td>


                                        <td>
                                            <a href="{{ route('show_details.show', $user->id) }}">

                                                <span
                                                    class="badge bg-success shadow-md dark:group-hover:bg-transparent">عرض</span>
                                            </a>
                                        </td>


                                        <td> <a href="{{ route('clients.show', $user->id) }}">
                                                <span
                                                    class="badge bg-success shadow-md dark:group-hover:bg-transparent">عرض</span>
                                            </a>
                                        </td>

                                        <td>
                                            <form id="active-form-{{ $user->id }}"
                                                action="{{ route('active_client', ['id' => $user->id]) }}" method="POST"
                                                style="display: none;">
                                                @csrf
                                            </form>
                                            <button type="button" class="btn btn-secondary ltr:mr-2 rtl:ml-2"
                                                onclick="confirmAction('{{ $user->id }}', 'active')">
                                                تنشيط
                                            </button>

                                            @if ($user->refund == 'not_paid')
                                                <form id="refund-form-{{ $user->id }}"
                                                    action="{{ route('refund_account', ['id' => $user->id]) }}"
                                                    method="POST" style="display: none;">
                                                    @csrf
                                                </form>
                                                <button type="button" class="btn btn-success ltr:mr-1 rtl:ml-1"
                                                    onclick="confirmAction('{{ $user->id }}', 'refund')">
                                                    تاكيد المرتجع
                                                </button>
                                            @endif
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
