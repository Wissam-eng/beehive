@extends('layouts.app')

@section('content')
    {{-- @dd($clients) --}}

    <style>
        .frm {
            display: flex;
            flex-wrap: wrap;
            width: 55%;
        }

        .contain {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
    </style>

    <!-- start main content section -->
    <div x-data="sales">
        <ul class="flex space-x-2 rtl:space-x-reverse">
            <li>
                <a href="javascript:;" class="text-primary hover:underline">Dashboard</a>
            </li>
            <li class="before:content-['/'] ltr:before:mr-1 rtl:before:ml-1">
                <span>سجل الخدمات</span>
            </li>
        </ul>

        <div class="pt-5">


            <div class="grid grid-cols-1 gap-6 contain">


                <div class="panel h-full w-full">
                    <div class="mb-5 flex items-center justify-between " style="display: block;text-align: center;">
                        <h5 class="text-lg font-semibold dark:text-white-light">{{ $client->name }} سجل خدمات</h5>
                    </div>
                    <div class="table-responsive">
                        <table id="clientssTable">
                            <thead>
                                <tr>
                                    <th class="ltr:rounded-l-md rtl:rounded-r-md">الخدمة</th>
                                    <th>التكلفة</th>
                                    <th>حالة الدفع</th>
                                </tr>
                            </thead>
                            <tbody>

                                @foreach ($services as $service)
                                    <tr class="group text-white-dark hover:text-black dark:hover:text-white-light/90">

                                        <td class="text-primary">{{ $service->service_name }}</td>
                                        <td class="text-primary">{{ $service->service_cost }}</td>
                                        <td class="text-primary">{{ $service->payment_status }}</td>

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
