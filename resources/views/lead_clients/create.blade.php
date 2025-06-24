<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Lead Client</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Create Lead Client</h2>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('lead-clients.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
            </div>
            <div class="mb-3">
                <label for="mobile_number" class="form-label">Mobile Number</label>
                <input type="text" class="form-control" id="mobile_number" name="mobile_number" value="{{ old('mobile_number') }}">
            </div>
            <div class="mb-3">
                <label for="note" class="form-label">Note</label>
                <textarea class="form-control" id="note" name="note">{{ old('note') }}</textarea>
            </div>
            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="new_lead" {{ old('status') == 'new_lead' ? 'selected' : '' }}>New Lead</option>
                    <option value="spam" {{ old('status') == 'spam' ? 'selected' : '' }}>Spam</option>
                    <option value="junk" {{ old('status') == 'junk' ? 'selected' : '' }}>Junk</option>
                    <option value="clear" {{ old('status') == 'clear' ? 'selected' : '' }}>Clear</option>
                    <option value="unmarked" {{ old('status', 'unmarked') == 'unmarked' ? 'selected' : '' }}>Unmarked</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="lead_type" class="form-label">Lead Type</label>
                <select class="form-select" id="lead_type" name="lead_type">
                    <option value="manual" {{ old('lead_type', 'manual') == 'manual' ? 'selected' : '' }}>Manual</option>
                    <option value="webhook" {{ old('lead_type') == 'webhook' ? 'selected' : '' }}>Webhook</option>
                    <option value="ppc" {{ old('lead_type') == 'ppc' ? 'selected' : '' }}>PPC</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="user_status" class="form-label">User Status</label>
                <select class="form-select" id="user_status" name="user_status">
                    <option value="normal" {{ old('user_status', 'normal') == 'normal' ? 'selected' : '' }}>Normal</option>
                    <option value="agent" {{ old('user_status') == 'agent' ? 'selected' : '' }}>Agent</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="user_type" class="form-label">User Type</label>
                <select class="form-select" id="user_type" name="user_type">
                    <option value="user" {{ old('user_type', 'user') == 'user' ? 'selected' : '' }}>User</option>
                    <option value="admin" {{ old('user_type') == 'admin' ? 'selected' : '' }}>Admin</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="admin_status" class="form-label">Admin Status</label>
                <select class="form-select" id="admin_status" name="admin_status">
                    <option value="Contacted" {{ old('admin_status', 'Contacted') == 'Contacted' ? 'selected' : '' }}>Contacted</option>
                    <option value="Appointment set" {{ old('admin_status') == 'Appointment set' ? 'selected' : '' }}>Appointment set</option>
                    <option value="Burst" {{ old('admin_status') == 'Burst' ? 'selected' : '' }}>Burst</option>
                    <option value="call_back_later" {{ old('admin_status') == 'call_back_later' ? 'selected' : '' }}>Call Back Later</option>
                    <option value="interested" {{ old('admin_status') == 'interested' ? 'selected' : '' }}>Interested</option>
                    <option value="not_interested" {{ old('admin_status') == 'not_interested' ? 'selected' : '' }}>Not Interested</option>
                    <option value="wrong_number" {{ old('admin_status') == 'wrong_number' ? 'selected' : '' }}>Wrong Number</option>
                    <option value="not_reachable" {{ old('admin_status') == 'not_reachable' ? 'selected' : '' }}>Not Reachable</option>
                    <option value="dnd" {{ old('admin_status') == 'dnd' ? 'selected' : '' }}>DND</option>
                    <option value="not_eligible" {{ old('admin_status') == 'not_eligible' ? 'selected' : '' }}>Not Eligible</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="is_admin_spam" class="form-label">Is Admin Spam</label>
                <select class="form-select" id="is_admin_spam" name="is_admin_spam">
                    <option value="0" {{ old('is_admin_spam', '0') == '0' ? 'selected' : '' }}>No</option>
                    <option value="1" {{ old('is_admin_spam') == '1' ? 'selected' : '' }}>Yes</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Create</button>
        </form>
        <br>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 