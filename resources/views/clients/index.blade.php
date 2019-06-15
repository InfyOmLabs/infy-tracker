@extends('layouts.app')
@section('title')
    Clients
@endsection

@section('content')
    <div class="container-fluid">
        <div class="animated fadeIn">
            @include('flash::message')
            <div class="page-header">
                <h3>Clients</h3>
                <div style="display: flex;align-items: center">
                    <a href="#" class="btn btn-primary" data-toggle="modal" data-target="#AddModal"></i>New
                        Client</a>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            @include('clients.table')
                            <div class="pull-right mr-3">

                            </div>
                        </div>
                        @include('clients.modal')
                        @include('clients.edit_modal')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        let clientCreateUrl = "{{ route('clients.store') }}";
        let clientUrl = "{{url('clients')}}/";
    </script>
    <script src="{{ mix('assets/js/clients/client.js') }}"></script>
@endsection

