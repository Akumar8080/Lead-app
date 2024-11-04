@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Lead Management</h1>

    <h2>Add New Lead</h2>
    <form id="add-lead-form" onsubmit="addLead(event)">
        <input type="text" id="new-lead-name" placeholder="Name" required>
        <input type="hidden" id="lead-id" value="">
        <input type="text" id="new-lead-email" placeholder="Email" required>
        <input type="text" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" maxlength="10" id="new-lead-mobile" placeholder="Mobile" required>
        <input type="text" id="new-lead-source" placeholder="Source" required>
        <textarea id="new-lead-description" placeholder="Description"></textarea>
        <select id="new-lead-status" required>
            <option value="new">New</option>
            <option value="accepted">Accepted</option>
            <option value="completed">Completed</option>
            <option value="rejected">Rejected</option>
            <option value="invalid">Invalid</option>
        </select>
        <button id="addUpdateLead" type="submit">Add Lead</button>
    </form>
    <hr>

    <table id="lead-table" class="table table-striped">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Mobile</th>
                <th>Description</th>
                <th>Source</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
<!-- Modal for Posting Update -->
<div class="modal fade" id="postUpdateModal" tabindex="-1" aria-labelledby="postUpdateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="postUpdateModalLabel">Post Update</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="post-update-form">
                    <div class="form-group">
                        <label for="user-name">User Name</label>
                        <input type="text" class="form-control" id="user-name" required>
                    </div>
                    <div class="form-group">
                        <label for="update-message">Update Message</label>
                        <textarea class="form-control" id="update-message" required></textarea>
                    </div>
                    <input type="hidden" id="lead-id">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="submit-update">Post Update</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
$(document).ready(function() {
    fetchLeads();

    function fetchLeads() {
        $('#lead-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '/api/leads',
                type: 'GET',
                data: function(d) {
                    d.filter = $('#filter').val();
                }
            },
            columns: [
                { data: 'name', name: 'name' },
                { data: 'email', name: 'email' },
                { data: 'mobile', name: 'mobile' },
                { data: 'description', name: 'description' },
                { data: 'source', name: 'source' },
                { data: 'status', name: 'status' },
                { 
                    data: null,
                    render: function(data, type, row) {
                        return `<button onclick="editLead(${row.id})">Edit</button>
                                <button onclick="showPostUpdateModal(${row.id})">Post Update</button>
                                <a href="/leads/${row.id}/updates"><button>View Updates</button></a>`;
                    }
                }
            ],
            paging: true, // Enable pagination
            pageLength: 20, // Default number of records per page
            lengthMenu: [10, 20, 50, 100]
        });
    }
    window.addLead = function(event) {
        event.preventDefault();
        if($('#lead-id').val() != ''){
            updateLead($('#lead-id').val());
        }else{

            const data = {
                name: $('#new-lead-name').val(),
                email: $('#new-lead-email').val(),
                mobile: $('#new-lead-mobile').val(),
                source: $('#new-lead-source').val(),
                description: $('#new-lead-description').val(),
                status: $('#new-lead-status').val()
            };
    
            $.ajax({
                url: '/api/leads',
                type: 'POST',
                data: JSON.stringify(data),
                contentType: 'application/json',
                success: function(response) {
                    $('#add-lead-form').trigger('reset');
                    $('#lead-table').DataTable().ajax.reload();
                    alert("Lead saved!");
                },
                error: function(response) {
                    alert(response.responseJSON.message);
                }
            });
        }
    };

    window.editLead = function(id) {
        $.get(`/api/leads/${id}`, function(lead) {
            // Fill in the edit form with lead data
            $('#lead-id').val(lead.id);
            $('#new-lead-name').val(lead.name);
            $('#new-lead-email').val(lead.email);
            $('#new-lead-mobile').val(lead.mobile);
            $('#new-lead-source').val(lead.source);
            $('#new-lead-description').val(lead.description);
            $('#new-lead-status').val(lead.status);
        });
        $("#addUpdateLead").html('Update Lead');
    };

    window.updateLead = function(id) {
        const data = {
            name: $('#new-lead-name').val(),
            email: $('#new-lead-email').val(),
            mobile: $('#new-lead-mobile').val(),
            source: $('#new-lead-source').val(),
            description: $('#new-lead-description').val(),
            status: $('#new-lead-status').val()
        };
        $.ajax({
            url: `/api/leads/${id}`,
            type: 'PUT',
            data: JSON.stringify(data),
            contentType: 'application/json',
            success: function(response) {
                $('#lead-table').DataTable().ajax.reload();
                $('#add-lead-form').trigger('reset');
                $("#addUpdateLead").html('Add Lead');
            },
            error: function(response) {
                alert(response.responseJSON.message);
            }
        });
    };

    window.showPostUpdateModal = function(leadId) {
        $('#lead-id').val(leadId); 
        $('#postUpdateModal').modal('show');
    };

    $('#submit-update').on('click', function() {
        const leadId = $('#lead-id').val();
        const leadMessage = $('#update-message').val();
        const userName = $('#user-name').val();

        $.post(`/api/leads/${leadId}/updates`, {
            lead_message: leadMessage,
            user: userName
        }, function() {
            $('#post-update-form').trigger('reset'); // Clear the form
            $('#postUpdateModal').modal('hide'); // Hide the modal
            $('#lead-table').DataTable().ajax.reload(); // Refresh the lead table
        }).fail(function() {
            alert('Failed to post update.');
        });
    });
});
</script>
@endsection
