@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Register') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <!-- Name -->
                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Name') }}</label>
                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <!-- Username -->
                        <div class="form-group row">
                            <label for="username" class="col-md-4 col-form-label text-md-right">{{ __('Username') }}</label>
                            <div class="col-md-6">
                                <input id="username" type="text" class="form-control @error('username') is-invalid @enderror" name="username" value="{{ old('username') }}" required autocomplete="username">
                                @error('username')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <!-- Password -->
                        <div class="form-group row">
                            <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>
                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <!-- Confirm Password -->
                        <div class="form-group row">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-right">{{ __('Confirm Password') }}</label>
                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                            </div>
                        </div>

                        <!-- Office -->
                        <div class="form-group row">
                            <label for="office" class="col-md-4 col-form-label text-md-right">{{ __('Office') }}</label>
                            <div class="col-md-6">
                                <select id="office" class="form-control @error('office') is-invalid @enderror" name="office" required>
                                    <option value="">Select Office</option>
                                    <option value="Regional Office">Regional Office</option>
                                    <option value="PENRO Camiguin">PENRO Camiguin</option>
                                    <option value="PENRO Bukidnon">PENRO Bukidnon</option>
                                    <option value="CENRO Don Carlos">CENRO Don Carlos</option>
                                    <option value="CENRO Manolo Fortich">CENRO Manolo Fortich</option>
                                    <option value="CENRO Talakag">CENRO Talakag</option>
                                    <option value="CENRO Valencia">CENRO Valencia</option>
                                    <option value="PENRO Lanao del Norte">PENRO Lanao del Norte</option>
                                    <option value="CENRO Iligan">CENRO Iligan</option>
                                    <option value="CENRO Kolambugan">CENRO Kolambugan</option>
                                    <option value="PENRO Misamis Occidental">PENRO Misamis Occidental</option>
                                    <option value="CENRO Oroquieta">CENRO Oroquieta</option>
                                    <option value="CENRO Ozamis">CENRO Ozamis</option>
                                    <option value="PENRO Misamis Oriental">PENRO Misamis Oriental</option>
                                    <option value="CENRO Gingoog">CENRO Gingoog</option>
                                    <option value="CENRO Initao">CENRO Initao</option>
                                </select>
                                @error('office')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                         <!-- Division -->
                         <div class="form-group row">
                            <label for="division" class="col-md-4 col-form-label text-md-right">{{ __('Division') }}</label>
                            <div class="col-md-6">
                                <select id="division" class="form-control @error('division') is-invalid @enderror" name="division" required>
                                    <option value="">Select Division</option>
                                    @foreach($divisions as $division)
                                        <option value="{{ $division->div_id }}">{{ $division->div_name }}</option>
                                    @endforeach
                                </select>
                                @error('division')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <!-- Section -->
                        <div class="form-group row">
                            <label for="section" class="col-md-4 col-form-label text-md-right">{{ __('Section') }}</label>
                            <div class="col-md-6">
                                <select id="section" class="form-control @error('section') is-invalid @enderror" name="section" required>
                                    <!-- Sections will be dynamically loaded based on the selected division -->
                                </select>
                                @error('section')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <!-- Role -->
                        <div class="form-group row">
                            <label for="role" class="col-md-4 col-form-label text-md-right">{{ __('Role') }}</label>
                            <div class="col-md-6">
                                <select id="role" class="form-control @error('role') is-invalid @enderror" name="role" required>
                                    <option value="">Select Role</option>
                                    <option value="admin">Admin</option>
                                    <option value="user">User</option>
                                </select>
                                @error('role')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">{{ __('Register') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add JQuery and JavaScript for cascading dropdowns -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
<script>
$(document).ready(function() {
    $('#division').change(function() {
        var div_id = $(this).val();
        if (div_id) {
            $.ajax({
                url: '{{ url("get-sections") }}/' + div_id,
                type: "GET",
                dataType: "json",
                success: function(data) {
                    $('#section').empty();
                    $('#section').append('<option value="">Select Section</option>');
                    $.each(data, function(key, value) {
                        $('#section').append('<option value="'+ key +'">'+ value +'</option>');
                    });
                }
            });
        } else {
            $('#section').empty();
            $('#section').append('<option value="">Select Section</option>');
        }
    });
});
</script>
@endsection
