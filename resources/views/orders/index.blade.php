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
                <span>الطلبات</span>
            </li>
        </ul>

        <div class="pt-5">


            <div class="grid grid-cols-1 gap-6 ">
                <div class="panel h-full w-full">
                    <div class="mb-5 flex items-center justify-between" style="display: block;text-align: center;">
                        <h5 class="text-lg font-semibold dark:text-white-light">الطلبات</h5>
                    </div>
                    <div class="table-responsive">
                        <table id="servicesTable" class="usersTable">
                            <thead>
                                <tr>
                                    <th class="ltr:rounded-l-md rtl:rounded-r-md">service</th>
                                    <th>cost </th>
                                    <th>status payment</th>
                                    <th>status</th>
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
                                        <td>{{ $service->payment_status }}</td>
                                        <td>{{ $service->status }}</td>
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
