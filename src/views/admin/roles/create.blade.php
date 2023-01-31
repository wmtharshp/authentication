@extends('layouts.admin')

@extends('header.admin_header1')

@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Roles</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Role</a></li>
                            <li class="breadcrumb-item active">Create</li>
                        </ol>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->


        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <!-- left column -->
                    <div class="col-md-12">
                        <!-- jquery validation -->
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Create New Role <small>jQuery Validation</small></h3>
                            </div>
                            <!-- /.card-header -->
                            <!-- form start -->
                            <form id="rolesFrom" method="POST" action="{{ route('roles.store') }}">
                                @csrf
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="exampleInputName">Name</label>
                                        <input type="text" name="name" class="form-control" id="exampleInputName"
                                            placeholder="Enter name">
                                    </div>
                                </div>
                                <div class="row m-2">
                                    <!-- Left col -->
                                    <div class="col-lg-12 my-2">
                                        <div class="icheck-primary d-inline">
                                            <input type="checkbox" class='permission' id="allPermission">
                                            <label for="allPermission">
                                                Assign Permissions
                                            </label>
                                        </div>
                                    </div>

                                    @foreach ($permissions_array as $key => $permission)
                                        <section class="col-lg-4">
                                            <div class="card direct-chat direct-chat-primary border border-primary">
                                                <div class="card-header">
                                                    <div class="icheck-primary d-inline ml-2">
                                                        <input type="checkbox" id="{{ $key }}"
                                                            value="{{ $key }}"
                                                            class="parent permission">
                                                        <label for="{{ $key }}">{{ $key }}</label>
                                                    </div>
                                                    <div class="card-tools">
                                                        <span title="3 New Messages"
                                                            class="badge badge-primary">{{ count($permission) }}</span>
                                                        <button type="button" class="btn btn-tool"
                                                            data-card-widget="collapse">
                                                            <i class="fas fa-minus"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <!-- /.card-header -->
                                                <div class="card-body">
                                                    <!-- Conversations are loaded here -->
                                                    <ul class="todo-list" data-widget="todo-list">


                                                        @foreach ($permission as $k => $value)
                                                            <!-- Message. Default to the left -->

                                                            <li class="pl-4 shadow">
                                                                <!-- checkbox -->
                                                                <div class="icheck-primary d-inline ml-2">
                                                                    <input type="checkbox"
                                                                        class="{{ $key }} permission child"
                                                                        name="permission[{{$value->name}}]" value="{{$value->name}}" data-class="{{ $key }}"
                                                                        name="todo1" id="{{ $value->name . $k }}">
                                                                    <label for="{{ $value->name . $k }}"></label>
                                                                </div>
                                                                <!-- todo text -->
                                                                <span class="text">{{ $value->description }}</span>
                                                                <!-- Emphasis label -->
                                                                <small
                                                                    class="badge badge-secondary">{{ $value->name }}</small>
                                                                <!-- General tools such as edit or delete-->

                                                            </li>
                                                        @endforeach

                                                    </ul>

                                                </div>

                                            </div>
                                        </section>
                                    @endforeach
                                    <!-- DIRECT CHAT -->


                                </div>

                                <!-- /.card-body -->
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary">Save Role</button>
                                    <a href="{{ route('roles.index') }}" class="btn btn-default">Back</a>
                                </div>
                            </form>
                        </div>
                        <!-- /.card -->
                    </div>
                    <!--/.col (left) -->
                    <!-- right column -->
                    <div class="col-md-6">

                    </div>
                    <!--/.col (right) -->
                </div>
                <!-- /.row -->
            </div><!-- /.container-fluid -->
        </section>

        @push('custom-script')
            <script src="{{ asset('assets/plugins/jquery-validation/jquery.validate.min.js') }}"></script>
            <script src="{{ asset('assets/plugins/jquery-validation/additional-methods.min.js') }}"></script>
            <script>
                $(function() {
                    $('#rolesFrom').validate({
                        rules: {
                            name: {
                                required: true,
                            }
                        },
                        messages: {
                            name: {
                                required: "Please enter a name",
                            }
                        },
                        errorElement: 'span',
                        errorPlacement: function(error, element) {
                            error.addClass('invalid-feedback');
                            element.closest('.form-group').append(error);
                        },
                        highlight: function(element, errorClass, validClass) {
                            $(element).addClass('is-invalid');
                        },
                        unhighlight: function(element, errorClass, validClass) {
                            $(element).removeClass('is-invalid');
                        }
                    });
                });

                $('body').on('change', '#allPermission', function() {

                    if ($(this).is(':checked')) {
                        $.each($('.permission'), function() {
                            $(this).prop('checked', true);
                            $(this).parent().closest('li').addClass('done');
                        });
                    } else {
                        $.each($('.permission'), function() {
                            $(this).prop('checked', false);
                            $(this).parent().closest('li').removeClass('done');
                        });
                    }

                });

                $('body').on('change', '.parent', function() {
                    let chield_ele = $(this).val();
                    if ($(this).is(':checked')) {
                        let chek_count = $('input.parent:checked').length;
                        let all_count = $('input.parent').length;
                        if (chek_count == all_count) {
                            $("#allPermission").prop('checked', true);
                        }
                        $.each($('.' + chield_ele), function() {
                            $(this).prop('checked', true);
                            $(this).parent().closest('li').addClass('done');
                        });
                    } else {
                        $.each($('.' + chield_ele), function() {
                            $(this).prop('checked', false);
                            $(this).parent().closest('li').removeClass('done');
                        });
                        $("#allPermission").prop('checked', false);
                    }
                });

                $('body').on('change', '.child', function() {
                    let parent_ele = $(this).attr("data-class");
                    if ($(this).is(':checked')) {
                        let child_chek_count = $('input.' + parent_ele + ':checked').length;
                        let child_all_count = $('input.' + parent_ele).length;
                        if (child_chek_count == child_all_count) {
                            $("#" + parent_ele).prop('checked', true);
                        }

                        let chek_count = $('input.parent:checked').length;
                        let all_count = $('input.parent').length;
                        if (chek_count == all_count) {
                            $("#allPermission").prop('checked', true);
                        }
                    } else {
                        $("#allPermission").prop('checked', false);
                        $("#" + parent_ele).prop('checked', false);

                    }
                });
            </script>
        @endpush
    </div>
@endsection
