@extends('layouts.app')

@section('content')
    {{-- @dd($users) --}}


    <!-- start main content section -->
    <div x-data="sales">
        <ul class="flex space-x-2 rtl:space-x-reverse">
            <li>
                <a href="javascript:;" class="text-primary hover:underline">Dashboard</a>
            </li>
            <li class="before:content-['/'] ltr:before:mr-1 rtl:before:ml-1">
                <span>Users</span>
            </li>
        </ul>

        <div class="pt-5">


            <div class="grid grid-cols-1 gap-6 ">
                <div class="panel h-full w-full">
                    <div class="mb-5 flex items-center justify-between">
                        <h5 class="text-lg font-semibold dark:text-white-light">Users</h5>
                    </div>
                    <div class="table-responsive">
                        <table id="usersTable" class="usersTable">
                            <thead>
                                <tr>
                                    <th class="ltr:rounded-l-md rtl:rounded-r-md">Name</th>
                                    <th>Email</th>
                                    <th>Created At</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $user)
                                <tr class="group text-white-dark hover:text-black dark:hover:text-white-light/90">
                                    <td class="min-w-[150px] text-black dark:text-white">
                                        <div class="flex items-center">
                                            <span class="whitespace-nowrap">{{ $user->name }}</span>
                                        </div>
                                    </td>
                                    <td class="text-primary">{{ $user->email }}</td>
                                    <td><a href="apps-invoice-preview.html">{{ $user->created_at }}</a></td>
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
