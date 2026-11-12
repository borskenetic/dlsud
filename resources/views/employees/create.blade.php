@extends('layouts.sidebar')

@section('title', 'Register Faculty & Staff')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/students/patron-form.css') }}?v=1">
@endsection

@section('content')
<div class="patron-form">
    <div class="patron-form__hero">
        <div>
            <p class="patron-form__kicker">Library patrons</p>
            <h1>Register faculty &amp; staff</h1>
            <p class="patron-form__lead">Add a faculty or staff patron for circulation and library attendance.</p>
        </div>
        <a href="{{ route('employees.index') }}" class="btn btn-quiet">Back to faculty</a>
    </div>

            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
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

            <form id="employeeForm" action="{{ route('employees.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <section class="patron-section">
                <h2>Faculty &amp; staff information</h2>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">First Name</label>
                        <input type="text" name="firstname" class="form-control" value="{{ old('firstname') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Last Name</label>
                        <input type="text" name="lastname" class="form-control" value="{{ old('lastname') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Middle Initial</label>
                        <input type="text" name="middle_initial" class="form-control" maxlength="16" value="{{ old('middle_initial') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Employee ID</label>
                        <input type="text" name="employee_id" class="form-control" value="{{ old('employee_id') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Designation</label>
                        <input type="text" name="designation" class="form-control" placeholder="e.g. Instructor I, Librarian"
                               value="{{ old('designation') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Select Program</label>
                        <select name="program" class="form-select" required>
                            <option value="">— Select program —</option>
                            @foreach ($programs as $program)
                                <option value="{{ $program->program_code }}" @selected(old('program') === $program->program_code)>
                                    {{ $program->program_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Year of start of work in this HEI</label>
                        <select name="year_start_work" class="form-select" required>
                            <option value="">— Select year —</option>
                            @foreach ($workStartYears as $yr)
                                <option value="{{ $yr }}" @selected(old('year_start_work') == (string) $yr)>{{ $yr }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Birthday</label>
                        <input type="date" name="birth_date" class="form-control" value="{{ old('birth_date') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Mobile Number</label>
                        <input type="text" name="mobile_number" class="form-control" placeholder="09XXXXXXXXX"
                               value="{{ old('mobile_number') }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Address</label>
                        <textarea name="address" class="form-control" rows="3">{{ old('address') }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Formal picture</label>
                        <input type="file" name="formal_picture" class="form-control" accept=".jpg,.jpeg,.png">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Signature (draw below)</label>
                        <canvas id="employeeSignaturePad" class="patron-signature" width="500" height="150"></canvas>
                        <input type="hidden" name="employee_signature" id="employeeSignatureInput" value="{{ old('employee_signature') }}">
                        <button type="button" id="clearEmployeeSignature" class="btn btn-outline-danger btn-sm mt-2">Clear</button>
                    </div>
                </div>
                </section>

                <section class="patron-section">
                <h2>Emergency contact</h2>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Contact person</label>
                        <input type="text" name="emergency_contact_name" class="form-control" value="{{ old('emergency_contact_name') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Relationship</label>
                        <input type="text" name="emergency_contact_relationship" class="form-control" value="{{ old('emergency_contact_relationship') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Contact number</label>
                        <input type="text" name="emergency_contact_number" class="form-control" value="{{ old('emergency_contact_number') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Emergency address</label>
                        <textarea name="emergency_address" class="form-control" rows="2">{{ old('emergency_address') }}</textarea>
                    </div>
                </div>
                </section>

                <div class="patron-actions">
                    <a href="{{ route('employees.index') }}" class="btn btn-quiet">Cancel</a>
                    <button type="submit" class="btn btn-save">Register faculty &amp; staff</button>
                </div>
            </form>
</div>
@endsection

@section('scripts')
<script src="{{ asset('vendor/signature_pad/signature_pad.umd.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const canvas = document.getElementById('employeeSignaturePad');
    if (!canvas || typeof SignaturePad === 'undefined') return;
    const signaturePad = new SignaturePad(canvas);
    const input = document.getElementById('employeeSignatureInput');
    document.getElementById('clearEmployeeSignature')?.addEventListener('click', () => {
        signaturePad.clear();
        input.value = '';
    });
    document.getElementById('employeeForm')?.addEventListener('submit', () => {
        if (!signaturePad.isEmpty()) input.value = signaturePad.toDataURL();
    });
});
</script>
@endsection
