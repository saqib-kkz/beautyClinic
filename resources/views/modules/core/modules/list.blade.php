@section('page_style')
    <link href="{{getadminasset('vendor/simple-datatables/style.css')}}" rel="stylesheet">
@endsection

@extends('layouts.main')
@section('title') Modules @endsection
@section('sub-title') List @endsection
@section('page')
    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                {!! displayAlert() !!}
                <div class="card">
                    <div class="card-header align-items-center justify-content-between d-flex py-3">
                        <h5 class="card-title">All Modules</h5>
                        {{-- @if(havepermission($moduleroute , "add")) --}}
                            <a type="button" class="btn btn-success shadow" href="{{route('core.module-create')}}">
                                <i class="bi bi-plus me-1"></i>Add New
                            </a>
                        {{-- @endif --}}
                    </div>
                    <div class="card-body">
                        <table class="table datatable">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Icon</th>
                                    <th scope="col">Name</th>
                                    <th scope="col">Route</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Created At</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(sizeof($list) > 0)
                                    @foreach($list as $data)
                                        <tr>
                                            <th scope="row">{{$data->id}}</th>
                                            <td><i class="menu-icon {{$data->icon}}"></i></td>
                                            <td><a href="#">{{$data->name}}</a></td>
                                            <td>{{$data->route}}</td>
                                            <td>
                                                <span class="badge {{($data->status == 'Active') ? 'bg-success' : 'bg-danger'}}">Success</span>
                                            </td>
                                            <td>{{$data->updated_at->diffForHumans()}}</td>
                                            <th>
                                                {{-- @if(havepermission($moduleroute, "update")) --}}
                                                    <a href="{{route('core.module-permissions' , $data->uid)}}"><i class="bx bxs-check-shield me-1"></i></a>
                                                    <a href="{{route('core.module-edit' , $data->uid)}}"><i class="bi bi-pencil-square"></i></a>
                                                {{-- @endif --}}
                                            </th>
                                        </tr>
                                    @endforeach
                                @else
                                    @if($errors->any())
                                        <p class="alert alert-danger">
                                            {{$errors->first()}}
                                        </p>
                                    @else
                                        @if (!empty($msg))
                                            <p class="alert alert-danger">
                                                {{$msg}}
                                            </p>
                                        @endif
                                    @endif
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </section>
@endsection

@section('page_script')

@endsection