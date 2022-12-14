@extends('admin_dashboard.layouts.main')
@section('page_content')
    <link rel="stylesheet" href="{{ asset('assets/css/toastr.css') }}">
    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                <i class="mdi mdi-home"></i>
            </span> FAQ
        </h3>
        <nav aria-label="breadcrumb">
        </nav>
    </div>
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <form id="toppartForm">
                    <input type="hidden" name="action" value="toppart">
                    <div class="card-body">
                        <h4 class="card-title">FAQ</h4>
                        <p class="card-description">Details here will show on FAQ</p>
                        <form class="forms-sample">
                            <div class="form-group">
                                <label for="exampleInputUsername1">Heading</label>
                                <input type="text" name="heading" class="form-control" id="heading"
                                    placeholder="Heading" name="heading" value="{{ $heading }}">
                            </div>
                            <div class="form-group">
                                <label for="exampleInputUsername1">Description</label>
                                <textarea class="form-control" rows="5" id="description" placeholder="Description" name="description">{{ $description }}</textarea>
                            </div>

                            <button type="submit" class="btn btn-gradient-primary me-2 btn-rounded">Submit</button>
                        </form>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- services --}}
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body" style="overflow-x:auto">
                <div class="row">
                    <div class="col-md-10">
                        <h4 class="card-title">Services</h4>
                        <p class="card-description">Press Add Button to add FAQs</code></p>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-gradient-primary btn-rounded" data-toggle="modal"
                            data-target="#imageAndContent" onclick="$('#modalForm').trigger('reset');">Add</button>
                    </div>
                </div>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th> sl. </th>
                            <th> Question </th>
                            <th> Answer </th>
                            <th> Action </th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (count($services) > 0)
                            @php
                                $count = 0;
                            @endphp
                            @foreach ($services as $item)
                                @php
                                    $count += 1;
                                @endphp
                                <tr>
                                    <td>{{ $count }}</td>
                                    <td> {{ $item->question }} </td>
                                    <td>
                                        <span
                                            style="white-space: nowrap;overflow: hidden;text-overflow: ellipsis; width:250px; display: block;">{{ $item->answer }}</span>
                                    </td>
                                    <td>
                                        <button class="btn btn-gradient-warning btn-rounded" data-toggle="modal"
                                            data-target="#imageAndContent" data-question='{{ $item->question }}'
                                            data-answer='{{ $item->answer }}' data-serviceid={{ $item->id }}
                                            onclick="onClickEdit(this)">Edit</button>
                                        <button class="btn btn-gradient-danger btn-rounded delete-service"
                                            data-deleteid="{{ $item->id }}">Delete</button>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="modal fade" id="imageAndContent" tabindex="-1" role="dialog" aria-labelledby="imageAndContent"
        aria-hidden="true">
        <div class="modal-dialog .modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add FAQ</h5>
                </div>
                <form id="modalForm" name="modalForm" class="customForm2">
                    <input type="hidden" name="action" value="downpart">
                    <input type="hidden" id="service_id" name="service_id" value="">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="" class="form-label">FAQ Question</label>
                            <input type="text" id="question" name="question" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="" class="form-label">Answer</label>
                            <textarea name="answer" id="answer" rows="5" class="form-control"></textarea>
                        </div>
                    </div>
                </form>
                <div class="modal-footer">
                    <button type="button" class="btn btn-gradient-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" form="modalForm" class="btn btn-gradient-primary">Save Changes</button>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('assets/js/toastr.js') }}"></script>
    <script>
        $('#toppartForm').submit(function(e) {
            e.preventDefault();
            holdOn();
            let formdata = new FormData($('#toppartForm')[0]);
            formdata.append('keyword', 'faq')
            $.ajaxSetup({
                headers: {
                    'accept': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            })
            $.ajax({
                type: "POST",
                url: "/faq-save",
                data: formdata,
                contentType: false,
                processData: false,
                success: function(response) {
                    if (response.status) {
                        toastr.success('Data Changed Successfully!')
                        setTimeout(() => {
                            closeHoldOn();
                            location.reload()
                        }, 2000);
                    } else {
                        toastr.error('Something Went Wrong. Please Contact With Developer!')
                    }
                }
            });
        });

        $('#modalForm').submit(function(e) {
            e.preventDefault();
            holdOn();
            let formdata = new FormData($('#modalForm')[0]);
            $.ajaxSetup({
                headers: {
                    'accept': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            })
            $.ajax({
                type: "POST",
                url: "/faq-save",
                data: formdata,
                contentType: false,
                processData: false,
                success: function(response) {
                    if (response.status) {
                        toastr.success('Data Changed Successfully!')
                        setTimeout(() => {
                            closeHoldOn();
                            location.reload()
                        }, 2000);
                    } else {
                        closeHoldOn();
                        toastr.error('Something Went Wrong. Please Contact With Developer!')
                    }
                }
            });
        });

        $('.delete-service').click(function(e) {
            e.preventDefault();
            holdOn();
            const service_id = $(this).data('deleteid');
            $.ajaxSetup({
                headers: {
                    'accept': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            })
            $.ajax({
                type: "POST",
                url: "/faq-delete",
                data: {
                    'id': service_id
                },
                success: function(response) {
                    if (response.status) {
                        toastr.success('Data Changed Successfully!')
                        setTimeout(() => {
                            closeHoldOn();
                            location.reload()
                        }, 2000);
                    } else {
                        closeHoldOn();
                        toastr.error('Something Went Wrong. Please Contact With Developer!')
                    }
                }
            });
        });

        function onClickEdit(element) {
            $('#modalForm').trigger('reset');
            $('#question').val($(element).data('question'));
            $('#answer').val($(element).data('answer'));
            $('#service_id').val($(element).data('serviceid'));
        }
    </script>
@endsection
