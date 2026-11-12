@extends('layouts.sidebar')

@section('title', 'Register Student')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/students/patron-form.css') }}?v=1">
@endsection

@section('content')
<div class="patron-form">
    <div class="patron-form__hero">
        <div>
            <p class="patron-form__kicker">Library patrons</p>
            <h1>Register student</h1>
            <p class="patron-form__lead">Add a library patron. The QR code is assigned when the record is saved.</p>
        </div>
        <a href="{{ route('students.index') }}" class="btn btn-quiet">Back to students</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form id="studentForm" action="{{ route('students.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="qrcode" value="{{ old('qrcode') }}">

        <section class="patron-section">
            <h2>Student information</h2>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label" for="firstname">First name</label>
                    <input id="firstname" type="text" name="firstname" class="form-control" value="{{ old('firstname') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="lastname">Last name</label>
                    <input id="lastname" type="text" name="lastname" class="form-control" value="{{ old('lastname') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="middle_initial">Middle initial</label>
                    <input id="middle_initial" type="text" name="middle_initial" class="form-control" value="{{ old('middle_initial') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="id_number">ID number</label>
                    <input id="id_number" type="text" name="id_number" class="form-control" value="{{ old('id_number') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="course">Course</label>
                    <select id="course" name="course" class="form-select">
                        <option value="">Select course</option>
                        @foreach($programs as $program)
                            <option value="{{ $program->program_code }}" @selected(old('course') == $program->program_code)>
                                {{ $program->program_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="year">Year level</label>
                    <select id="year" name="year" class="form-select">
                        <option value="">Select year</option>
                        @foreach(['1st Year','2nd Year','3rd Year','4th Year','5th Year','6th Year'] as $yr)
                            <option value="{{ $yr }}" @selected(old('year') == $yr)>{{ $yr }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="birthday">Birthday</label>
                    <input id="birthday" type="date" name="birthday" class="form-control" value="{{ old('birthday') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="mobile_number">Mobile number</label>
                    <input id="mobile_number" type="text" name="mobile_number" class="form-control" placeholder="09XXXXXXXXX" value="{{ old('mobile_number') }}">
                </div>
                <div class="col-12">
                    <label class="form-label" for="address">Address</label>
                    <textarea id="address" name="address" class="form-control" rows="3">{{ old('address') }}</textarea>
                </div>
            </div>
        </section>

        <section class="patron-section">
            <h2>Emergency contact</h2>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label" for="emergency_person">Contact person</label>
                    <input id="emergency_person" type="text" name="emergency_person" class="form-control" value="{{ old('emergency_person') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="emergency_relationship">Relationship</label>
                    <input id="emergency_relationship" type="text" name="emergency_relationship" class="form-control" value="{{ old('emergency_relationship') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="emergency_number">Contact number</label>
                    <input id="emergency_number" type="text" name="emergency_number" class="form-control" placeholder="09XXXXXXXXX" value="{{ old('emergency_number') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="emergency_address">Address</label>
                    <textarea id="emergency_address" name="emergency_address" class="form-control" rows="2">{{ old('emergency_address') }}</textarea>
                </div>
            </div>
        </section>

        <section class="patron-section">
            <h2>Photo and signature</h2>
            <div class="patron-media">
                <div>
                    <label class="form-label" for="profile_picture">Profile picture</label>
                    <div class="patron-photo patron-photo--empty mb-2">No photo yet</div>
                    <input id="profile_picture" type="file" name="profile_picture" class="form-control" accept=".jpg,.jpeg,.png">
                </div>
                <div>
                    <label class="form-label" for="studentSignaturePad">Signature</label>
                    <canvas id="studentSignaturePad" class="patron-signature" width="500" height="150"></canvas>
                    <input type="hidden" name="student_signature" id="studentSignatureInput" value="{{ old('student_signature') }}">
                    <button type="button" id="clearStudentSignature" class="btn btn-outline-danger btn-sm mt-2">Clear</button>
                </div>
            </div>
        </section>

        <div class="patron-actions">
            <a href="{{ route('students.index') }}" class="btn btn-quiet">Cancel</a>
            <button type="submit" class="btn btn-save">Register student</button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script src="{{ asset('vendor/signature_pad/signature_pad.umd.min.js') }}"></script>
<script>
    const canvas = document.getElementById('studentSignaturePad');
    const signaturePad = new SignaturePad(canvas);
    const input = document.getElementById('studentSignatureInput');

    document.getElementById('clearStudentSignature').addEventListener('click', () => {
        signaturePad.clear();
        input.value = '';
    });

    document.getElementById('studentForm').addEventListener('submit', () => {
        if (!signaturePad.isEmpty()) {
            input.value = signaturePad.toDataURL();
        }
    });
</script>
@endsection
