@extends('layouts.app')

@section('content')
    {{-- @dd($services) --}}


    <!-- start main content section -->
    <div x-data="sales">
        <ul class="flex space-x-2 rtl:space-x-reverse">
            <li>
                <a href="javascript:;" class="text-primary hover:underline">لوحة التحكم</a>
            </li>
            <li class="before:content-['/'] ltr:before:mr-1 rtl:before:ml-1">
                <span>الطلبات المعلقة</span>
            </li>
        </ul>

        <div class="pt-5">


            <div class="grid grid-cols-1 gap-6 ">
                <div class="panel h-full w-full">
                    <div class="mb-5 flex items-center justify-between"style="display: block;text-align: center;">
                        <h5 class="text-lg font-semibold dark:text-white-light">الطلبات المعلقة</h5>
                    </div>
                    <div class="table-responsive">
                        <table id="servicesTable" class="usersTable">
                            <thead>
                                <tr>
                                    <th class="ltr:rounded-l-md rtl:rounded-r-md">العميل</th>
                                    <th class="ltr:rounded-l-md rtl:rounded-r-md">الخدمة</th>
                                    <th>التكلفة </th>
                                    <th>حالة الدفع</th>
                                    <th>الحالة</th>
                                    <th>عدد الايام من تاريخ الانشاء</th>
                                    <th>Acton</th>

                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($orders_cancel as $service)
                                    <tr class="group text-white-dark hover:text-black dark:hover:text-white-light/90">
                                        <td class="min-w-[150px] text-black dark:text-white">
                                            <div class="flex items-center">
                                                <span class="whitespace-nowrap">{{ $service->client->name }}</span>
                                            </div>
                                        </td>
                                        <td class="min-w-[150px] text-black dark:text-white">
                                            <div class="flex items-center">
                                                <span class="whitespace-nowrap">{{ $service->order->service_name }}</span>
                                            </div>
                                        </td>
                                        <td class="text-primary">{{ $service->order->service_cost }}</td>
                                        <td>{{ $service->order->payment_status }}
                                        </td>

                                        <td>{{ $service->order->status }}</td>
                                        <td>
                                            <span class="badge bg-primary shadow-md dark:group-hover:bg-transparent">
                                                {{ \Carbon\Carbon::parse($service->order->created_at)->diffInDays(\Carbon\Carbon::now()) }}
                                            </span>
                                        </td>

                                        <td>
                                            <form id="inactive-form-{{ $service->order_id }}"
                                                action="{{ route('inactive_order', ['id' => $service->order_id]) }}"
                                                method="POST" style="display: none;">
                                                @csrf
                                                @method('POST')
                                            </form>
                                            <button type="button" class="btn btn-danger ltr:mr-2 rtl:ml-2"
                                                onclick="confirmAction({{ $service->order_id }}, 'inactive')">
                                                الغاء
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



@endsection
