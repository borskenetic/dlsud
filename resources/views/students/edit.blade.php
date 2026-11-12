@extends('layouts.sidebar')

@section('title', 'Edit Student')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/students/patron-form.css') }}?v=1">
@endsection

@section('content')
<div class="patron-form">
    <div class="patron-form__hero">
        <div>
            <p class="patron-form__kicker">Library patrons</p>
            <h1>Edit student</h1>
            <p class="patron-form__lead">{{ trim($student->firstname.' '.$student->lastname) }} · {{ $student->id_number }}</p>
        </div>
        <a href="{{ route('students.index') }}" class="btn btn-quiet">Back to students</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form id="studentForm" method="POST" action="{{ route('students.update', $student->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <section class="patron-section">
            <h2>Student information</h2>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label" for="id_number">ID number</label>
                    <input id="id_number" type="text" name="id_number" class="form-control" value="{{ old('id_number', $student->id_number) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="qrcode">QR code</label>
                    <input id="qrcode" type="text" name="qrcode" class="form-control" value="{{ old('qrcode', $student->qrcode) }}" readonly>
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="firstname">First name</label>
                    <input id="firstname" type="text" name="firstname" class="form-control" value="{{ old('firstname', $student->firstname) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="lastname">Last name</label>
                    <input id="lastname" type="text" name="lastname" class="form-control" value="{{ old('lastname', $student->lastname) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="middle_initial">Middle initial</label>
                    <input id="middle_initial" type="text" name="middle_initial" class="form-control" value="{{ old('middle_initial', $student->middle_initial) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="birthday">Birthday</label>
                    <input id="birthday" type="date" name="birthday" class="form-control" value="{{ old('birthday', $student->birthday?->format('Y-m-d')) }}">
                    @error('birthday')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="mobile_number">Mobile number</label>
                    <input id="mobile_number" type="text" name="mobile_number" class="form-control" placeholder="09XXXXXXXXX" value="{{ old('mobile_number', $student->mobile_number) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="course">Course</label>
                    <input id="course" type="text" name="course" class="form-control" value="{{ old('course', $student->course) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="year">Year level</label>
                    <select id="year" name="year" class="form-select">
                        <option value="">Select year</option>
                        @foreach ([
                            '1st Year' => ['1st Year', 'First Year'],
                            '2nd Year' => ['2nd Year', 'Second Year'],
                            '3rd Year' => ['3rd Year', 'Third Year'],
                            '4th Year' => ['4th Year', 'Fourth Year'],
                            '5th Year' => ['5th Year', 'Fifth Year'],
                            '6th Year' => ['6th Year', 'Sixth Year'],
                        ] as $label => $aliases)
                            <option value="{{ $label }}" @selected(in_array(old('year', $student->year), $aliases, true))>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label" for="address">Address</label>
                    <textarea id="address" name="address" class="form-control" rows="3">{{ old('address', $student->address) }}</textarea>
                </div>
            </div>
        </section>

        <section class="patron-section">
            <h2>Emergency contact</h2>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label" for="emergency_person">Contact person</label>
                    <input id="emergency_person" type="text" name="emergency_person" class="form-control" value="{{ old('emergency_person', $student->emergency_person) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="emergency_relationship">Relationship</label>
                    <input id="emergency_relationship" type="text" name="emergency_relationship" class="form-control" value="{{ old('emergency_relationship', $student->emergency_relationship) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="emergency_number">Contact number</label>
                    <input id="emergency_number" type="text" name="emergency_number" class="form-control" placeholder="09XXXXXXXXX" value="{{ old('emergency_number', $student->emergency_number) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="emergency_address">Address</label>
                    <textarea id="emergency_address" name="emergency_address" class="form-control" rows="2">{{ old('emergency_address', $student->emergency_address) }}</textarea>
                </div>
            </div>
        </section>

        <section class="patron-section">
            <h2>Photo and signature</h2>
            <div class="patron-media">
                <div>
                    <label class="form-label" for="profile_picture">Profile picture</label>
                    @if($student->profile_picture)
                        <img src="{{ asset($student->profile_picture) }}" alt="Profile picture" class="patron-photo d-block mb-2">
                    @else
                        <div class="patron-photo patron-photo--empty mb-2">No photo yet</div>
                    @endif
                    <input id="profile_picture" type="file" name="profile_picture" class="form-control" accept=".jpg,.jpeg,.png">
                </div>
                <div>
                    <label class="form-label" for="studentSignaturePad">Signature</label>
                    <canvas id="studentSignaturePad" class="patron-signature" width="500" height="150"></canvas>
                    <input type="hidden" name="student_signature" id="studentSignatureInput" value="{{ old('student_signature', $student->student_signature) }}">
                    <button type="button" id="clearStudentSignature" class="btn btn-outline-danger btn-sm mt-2">Clear</button>
                    @if($student->student_signature)
                        <img src="{{ asset($student->student_signature) }}" alt="Current signature" class="patron-signature-preview d-block">
                    @endif
                </div>
            </div>
        </section>

        <div class="patron-actions">
            <a href="{{ route('students.index') }}" class="btn btn-quiet">Cancel</a>
            <button type="submit" class="btn btn-save">Update student</button>
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
