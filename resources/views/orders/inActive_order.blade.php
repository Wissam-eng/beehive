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
                        <table id="servicesTable">
                            <thead>
                                <tr>
                                    <th class="ltr:rounded-l-md rtl:rounded-r-md">الخدمة</th>
                                    <th>التكلفة </th>
                                    <th>حالة الدفع</th>
                                    <th>الحالة</th>
                                    <th>عدد الايام من تاريخ الانشاء</th>
                                    <th>حالة المرتجع</th>
                                    <th>Acton</th>

                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($services as $service)
                                    <tr class="group text-white-dark hover:text-black dark:hover:text-white-light/90">
                                        <td class="min-w-[150px] text-black dark:text-white">
                                            <div class="flex items-center">
                                                <span class="whitespace-nowrap">{{ $service->service_name }}</span>
                                            </div>
                                        </td>
                                        <td class="text-primary">{{ $service->service_cost }}</td>
                                        <td><a href="apps-invoice-preview.html">{{ $service->payment_status }}</a></td>
                                        <td><a href="apps-invoice-preview.html">{{ $service->status }}</a></td>
                                        <td>
                                            <span class="badge bg-primary shadow-md dark:group-hover:bg-transparent">
                                                {{ \Carbon\Carbon::parse($service->created_at)->diffInDays(\Carbon\Carbon::now()) }}
                                            </span>
                                        </td>

                                        <td>
                                            <span
                                                class="badge bg-primary shadow-md dark:group-hover:bg-transparent">{{ $service->refund }}
                                            </span>
                                        </td>

                                        <td>
                                            @if ($service->refund == 'not')
                                                <form id="refund-form-{{ $service->id }}"
                                                    action="{{ route('refund_service', ['id' => $service->id]) }}"
                                                    method="POST" style="display: none;">
                                                    @csrf
                                                </form>
                                                <button type="button" class="btn btn-success ltr:mr-1 rtl:ml-1"
                                                    onclick="confirmAction('{{ $service->id }}', 'refund')">
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
