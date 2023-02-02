@extends('layouts.admin')

@extends('header.admin_header1')

@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Permissions</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Permission</a></li>
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
                                <h3 class="card-title">Create New Permission <small>jQuery Validation</small></h3>
                            </div>
                            <!-- /.card-header -->
                            <!-- form start -->
                            <form id="permissionFrom" method="POST" action="{{ route('permissions.store') }}">
                                @csrf
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="exampleInputName">Title</label>
                                        <input type="text" name="title" class="form-control" id="title"
                                            placeholder="Enter title">
                                    </div>
                                    <div class="row" id="permission_div">
                                        <div class="col-md-6">
                                            <div class="d-flex align-items-center">
                                                <div class="d-flex">
                                                    <div class="form-group">
                                                        <label for="exampleInputName">Name</label>
                                                        <input type="text" name="name[1]" class="form-control"
                                                            id="exampleInputName" placeholder="Enter name">
                                                    </div>
                                                    <div class="form-group mx-4">
                                                        <label for="exampleFormControlTextarea1">Sort Description</label>
                                                        <textarea class="form-control" name="description[1]" id="exampleFormControlTextarea1" rows="2"></textarea>
                                                    </div>
                                                </div>
                                                <div class="btn btn-primary" id="more" data-id="1"><i
                                                        class="fa-solid fa-plus"></i>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <!-- /.card-body -->
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary">Save permission</button>
                                    <a href="{{ route('permissions.index') }}" class="btn btn-default">Back</a>
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
                    $('#permissionFrom').validate({
                        rules: {
                            title: {
                                required: true,
                            },
                        },
                        messages: {
                            title: {
                                required: "Please enter a title",
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

                    $("#permissionFrom").submit(function() {
                        let submit = true;
                        $.each($('#permissionFrom input'), function() {
                            if (!this.value) {
                                if (this.name != 'title') {
                                    if (!$(this).hasClass('is-invalid')) {
                                        $(this).addClass('is-invalid');
                                        const error = document.createElement("span");
                                        error.className = 'invalid-feedback';
                                        error.innerText = 'this field is required';
                                        $(this).parent().closest('.form-group').append(error);
                                        submit = false;
                                    }
                                }
                            }
                        });
                        $.each($('#permissionFrom textarea'), function() {
                            if (!this.value) {
                                if (this.name != 'title') {
                                    if (!$(this).hasClass('is-invalid')) {
                                        $(this).addClass('is-invalid');
                                        const error = document.createElement("span");
                                        error.className = 'invalid-feedback';
                                        error.innerText = 'this field is required';
                                        $(this).parent().closest('.form-group').append(error);
                                        submit = false;
                                    }
                                }
                            }
                        });

                        if(submit == false){

                            return false;
                        }
                    });
                });

                $("body").on("click", "#more", function() {
                    const id = parseInt($(this).attr("data-id")) + 1;
                    const div = `<div class="col-md-6" id="delete${id}">
                                    <div class="d-flex align-items-center">
                                            <div class="d-flex">
                                                <div class="form-group">
                                                <label for="exampleInputName">Name</label>
                                                <input type="text" name="name[${id}]" class="form-control"
                                                    id="exampleInputName" placeholder="Enter name">
                                                </div>
                                                <div class="form-group mx-4">
                                                    <label for="exampleFormControlTextarea1">Sort Description</label>
                                                    <textarea class="form-control" name="description[${id}]" id="exampleFormControlTextarea1" rows="2"></textarea>
                                                </div>
                                            </div>
                                            <div class="btn btn-danger less" data-id="${id}"><i class="fa-solid fa-minus"></i>
                                        </div>
                                    </div>
                                </div>`;
                    $(this).attr('data-id', id);
                    $("#permission_div").append(div);
                });
                $("body").on("click", ".less", function() {
                    const id = $(this).attr("data-id");
                    $("#delete" + id).remove();
                });
            </script>
        @endpush
    </div>
@endsection
