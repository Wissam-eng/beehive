@extends('layouts.app')

@section('content')
    {{-- @dd($clients) --}}

    <style>
        .frm {
            display: flex;
            flex-wrap: wrap;
            width: 55%;
            gap: 2%;
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
                <span>تفاصيل الحساب</span>
            </li>
        </ul>

        <div class="pt-5">







            <div class="grid grid-cols-1 gap-6 contain">


                <div class="mb-5 flex items-center justify-between" style="display: block;text-align: center;">
                    <h5 class="text-lg font-semibold dark:text-white-light"> {{ $clients->name }} تفاصيل</h5>
                </div>


                <!-- input text -->
                <form class="frm">







                    <div>

                        <label for="url">الايميل</label>
                        <input type="text" placeholder="Some Text..." value="{{ $clients->email }}" class="form-input"
                            disabled />
                    </div>

                    <div
                    <label for="url">تاريخ الميلاد</label>
                    <input type="text" placeholder="Some Text..." value="{{ $clients->birth_date }}" class="form-input"
                        disabled />

                    </div>

                    <div>
                    <label for="url">الموبايل</label>
                    <input type="text" placeholder="Some Text..." value="{{ $clients->mobile }}" class="form-input"
                        disabled />

                    </div>

                    <div>
                    <label for="url">موبايل اخر</label>
                    <input type="text" placeholder="Some Text..." value="{{ $clients->another_mobile }}"
                        class="form-input" disabled />
                    </div>

                    <div>

                    <label for="url">الرقم الوطني</label>
                    <input type="text" placeholder="Some Text..." value="{{ $clients->na_number }}" class="form-input"
                        disabled />

                    </div>

                    <div>
                    <label for="url">العمر</label>
                    <input type="text" placeholder="Some Text..." value="{{ $clients->age }}" class="form-input"
                        disabled />

                    </div>

                    <div>
                    <label for="url">المدينة</label>
                    <input type="text" placeholder="Some Text..." value="{{ $clients->city }}" class="form-input"
                        disabled />

                    </div>

                    <div>
                    <label for="url">الوظيفة</label>
                    <input type="text" placeholder="Some Text..." value="{{ $clients->work }}" class="form-input"
                        disabled />

                    </div>

                    <div>
                    <label for="url">المركز</label>
                    <input type="text" placeholder="Some Text..." value="{{ $clients->center }}" class="form-input"
                        disabled />

                    </div>

                    <div>
                    <label for="url">رقم الهاتف</label>
                    <input type="text" placeholder="Some Text..." value="{{ $clients->landline }}" class="form-input"
                        disabled />
                    </div>

                    <div>

                    <label for="url">المحافظة</label>
                    <input type="text" placeholder="Some Text..." value="{{ $clients->governorate }}" class="form-input"
                        disabled />

                    </div>

                    <div>
                    <label for="url">الشارع</label>
                    <input type="text" placeholder="Some Text..." value="{{ $clients->Village_Street }}"
                        class="form-input" disabled />
                    </div>

                    <div>

                    <label for="url">عدد الابناء</label>
                    <input type="text" placeholder="Some Text..." value="{{ $clients->num_of_children }}"
                        class="form-input" disabled />
                    </div>

                    <div>

                    <label for="url">المؤهل العلمي</label>
                    <input type="text" placeholder="Some Text..." value="{{ $clients->Academic_qualification }}"
                        class="form-input" disabled />

                    </div>

                    <div>
                    <label for="url">الجنس</label>
                    <input type="text" placeholder="Some Text..." value="{{ $clients->gender }}" class="form-input"
                        disabled />

                    </div>

                    <div>
                    <label for="url">الحالة الاجتماعية</label>
                    <input type="text" placeholder="Some Text..." value="{{ $clients->marital_status }}"
                        class="form-input" disabled />

                    </div>

                    <div>
                    <label for="url">الحالة</label>
                    <input type="text" placeholder="Some Text..." value="{{ $clients->status }}" class="form-input"
                        disabled />
                    </div>




                </form>








                {{-- <div class="panel h-full w-full">
                    <div class="mb-5 flex items-center justify-between">
                        <h5 class="text-lg font-semibold dark:text-white-light">clients active</h5>
                    </div>
                    <div class="table-responsive">
                        <table id="clientssTable">
                            <thead>
                                <tr>
                                    <th class="ltr:rounded-l-md rtl:rounded-r-md">Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>status</th>
                                    <th>services</th>
                                    <th>Details</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>


                                <tr class="group text-white-dark hover:text-black dark:hover:text-white-light/90">
                                    <td class="min-w-[150px] text-black dark:text-white">
                                        <div class="flex items-center">
                                            <span class="whitespace-nowrap">{{ $clients->name }}</span>
                                        </div>
                                    </td>
                                    <td class="text-primary">{{ $clients->email }}</td>
                                    <td><a href="apps-invoice-preview.html">{{ $clients->mobile }}</a></td>
                                    <td>
                                        <span
                                            class="badge bg-primary shadow-md dark:group-hover:bg-transparent">{{ $clients->status }}</span>
                                    </td>


                                    <td>
                                        <a href="apps-invoice-preview.html">{{ $clients->created_at }}
                                            <span
                                                class="badge bg-success shadow-md dark:group-hover:bg-transparent">show</span>
                                        </a>
                                    </td>


                                    <td><a href="apps-invoice-preview.html">{{ $clients->created_at }}
                                            <span
                                                class="badge bg-success shadow-md dark:group-hover:bg-transparent">show</span>
                                        </a></td>
                                    <td>

                                        <button type="button" class="btn btn-danger ltr:mr-2 rtl:ml-2">
                                            Active
                                        </button>

                                    </td>
                                </tr>

                            </tbody>
                        </table>

                    </div>
                </div> --}}

            </div>
        </div>
    </div>
    <!-- end main content section -->
@endsection
