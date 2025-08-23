@extends('layouts.main')
@section('title') Modules @endsection
@section('sub-title') List @endsection
@section('page')
    <section class="section">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Create New Module</h5>

                <form class="row g-3" method="post" action="{{route('core.module-save')}}">
                    @csrf
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="name" name="name" placeholder="Name">
                            <label for="name">Name</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="icon" name="icon" placeholder="Icon">
                            <label for="icon">Icon</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="route" name="route" placeholder="Route">
                            <label for="route">Route</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="basic-url" class="form-label">Status</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="status" id="status" checked="">
                            <label class="form-check-label" for="status">Active</label>
                        </div>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-check me-1"></i>Submit</button>
                        <button type="reset" class="btn btn-secondary"><i class="bx bx-undo me-1"></i>Reset</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection

@section('page_script')
@endsection