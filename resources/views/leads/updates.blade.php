@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Updates for Lead ID: {{ $leadId }}</h1>

    <table class="table table-striped" id="updates-table">
        <thead>
            <tr>
                <th>Message</th>
                <th>User</th>
                <th>Timestamp</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    const leadId = {{ $leadId }};
    fetchUpdates(leadId);

    function fetchUpdates(leadId) {
        $('#updates-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: `/api/leads/${leadId}/updates`,
                type: 'GET'
            },
            columns: [
                { data: 'lead_message', name: 'lead_message' },
                { data: 'user', name: 'user' },
                { data: 'created_at', name: 'created_at' }
            ]
        });
    }
});
</script>
@endsection
